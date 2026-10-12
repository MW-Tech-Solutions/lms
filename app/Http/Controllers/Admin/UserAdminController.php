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
    public function index(): View
    {
        $users = User::with(['department', 'roleAssignments.role', 'roleAssignments.context'])->latest()->paginate(20);
        $roles = Role::withCount('roleAssignments')->get();
        $contexts = Context::all();
        $departments = \App\Models\Department::all();

        return view('admin.users.index', compact('users', 'roles', 'contexts', 'departments'));
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
}
