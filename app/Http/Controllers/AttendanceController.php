<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Render the rolling QR attendance check-in view.
     */
    public function showRollingQr(int $courseId, Request $request): View
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $session = AttendanceSession::with('course')->findOrFail($sessionId);
        } else {
            $session = AttendanceSession::with('course')
                ->where('course_id', $courseId)
                ->latest('session_date')
                ->firstOrFail();
        }

        return view('attendance.qr', compact('session'));
    }

    /**
     * Verify rolling token, check geofence boundary, and record attendance.
     */
    public function checkin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'exists:attendance_sessions,id'],
            'token' => ['required', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();
        $session = AttendanceSession::findOrFail($validated['session_id']);

        // Verify rolling token timestamp within +/- 30 seconds (2 slots of 15s)
        $tokenParts = explode('_', $validated['token']);
        $submittedSecret = $tokenParts[0] ?? '';
        $submittedTimestamp = (int) ($tokenParts[1] ?? 0);

        $currentTimestampSlot = (int) floor(time() / 15);
        $timeDelta = abs($currentTimestampSlot - $submittedTimestamp);

        if ($submittedSecret !== $session->qr_secret || $timeDelta > 2) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired QR token. Please rescan.'], 422);
        }

        // Validate Geofence if coordinates configured
        if ($session->lat_boundary !== null && $session->lng_boundary !== null) {
            if (!isset($validated['lat'], $validated['lng'])) {
                return response()->json(['success' => false, 'message' => 'Geolocation coordinates required for this attendance session.'], 422);
            }

            $dist = $this->calculateHaversineDistance(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $session->lat_boundary,
                (float) $session->lng_boundary
            );

            if ($dist > (float) $session->radius_meters) {
                return response()->json(['success' => false, 'message' => sprintf('Outside geofence area. Distance: %dm (allowed: %dm).', round($dist), $session->radius_meters)], 422);
            }
        }

        $record = AttendanceRecord::updateOrCreate(
            ['session_id' => $session->id, 'user_id' => $user->id],
            [
                'status' => AttendanceRecord::STATUS_PRESENT,
                'checkin_method' => AttendanceRecord::METHOD_QR_SCAN,
                'verified_ip' => $request->ip(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance verified and recorded successfully.',
            'record_id' => $record->id,
        ]);
    }

    /**
     * Compute distance in meters using Haversine formula.
     */
    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
