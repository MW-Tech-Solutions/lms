<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AntiCheatLog;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AntiCheatMonitorService
{
    /**
     * Log an anti-cheat proctoring event and optionally persist webcam capture snapshots.
     */
    public function logEvent(QuizAttempt $attempt, string $eventType, ?string $snapshotBase64 = null): AntiCheatLog
    {
        $validTypes = [
            AntiCheatLog::EVENT_TAB_SWITCH,
            AntiCheatLog::EVENT_COPY_ATTEMPT,
            AntiCheatLog::EVENT_DEVICE_DISCONNECTED,
            AntiCheatLog::EVENT_WEBCAM_FLAG,
        ];

        if (!in_array($eventType, $validTypes, true)) {
            $eventType = AntiCheatLog::EVENT_TAB_SWITCH;
        }

        $snapshotUrl = null;
        if ($snapshotBase64 !== null && str_starts_with($snapshotBase64, 'data:image')) {
            $snapshotUrl = $this->storeSnapshot($attempt->id, $snapshotBase64);
        }

        return AntiCheatLog::create([
            'attempt_id' => $attempt->id,
            'event_type' => $eventType,
            'snapshot_url' => $snapshotUrl,
            'recorded_at' => Carbon::now(),
        ]);
    }

    /**
     * Persist base64 encoded webcam image to secure storage.
     */
    protected function storeSnapshot(int $attemptId, string $base64Image): string
    {
        // Strip data:image/...;base64,
        $parts = explode(',', $base64Image, 2);
        $imageData = base64_decode(end($parts));

        $fileName = sprintf('proctoring/attempt_%d_%s.jpg', $attemptId, uniqid('', true));
        Storage::disk('public')->put($fileName, $imageData);

        return Storage::url($fileName);
    }

    /**
     * Calculate comprehensive proctoring integrity score (0 - 100, where 100 is pristine integrity).
     *
     * @return array{score: int, total_flags: int, tab_switches: int, copy_attempts: int, webcam_flags: int}
     */
    public function getAttemptIntegrityReport(QuizAttempt $attempt): array
    {
        $logs = AntiCheatLog::where('attempt_id', $attempt->id)->get();

        $tabSwitches = $logs->where('event_type', AntiCheatLog::EVENT_TAB_SWITCH)->count();
        $copyAttempts = $logs->where('event_type', AntiCheatLog::EVENT_COPY_ATTEMPT)->count();
        $webcamFlags = $logs->where('event_type', AntiCheatLog::EVENT_WEBCAM_FLAG)->count();
        $disconnects = $logs->where('event_type', AntiCheatLog::EVENT_DEVICE_DISCONNECTED)->count();

        $penalty = ($tabSwitches * 10) + ($copyAttempts * 15) + ($webcamFlags * 20) + ($disconnects * 5);
        $integrityScore = max(0, 100 - $penalty);

        return [
            'score' => $integrityScore,
            'total_flags' => $logs->count(),
            'tab_switches' => $tabSwitches,
            'copy_attempts' => $copyAttempts,
            'webcam_flags' => $webcamFlags,
        ];
    }
}
