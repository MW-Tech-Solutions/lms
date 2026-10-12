<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Context;
use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            \App\Services\AuditService::log('USER_LOGIN', $user, null, ['email' => $user->email]);

            if ($user->roleAssignments()->whereHas('role', fn ($q) => $q->whereIn('name', ['SUPER_ADMIN', 'CORP_MANAGER', 'INSTRUCTOR']))->exists()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('my-learning'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our identity records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:EMPLOYEE,STUDENT,HYBRID'],
            'employee_id' => ['nullable', 'string', 'max:64'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'employee_id' => $validated['employee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'job_title' => $user->user_type === 'EMPLOYEE' ? 'Corporate Associate' : 'Student Scholar',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        // Assign default CBAC Role
        $roleName = $user->user_type === 'EMPLOYEE' ? Role::LEARNER_EMPLOYEE : Role::LEARNER_STUDENT;
        $role = Role::where('name', $roleName)->first();
        $context = Context::where('context_level', Context::LEVEL_SYSTEM)->first();

        if ($role && $context) {
            RoleAssignment::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'context_id' => $context->id,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        \App\Services\AuditService::log('USER_REGISTER', $user, null, ['name' => $user->name, 'email' => $user->email, 'type' => $user->user_type]);

        return redirect()->route('my-learning')->with('success', 'Account registered successfully! Welcome to your learning portal.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            \App\Services\AuditService::log('USER_LOGOUT', $user, null, ['email' => $user->email]);
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been safely signed out.');
    }
}
