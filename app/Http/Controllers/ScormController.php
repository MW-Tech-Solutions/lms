<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ScormPackage;
use App\Services\ScormRuntimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScormController extends Controller
{
    /**
     * Render the SCORM Runtime Sandbox Player with full CMI JavaScript API implementation.
     */
    public function launch(int $packageId): View
    {
        $package = ScormPackage::find($packageId) ?? ScormPackage::first();
        if (!$package) {
            $package = new ScormPackage([
                'id' => 1,
                'title' => 'Default Interactive Compliance Module',
                'version' => '1.2',
                'entry_url' => '',
                'storage_path' => 'demo',
            ]);
        }

        return view('scorm.player', compact('package'));
    }

    /**
     * AJAX endpoint to initialize SCORM CMI data model.
     */
    public function initialize(int $packageId, ScormRuntimeService $runtime): JsonResponse
    {
        $user = Auth::user();
        $package = ScormPackage::findOrFail($packageId);
        $cmiData = $runtime->initializeState($user, $package);

        return response()->json(['success' => true, 'data' => $cmiData]);
    }

    /**
     * AJAX endpoint to commit CMI data updates from SCORM package runtime.
     */
    public function commit(int $packageId, Request $request, ScormRuntimeService $runtime): JsonResponse
    {
        $user = Auth::user();
        $package = ScormPackage::findOrFail($packageId);
        $payload = $request->json()->all();

        $result = $runtime->commitState($user, $package, $payload);

        return response()->json($result);
    }
}
