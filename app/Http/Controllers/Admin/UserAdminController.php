<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Context;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = strtoupper(trim((string) $request->query('role', 'ALL')));
        $levelFilter = (int) $request->query('level', 0);
        $statusFilter = strtoupper(trim((string) $request->query('status', 'ALL'))); // ALL, ACTIVE, ARCHIVED

        $query = User::with(['department', 'roleAssignments.role', 'roleAssignments.context', 'teachingLevels'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Archive / Status filter
        if ($statusFilter === 'ARCHIVED') {
            $query->where('is_archived', true);
        } elseif ($statusFilter === 'ACTIVE') {
            $query->where('is_archived', false)->where('is_active', true);
        }

        // Role filter
        if ($roleFilter === 'TEACHER') {
            $query->whereHas('roleAssignments.role', function ($rq) {
                $rq->whereIn('name', ['TEACHER', 'TEACHER_NON_EDITING', 'INSTRUCTOR']);
            });
        } elseif ($roleFilter === 'PARENT') {
            $query->where(function ($q) {
                $q->where('user_type', 'PARENT')
                  ->orWhereExists(function ($sub) {
                      $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('parent_students')
                          ->whereColumn('parent_students.parent_id', 'users.id');
                  });
            });
        } elseif ($roleFilter === 'STUDENT') {
            $query->where(function ($q) {
                $q->where('user_type', 'STUDENT')
                  ->orWhereHas('roleAssignments.role', function ($rq) {
                      $rq->whereIn('name', ['STUDENT', 'LEARNER_STUDENT']);
                  });
            });
        } elseif ($roleFilter === 'ADMIN') {
            $query->whereHas('roleAssignments.role', function ($rq) {
                $rq->whereIn('name', ['SUPER_ADMIN', 'MANAGER', 'CORP_MANAGER', 'COURSE_CREATOR']);
            });
        }

        // Level filter (specifically filters teachers authorized to tutor that level)
        if ($levelFilter > 0) {
            $query->whereHas('teachingLevels', function ($lq) use ($levelFilter) {
                $lq->where('course_categories.id', $levelFilter);
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Persona category counts
        $counts = [
            'all' => User::count(),
            'teachers' => User::whereHas('roleAssignments.role', fn($q) => $q->whereIn('name', ['TEACHER', 'TEACHER_NON_EDITING', 'INSTRUCTOR']))->count(),
            'parents' => User::where('user_type', 'PARENT')->orWhereExists(fn($sub) => $sub->select(\Illuminate\Support\Facades\DB::raw(1))->from('parent_students')->whereColumn('parent_students.parent_id', 'users.id'))->count(),
            'students' => User::where('user_type', 'STUDENT')->orWhereHas('roleAssignments.role', fn($q) => $q->whereIn('name', ['STUDENT', 'LEARNER_STUDENT']))->count(),
            'admins' => User::whereHas('roleAssignments.role', fn($q) => $q->whereIn('name', ['SUPER_ADMIN', 'MANAGER', 'CORP_MANAGER', 'COURSE_CREATOR']))->count(),
            'archived' => User::where('is_archived', true)->count(),
        ];

        $roles = Role::withCount('roleAssignments')->get();
        $contexts = Context::all();
        $departments = \App\Models\Department::all();
        $cambridgeLevels = \App\Models\CourseCategory::orderBy('id', 'asc')->get();

        return view('admin.users.index', compact(
            'users', 'roles', 'contexts', 'departments', 'cambridgeLevels',
            'search', 'roleFilter', 'levelFilter', 'statusFilter', 'counts'
        ));
    }

    public function store(Request $request, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'user_type' => ['required', 'in:EMPLOYEE,STUDENT,HYBRID'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'context_id' => ['nullable', 'exists:contexts,id'],
        ]);

        $defaultTenant = \App\Models\Tenant::first();

        $user = User::create([
            'tenant_id' => $defaultTenant?->id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'employee_id' => $validated['employee_id'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($validated['role_id']) && !empty($validated['context_id'])) {
            RoleAssignment::create([
                'user_id' => $user->id,
                'role_id' => (int) $validated['role_id'],
                'context_id' => (int) $validated['context_id'],
            ]);
            $permService->flushUserCache($user);
        }

        \App\Services\AuditService::log('USER_CREATED', $user, null, [
            'created_by' => auth()->user()->name ?? 'System Admin',
        ]);

        return back()->with('success', sprintf('User %s created successfully.', $user->name));
    }

    public function update(Request $request, User $user, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'user_type' => ['required', 'in:EMPLOYEE,STUDENT,HYBRID'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_type = $validated['user_type'];
        $user->employee_id = $validated['employee_id'] ?? null;
        $user->department_id = $validated['department_id'] ?? null;
        $user->is_active = $request->boolean('is_active');

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();
        $permService->flushUserCache($user);

        \App\Services\AuditService::log('USER_UPDATED', $user, null, [
            'updated_by' => auth()->user()->name ?? 'System Admin',
        ]);

        return back()->with('success', sprintf('User %s updated successfully.', $user->name));
    }

    public function assignRole(Request $request, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'context_id' => ['required', 'exists:contexts,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $assignment = RoleAssignment::firstOrCreate([
            'user_id' => $user->id,
            'role_id' => (int) $validated['role_id'],
            'context_id' => (int) $validated['context_id'],
        ]);

        \App\Services\AuditService::log('ROLE_ASSIGNED', $user, null, [
            'role_id' => $validated['role_id'],
            'context_id' => $validated['context_id'],
            'assigned_by' => auth()->user()->name ?? 'System Admin',
        ]);

        $permService->flushUserCache($user);

        return back()->with('success', sprintf('CBAC Role successfully assigned to %s.', $user->name));
    }

    public function removeRole(Request $request, PermissionService $permService): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:role_assignments,id'],
        ]);

        $assignment = RoleAssignment::with('user', 'role')->findOrFail($validated['assignment_id']);
        $userName = $assignment->user?->name ?? 'User';
        $roleName = $assignment->role?->name ?? 'Role';

        $user = $assignment->user;
        $assignment->delete();

        if ($user) {
            $permService->flushUserCache($user);
            \App\Services\AuditService::log('ROLE_REMOVED', $user, null, [
                'role' => $roleName,
                'removed_by' => auth()->user()->name ?? 'System Admin',
            ]);
        }

        return back()->with('success', sprintf('Role %s removed from %s.', $roleName, $userName));
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $roleName = strtoupper(trim(str_replace(' ', '_', $validated['name'])));

        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['is_system' => false]
        );

        return back()->with('success', sprintf('Role %s registered in CBAC registry.', $role->name));
    }

    /**
     * Archive or Unarchive a user account.
     */
    public function toggleArchive(User $user): RedirectResponse
    {
        $admin = auth()->user();

        // Prevent self-archiving or archiving root super admin
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot archive your own active administrator account.');
        }

        if ($user->isSuperAdmin() && $user->id === 1) {
            return back()->with('error', 'The primary Root Super Administrator cannot be archived.');
        }

        $user->is_archived = !$user->is_archived;
        // If archived, deactivate login capability
        if ($user->is_archived) {
            $user->is_active = false;
        }
        $user->save();

        $action = $user->is_archived ? 'USER_ARCHIVED' : 'USER_UNARCHIVED';
        \App\Services\AuditService::log($action, $user, null, [
            'is_archived' => $user->is_archived,
            'archived_by' => $admin->name,
        ]);

        $statusMsg = $user->is_archived ? 'archived and restricted from accessing the platform' : 'restored from archives to active identity registry';

        return back()->with('success', sprintf('User %s has been successfully %s.', $user->name, $statusMsg));
    }

    /**
     * Permanently delete a user account and clean up dependencies.
     */
    public function destroy(User $user, PermissionService $permService): RedirectResponse
    {
        $admin = auth()->user();

        // Prevent self-deletion
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot delete your own logged-in account.');
        }

        // Prevent deleting root super administrator (ID 1)
        if ($user->isSuperAdmin() && $user->id === 1) {
            return back()->with('error', 'Action prohibited: Root Super Administrator identity cannot be deleted.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Clean up role assignments
        $user->roleAssignments()->delete();

        // Clean up teacher level assignments & requests
        \App\Models\TeacherLevelAssignment::where('user_id', $user->id)->delete();
        \App\Models\TeacherLevelRequest::where('user_id', $user->id)->delete();

        // Flush permissions cache
        $permService->flushUserCache($user);

        // Delete profile and user
        $user->profile()->delete();
        $user->delete();

        \App\Services\AuditService::log('USER_DELETED', null, null, [
            'deleted_user_name' => $userName,
            'deleted_user_email' => $userEmail,
            'deleted_by' => $admin->name,
        ]);

        return back()->with('success', sprintf('User %s (%s) has been permanently deleted from identity records.', $userName, $userEmail));
    }
}
