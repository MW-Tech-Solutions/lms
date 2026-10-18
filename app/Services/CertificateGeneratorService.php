<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateGeneratorService
{
    /**
     * Issue an official cryptographic certificate with embedded verification QR code and PDF generation.
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

        $verificationUrl = url('/verify/' . $certificate->uuid);

        // 2. Resolve High-Res Logo Base64
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

        // 3. Resolve Authorized Signature Base64
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

        // 4. Resolve Verification QR Code Base64
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
            $ctx = stream_context_create(['http' => ['timeout' => 3]]);
            $qrData = @file_get_contents($qrApiUrl, false, $ctx);
            if ($qrData && strlen($qrData) > 50) {
                @file_put_contents($qrFilePath, $qrData);
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
            }
        }

        // 5. Render Landscape PDF via Blade
        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'user' => $user,
            'course' => $course,
            'verificationUrl' => $verificationUrl,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
            'signatureBase64' => $signatureBase64,
            'issuedDate' => ($certificate->issued_at ?? $issuedAt)->format('F d, Y'),
            'expiresDate' => $certificate->expires_at ? $certificate->expires_at->format('F d, Y') : null,
            'site_settings' => $settings,
        ])->setPaper('a4', 'landscape');

        $relativePdfPath = sprintf('certificates/%s.pdf', $certificate->uuid);
        Storage::disk('public')->put($relativePdfPath, $pdf->output());

        $certificate->update([
            'pdf_url' => Storage::url($relativePdfPath),
        ]);

        return $certificate;
    }
}
