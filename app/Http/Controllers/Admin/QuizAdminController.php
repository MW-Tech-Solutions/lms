<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseSection;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizAdminController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        $coursesQuery = Course::with('sections')->latest();
        if ($isTeacher) {
            $coursesQuery->where('instructor_id', $user->id);
        }
        $courses = $coursesQuery->get();
        $courseIds = $courses->pluck('id')->toArray();

        // Quizzes query
        $quizzesQuery = Quiz::with('questions', 'attempts')->latest();
        if ($isTeacher) {
            // Quizzes linked to teacher's courses
            $quizzesQuery->whereHas('questions', function ($q) use ($courseIds, $user) {
                $q->where(function ($sub) use ($courseIds, $user) {
                    $sub->whereIn('metadata_json->course_id', $courseIds)
                        ->orWhere('metadata_json->instructor_id', $user->id);
                });
            })->orWhereIn('id', function ($q) use ($courseIds) {
                $q->select('instance_id')
                    ->from('course_modules')
                    ->where('module_type', CourseModule::TYPE_QUIZ)
                    ->whereIn('section_id', function ($sq) use ($courseIds) {
                        $sq->select('id')->from('course_sections')->whereIn('course_id', $courseIds);
                    });
            });
        }
        $quizzes = $quizzesQuery->get();

        $cheatLogsQuery = AntiCheatLog::with('attempt.user')->latest('recorded_at');
        if ($isTeacher) {
            $cheatLogsQuery->whereIn('attempt_id', function ($q) use ($quizzes) {
                $q->select('id')->from('quiz_attempts')->whereIn('quiz_id', $quizzes->pluck('id'));
            });
        }
        $cheatLogs = $cheatLogsQuery->paginate(20);

        // Available questions for modal selection
        $availableQuestionsQuery = Question::with('options')->latest();
        if ($isTeacher) {
            $availableQuestionsQuery->where(function ($q) use ($courseIds, $user) {
                $q->whereIn('metadata_json->course_id', $courseIds)
                  ->orWhere('metadata_json->instructor_id', $user->id);
            });
        }
        $availableQuestions = $availableQuestionsQuery->take(50)->get();

        return view('admin.quizzes.index', compact('quizzes', 'cheatLogs', 'courses', 'availableQuestions', 'isTeacher'));
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
            'question_mode' => ['nullable', 'string', 'in:none,random,specific'],
            'fetch_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'fetch_chapter' => ['nullable', 'string', 'max:255'],
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
        ]);

        $user = Auth::user();
        if ($user && $user->isTeacher() && !$user->isSuperAdmin()) {
            if (!$user->isApprovedTeacher()) {
                return redirect()->route('admin.dashboard')->with('error', 'Your instructor account is currently pending administrator verification. An administrator must approve your faculty profile before you can initiate assessments or exams.');
            }

            if (!empty($validated['course_id'])) {
                $course = Course::find($validated['course_id']);
                if ($course && $course->instructor_id !== $user->id) {
                    return back()->withErrors(['course_id' => 'You can only attach assessments to courses you created.']);
                }
            }
        }

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

        // Attach questions according to chosen mode
        $questionMode = $request->input('question_mode', 'none');
        if ($questionMode === 'random' && !empty($validated['fetch_count'])) {
            $qQuery = Question::query();
            if (!empty($validated['course_id'])) {
                $qQuery->where('metadata_json->course_id', (int) $validated['course_id']);
            }
            if (!empty($validated['fetch_chapter'])) {
                $qQuery->where('metadata_json->chapter', $validated['fetch_chapter']);
            }
            $randomQuestionIds = $qQuery->inRandomOrder()->limit((int) $validated['fetch_count'])->pluck('id')->toArray();
            if (!empty($randomQuestionIds)) {
                $quiz->questions()->syncWithoutDetaching($randomQuestionIds);
            }
        } elseif ($questionMode === 'specific' && !empty($validated['question_ids'])) {
            $quiz->questions()->syncWithoutDetaching($validated['question_ids']);
        }

        \App\Services\AuditService::log('ASSESSMENT_CREATED', $quiz, null, [
            'created_by' => auth()->user()->name ?? 'Faculty Lead',
            'questions_attached' => $quiz->questions()->count(),
        ]);

        return back()->with('success', sprintf('Assessment "%s" created successfully with %d questions attached.', $quiz->title, $quiz->questions()->count()));
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

    /**
     * Question Bank Management
     */
    public function questionsIndex(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user && $user->isTeacher() && !$user->isSuperAdmin();

        $coursesQuery = Course::with('sections')->latest();
        if ($isTeacher) {
            $coursesQuery->where('instructor_id', $user->id);
        }
        $courses = $coursesQuery->get();
        $courseIds = $courses->pluck('id')->toArray();

        $questionsQuery = Question::with('options', 'bank')->latest();

        if ($isTeacher) {
            $questionsQuery->where(function ($q) use ($courseIds, $user) {
                $q->whereIn('metadata_json->course_id', $courseIds)
                  ->orWhere('metadata_json->instructor_id', $user->id);
            });
        }

        if ($request->filled('course_id')) {
            $questionsQuery->where('metadata_json->course_id', (int) $request->course_id);
        }

        if ($request->filled('chapter')) {
            $questionsQuery->where('metadata_json->chapter', 'like', '%' . $request->chapter . '%');
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $questionsQuery->where('question_text', 'like', "%{$s}%");
        }

        $questions = $questionsQuery->paginate(15)->withQueryString();

        // Get unique chapters list for quick filter
        $chapters = Question::whereNotNull('metadata_json->chapter')
            ->get()
            ->pluck('chapter')
            ->filter()
            ->unique()
            ->values();

        return view('admin.quizzes.questions', compact('questions', 'courses', 'chapters', 'isTeacher'));
    }

    public function questionStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'chapter' => ['nullable', 'string', 'max:255'],
            'question_text' => ['required', 'string'],
            'type' => ['required', 'string', 'in:SINGLE_CHOICE,TRUE_FALSE,SHORT_ANSWER'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['nullable', 'array', 'min:2'],
            'options.*' => ['nullable', 'string'],
            'correct_option' => ['nullable'],
        ]);

        $user = Auth::user();
        if ($user && $user->isTeacher() && !$user->isSuperAdmin() && !empty($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course && $course->instructor_id !== $user->id) {
                return back()->withErrors(['course_id' => 'You can only add questions to courses you created.']);
            }
        }

        $bank = QuestionBank::firstOrCreate(
            ['name' => 'Cambridge Academic Question Bank'],
            ['department_id' => null]
        );

        $courseTitle = !empty($validated['course_id']) ? Course::find($validated['course_id'])?->title : null;

        $metadata = [
            'course_id' => !empty($validated['course_id']) ? (int) $validated['course_id'] : null,
            'course_title' => $courseTitle,
            'chapter' => $validated['chapter'] ?? 'General Topics',
            'instructor_id' => $user?->id,
            'created_by' => $user?->name,
        ];

        $question = Question::create([
            'bank_id' => $bank->id,
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'points' => (int) $validated['points'],
            'metadata_json' => $metadata,
        ]);

        if ($validated['type'] === 'SINGLE_CHOICE' && !empty($validated['options'])) {
            $correctIdx = (int) ($validated['correct_option'] ?? 0);
            foreach ($validated['options'] as $idx => $optText) {
                if (!empty(trim((string)$optText))) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim((string)$optText),
                        'is_correct' => ($idx === $correctIdx),
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        } elseif ($validated['type'] === 'TRUE_FALSE') {
            $isTrueCorrect = ($request->input('tf_correct') === 'true');
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'True',
                'is_correct' => $isTrueCorrect,
                'sort_order' => 1,
            ]);
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'False',
                'is_correct' => !$isTrueCorrect,
                'sort_order' => 2,
            ]);
        }

        return back()->with('success', 'Question added to Question Bank successfully.');
    }

    public function questionDestroy(Question $question): RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->isTeacher() && !$user->isSuperAdmin()) {
            if ($question->instructor_id && $question->instructor_id !== $user->id) {
                return back()->withErrors(['error' => 'Unauthorized: You can only delete your own questions.']);
            }
        }

        $question->delete();
        return back()->with('success', 'Question removed from question bank.');
    }

    public function downloadQuestionTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="question_bank_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");
            
            // Header row
            fputcsv($handle, [
                'Course_ID',
                'Chapter',
                'Question_Type',
                'Question_Text',
                'Option_A',
                'Option_B',
                'Option_C',
                'Option_D',
                'Correct_Option',
                'Points',
            ]);

            // Sample row 1
            fputcsv($handle, [
                '1',
                'Chapter 1: Vectors & Kinematics',
                'SINGLE_CHOICE',
                'What is the SI unit of velocity in Cambridge Physics (9702)?',
                'm/s',
                'km/h',
                'm/s²',
                'Newtons',
                'A',
                '2',
            ]);

            // Sample row 2
            fputcsv($handle, [
                '1',
                'Chapter 2: Forces & Dynamics',
                'SINGLE_CHOICE',
                'According to Newton\'s third law, action and reaction forces act on:',
                'The same body in the same direction',
                'Different bodies in opposite directions',
                'The same body in opposite directions',
                'Bodies in contact only',
                'B',
                '2',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    public function uploadQuestions(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
            'target_course_id' => ['nullable', 'exists:courses,id'],
            'target_chapter' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('csv_file');
        if (!$file || !$file->isValid()) {
            return back()->withErrors(['csv_file' => 'Invalid file uploaded.']);
        }

        $user = Auth::user();
        $bank = QuestionBank::firstOrCreate(
            ['name' => 'Cambridge Academic Question Bank'],
            ['department_id' => null]
        );

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Failed to read the uploaded CSV file.']);
        }

        // Detect BOM and read header
        $header = fgetcsv($handle);
        $importedCount = 0;

        while (($row = fgetcsv($handle, 4096, ',')) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Expected columns: Course_ID, Chapter, Question_Type, Question_Text, Option_A, Option_B, Option_C, Option_D, Correct_Option, Points
            $courseId = !empty($row[0]) ? (int) trim($row[0]) : (!empty($request->target_course_id) ? (int) $request->target_course_id : null);
            $chapter = !empty($row[1]) ? trim($row[1]) : ($request->target_chapter ?? 'General Chapter');
            $type = !empty($row[2]) ? strtoupper(trim($row[2])) : 'SINGLE_CHOICE';
            $questionText = !empty($row[3]) ? trim($row[3]) : null;
            $optA = $row[4] ?? '';
            $optB = $row[5] ?? '';
            $optC = $row[6] ?? '';
            $optD = $row[7] ?? '';
            $correctLetter = !empty($row[8]) ? strtoupper(trim($row[8])) : 'A';
            $points = !empty($row[9]) ? (int) trim($row[9]) : 1;

            if (empty($questionText)) {
                continue;
            }

            $courseTitle = $courseId ? Course::find($courseId)?->title : null;

            $metadata = [
                'course_id' => $courseId,
                'course_title' => $courseTitle,
                'chapter' => $chapter,
                'instructor_id' => $user?->id,
                'imported_from_excel' => true,
            ];

            $question = Question::create([
                'bank_id' => $bank->id,
                'type' => 'SINGLE_CHOICE',
                'question_text' => $questionText,
                'points' => $points > 0 ? $points : 1,
                'metadata_json' => $metadata,
            ]);

            $options = [
                'A' => $optA,
                'B' => $optB,
                'C' => $optC,
                'D' => $optD,
            ];

            $order = 1;
            foreach ($options as $letter => $text) {
                if (!empty(trim((string)$text))) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim((string)$text),
                        'is_correct' => ($letter === $correctLetter),
                        'sort_order' => $order++,
                    ]);
                }
            }

            $importedCount++;
        }

        fclose($handle);

        return back()->with('success', sprintf('Successfully imported %d questions into the Question Bank from your file.', $importedCount));
    }
}
