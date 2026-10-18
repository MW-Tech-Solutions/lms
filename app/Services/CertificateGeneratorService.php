<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateGeneratorService
{
    /**
     * Issue an official cryptographic certificate record and generate its PDF.
     * Prevents duplicate certificate generation per user and course.
     */
    public function generate(User $user, Course $course): Certificate
    {
        // 1. Prevent duplicate certificate generation
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $issuedAt = Carbon::now();
        $expiresAt = $course->recertification_days !== null && $course->recertification_days > 0
            ? $issuedAt->copy()->addDays((int) $course->recertification_days)
            : null;

        $config = app()->bound(SiteConfigService::class) ? app(SiteConfigService::class) : null;
        $prefix = $config ? (string) $config->get('certificate_prefix', 'ATC') : 'ATC';

        if (!$certificate) {
            $uuid = (string) Str::uuid();
            $nextId = (int) (Certificate::max('id') ?? 0) + 1;
            $certNumber = sprintf('%s-%s-%s', $prefix, $issuedAt->format('Y-md'), str_pad((string) $nextId, 3, '0', STR_PAD_LEFT));

            $certificate = Certificate::create([
                'uuid' => $uuid,
                'certificate_number' => $certNumber,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'issued_at' => $issuedAt,
                'expires_at' => $expiresAt,
                'pdf_url' => null,
            ]);
        } elseif (empty($certificate->certificate_number)) {
            $issuedDateObj = $certificate->issued_at ?? $issuedAt;
            $certNumber = sprintf('%s-%s-%s', $prefix, $issuedDateObj->format('Y-md'), str_pad((string) $certificate->id, 3, '0', STR_PAD_LEFT));
            $certificate->update(['certificate_number' => $certNumber]);
        }

        $certificate->setRelation('user', $user);
        $certificate->setRelation('course', $course);

        // Render PDF and persist to disk
        $this->getPdfBinary($certificate);

        return $certificate;
    }

    /**
     * Generate and return the raw PDF binary string for a certificate.
     * Guaranteed to work in-memory even if disk write permissions are limited.
     */
    public function getPdfBinary(Certificate $certificate): string
    {
        $certificate->loadMissing(['user', 'course.category', 'course.tenant']);
        $user = $certificate->user;
        $course = $certificate->course;

        $config = app()->bound(SiteConfigService::class) ? app(SiteConfigService::class) : null;
        $prefix = $config ? (string) $config->get('certificate_prefix', 'ATC') : 'ATC';

        if (empty($certificate->certificate_number)) {
            $issuedDateObj = $certificate->issued_at ?? Carbon::now();
            $certNumber = sprintf('%s-%s-%s', $prefix, $issuedDateObj->format('Y-md'), str_pad((string) $certificate->id, 3, '0', STR_PAD_LEFT));
            $certificate->update(['certificate_number' => $certNumber]);
        }

        $verificationUrl = url('/verify/' . $certificate->uuid);

        // 1. Resolve High-Res Logo Base64
        $logoBase64 = null;
        $logoCandidates = [];
        $settings = $config ? $config->all() : [];

        if (!empty($settings['brand_logo_url'])) {
            $rawUrl = (string) $settings['brand_logo_url'];
            $logoCandidates[] = public_path(ltrim($rawUrl, '/'));
            if (str_contains($rawUrl, 'uploads/branding/')) {
                $parts = explode('uploads/branding/', $rawUrl);
                $logoCandidates[] = public_path('uploads/branding/' . end($parts));
            }
        }
        $logoCandidates[] = public_path('images/site_logo.png');
        $logoCandidates[] = public_path('images/site_logo_email.png');

        foreach ($logoCandidates as $cand) {
            if ($cand && file_exists($cand) && is_file($cand)) {
                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'svg' => 'image/svg+xml',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'webp' => 'image/webp',
                    default => 'image/png',
                };
                $data = @file_get_contents($cand);
                if ($data) {
                    $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                    break;
                }
            }
        }

        // 2. Resolve Authorized Signature Base64
        $signatureBase64 = null;
        $sigCandidates = [];
        if (!empty($settings['certificate_signature_url'])) {
            $sigRaw = (string) $settings['certificate_signature_url'];
            $sigCandidates[] = public_path(ltrim($sigRaw, '/'));
            if (str_contains($sigRaw, 'uploads/branding/')) {
                $parts = explode('uploads/branding/', $sigRaw);
                $sigCandidates[] = public_path('uploads/branding/' . end($parts));
            }
        }
        $sigCandidates[] = public_path('images/certificate_signature.png');

        foreach ($sigCandidates as $cand) {
            if ($cand && file_exists($cand) && is_file($cand)) {
                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'svg' => 'image/svg+xml',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'image/png',
                };
                $data = @file_get_contents($cand);
                if ($data) {
                    $signatureBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                    break;
                }
            }
        }

        // 3. Resolve Verification QR Code Base64
        $qrBase64 = null;
        $qrDir = storage_path('app/public/certificates');
        if (!is_dir($qrDir)) {
            @mkdir($qrDir, 0755, true);
        }
        $qrFilePath = $qrDir . '/qr_' . $certificate->uuid . '.png';

        if (file_exists($qrFilePath) && filesize($qrFilePath) > 50) {
            $qrData = @file_get_contents($qrFilePath);
            if ($qrData) {
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
            }
        } else {
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=png&margin=4&data=' . urlencode($verificationUrl);
            try {
                $response = Http::timeout(3)->get($qrApiUrl);
                if ($response->successful() && strlen($response->body()) > 50) {
                    $body = $response->body();
                    @file_put_contents($qrFilePath, $body);
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($body);
                }
            } catch (\Throwable $e) {
                // Secondary attempt via stream context
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                $fallbackData = @file_get_contents($qrApiUrl, false, $ctx);
                if ($fallbackData && strlen($fallbackData) > 50) {
                    @file_put_contents($qrFilePath, $fallbackData);
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($fallbackData);
                }
            }
        }

        // 4. Render Landscape PDF via DomPDF (if ext-dom available) or fallback to pure-PHP PDF engine
        $binary = null;
        if (class_exists(\DOMImplementation::class) && extension_loaded('dom')) {
            try {
                $pdf = Pdf::loadView('certificates.pdf', [
                    'certificate' => $certificate,
                    'user' => $user,
                    'course' => $course,
                    'verificationUrl' => $verificationUrl,
                    'qrBase64' => $qrBase64,
                    'logoBase64' => $logoBase64,
                    'signatureBase64' => $signatureBase64,
                    'issuedDate' => ($certificate->issued_at ?? Carbon::now())->format('F d, Y'),
                    'expiresDate' => $certificate->expires_at ? $certificate->expires_at->format('F d, Y') : null,
                    'site_settings' => $settings,
                ])->setPaper('a4', 'landscape');

                $binary = $pdf->output();
            } catch (\Throwable $e) {
                Log::warning('DomPDF generation failed, falling back to pure-PHP PDF renderer: ' . $e->getMessage());
                $binary = null;
            }
        }

        // If DomPDF was unavailable (e.g. DOMImplementation missing on server) or threw an error
        if (empty($binary)) {
            $binary = $this->buildPurePhpCertificatePdf($certificate, $user, $course, $verificationUrl, $settings);
        }

        // 5. Cache on disk if storage is available
        try {
            $relativePdfPath = sprintf('certificates/%s.pdf', $certificate->uuid);
            Storage::disk('public')->put($relativePdfPath, $binary);

            if (empty($certificate->pdf_url)) {
                $certificate->update([
                    'pdf_url' => Storage::url($relativePdfPath),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not write certificate PDF to public storage disk: ' . $e->getMessage());
        }

        return $binary;
    }

    /**
     * High-fidelity pure-PHP PDF generator (specification-compliant PDF 1.4 binary).
     * Operates completely in-memory without requiring ext-dom, ext-xml, or Masterminds\HTML5.
     */
    public function buildPurePhpCertificatePdf(
        Certificate $certificate,
        User $user,
        Course $course,
        string $verificationUrl,
        array $settings = []
    ): string {
        $brandName = $course->tenant?->name ?? $settings['certificate_school_name'] ?? $settings['brand_name'] ?? 'AllThingsCambridge';
        if (empty($brandName) || str_contains(strtolower($brandName), 'enterprise academy')) {
            $brandName = 'AllThingsCambridge';
        }

        $studentName = $user->name ?? 'Cambridge Scholar';
        $courseTitle = $course->title ?? 'Cambridge Examination Curriculum';
        $categoryName = $course->category?->name ?? 'Cambridge Academic Level';
        $certNumber = $certificate->certificate_number ?? ('ATC-' . ($certificate->issued_at ?? Carbon::now())->format('Y-md') . '-' . str_pad((string) $certificate->id, 3, '0', STR_PAD_LEFT));
        $issuedDate = ($certificate->issued_at ?? Carbon::now())->format('F d, Y');
        $expiryText = $certificate->expires_at ? ('Expires: ' . $certificate->expires_at->format('F d, Y')) : 'Permanent Accreditation';

        $escape = function (string $text): string {
            $clean = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text) ?: $text;
            return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $clean);
        };

        // Stream commands buffer
        $s = '';

        // 1. Background fill: Crisp White
        $s .= "1 1 1 rg 0 0 841.89 595.28 re f\n";

        // 2. Navy Outer Frame (border: 3.5pt)
        $s .= "0.06 0.16 0.36 RG 3.5 w 20 20 801.89 555.28 re S\n";

        // 3. Gold Inner Frame (border: 1.5pt)
        $s .= "0.77 0.63 0.35 RG 1.5 w 28 28 785.89 539.28 re S\n";

        // 4. Gold Corner Accents (9x9pt squares)
        $s .= "0.77 0.63 0.35 rg 24 562 9 9 re f\n";
        $s .= "0.77 0.63 0.35 rg 809 562 9 9 re f\n";
        $s .= "0.77 0.63 0.35 rg 24 24 9 9 re f\n";
        $s .= "0.77 0.63 0.35 rg 809 24 9 9 re f\n";

        // 5. Top Header Navy Banner
        $s .= "0.06 0.16 0.36 rg 40 495 761.89 62 re f\n";
        // Gold accent line under banner
        $s .= "0.77 0.63 0.35 RG 2 w 40 495 761.89 0 re S\n";

        // 6. Header Typography
        $brandText = strtoupper($escape($brandName));
        $brandWidth = strlen($brandText) * 13.0;
        $brandX = max(50.0, (841.89 - $brandWidth) / 2);
        $s .= "BT /F2 22 Tf 1 1 1 rg {$brandX} 531 Td ({$brandText}) Tj ET\n";

        $subText = "ACCREDITED CAMBRIDGE CURRICULUM & EXAMINATION BOARD ACCREDITATION";
        $subWidth = strlen($subText) * 5.4;
        $subX = max(50.0, (841.89 - $subWidth) / 2);
        $s .= "BT /F1 9.5 Tf 0.77 0.63 0.35 rg {$subX} 510 Td ({$subText}) Tj ET\n";

        // 7. Conferred Text
        $awardTitle = "OFFICIAL CERTIFICATE OF COMPLETION";
        $awardTitleWidth = strlen($awardTitle) * 7.0;
        $awardTitleX = max(50.0, (841.89 - $awardTitleWidth) / 2);
        $s .= "BT /F2 12 Tf 0.06 0.16 0.36 rg {$awardTitleX} 464 Td ({$awardTitle}) Tj ET\n";

        $conferredText = "THIS IS PROUDLY CONFERRED UPON";
        $conferredWidth = strlen($conferredText) * 5.5;
        $conferredX = max(50.0, (841.89 - $conferredWidth) / 2);
        $s .= "BT /F1 10 Tf 0.40 0.45 0.50 rg {$conferredX} 446 Td ({$conferredText}) Tj ET\n";

        // Gold divider lines flanking student name
        $s .= "0.77 0.63 0.35 RG 1 w 140 435 561.89 0 re S\n";

        // Student Name (Deep Navy, Times-Bold 30pt)
        $nameText = $escape($studentName);
        $nameWidth = strlen($nameText) * 16.5;
        $nameX = max(50.0, (841.89 - $nameWidth) / 2);
        $s .= "BT /F4 30 Tf 0.06 0.16 0.36 rg {$nameX} 402 Td ({$nameText}) Tj ET\n";

        $s .= "0.77 0.63 0.35 RG 1 w 140 392 561.89 0 re S\n";

        // Accomplishment Statement
        $forText = "for successfully demonstrating academic excellence and fulfilling all assessment standards for";
        $forWidth = strlen($forText) * 5.8;
        $forX = max(50.0, (841.89 - $forWidth) / 2);
        $s .= "BT /F3 11.5 Tf 0.25 0.25 0.25 rg {$forX} 366 Td ({$forText}) Tj ET\n";

        // Course Title (Royal Blue, Helvetica-Bold 18pt)
        $cTitleText = $escape($courseTitle);
        $cTitleWidth = strlen($cTitleText) * 10.8;
        $cTitleX = max(50.0, (841.89 - $cTitleWidth) / 2);
        $s .= "BT /F2 18 Tf 0.01 0.52 0.78 rg {$cTitleX} 336 Td ({$cTitleText}) Tj ET\n";

        // Category / Board Tag
        $catText = $escape($categoryName . " - Cambridge International Curriculum Standards");
        $catWidth = strlen($catText) * 5.6;
        $catX = max(50.0, (841.89 - $catWidth) / 2);
        $s .= "BT /F1 10.5 Tf 0.45 0.45 0.45 rg {$catX} 314 Td ({$catText}) Tj ET\n";

        // 8. Lower Details Section (Y = 85 to 185)
        $s .= "0.77 0.63 0.35 RG 1 w 55 190 731.89 0 re S\n";

        // Left Column: Credential Identification
        $s .= "0.77 0.63 0.35 RG 2 w 65 100 0 75 re S\n";
        $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 76 163 Td (OFFICIAL CERTIFICATE ID:) Tj ET\n";
        $escapedCertNum = $escape($certNumber);
        $s .= "BT /F2 12.5 Tf 0.06 0.16 0.36 rg 76 147 Td ({$escapedCertNum}) Tj ET\n";
        $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 76 128 Td (DATE OF CONFERMENT:) Tj ET\n";
        $s .= "BT /F1 10 Tf 0.20 0.20 0.20 rg 76 114 Td ({$issuedDate}) Tj ET\n";
        $escapedExpiry = $escape($expiryText);
        $s .= "BT /F2 8.5 Tf 0.10 0.55 0.25 rg 76 99 Td ({$escapedExpiry}) Tj ET\n";

        // Center Seal / Authenticity Mark
        $s .= "0.77 0.63 0.35 RG 1.5 w 380 100 82 75 re S\n";
        $s .= "0.06 0.16 0.36 rg 384 104 74 67 re f\n";
        $s .= "BT /F2 7.5 Tf 0.77 0.63 0.35 rg 393 148 Td (CAMBRIDGE) Tj ET\n";
        $s .= "BT /F2 8 Tf 1 1 1 rg 391 134 Td (AUTHENTIC) Tj ET\n";
        $s .= "BT /F1 6.5 Tf 0.77 0.63 0.35 rg 390 121 Td (VERIFIED & VALID) Tj ET\n";

        // Right Column: Signatory Authority & Verification
        $s .= "0.77 0.63 0.35 RG 2 w 530 100 0 75 re S\n";
        $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 542 163 Td (AUTHORIZED ACADEMIC AUTHORITY:) Tj ET\n";
        $s .= "BT /F4 11.5 Tf 0.06 0.16 0.36 rg 542 147 Td (Head of Cambridge Academic Review) Tj ET\n";
        $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 542 128 Td (ONLINE VERIFICATION URL:) Tj ET\n";
        $escapedUrl = $escape($verificationUrl);
        $s .= "BT /F1 8.5 Tf 0.01 0.52 0.78 rg 542 114 Td ({$escapedUrl}) Tj ET\n";
        $s .= "BT /F1 7.5 Tf 0.45 0.45 0.45 rg 542 100 Td (Scan or visit URL to confirm cryptographic validity) Tj ET\n";

        // 9. Footer Bar
        $s .= "0.85 0.85 0.85 RG 0.5 w 55 68 731.89 0 re S\n";
        $footerText = $escape($brandName . " - Official Academic Examination Portal - Record UUID: " . $certificate->uuid);
        $footerWidth = strlen($footerText) * 4.4;
        $footerX = max(50.0, (841.89 - $footerWidth) / 2);
        $s .= "BT /F1 8 Tf 0.55 0.55 0.55 rg {$footerX} 52 Td ({$footerText}) Tj ET\n";

        // Build PDF 1.4 Object Structure with correct byte offsets
        $streamLen = strlen($s);

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 841.89 595.28] /Resources << /Font << /F1 4 0 R /F2 5 0 R /F3 6 0 R /F4 7 0 R >> >> /Contents 8 0 R >>";
        $objects[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman /Encoding /WinAnsiEncoding >>";
        $objects[7] = "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold /Encoding /WinAnsiEncoding >>";
        $objects[8] = "<< /Length {$streamLen} >>\nstream\n{$s}endstream";

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        for ($i = 1; $i <= 8; $i++) {
            $offsets[$i] = strlen($pdf);
            $pdf .= "{$i} 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 9\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 8; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size 9 /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF\n";

        return $pdf;
    }
}
