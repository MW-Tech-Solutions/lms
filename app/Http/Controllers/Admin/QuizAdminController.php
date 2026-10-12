<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseSection;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAdminController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::with('questions', 'attempts')->latest()->get();
        $cheatLogs = AntiCheatLog::with('attempt.user')->latest('recorded_at')->paginate(20);
        $courses = Course::with('sections')->get();

        return view('admin.quizzes.index', compact('quizzes', 'cheatLogs', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'time_limit_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:50'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'blur_detection' => ['nullable', 'boolean'],
            'block_copy_paste' => ['nullable', 'boolean'],
            'webcam_snapshots' => ['nullable', 'boolean'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $antiCheatFlags = [
            'blur_detection' => $request->boolean('blur_detection', true),
            'block_copy_paste' => $request->boolean('block_copy_paste', true),
            'webcam_snapshots' => $request->boolean('webcam_snapshots', true),
            'is_active' => $request->boolean('is_active', true),
        ];

        $quiz = Quiz::create([
            'title' => $validated['title'],
            'time_limit_minutes' => (int) $validated['time_limit_minutes'],
            'max_attempts' => (int) $validated['max_attempts'],
            'passing_percentage' => (int) $validated['passing_percentage'],
            'shuffle_questions' => $request->boolean('shuffle_questions', false),
            'anti_cheat_flags' => $antiCheatFlags,
        ]);

        // If a course is selected, attach as module to first section
        if (!empty($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course) {
                $section = $course->sections()->first() ?? CourseSection::create([
                    'course_id' => $course->id,
                    'title' => 'Assessment & Knowledge Checks',
                    'sort_order' => 1,
                ]);

                CourseModule::create([
                    'section_id' => $section->id,
                    'module_type' => CourseModule::TYPE_QUIZ,
                    'instance_id' => $quiz->id,
                    'sort_order' => $section->modules()->count(),
                    'is_mandatory' => true,
                ]);
            }
        }

        \App\Services\AuditService::log('ASSESSMENT_CREATED', $quiz, null, [
            'created_by' => auth()->user()->name ?? 'Faculty Lead',
        ]);

        return back()->with('success', sprintf('Assessment "%s" created and activated successfully.', $quiz->title));
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'time_limit_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:50'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'blur_detection' => ['nullable', 'boolean'],
            'block_copy_paste' => ['nullable', 'boolean'],
            'webcam_snapshots' => ['nullable', 'boolean'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $flags = $quiz->anti_cheat_flags ?? [];
        $flags['blur_detection'] = $request->boolean('blur_detection');
        $flags['block_copy_paste'] = $request->boolean('block_copy_paste');
        $flags['webcam_snapshots'] = $request->boolean('webcam_snapshots');
        $flags['is_active'] = $request->boolean('is_active');

        $quiz->update([
            'title' => $validated['title'],
            'time_limit_minutes' => (int) $validated['time_limit_minutes'],
            'max_attempts' => (int) $validated['max_attempts'],
            'passing_percentage' => (int) $validated['passing_percentage'],
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'anti_cheat_flags' => $flags,
        ]);

        \App\Services\AuditService::log('ASSESSMENT_UPDATED', $quiz, null, [
            'updated_by' => auth()->user()->name ?? 'Faculty Lead',
        ]);

        return back()->with('success', sprintf('Assessment "%s" updated successfully.', $quiz->title));
    }

    public function toggleStatus(Quiz $quiz): RedirectResponse
    {
        $flags = $quiz->anti_cheat_flags ?? [];
        $current = $flags['is_active'] ?? true;
        $flags['is_active'] = !$current;

        $quiz->anti_cheat_flags = $flags;
        $quiz->save();

        $statusLabel = $flags['is_active'] ? 'Activated' : 'Deactivated';

        return back()->with('success', sprintf('Assessment "%s" %s successfully.', $quiz->title, $statusLabel));
    }
}
