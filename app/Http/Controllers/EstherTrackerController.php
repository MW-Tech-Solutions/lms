<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CambridgeExamSeries;
use App\Models\CambridgePaperComponent;
use App\Models\Course;
use App\Models\PastPaperAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EstherTrackerController extends Controller
{
    /**
     * Display the Esther Math Revision Tracker dashboard for a syllabus/course.
     */
    public function show(Request $request, string $courseSlug = 'cambridge-a-level-mathematics-9709'): View
    {
        $course = Course::with('category')->where('slug', $courseSlug)->firstOrFail();

        $components = CambridgePaperComponent::where('course_id', $course->id)
            ->orderBy('sort_order')
            ->get();

        $series = CambridgeExamSeries::orderBy('year', 'desc')
            ->orderByRaw("FIELD(season, 'M/J', 'F/M', 'O/N')")
            ->get();

        $user = Auth::user();

        // Retrieve existing attempts for this user and course
        $attempts = collect();
        if ($user) {
            $attempts = PastPaperAttempt::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('is_latest', true)
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '_' . $item->season . '_' . $item->component_id;
                });
        }

        // Calculate mastery stats
        $totalTracked = $attempts->count();
        $greenCount = $attempts->where('status_color', PastPaperAttempt::STATUS_GREEN)->count();
        $amberCount = $attempts->where('status_color', PastPaperAttempt::STATUS_AMBER)->count();
        $redCount = $attempts->where('status_color', PastPaperAttempt::STATUS_RED)->count();

        $overallMastery = $totalTracked > 0 ? round(($greenCount / $totalTracked) * 100, 1) : 0;

        return view('learning.tracker', compact(
            'course',
            'components',
            'series',
            'attempts',
            'totalTracked',
            'greenCount',
            'amberCount',
            'redCount',
            'overallMastery'
        ));
    }

    /**
     * Record a past-paper score attempt via AJAX.
     * Enforces formula: Percentage = (Score Obtained / Maximum Score) * 100
     * Thresholds: Green >= 75%, Amber 50%-74.99%, Red < 50%
     */
    public function recordScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'year' => ['required', 'integer', 'min:2020', 'max:2030'],
            'season' => ['required', 'string', 'in:F/M,M/J,O/N'],
            'component_id' => ['required', 'exists:cambridge_paper_components,id'],
            'score_obtained' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'gt:0'],
            'variant' => ['nullable', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $score = (float) $validated['score_obtained'];
        $maxScore = (float) $validated['max_score'];

        if ($score > $maxScore) {
            return response()->json([
                'success' => false,
                'message' => 'Obtained score cannot exceed the maximum paper score.',
            ], 422);
        }

        $percentage = round(($score / $maxScore) * 100, 2);
        $statusColor = PastPaperAttempt::calculateStatusColor($percentage);

        $userId = Auth::id();

        // Archive previous attempts for this cell
        $previousAttemptsCount = PastPaperAttempt::where('user_id', $userId)
            ->where('course_id', $validated['course_id'])
            ->where('year', $validated['year'])
            ->where('season', $validated['season'])
            ->where('component_id', $validated['component_id'])
            ->count();

        PastPaperAttempt::where('user_id', $userId)
            ->where('course_id', $validated['course_id'])
            ->where('year', $validated['year'])
            ->where('season', $validated['season'])
            ->where('component_id', $validated['component_id'])
            ->update(['is_latest' => false]);

        $attempt = PastPaperAttempt::create([
            'user_id' => $userId,
            'course_id' => $validated['course_id'],
            'component_id' => $validated['component_id'],
            'year' => $validated['year'],
            'season' => $validated['season'],
            'variant' => $validated['variant'] ?? 1,
            'score_obtained' => $score,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'status_color' => $statusColor,
            'notes' => $validated['notes'] ?? null,
            'attempt_number' => $previousAttemptsCount + 1,
            'is_latest' => true,
        ]);

        return response()->json([
            'success' => true,
            'attempt' => $attempt,
            'percentage' => $percentage,
            'status_color' => $statusColor,
            'message' => sprintf('Score recorded: %.1f%% (%s)', $percentage, $statusColor),
        ]);
    }
}
