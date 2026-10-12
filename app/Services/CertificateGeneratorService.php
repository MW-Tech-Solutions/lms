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
     */
    public function generate(User $user, Course $course): Certificate
    {
        $uuid = (string) Str::uuid();
        $issuedAt = Carbon::now();
        $expiresAt = $course->recertification_days !== null && $course->recertification_days > 0
            ? $issuedAt->copy()->addDays((int) $course->recertification_days)
            : null;

        $certificate = Certificate::create([
            'uuid' => $uuid,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
            'pdf_url' => null,
        ]);

        $verificationUrl = url('/verify/' . $certificate->uuid);

        // Render PDF through Blade
        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'user' => $user,
            'course' => $course,
            'verificationUrl' => $verificationUrl,
            'issuedDate' => $issuedAt->format('F d, Y'),
            'expiresDate' => $expiresAt ? $expiresAt->format('F d, Y') : null,
        ])->setPaper('a4', 'landscape');

        $relativePdfPath = sprintf('certificates/%s.pdf', $certificate->uuid);
        Storage::disk('public')->put($relativePdfPath, $pdf->output());

        $certificate->update([
            'pdf_url' => Storage::url($relativePdfPath),
        ]);

        return $certificate;
    }
}
