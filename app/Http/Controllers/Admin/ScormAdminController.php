<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScormPackage;
use App\Models\ScormTrack;
use App\Services\ScormRuntimeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScormAdminController extends Controller
{
    public function index(): View
    {
        $packages = ScormPackage::latest()->get();
        $recentTracks = ScormTrack::with(['user', 'scormPackage'])->latest('updated_at')->take(10)->get();

        return view('admin.scorm.index', compact('packages', 'recentTracks'));
    }

    public function upload(Request $request, ScormRuntimeService $runtime): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'scorm_file' => ['required', 'file', 'mimes:zip', 'max:102400'], // max 100MB
        ]);

        $file = $request->file('scorm_file');
        $title = $request->string('title')->toString();

        try {
            $pkg = $runtime->ingestPackage($file, $title);
            \App\Services\AuditService::log('SCORM_PACKAGE_INGESTED', $pkg, null, [
                'title' => $pkg->title,
                'version' => $pkg->version,
                'uploaded_by' => auth()->user()->name ?? 'Admin',
            ]);
            return back()->with('success', sprintf('SCORM package "%s" (%s) ingested and deployed successfully.', $pkg->title, $pkg->version));
        } catch (\Throwable $e) {
            return back()->with('error', 'SCORM ingestion failed: ' . $e->getMessage());
        }
    }
}
