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
        $users = User::with(['department', 'roleAssignments.role', 'roleAssignments.context'])->paginate(20);
        $roles = Role::all();
        $contexts = Context::all();

        return view('admin.users.index', compact('users', 'roles', 'contexts'));
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
}
