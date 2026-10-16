<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LevelAdminController extends Controller
{
    public function index(Request $request): View
    {
        $selectedLevelId = $request->get('level_id');
        $statusFilter = $request->get('status', 'ALL'); // ALL, PENDING, APPROVED
        $search = trim((string) $request->get('search', ''));

        // Fetch all levels in progression order with courses count
        $levels = CourseCategory::withCount('courses')->orderBy('id')->get();

        // Query courses/materials uploaded by teachers
        $query = Course::with(['category', 'instructor', 'sections.modules'])
            ->latest('updated_at');

        if ($selectedLevelId) {
            $query->where('category_id', $selectedLevelId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($statusFilter === 'APPROVED') {
            $query->where('is_published', true);
        } elseif ($statusFilter === 'PENDING') {
            $query->where('is_published', false);
        }

        $materials = $query->paginate(12)->withQueryString();

        // Teacher requests for additional Cambridge level authorization
        $levelRequestsQuery = \App\Models\TeacherLevelRequest::with(['user.profile', 'category', 'reviewer'])
            ->latest();
        if ($selectedLevelId) {
            $levelRequestsQuery->where('category_id', $selectedLevelId);
        }
        $levelRequests = $levelRequestsQuery->paginate(10, ['*'], 'requests_page')->withQueryString();
        $pendingRequestsCount = \App\Models\TeacherLevelRequest::where('status', 'PENDING')->count();

        $stats = [
            'total_materials' => Course::count(),
            'approved_materials' => Course::where('is_published', true)->count(),
            'pending_review' => Course::where('is_published', false)->count(),
            'total_levels' => $levels->count(),
            'pending_level_requests' => $pendingRequestsCount,
        ];

        return view('admin.levels.index', compact('levels', 'materials', 'levelRequests', 'pendingRequestsCount', 'selectedLevelId', 'statusFilter', 'stats', 'search'));
    }

    public function approveMaterial(Course $course): RedirectResponse
    {
        $course->is_published = true;
        $course->material_approval_status = 'APPROVED';
        $course->material_rejection_reason = null;
        $course->save();

        AuditService::log('TEACHER_MATERIAL_APPROVED', $course, null, [
            'approved_by' => auth()->user()->name ?? 'Administrator',
            'course_title' => $course->title,
            'level' => $course->category?->name ?? 'Unassigned',
        ]);

        return back()->with('success', sprintf('Material "%s" approved and published for students.', $course->title));
    }

    public function rejectMaterial(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $validated['reason'] ?? $request->input('reason', 'Material syllabus does not comply with platform curriculum standards.');

        $course->is_published = false;
        $course->material_approval_status = 'REJECTED';
        $course->material_rejection_reason = $reason;
        $course->save();

        // Dispatch formal email to instructor with direct link requiring authentication
        if ($course->instructor && $course->instructor->email) {
            $directAuthLink = route('login', ['redirect' => route('admin.courses.index')]);
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.compliance_notice',
                    [
                        'recipientName' => $course->instructor->name,
                        'heading' => 'Curriculum Material Review Notice',
                        'introText' => "Your uploaded curriculum material for \"{$course->title}\" was reviewed by the academic board and could not be approved.",
                        'reasonText' => $course->material_rejection_reason,
                        'actionUrl' => $directAuthLink,
                        'actionText' => 'View & Update Material Submission',
                        'subject' => "Curriculum Material Non-Compliance Notice: {$course->title}",
                    ],
                    function ($m) use ($course) {
                        $m->to($course->instructor->email)->subject("Curriculum Material Non-Compliance Notice: {$course->title}");
                    }
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed sending material rejection email: ' . $e->getMessage());
            }
        }

        AuditService::log('TEACHER_MATERIAL_REJECTED', $course, null, [
            'rejected_by' => auth()->user()->name ?? 'Administrator',
            'course_title' => $course->title,
            'reason' => $course->material_rejection_reason,
            'level' => $course->category?->name ?? 'Unassigned',
        ]);

        return back()->with('info', sprintf('Material "%s" rejected with compliance notice dispatched to instructor.', $course->title));
    }

    public function storeLevel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:course_categories,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:course_categories,slug'],
            'parent_id' => ['nullable', 'exists:course_categories,id'],
        ]);

        $defaultTenant = \App\Models\Tenant::first();
        $slug = !empty($validated['slug']) ? \Illuminate\Support\Str::slug($validated['slug']) : \Illuminate\Support\Str::slug($validated['name']);

        $level = CourseCategory::create([
            'tenant_id' => $defaultTenant?->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?: null,
        ]);

        AuditService::log('CAMBRIDGE_LEVEL_CREATED', $level, null, [
            'created_by' => auth()->user()->name ?? 'Administrator',
            'name' => $level->name,
            'slug' => $level->slug,
        ]);

        return back()->with('success', sprintf('Cambridge Level "%s" created successfully and ready for course curation.', $level->name));
    }

    public function updateLevel(Request $request, CourseCategory $level): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:course_categories,name,' . $level->id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:course_categories,slug,' . $level->id],
            'parent_id' => ['nullable', 'exists:course_categories,id'],
        ]);

        $level->name = $validated['name'];
        if (!empty($validated['slug'])) {
            $level->slug = \Illuminate\Support\Str::slug($validated['slug']);
        }
        $level->parent_id = $validated['parent_id'] ?: null;
        $level->save();

        AuditService::log('CAMBRIDGE_LEVEL_UPDATED', $level, null, [
            'updated_by' => auth()->user()->name ?? 'Administrator',
            'name' => $level->name,
        ]);

        return back()->with('success', sprintf('Cambridge Level "%s" updated successfully.', $level->name));
    }

    public function destroyLevel(CourseCategory $level): RedirectResponse
    {
        $coursesCount = $level->courses()->count();
        if ($coursesCount > 0) {
            return back()->with('error', sprintf('Cannot delete level "%s" because it has %d associated courses/materials.', $level->name, $coursesCount));
        }

        $name = $level->name;
        $level->delete();

        AuditService::log('CAMBRIDGE_LEVEL_DELETED', null, null, [
            'deleted_by' => auth()->user()->name ?? 'Administrator',
            'name' => $name,
        ]);

        return back()->with('success', sprintf('Cambridge Level "%s" deleted successfully.', $name));
    }
}

