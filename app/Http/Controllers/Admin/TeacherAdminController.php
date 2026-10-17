<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Context;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AuditService;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = strtoupper((string) $request->get('status', 'ALL'));
        $search = trim((string) $request->get('search', ''));
        $sortBy = (string) $request->get('sort', 'latest');

        // Query all teacher accounts (by role or employee type)
        $query = User::with(['profile', 'department', 'roleAssignments.role'])
            ->where(function ($q) {
                $q->whereHas('roleAssignments.role', function ($rq) {
                    $rq->whereIn('name', [Role::TEACHER, Role::TEACHER_NON_EDITING]);
                })->orWhere('user_type', 'EMPLOYEE');
            });

        // Filter by approval status
        if (in_array($status, ['APPROVED', 'PENDING', 'UNAPPROVED', 'REJECTED'], true)) {
            $query->where('approval_status', $status);
        }

        // Search by name or email
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        // Perfect pagination with appended query strings
        $teachers = $query->paginate(10)->withQueryString();

        // Status counts for badge tabs
        $baseCountQuery = User::where(function ($q) {
            $q->whereHas('roleAssignments.role', function ($rq) {
                $rq->whereIn('name', [Role::TEACHER, Role::TEACHER_NON_EDITING]);
            })->orWhere('user_type', 'EMPLOYEE');
        });

        $counts = [
            'all' => (clone $baseCountQuery)->count(),
            'approved' => (clone $baseCountQuery)->where('approval_status', 'APPROVED')->count(),
            'pending' => (clone $baseCountQuery)->where('approval_status', 'PENDING')->count(),
            'unapproved' => (clone $baseCountQuery)->where('approval_status', 'UNAPPROVED')->count(),
            'rejected' => (clone $baseCountQuery)->where('approval_status', 'REJECTED')->count(),
        ];

        return view('admin.teachers.index', compact('teachers', 'counts', 'status', 'search', 'sortBy'));
    }

    public function updateStatus(Request $request, User $user, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'approval_status' => ['required', 'in:APPROVED,PENDING,UNAPPROVED,REJECTED'],
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $newStatus = $validated['approval_status'];
        $oldStatus = $user->approval_status ?? 'PENDING';

        $user->approval_status = $newStatus;
        if ($newStatus === 'APPROVED') {
            $user->is_active = true;
            $user->rejection_reason = null;
        } elseif ($newStatus === 'REJECTED') {
            $user->is_active = false;
            $user->rejection_reason = $validated['rejection_reason'] ?? 'Registration credentials or syllabus alignment was not compliant with platform requirements.';

            // Dispatch formal non-compliance rejection notification email with authenticated redirect link
            $directAuthLink = route('login', ['redirect' => route('home')]);
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.compliance_notice',
                    [
                        'recipientName' => $user->name,
                        'heading' => 'Registration Compliance Review Notice',
                        'introText' => 'Your instructor/educator account registration could not be approved at this time following academic compliance review.',
                        'reasonText' => $user->rejection_reason,
                        'actionUrl' => $directAuthLink,
                        'actionText' => 'Access Account & Update Profile',
                        'subject' => 'Registration Compliance Review & Rejection Notice',
                    ],
                    function ($m) use ($user) {
                        $m->to($user->email)->subject('Registration Compliance Review & Rejection Notice');
                    }
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Teacher rejection email delivery failed: ' . $e->getMessage());
            }
        }
        $user->save();

        AuditService::log('TEACHER_APPROVAL_STATUS_CHANGED', $user, null, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'rejection_reason' => $user->rejection_reason,
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Teacher %s approval status successfully updated to %s.', $user->name, $newStatus));
    }

    public function store(Request $request, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'approval_status' => ['required', 'in:APPROVED,PENDING,UNAPPROVED,REJECTED'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
            'bio' => ['nullable', 'string'],
            'about_instructor' => ['nullable', 'string'],
            'teaching_methodology' => ['nullable', 'string'],
        ]);

        $defaultTenant = \App\Models\Tenant::first();

        $user = User::create([
            'tenant_id' => $defaultTenant?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'user_type' => 'EMPLOYEE',
            'is_active' => $validated['approval_status'] === 'APPROVED',
            'approval_status' => $validated['approval_status'],
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'avatar_url' => $validated['avatar_url'] ?? asset('images/profile-img.png'),
            'job_title' => $validated['job_title'] ?? 'Cambridge Specialist Tutor',
            'bio' => $validated['about_instructor'] ?? $validated['bio'] ?? null,
            'about_instructor' => $validated['about_instructor'] ?? $validated['bio'] ?? null,
            'teaching_methodology' => $validated['teaching_methodology'] ?? null,
            'kyc_status' => !empty($validated['teaching_methodology']) ? 'VERIFIED' : 'PENDING',
            'kyc_submitted_at' => now(),
        ]);

        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['is_system' => true]);
        $systemContext = Context::first() ?? Context::create(['name' => 'System Global', 'context_type' => 'SYSTEM']);

        RoleAssignment::create([
            'user_id' => $user->id,
            'role_id' => $teacherRole->id,
            'context_id' => $systemContext->id,
        ]);

        $permService->flushUserCache($user);

        AuditService::log('TEACHER_REGISTERED_BY_ADMIN', $user, null, [
            'approval_status' => $validated['approval_status'],
            'created_by' => auth()->user()->name ?? 'Administrator',
        ]);

        return back()->with('success', sprintf('Teacher %s registered successfully with status: %s.', $user->name, $validated['approval_status']));
    }

    /**
     * Teacher submits a request for an additional Cambridge Level.
     */
    public function requestLevel(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (!$user || !$user->isTeacher()) {
            abort(403, 'Unauthorized. Only teachers can request additional Cambridge teaching levels.');
        }

        // A teacher MUST be approved before they can request new or additional levels
        if ($user->approval_status !== 'APPROVED') {
            return back()->with('error', 'Account Verification Required: You must be an approved teacher before you can request additional Cambridge levels. Please await administrative approval for your initial teaching credentials.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:course_categories,id'],
            'request_notes' => ['required', 'string', 'max:2000'],
        ]);

        $categoryId = (int) $validated['category_id'];

        // Check if already assigned
        $alreadyAssigned = \App\Models\TeacherLevelAssignment::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->exists();

        if ($alreadyAssigned) {
            return back()->with('info', 'You are already authorized to tutor and upload materials for this Cambridge level.');
        }

        // Check if already has a pending request
        $existingPending = \App\Models\TeacherLevelRequest::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->where('status', 'PENDING')
            ->first();

        if ($existingPending) {
            return back()->with('warning', 'You already have a pending authorization request for this Cambridge level under review by administrators.');
        }

        $levelRequest = \App\Models\TeacherLevelRequest::create([
            'user_id' => $user->id,
            'category_id' => $categoryId,
            'status' => 'PENDING',
            'request_notes' => $validated['request_notes'],
        ]);

        $category = \App\Models\CourseCategory::find($categoryId);

        AuditService::log('TEACHER_LEVEL_REQUESTED', $user, null, [
            'category_id' => $categoryId,
            'category_name' => $category?->name,
            'notes' => $validated['request_notes'],
        ]);

        return back()->with('success', sprintf('Your request to tutor "%s" has been submitted to site administrators for accreditation review.', $category?->name));
    }

    /**
     * Admin approves a teacher's level request and grants teaching authority.
     */
    public function approveLevelRequest(\App\Models\TeacherLevelRequest $levelRequest): RedirectResponse
    {
        $admin = auth()->user();

        $levelRequest->update([
            'status' => 'APPROVED',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_feedback' => 'Approved by academic administration.',
        ]);

        // Automatically assign level to teacher
        \App\Models\TeacherLevelAssignment::firstOrCreate([
            'user_id' => $levelRequest->user_id,
            'category_id' => $levelRequest->category_id,
        ]);

        $teacher = $levelRequest->user;
        $category = $levelRequest->category;

        AuditService::log('TEACHER_LEVEL_REQUEST_APPROVED', $teacher, null, [
            'category_id' => $levelRequest->category_id,
            'category_name' => $category?->name,
            'reviewed_by' => $admin->name,
        ]);

        return back()->with('success', sprintf('Cambridge Level "%s" successfully approved and assigned to teacher %s.', $category?->name ?? 'Level', $teacher?->name ?? 'Teacher'));
    }

    /**
     * Admin rejects a teacher's level request.
     */
    public function rejectLevelRequest(Request $request, \App\Models\TeacherLevelRequest $levelRequest): RedirectResponse
    {
        $admin = auth()->user();
        $feedback = $request->input('admin_feedback', 'Level authorization application declined.');

        $levelRequest->update([
            'status' => 'REJECTED',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_feedback' => $feedback,
        ]);

        $teacher = $levelRequest->user;
        $category = $levelRequest->category;

        AuditService::log('TEACHER_LEVEL_REQUEST_REJECTED', $teacher, null, [
            'category_id' => $levelRequest->category_id,
            'category_name' => $category?->name,
            'admin_feedback' => $feedback,
            'reviewed_by' => $admin->name,
        ]);

        return back()->with('info', sprintf('Level request for "%s" has been rejected.', $category?->name ?? 'Level'));
    }
}
