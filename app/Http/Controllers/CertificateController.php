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
            if (Storage::disk('public')->exists($relativePdfPath)) {
                return Storage::disk('public')->download(
                    $relativePdfPath,
                    sprintf('certificate-%s.pdf', $certificate->uuid)
                );
            }

            // Generate on demand if missing
            try {
                /** @var CertificateGeneratorService $service */
                $service = app(CertificateGeneratorService::class);
                $service->generate($certificate->user, $certificate->course);

                if (Storage::disk('public')->exists($relativePdfPath)) {
                    return Storage::disk('public')->download(
                        $relativePdfPath,
                        sprintf('certificate-%s.pdf', $certificate->uuid)
                    );
                }
            } catch (\Throwable $e) {
                // If PDF generator encounters any issue, fall through to view
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
            ->get();

        return view('certificates.my', compact('certificates'));
    }
}
