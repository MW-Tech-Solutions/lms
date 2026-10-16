<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ResourceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceRequestController extends Controller
{
    /**
     * Submit a resource request for uncreated past papers or study materials.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'level_name' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:100'],
            'topic' => ['required', 'string', 'max:150'],
            'resource_type' => ['required', 'string', 'max:50'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $resourceRequest = ResourceRequest::create([
            'user_id' => Auth::id(),
            'level_name' => $validated['level_name'],
            'subject' => $validated['subject'],
            'topic' => $validated['topic'],
            'resource_type' => $validated['resource_type'],
            'details' => $validated['details'] ?? null,
            'status' => ResourceRequest::STATUS_PENDING,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'request' => $resourceRequest,
                'message' => 'Thank you! Your resource request has been submitted to the Cambridge academic team.',
            ]);
        }

        return back()->with('success', 'Your resource request has been received. Our Cambridge content team will prepare it as fast as possible!');
    }
}
