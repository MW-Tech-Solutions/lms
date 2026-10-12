<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ComplianceRecertificationEngine
{
    /**
     * Inspect active enrollments, identify expired compliance tracks, archive records,
     * and queue renewal notifications.
     *
     * @return array{expired_count: int, warning_count: int}
     */
    public function checkAndProcessRecertifications(): array
    {
        $now = Carbon::now();
        $warningWindow = Carbon::now()->addDays(30);

        $expiredCount = 0;
        $warningCount = 0;

        // 1. Process Expired Certifications/Enrollments
        $expiredEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', Enrollment::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expiredEnrollments as $enrollment) {
            $user = $enrollment->user;
            $course = $enrollment->course;

            // Transition enrollment to EXPIRED
            $enrollment->update([
                'status' => Enrollment::STATUS_EXPIRED,
            ]);

            $expiredCount++;

            Log::channel('single')->warning(sprintf(
                '[COMPLIANCE ENGINE] Recertification EXPIRED for User ID: %d (%s) on Course ID: %d ("%s"). Expiration: %s',
                $user->id,
                $user->email,
                $course->id,
                $course->title,
                $enrollment->expires_at?->toIso8601String()
            ));

            // Automatically queue renewal re-enrollment if recertification cycle configured
            if ($course->recertification_days !== null && $course->recertification_days > 0) {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_ACTIVE,
                    'enrolled_at' => $now,
                    'expires_at' => $now->copy()->addDays((int) $course->recertification_days),
                ]);
            }
        }

        // 2. Identify Approaching Expirations (30-day window)
        $impendingEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', Enrollment::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$now, $warningWindow])
            ->get();

        foreach ($impendingEnrollments as $enrollment) {
            $warningCount++;
            Log::channel('single')->info(sprintf(
                '[COMPLIANCE ENGINE] Impending Recertification Due in %d days for User: %s, Course: "%s"',
                $now->diffInDays($enrollment->expires_at),
                $enrollment->user->email,
                $enrollment->course->title
            ));
        }

        return [
            'expired_count' => $expiredCount,
            'warning_count' => $warningCount,
        ];
    }
}
