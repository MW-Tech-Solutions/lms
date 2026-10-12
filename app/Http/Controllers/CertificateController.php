<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    /**
     * Public credential authentication endpoint (/verify/{uuid}).
     */
    public function verify(string $uuid): View
    {
        $certificate = Certificate::with(['user', 'course'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * List user's earned certificates.
     */
    public function myCertificates(): View
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->get();

        return view('learning.my', [
            'enrollments' => Auth::user()->enrollments()->with('course')->get(),
            'certificates' => $certificates,
        ]);
    }
}
