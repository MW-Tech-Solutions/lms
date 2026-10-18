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

        // If download, pdf, or view requested: stream directly in browser PDF viewer
        if ($request->has('download') || $request->has('pdf') || $request->has('view')) {
            /** @var CertificateGeneratorService $service */
            $service = app(CertificateGeneratorService::class);
            $downloadFilename = sprintf('certificate-%s.pdf', $certificate->certificate_number ?: $certificate->uuid);

            try {
                $pdfBinary = $service->getPdfBinary($certificate);
                $disposition = $request->boolean('attachment') ? 'attachment' : 'inline';

                return response($pdfBinary, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('%s; filename="%s"', $disposition, $downloadFilename),
                    'Content-Length' => strlen($pdfBinary),
                    'Cache-Control' => 'private, max-age=3600, must-revalidate',
                    'Pragma' => 'public',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Certificate PDF generation error: ' . $e->getMessage(), [
                    'uuid' => $certificate->uuid,
                    'trace' => $e->getTraceAsString(),
                ]);

                $relativePdfPath = sprintf('certificates/%s.pdf', $certificate->uuid);
                if (Storage::disk('public')->exists($relativePdfPath)) {
                    $disposition = $request->boolean('attachment') ? 'attachment' : 'inline';
                    return response(Storage::disk('public')->get($relativePdfPath), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => sprintf('%s; filename="%s"', $disposition, $downloadFilename),
                    ]);
                }
            }
        }

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * Dedicated direct PDF stream endpoint (/verify/{uuid}/pdf).
     */
    public function pdf(string $uuid, Request $request): Response
    {
        $certificate = Certificate::with(['user', 'course.category', 'course.tenant'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        /** @var CertificateGeneratorService $service */
        $service = app(CertificateGeneratorService::class);
        $downloadFilename = sprintf('certificate-%s.pdf', $certificate->certificate_number ?: $certificate->uuid);
        $pdfBinary = $service->getPdfBinary($certificate);
        $disposition = $request->boolean('attachment') ? 'attachment' : 'inline';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('%s; filename="%s"', $disposition, $downloadFilename),
            'Content-Length' => strlen($pdfBinary),
            'Cache-Control' => 'private, max-age=3600, must-revalidate',
            'Pragma' => 'public',
        ]);
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
