<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    /**
     * Public credential authentication endpoint (/verify/{uuid}).
     */
    public function verify(string $uuid, Request $request): View|Response
    {
        $certificate = Certificate::with(['user', 'course.category'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // If download requested, serve or generate the PDF
        if ($request->has('download')) {
            $relativePdfPath = sprintf('certificates/%s.pdf', $certificate->uuid);

            // Generate or regenerate with the updated landscape template, QR code, and certificate number
            try {
                /** @var CertificateGeneratorService $service */
                $service = app(CertificateGeneratorService::class);
                $certificate = $service->generate($certificate->user, $certificate->course);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Certificate generation failed on download: ' . $e->getMessage());
            }

            if (Storage::disk('public')->exists($relativePdfPath)) {
                $downloadFilename = sprintf('certificate-%s.pdf', $certificate->certificate_number ?: $certificate->uuid);
                return Storage::disk('public')->download($relativePdfPath, $downloadFilename);
            }
        }

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * List user's earned certificates on a dedicated, high-performance view.
     */
    public function myCertificates(): View
    {
        $certificates = Certificate::with('course.category')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->get()
            ->unique('course_id');

        return view('certificates.my', compact('certificates'));
    }
}
