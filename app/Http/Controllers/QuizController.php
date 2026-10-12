<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Services\AntiCheatMonitorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Start or resume a proctored examination attempt.
     */
    public function take(Quiz $quiz): View
    {
        $user = Auth::user();

        // Retrieve existing in-progress attempt or create new
        $attempt = QuizAttempt::firstOrCreate(
            [
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'state' => QuizAttempt::STATE_IN_PROGRESS,
            ],
            [
                'attempt_number' => QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $user->id)->count() + 1,
                'started_at' => Carbon::now(),
            ]
        );

        $questions = $quiz->questions()->with('options')->get();

        if ($questions->isEmpty()) {
            // Fallback: load questions from question bank
            $questions = Question::with('options')->take(10)->get();
        }

        return view('quiz.take', compact('quiz', 'attempt', 'questions'));
    }

    /**
     * AJAX autosave endpoint to persist in-progress answers every 15s.
     */
    public function autosave(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:quiz_attempts,id'],
            'answers' => ['nullable', 'array'],
        ]);

        $attempt = QuizAttempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answers = $validated['answers'] ?? [];
        foreach ($answers as $questionId => $givenAnswer) {
            QuizAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => (int) $questionId],
                ['given_answer_json' => is_array($givenAnswer) ? $givenAnswer : [$givenAnswer]]
            );
        }

        return response()->json(['success' => true, 'timestamp' => Carbon::now()->toIso8601String()]);
    }

    /**
     * Finalize and grade examination submission.
     */
    public function submit(Quiz $quiz, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:quiz_attempts,id'],
            'answers' => ['nullable', 'array'],
        ]);

        $attempt = QuizAttempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answers = $validated['answers'] ?? [];
        $totalEarnedPoints = 0.0;
        $totalMaxPoints = 0.0;

        $questions = $quiz->questions()->with('options')->get();
        if ($questions->isEmpty()) {
            $questions = Question::with('options')->take(10)->get();
        }

        foreach ($questions as $q) {
            $totalMaxPoints += (float) $q->points;
            $given = $answers[$q->id] ?? null;
            $isCorrect = false;
            $awarded = 0.0;

            if ($q->type === 'SINGLE_CHOICE' || $q->type === 'TRUE_FALSE') {
                $correctOpt = $q->options->firstWhere('is_correct', true);
                if ($correctOpt && (string) $given === (string) $correctOpt->id) {
                    $isCorrect = true;
                    $awarded = (float) $q->points;
                }
            } elseif ($q->type === 'MULTI_CHOICE') {
                $correctIds = $q->options->where('is_correct', true)->pluck('id')->map(fn ($id) => (string) $id)->all();
                $givenArr = is_array($given) ? array_map('strval', $given) : [];
                sort($correctIds);
                sort($givenArr);
                if ($correctIds === $givenArr) {
                    $isCorrect = true;
                    $awarded = (float) $q->points;
                }
            } else {
                // Short answer / essay default to awarded on submission
                if (!empty($given)) {
                    $isCorrect = true;
                    $awarded = (float) $q->points;
                }
            }

            $totalEarnedPoints += $awarded;

            QuizAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $q->id],
                [
                    'given_answer_json' => is_array($given) ? $given : [$given],
                    'is_correct' => $isCorrect,
                    'awarded_points' => $awarded,
                ]
            );
        }

        $percentage = $totalMaxPoints > 0 ? round(($totalEarnedPoints / $totalMaxPoints) * 100, 2) : 100.0;

        $attempt->update([
            'state' => QuizAttempt::STATE_GRADED,
            'score_obtained' => $percentage,
            'submitted_at' => Carbon::now(),
        ]);

        $passed = $percentage >= $quiz->passing_percentage;

        return redirect()->route('my-learning')->with(
            $passed ? 'success' : 'error',
            sprintf(
                'Exam completed! Your final score: %s%% (Passing requirement: %d%%). Status: %s.',
                $percentage,
                $quiz->passing_percentage,
                $passed ? 'PASSED & COMPLIANT' : 'DID NOT PASS'
            )
        );
    }

    /**
     * AJAX endpoint to log proctoring anti-cheat telemetry and webcam snapshots.
     */
    public function logAntiCheat(Request $request, AntiCheatMonitorService $monitor): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:quiz_attempts,id'],
            'event_type' => ['required', 'string'],
            'snapshot_base64' => ['nullable', 'string'],
        ]);

        $attempt = QuizAttempt::findOrFail($validated['attempt_id']);
        $log = $monitor->logEvent($attempt, $validated['event_type'], $validated['snapshot_base64'] ?? null);

        return response()->json(['success' => true, 'log_id' => $log->id]);
    }
}
