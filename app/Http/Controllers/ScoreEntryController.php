<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\PastPaperAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreEntryController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'level' => ['required', 'string', 'in:primary,secondary,igcse,a-level'],
            'exam_year' => ['required', 'integer', 'min:2018', 'max:2030'],
            'season' => ['required', 'string', 'in:F/M,M/J,O/N,SPECIMEN'],
            'variant' => ['nullable', 'integer', 'in:1,2,3'],
            'component_name' => ['required', 'string', 'max:255'],
            'score_obtained' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $score = (float) $validated['score_obtained'];
        $max = (float) $validated['max_score'];

        if ($score > $max) {
            if ($request->wantsJson()) {
                return response()->json(['error' => "Score ($score) cannot exceed maximum score ($max)."], 422);
            }
            return back()->with('error', "Score cannot exceed maximum score.");
        }

        $percentage = round(($score / $max) * 100, 2);
        $statusColor = $percentage >= 75.0 ? 'GREEN' : ($percentage >= 50.0 ? 'AMBER' : 'RED');

        // Find or map course by level
        $slugMap = [
            'primary' => 'primary-checkpoint',
            'secondary' => 'lower-secondary',
            'igcse' => 'igcse',
            'a-level' => 'a-level',
        ];

        $categorySlug = $slugMap[$validated['level']] ?? 'igcse';
        $category = CourseCategory::where('slug', $categorySlug)->first();
        
        $course = Course::where('category_id', $category?->id)->first() ?? Course::first();

        // Get or assign user (auth or first user if guest demo)
        $userId = auth()->id() ?? User::where('user_type', 'STUDENT')->first()?->id ?? User::first()->id;

        $attempt = PastPaperAttempt::create([
            'user_id' => $userId,
            'course_id' => $course?->id ?? 1,
            'year' => (int) $validated['exam_year'],
            'season' => $validated['season'],
            'variant' => (int) ($validated['variant'] ?? 1),
            'score_obtained' => $score,
            'max_score' => $max,
            'percentage' => $percentage,
            'status_color' => $statusColor,
            'notes' => ($validated['component_name'] ?? 'Past Paper') . ($validated['notes'] ? ' — ' . $validated['notes'] : ''),
            'is_latest' => true,
        ]);

        $statusText = match($statusColor) {
            'GREEN' => 'Mastery achieved (>= 75%)',
            'AMBER' => 'Developing proficiency (50–74%)',
            'RED' => 'Remedial action recommended (< 50%)',
        };

        $msg = sprintf(
            'Score saved! %s — %.1f%% (%s / %s). %s',
            $validated['component_name'],
            $percentage,
            $score,
            $max,
            $statusText
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'percentage' => $percentage,
                'status_color' => $statusColor,
                'attempt' => $attempt,
            ]);
        }

        return back()->with('success', $msg);
    }

    public function history(): View
    {
        $userId = auth()->id() ?? User::where('user_type', 'STUDENT')->first()?->id ?? 1;

        $attempts = PastPaperAttempt::with('course')
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate(15);

        $stats = [
            'total' => PastPaperAttempt::where('user_id', $userId)->count(),
            'mastery' => PastPaperAttempt::where('user_id', $userId)->where('status_color', 'GREEN')->count(),
            'developing' => PastPaperAttempt::where('user_id', $userId)->where('status_color', 'AMBER')->count(),
            'remedial' => PastPaperAttempt::where('user_id', $userId)->where('status_color', 'RED')->count(),
            'average_pct' => round((float) PastPaperAttempt::where('user_id', $userId)->avg('percentage'), 1),
        ];

        return view('student.scores.history', compact('attempts', 'stats'));
    }

    public function generateReport(): View
    {
        $userId = auth()->id() ?? User::where('user_type', 'STUDENT')->first()?->id ?? 1;
        $user = User::find($userId);

        $attempts = PastPaperAttempt::with('course')
            ->where('user_id', $userId)
            ->orderBy('year', 'desc')
            ->orderBy('season', 'desc')
            ->get();

        $stats = [
            'total' => $attempts->count(),
            'mastery' => $attempts->where('status_color', 'GREEN')->count(),
            'developing' => $attempts->where('status_color', 'AMBER')->count(),
            'remedial' => $attempts->where('status_color', 'RED')->count(),
            'average_pct' => $attempts->count() > 0 ? round((float) $attempts->avg('percentage'), 1) : 0,
        ];

        return view('student.scores.report', compact('user', 'attempts', 'stats'));
    }
}
