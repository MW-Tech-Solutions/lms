<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Services\CertificateGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LearningController extends Controller
{
    /**
     * Learner personal learning and compliance dashboard.
     */
    public function myLearning(): View
    {
        $user = Auth::user();

        $enrollments = Enrollment::with('course.category')
            ->where('user_id', $user->id)
            ->latest('enrolled_at')
            ->get();

        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get();

        $linkedParents = \App\Models\ParentStudent::with('parent')
            ->where('student_id', $user->id)
            ->get();

        $activeInvite = \App\Models\ParentInvite::where('student_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return view('learning.my', compact('enrollments', 'certificates', 'linkedParents', 'activeInvite'));
    }

    /**
     * Interactive course learning player with two-column responsive curriculum layout.
     */
    public function player(string $courseSlug, Request $request): View
    {
        $user = Auth::user();

        $course = Course::with(['sections.modules'])
            ->where('slug', $courseSlug)
            ->firstOrFail();

        // Ensure active enrollment exists
        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status' => Enrollment::STATUS_ACTIVE, 'enrolled_at' => Carbon::now()]
        );

        // Ensure course has at least one structured curriculum section
        if ($course->sections->isEmpty()) {
            CourseSection::create([
                'course_id' => $course->id,
                'title' => 'Core Curriculum & Syllabus Framework',
                'sort_order' => 0,
            ]);
            $course->load('sections.modules');
        }

        $allModules = $course->sections->flatMap->modules;

        // Ensure section has active learning modules
        if ($allModules->isEmpty()) {
            $section = $course->sections->first();

            CourseModule::create([
                'section_id' => $section->id,
                'module_type' => CourseModule::TYPE_VIDEO,
                'sort_order' => 0,
                'is_mandatory' => true,
            ]);

            CourseModule::create([
                'section_id' => $section->id,
                'module_type' => CourseModule::TYPE_PAGE,
                'sort_order' => 1,
                'is_mandatory' => false,
            ]);

            $course->load('sections.modules');
            $allModules = $course->sections->flatMap->modules;
        }

        $totalModulesCount = $allModules->count();
        $completedModuleIds = session()->get("course_{$course->id}_completed_modules", []);
        $allCourseModuleIds = $allModules->pluck('id')->all();

        // Separate instructional modules from assessment (quiz) modules
        $instructionalModules = $allModules->where('module_type', '!=', CourseModule::TYPE_QUIZ);
        $instructionalIds = $instructionalModules->pluck('id')->all();
        $isInstructionalCompleted = count($instructionalIds) === 0
            || count(array_intersect($instructionalIds, $completedModuleIds)) === count($instructionalIds);

        // Find active quiz for the course if available
        $quizModule = $allModules->firstWhere('module_type', CourseModule::TYPE_QUIZ);
        $quiz = null;
        $hasActiveQuiz = false;

        if ($quizModule && $quizModule->instance_id) {
            $quiz = \App\Models\Quiz::find($quizModule->instance_id);
        }
        if (!$quiz) {
            $quiz = \App\Models\Quiz::first();
        }

        if ($quiz) {
            $hasActiveQuiz = $quiz->questions()->count() > 0 || \App\Models\Question::count() > 0;
        }

        $config = app()->bound(SiteConfigService::class) ? app(SiteConfigService::class) : null;
        $requiredQuizScore = $config ? (float) ($config->get('certificate_min_score', '80') ?: 80.0) : 80.0;

        $bestAttempt = null;
        $quizScore = null;
        $hasPassedQuiz = false;

        if ($quiz) {
            $bestAttempt = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('state', \App\Models\QuizAttempt::STATE_GRADED)
                ->orderByDesc('score_obtained')
                ->first();

            if ($bestAttempt) {
                $quizScore = (float) $bestAttempt->score_obtained;
                $hasPassedQuiz = $quizScore >= $requiredQuizScore;
            }
        }

        $existingCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // Determine granular quiz and certificate status
        if ($existingCertificate) {
            $quizStatus = 'CLAIMED';
        } elseif (!$isInstructionalCompleted) {
            $quizStatus = 'INSTRUCTIONAL_INCOMPLETE';
        } elseif (!$hasActiveQuiz) {
            $quizStatus = 'AWAITING_TEST';
        } elseif ($bestAttempt === null) {
            $quizStatus = 'TEST_REQUIRED';
        } elseif (!$hasPassedQuiz) {
            $quizStatus = 'TEST_FAILED';
        } else {
            $quizStatus = 'TEST_PASSED';
        }

        $canAccessCertificate = ($existingCertificate !== null)
            || ($isInstructionalCompleted && $hasActiveQuiz && $hasPassedQuiz);

        $isAllCompleted = $canAccessCertificate || ($enrollment->status === Enrollment::STATUS_COMPLETED);

        $requestedModuleId = $request->query('module_id');
        $activeModule = null;

        if ($requestedModuleId) {
            $activeModule = $allModules->firstWhere('id', (int) $requestedModuleId);
        }

        // When resuming, open the first uncompleted module, or the first module
        if (!$activeModule) {
            $activeModule = $allModules->first(function ($mod) use ($completedModuleIds) {
                return !in_array($mod->id, $completedModuleIds, true);
            }) ?? $allModules->first();
        }

        return view('learning.player', compact(
            'course',
            'activeModule',
            'totalModulesCount',
            'completedModuleIds',
            'isAllCompleted',
            'enrollment',
            'quiz',
            'hasActiveQuiz',
            'quizScore',
            'hasPassedQuiz',
            'requiredQuizScore',
            'quizStatus',
            'canAccessCertificate',
            'existingCertificate'
        ));
    }

    /**
     * AJAX endpoint to mark module complete and trigger certificate generation upon final completion.
     */
    public function markModuleComplete(Request $request, CertificateGeneratorService $certService): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => ['required', 'integer', 'exists:course_modules,id'],
            'watch_percentage' => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();
        $module = CourseModule::with('section.course')->findOrFail($validated['module_id']);
        $course = $module->section->course;

        $sessionKey = "course_{$course->id}_completed_modules";
        $completed = session()->get($sessionKey, []);

        // Strict 90% video watch verification
        if ($module->module_type === CourseModule::TYPE_VIDEO && !in_array($module->id, $completed, true)) {
            $watchPct = isset($validated['watch_percentage']) ? (float) $validated['watch_percentage'] : 0.0;
            if ($watchPct < 90.0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must watch at least 90% of the instructional video to complete this module.',
                    'required_percentage' => 90,
                    'current_percentage' => $watchPct,
                ], 422);
            }
        }

        if (!in_array($module->id, $completed, true)) {
            $completed[] = $module->id;
            session()->put($sessionKey, $completed);
        }

        $allCourseModules = $course->sections->flatMap->modules;
        $instructionalModules = $allCourseModules->where('module_type', '!=', CourseModule::TYPE_QUIZ);
        $instructionalIds = $instructionalModules->pluck('id')->all();
        $isInstructionalDone = count($instructionalIds) === 0 || count(array_intersect($instructionalIds, $completed)) === count($instructionalIds);

        // Check quiz requirement (80% passing score)
        $quizModule = $allCourseModules->firstWhere('module_type', CourseModule::TYPE_QUIZ);
        $quiz = $quizModule && $quizModule->instance_id ? \App\Models\Quiz::find($quizModule->instance_id) : \App\Models\Quiz::first();
        $hasActiveQuiz = $quiz && ($quiz->questions()->count() > 0 || \App\Models\Question::count() > 0);

        $config = app()->bound(SiteConfigService::class) ? app(SiteConfigService::class) : null;
        $requiredQuizScore = $config ? (float) ($config->get('certificate_min_score', '80') ?: 80.0) : 80.0;

        $hasPassedQuiz = false;
        if ($quiz) {
            $bestAttempt = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('state', \App\Models\QuizAttempt::STATE_GRADED)
                ->orderByDesc('score_obtained')
                ->first();
            if ($bestAttempt && (float)$bestAttempt->score_obtained >= $requiredQuizScore) {
                $hasPassedQuiz = true;
            }
        }

        // Only issue certificate if instructional modules are completed AND student passed active quiz with >= 80%
        $certUnlocked = false;
        if ($isInstructionalDone && $hasActiveQuiz && $hasPassedQuiz) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                $enrollment->update([
                    'status' => Enrollment::STATUS_COMPLETED,
                    'completed_at' => Carbon::now(),
                ]);
            }

            // Issue or retrieve existing unique PDF Certificate
            $certService->generate($user, $course);
            $certUnlocked = true;
        }

        return response()->json([
            'success' => true,
            'completed_count' => count($completed),
            'all_completed' => $isInstructionalDone && $hasActiveQuiz && $hasPassedQuiz,
            'certificate_unlocked' => $certUnlocked,
        ]);
    }

    /**
     * Handle practical assignment upload and grading submission.
     */
    public function submitAssignment(Request $request): RedirectResponse
    {
        $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'submission_file' => ['required', 'file', 'max:20480'], // max 20MB
            'submission_text' => ['nullable', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        $file = $request->file('submission_file');
        $filePath = $file->store('assignments/submissions', 'public');

        AssignmentSubmission::create([
            'assignment_id' => (int) $request->input('assignment_id'),
            'user_id' => $user->id,
            'submission_text' => $request->input('submission_text'),
            'file_paths_json' => [$filePath],
            'status' => AssignmentSubmission::STATUS_SUBMITTED,
        ]);

        return back()->with('success', 'Assignment document successfully submitted for review!');
    }
}
