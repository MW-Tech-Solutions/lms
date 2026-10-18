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

        // 4. Generate high-fidelity certificate PDF binary matching attached design
        $binary = $this->buildPurePhpCertificatePdf($certificate, $user, $course, $verificationUrl, $settings);

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
     * Generate dynamic composite certificate JPEG image matching the attached design.
     * Renders cursive calligraphy recipient name, emerald course title, date, ID,
     * and dynamic scannable QR code directly onto the base certificate template.
     */
    public function generateCertificateCompositeImage(
        Certificate $certificate,
        User $user,
        Course $course,
        string $verificationUrl,
        array $settings = []
    ): ?string {
        if (!function_exists('imagecreatefromjpeg')) {
            return null;
        }

        $templatePath = public_path('images/certificate_template_base.jpg');
        if (!file_exists($templatePath) || filesize($templatePath) < 5000) {
            return null;
        }

        $im = @imagecreatefromjpeg($templatePath);
        if (!$im) {
            return null;
        }

        $studentName = trim($user->name ?? 'Cambridge Scholar');
        $courseTitle = trim($course->title ?? 'Cambridge Examination Curriculum');
        $certNumber = $certificate->certificate_number ?? ('ATC-' . ($certificate->issued_at ?? Carbon::now())->format('Y-md') . '-' . str_pad((string) $certificate->id, 3, '0', STR_PAD_LEFT));
        $issuedDate = ($certificate->issued_at ?? Carbon::now())->format('F d, Y');

        $fontScript = public_path('fonts/GreatVibes-Regular.ttf');
        $fontSans = public_path('fonts/Montserrat-Bold.ttf');

        // Colors matching attached certificate design
        $darkGreen = imagecolorallocate($im, 6, 78, 59);     // #064E3B for cursive student name
        $lineGreen = imagecolorallocate($im, 4, 120, 87);    // #047857 for underline
        $courseGreen = imagecolorallocate($im, 5, 150, 105); // #059669 for course title
        $darkSlate = imagecolorallocate($im, 15, 23, 42);    // #0F172A for metadata (Date, ID)

        // 1. Draw Recipient Name (Calligraphic Cursive)
        if (file_exists($fontScript) && function_exists('imagettfbbox')) {
            $nameFontSize = 40;
            $bbox = imagettfbbox($nameFontSize, 0, $fontScript, $studentName);
            $nameWidth = abs($bbox[4] - $bbox[0]);
            if ($nameWidth > 580) {
                $nameFontSize = max(20, (int) (40 * (580 / $nameWidth)));
                $bbox = imagettfbbox($nameFontSize, 0, $fontScript, $studentName);
                $nameWidth = abs($bbox[4] - $bbox[0]);
            }
            $nameX = (int) ((942 - $nameWidth) / 2);
            $nameY = 328;
            imagettftext($im, $nameFontSize, 0, $nameX, $nameY, $darkGreen, $fontScript, $studentName);

            // Centered horizontal green underline
            $lineWidth = min(560, max(380, (int) ($nameWidth * 1.15)));
            $lineX1 = (int) ((942 - $lineWidth) / 2);
            $lineX2 = $lineX1 + $lineWidth;
            imagesetthickness($im, 2);
            imageline($im, $lineX1, 344, $lineX2, 344, $lineGreen);
        } else {
            // GD built-in font fallback
            $nameWidth = strlen($studentName) * 9;
            $nameX = max(60, (int) ((942 - $nameWidth) / 2));
            imagestring($im, 5, $nameX, 315, $studentName, $darkGreen);
        }

        // 2. Draw Course Title (Bold Emerald Green)
        if (file_exists($fontSans) && function_exists('imagettfbbox')) {
            $courseFontSize = 18;
            $cBox = imagettfbbox($courseFontSize, 0, $fontSans, $courseTitle);
            $courseWidth = abs($cBox[4] - $cBox[0]);
            if ($courseWidth > 580) {
                $courseFontSize = max(11, (int) (18 * (580 / $courseWidth)));
                $cBox = imagettfbbox($courseFontSize, 0, $fontSans, $courseTitle);
                $courseWidth = abs($cBox[4] - $cBox[0]);
            }
            $courseX = (int) ((942 - $courseWidth) / 2);
            $courseY = 404;
            imagettftext($im, $courseFontSize, 0, $courseX, $courseY, $courseGreen, $fontSans, $courseTitle);
        } else {
            $courseWidth = strlen($courseTitle) * 9;
            $courseX = max(60, (int) ((942 - $courseWidth) / 2));
            imagestring($im, 5, $courseX, 392, $courseTitle, $courseGreen);
        }

        // 3. Draw Completion Date
        if (file_exists($fontSans)) {
            imagettftext($im, 10, 0, 137, 540, $darkSlate, $fontSans, $issuedDate);
        } else {
            imagestring($im, 3, 137, 532, $issuedDate, $darkSlate);
        }

        // 4. Draw Certificate ID
        if (file_exists($fontSans)) {
            imagettftext($im, 10, 0, 480, 540, $darkSlate, $fontSans, $certNumber);
        } else {
            imagestring($im, 3, 480, 532, $certNumber, $darkSlate);
        }

        // 5. Draw Dynamic QR Code
        $qrDir = storage_path('app/public/certificates');
        if (!is_dir($qrDir)) {
            @mkdir($qrDir, 0755, true);
        }
        $qrFilePath = $qrDir . '/qr_' . $certificate->uuid . '.png';
        $qrRaw = null;

        if (file_exists($qrFilePath) && filesize($qrFilePath) > 50) {
            $qrRaw = @file_get_contents($qrFilePath);
        } else {
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=0&data=' . urlencode($verificationUrl);
            try {
                $resp = Http::timeout(3)->get($qrApiUrl);
                if ($resp->successful() && strlen($resp->body()) > 50) {
                    $qrRaw = $resp->body();
                    @file_put_contents($qrFilePath, $qrRaw);
                }
            } catch (\Throwable $e) {}
        }

        if ($qrRaw && function_exists('imagecreatefromstring')) {
            $qrIm = @imagecreatefromstring($qrRaw);
            if ($qrIm) {
                imagecopyresampled($im, $qrIm, 817, 476, 0, 0, 60, 60, imagesx($qrIm), imagesy($qrIm));
                @imagedestroy($qrIm);
            }
        }

        ob_start();
        imagejpeg($im, null, 96);
        $compositeData = ob_get_clean();
        @imagedestroy($im);

        return $compositeData ?: null;
    }

    /**
     * High-fidelity pure-PHP PDF generator (specification-compliant PDF 1.4 binary).
     * Embeds the exact Cambridge certificate template image and renders dynamic typography,
     * dates, IDs, and scannable QR codes without requiring ext-dom, ext-xml, or Masterminds\HTML5.
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

        $escape = function (string $text): string {
            $clean = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text) ?: $text;
            return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $clean);
        };

        // 1. Resolve composite certificate image (with exact fonts and dynamic fields)
        $bgJpgData = $this->generateCertificateCompositeImage($certificate, $user, $course, $verificationUrl, $settings);
        if (empty($bgJpgData)) {
            $templatePath = public_path('images/certificate_template_base.jpg');
            $bgJpgData = (file_exists($templatePath) && filesize($templatePath) > 5000)
                ? @file_get_contents($templatePath)
                : null;
        }

        // 2. Page stream commands
        $s = '';

        if (!empty($bgJpgData)) {
            // Draw exact template background across full A4 landscape page
            $s .= "q 841.89 0 0 595.28 0 0 cm /Im1 Do Q\n";

            // Searchable / accessible PDF text operators overlay
            $escapedName = $escape($studentName);
            $nameLen = strlen($escapedName);
            $nameFontSize = $nameLen > 30 ? 21 : ($nameLen > 22 ? 23 : 26);
            $nameWidth = $nameLen * ($nameFontSize * 0.58);
            $nameX = max(60.0, (841.89 - $nameWidth) / 2);
            $s .= "BT /F4 {$nameFontSize} Tf 0.047 0.29 0.20 rg {$nameX} 320 Td ({$escapedName}) Tj ET\n";

            $escapedCourse = $escape($courseTitle);
            $courseLen = strlen($escapedCourse);
            $courseFontSize = $courseLen > 45 ? 14 : ($courseLen > 32 ? 16 : 18);
            $courseWidth = $courseLen * ($courseFontSize * 0.58);
            $courseX = max(60.0, (841.89 - $courseWidth) / 2);
            $s .= "BT /F2 {$courseFontSize} Tf 0.02 0.55 0.38 rg {$courseX} 244 Td ({$escapedCourse}) Tj ET\n";

            $escapedDate = $escape($issuedDate);
            $s .= "BT /F2 10 Tf 0.06 0.09 0.16 rg 127 115.5 Td ({$escapedDate}) Tj ET\n";

            $escapedCertNum = $escape($certNumber);
            $s .= "BT /F2 10 Tf 0.06 0.09 0.16 rg 447 115.5 Td ({$escapedCertNum}) Tj ET\n";
        } else {
            // High-fidelity vector fallback if background template image is missing
            $s .= "1 1 1 rg 0 0 841.89 595.28 re f\n";
            $s .= "0.06 0.16 0.36 RG 3.5 w 20 20 801.89 555.28 re S\n";
            $s .= "0.77 0.63 0.35 RG 1.5 w 28 28 785.89 539.28 re S\n";
            $s .= "0.77 0.63 0.35 rg 24 562 9 9 re f\n";
            $s .= "0.77 0.63 0.35 rg 809 562 9 9 re f\n";
            $s .= "0.77 0.63 0.35 rg 24 24 9 9 re f\n";
            $s .= "0.77 0.63 0.35 rg 809 24 9 9 re f\n";
            $s .= "0.06 0.16 0.36 rg 40 495 761.89 62 re f\n";
            $s .= "0.77 0.63 0.35 RG 2 w 40 495 761.89 0 re S\n";

            $brandText = strtoupper($escape($brandName));
            $brandWidth = strlen($brandText) * 13.0;
            $brandX = max(50.0, (841.89 - $brandWidth) / 2);
            $s .= "BT /F2 22 Tf 1 1 1 rg {$brandX} 531 Td ({$brandText}) Tj ET\n";

            $subText = "ACCREDITED CAMBRIDGE CURRICULUM & EXAMINATION BOARD ACCREDITATION";
            $subWidth = strlen($subText) * 5.4;
            $subX = max(50.0, (841.89 - $subWidth) / 2);
            $s .= "BT /F1 9.5 Tf 0.77 0.63 0.35 rg {$subX} 510 Td ({$subText}) Tj ET\n";

            $awardTitle = "OFFICIAL CERTIFICATE OF COMPLETION";
            $awardTitleWidth = strlen($awardTitle) * 7.0;
            $awardTitleX = max(50.0, (841.89 - $awardTitleWidth) / 2);
            $s .= "BT /F2 12 Tf 0.06 0.16 0.36 rg {$awardTitleX} 464 Td ({$awardTitle}) Tj ET\n";

            $conferredText = "THIS IS PROUDLY CONFERRED UPON";
            $conferredWidth = strlen($conferredText) * 5.5;
            $conferredX = max(50.0, (841.89 - $conferredWidth) / 2);
            $s .= "BT /F1 10 Tf 0.40 0.45 0.50 rg {$conferredX} 446 Td ({$conferredText}) Tj ET\n";

            $s .= "0.77 0.63 0.35 RG 1 w 140 435 561.89 0 re S\n";

            $escapedName = $escape($studentName);
            $nameWidth = strlen($escapedName) * 16.5;
            $nameX = max(50.0, (841.89 - $nameWidth) / 2);
            $s .= "BT /F4 30 Tf 0.06 0.16 0.36 rg {$nameX} 402 Td ({$escapedName}) Tj ET\n";

            $s .= "0.77 0.63 0.35 RG 1 w 140 392 561.89 0 re S\n";

            $forText = "for successfully demonstrating academic excellence and fulfilling all assessment standards for";
            $forWidth = strlen($forText) * 5.8;
            $forX = max(50.0, (841.89 - $forWidth) / 2);
            $s .= "BT /F3 11.5 Tf 0.25 0.25 0.25 rg {$forX} 366 Td ({$forText}) Tj ET\n";

            $escapedCourse = $escape($courseTitle);
            $courseWidth = strlen($escapedCourse) * 10.8;
            $courseX = max(60.0, (841.89 - $courseWidth) / 2);
            $s .= "BT /F2 18 Tf 0.01 0.52 0.78 rg {$courseX} 336 Td ({$escapedCourse}) Tj ET\n";

            $s .= "0.77 0.63 0.35 RG 1 w 55 190 731.89 0 re S\n";
            $s .= "0.77 0.63 0.35 RG 2 w 65 100 0 75 re S\n";
            $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 76 163 Td (OFFICIAL CERTIFICATE ID:) Tj ET\n";
            $escapedCertNum = $escape($certNumber);
            $s .= "BT /F2 12.5 Tf 0.06 0.16 0.36 rg 76 147 Td ({$escapedCertNum}) Tj ET\n";
            $s .= "BT /F2 8 Tf 0.45 0.45 0.45 rg 76 128 Td (DATE OF CONFERMENT:) Tj ET\n";
            $escapedDate = $escape($issuedDate);
            $s .= "BT /F1 10 Tf 0.20 0.20 0.20 rg 76 114 Td ({$escapedDate}) Tj ET\n";

            $s .= "0.85 0.85 0.85 RG 0.5 w 55 68 731.89 0 re S\n";
            $footerText = $escape($brandName . " - Official Academic Examination Portal - Record UUID: " . $certificate->uuid);
            $footerWidth = strlen($footerText) * 4.4;
            $footerX = max(50.0, (841.89 - $footerWidth) / 2);
            $s .= "BT /F1 8 Tf 0.55 0.55 0.55 rg {$footerX} 52 Td ({$footerText}) Tj ET\n";
        }

        // Build PDF 1.4 Object Structure with correct byte offsets
        $streamLen = strlen($s);

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";

        $xObjStr = '';
        if (!empty($bgJpgData)) {
            $xObjStr .= ' /Im1 9 0 R';
        }
        $xObjDict = !empty($xObjStr) ? " /XObject <<{$xObjStr} >>" : '';

        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 841.89 595.28] /Resources << /Font << /F1 4 0 R /F2 5 0 R /F3 6 0 R /F4 7 0 R >>{$xObjDict} >> /Contents 8 0 R >>";
        $objects[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman /Encoding /WinAnsiEncoding >>";
        $objects[7] = "<< /Type /Font /Subtype /Type1 /BaseFont /Times-BoldItalic /Encoding /WinAnsiEncoding >>";
        $objects[8] = "<< /Length {$streamLen} >>\nstream\n{$s}endstream";

        $objCount = 8;
        if (!empty($bgJpgData)) {
            $bgLen = strlen($bgJpgData);
            $objects[9] = "<< /Type /XObject /Subtype /Image /Width 942 /Height 670 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$bgLen} >>\nstream\n{$bgJpgData}\nendstream";
            $objCount = 9;
        }

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        for ($i = 1; $i <= $objCount; $i++) {
            $offsets[$i] = strlen($pdf);
            $pdf .= "{$i} 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $totalObjs = $objCount + 1;
        $pdf .= "xref\n0 {$totalObjs}\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $objCount; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size {$totalObjs} /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF\n";

        return $pdf;
    }
}
