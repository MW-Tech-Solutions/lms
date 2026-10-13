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
use Illuminate\Support\Str;
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
            $isAdminRole = $user->roleAssignments()->whereHas('role', fn ($q) => $q->whereIn('name', [
                'SUPER_ADMIN', 'MANAGER', 'COURSE_CREATOR', 'TEACHER', 'TEACHER_NON_EDITING',
                'CORP_MANAGER', 'INSTRUCTOR', 'TRAINER'
            ]))->exists();

            $intendedUrl = session()->get('url.intended');
            if (!$isAdminRole && $intendedUrl && str_contains($intendedUrl, '/admin')) {
                session()->forget('url.intended');
            }

            if ($isAdminRole) {
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

        $jobTitle = match ($user->user_type) {
            'HYBRID' => 'Faculty & Curriculum Instructor',
            'EMPLOYEE' => 'Corporate Associate',
            default => 'Student Scholar',
        };

        UserProfile::create([
            'user_id' => $user->id,
            'job_title' => $jobTitle,
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        // Assign default CBAC Role
        $roleName = match ($user->user_type) {
            'HYBRID' => Role::TEACHER,
            'EMPLOYEE' => Role::LEARNER_EMPLOYEE,
            default => Role::LEARNER_STUDENT,
        };
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

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('status', 'If your email is registered in our portal, you will receive a secure password reset link shortly.');
        }

        $token = Str::random(64);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        \App\Services\AuditService::log('PASSWORD_RESET_REQUESTED', $user, null, ['email' => $user->email]);

        return back()->with('status', 'A secure password recovery link has been generated for your account.')
            ->with('dev_reset_url', $resetUrl);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired password reset request. Please request a new link.']);
        }

        // Check if token expired (60 minutes)
        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new link.']);
        }

        // Check token hash or raw string
        if (!\Illuminate\Support\Facades\Hash::check($request->token, $record->token) && $request->token !== $record->token) {
            return back()->withErrors(['email' => 'Invalid security token provided.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No active user found associated with this email.']);
        }

        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        \App\Services\AuditService::log('PASSWORD_RESET_COMPLETED', $user, null, ['email' => $user->email]);

        return redirect()->route('login')->with('status', 'Your password has been successfully reset! You may now sign in with your new password.');
    }
}
