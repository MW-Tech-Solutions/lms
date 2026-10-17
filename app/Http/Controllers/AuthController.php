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

        if ($request->filled('redirect')) {
            session()->put('url.intended', $request->input('redirect'));
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // Check if 2FA is required
            if ($user->two_factor_secret && session('2fa_verified_user_id') !== $user->id) {
                // If 2FA enabled, redirect to challenge
                session()->put('2fa_pending_user_id', $user->id);
                session()->put('2fa_pending_remember', $remember);
                Auth::logout();
                return redirect()->route('auth.2fa.challenge');
            }

            $isTeacher = $user->isTeacher();
            $isAdminRole = $user->isSuperAdmin() || $user->hasRole([
                'SUPER_ADMIN', 'MANAGER', 'COURSE_CREATOR', 'TEACHER', 'TEACHER_NON_EDITING',
                'CORP_MANAGER', 'INSTRUCTOR', 'TRAINER'
            ]);

            $intendedUrl = session()->get('url.intended');
            if (!$isAdminRole && !$isTeacher && $intendedUrl && str_contains($intendedUrl, '/admin')) {
                session()->forget('url.intended');
            }

            // Check if parent account logging in
            if (!$isAdminRole && !$isTeacher && !session()->has('url.intended') && \App\Models\ParentStudent::where('parent_id', $user->id)->exists()) {
                return redirect()->route('parent.dashboard');
            }

            // Teachers must always be redirected to their teaching dashboard (admin.dashboard)
            if ($isTeacher || $isAdminRole) {
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
            'school_code' => ['nullable', 'string', 'max:64'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'about_instructor' => ['nullable', 'string'],
            'teaching_methodology' => ['nullable', 'string'],
        ]);

        // If school_code is supplied, look up the school tenant
        $schoolTenant = null;
        if (!empty($validated['school_code'])) {
            $codeClean = strtoupper(trim($validated['school_code']));
            $schoolTenant = \App\Models\Tenant::where('school_code', $codeClean)
                ->orWhere('subdomain', strtolower($codeClean))
                ->first();
        }

        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = app(\App\Services\SiteConfigService::class)->generateIdForType($validated['user_type']);
        }

        $user = User::create([
            'tenant_id' => $schoolTenant ? $schoolTenant->id : null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'employee_id' => $validated['employee_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'is_active' => true,
            'approval_status' => $schoolTenant ? 'APPROVED' : 'PENDING',
        ]);

        // If user joined via valid school code, grant full school access (auto-enroll in active courses)
        if ($schoolTenant) {
            $publishedCourses = \App\Models\Course::where('is_published', true)->get();
            foreach ($publishedCourses as $pCourse) {
                \App\Models\Enrollment::firstOrCreate([
                    'user_id' => $user->id,
                    'course_id' => $pCourse->id,
                ], [
                    'tenant_id' => $schoolTenant->id,
                    'status' => \App\Models\Enrollment::STATUS_ACTIVE,
                    'enrolled_at' => now(),
                    'enrollment_type' => 'INSTITUTIONAL',
                ]);
            }
        }

        $jobTitle = match ($user->user_type) {
            'HYBRID' => 'Faculty & Curriculum Instructor',
            'EMPLOYEE' => 'Corporate Associate',
            default => 'Student Scholar',
        };

        UserProfile::create([
            'user_id' => $user->id,
            'avatar_url' => asset('images/profile-img.png'),
            'job_title' => $jobTitle,
            'timezone' => 'UTC',
            'locale' => 'en',
            'about_instructor' => $validated['about_instructor'] ?? null,
            'teaching_methodology' => $validated['teaching_methodology'] ?? null,
            'bio' => $validated['about_instructor'] ?? null,
            'kyc_status' => !empty($validated['teaching_methodology']) ? 'SUBMITTED' : 'PENDING',
            'kyc_submitted_at' => !empty($validated['teaching_methodology']) ? now() : null,
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

        // Staff (EMPLOYEE) or Academic Faculty (HYBRID) must be redirected to their Admin Console, NOT learner dashboard
        if ($user->user_type === 'EMPLOYEE' || $user->user_type === 'HYBRID' || $user->isTeacher()) {
            return redirect()->route('admin.dashboard')->with('success', 'Staff / Faculty account registered successfully! Welcome to your administrative console.');
        }

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

        return redirect('/');
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
            return back()->with('status', 'If your email is registered in our portal, you will receive a secure password reset code shortly.');
        }

        // Generate high-entropy secure token and human-friendly 6-digit verification code
        $token = Str::random(64);
        $code = (string) random_int(100000, 999999);

        // Store combined identifier so both token or code can authorize reset
        // Format stored in DB: $token . '|' . $code
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token . '|' . $code,
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Send Email with Reset Code
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\PasswordResetCodeMail($user, $code, $resetUrl, 60)
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Password reset email could not be sent: ' . $e->getMessage());
        }

        \App\Services\AuditService::log('PASSWORD_RESET_REQUESTED', $user, null, ['email' => $user->email, 'code_sent' => true]);

        return back()
            ->with('status', 'A 6-digit password reset verification code has been dispatched to your email.')
            ->with('dev_reset_url', $resetUrl)
            ->with('dev_reset_code', $code)
            ->with('submitted_email', $user->email);
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
            'token' => ['nullable', 'string'],
            'reset_code' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired password reset request. Please request a new code.']);
        }

        // Check if token expired (60 minutes)
        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset request has expired. Please request a new code.']);
        }

        // Parse stored token: token|code or legacy single token
        $parts = explode('|', (string) $record->token, 2);
        $storedToken = $parts[0] ?? '';
        $storedCode = $parts[1] ?? '';

        $providedToken = trim((string) $request->input('token', ''));
        $providedCode = trim((string) $request->input('reset_code', ''));

        $tokenValid = false;
        if (!empty($providedToken)) {
            $tokenValid = ($providedToken === $storedToken) 
                || ($providedToken === $record->token) 
                || \Illuminate\Support\Facades\Hash::check($providedToken, $record->token);
        }

        $codeValid = false;
        if (!empty($providedCode) && !empty($storedCode)) {
            $codeValid = hash_equals($storedCode, $providedCode);
        }

        if (!$tokenValid && !$codeValid) {
            return back()->withErrors(['reset_code' => 'The 6-digit verification code or security token is incorrect.']);
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

        return redirect()->route('login')->with('status', 'Your password has been successfully reset! You may now sign in with your new credentials.');
    }
}
