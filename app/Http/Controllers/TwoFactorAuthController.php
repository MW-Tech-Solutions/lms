<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorAuthController extends Controller
{
    public function showChallenge(TwoFactorAuthService $tfaService): View|RedirectResponse
    {
        $userId = session('2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // If email 2FA and code not yet sent, send it now
        if ($user->two_factor_type === 'EMAIL' && !$user->two_factor_email_code) {
            $tfaService->sendEmailOtp($user);
        }

        return view('auth.2fa_challenge', compact('user'));
    }

    public function verifyChallenge(Request $request, TwoFactorAuthService $tfaService): RedirectResponse
    {
        $userId = session('2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $request->validate([
            'code' => ['required', 'string', 'min:6', 'max:8'],
        ]);

        $user = User::findOrFail($userId);
        $code = trim($request->input('code'));
        $isValid = false;

        if ($user->two_factor_type === 'EMAIL') {
            $isValid = $tfaService->verifyEmailOtp($user, $code);
        } elseif ($user->two_factor_type === 'AUTHENTICATOR' && $user->two_factor_secret) {
            $isValid = $tfaService->verifyTotp($user->two_factor_secret, $code);
        } else {
            // Fallback: test both
            $isValid = ($user->two_factor_secret && $tfaService->verifyTotp($user->two_factor_secret, $code))
                || $tfaService->verifyEmailOtp($user, $code);
        }

        if (!$isValid) {
            return back()->withErrors(['code' => 'The authentication code entered is invalid or expired. Please try again.']);
        }

        // Authentication successful
        $remember = (bool) session('2fa_pending_remember', false);
        session()->forget(['2fa_pending_user_id', '2fa_pending_remember']);
        session()->put('2fa_verified_user_id', $user->id);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $isAdminRole = $user->roleAssignments()->whereHas('role', fn ($q) => $q->whereIn('name', [
            'SUPER_ADMIN', 'MANAGER', 'COURSE_CREATOR', 'TEACHER', 'TEACHER_NON_EDITING',
            'CORP_MANAGER', 'INSTRUCTOR', 'TRAINER'
        ]))->exists();

        $intendedUrl = session()->get('url.intended');
        if (!$isAdminRole && $intendedUrl && str_contains($intendedUrl, '/admin')) {
            session()->forget('url.intended');
        }

        if (!$isAdminRole && !session()->has('url.intended') && \App\Models\ParentStudent::where('parent_id', $user->id)->exists()) {
            return redirect()->route('parent.dashboard');
        }

        if ($isAdminRole) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('my-learning'));
    }

    public function resendEmailOtp(TwoFactorAuthService $tfaService): RedirectResponse
    {
        $userId = session('2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);
        $tfaService->sendEmailOtp($user);

        return back()->with('status', 'A new 6-digit verification security code has been sent to ' . $user->email . '.');
    }

    /**
     * Show profile security settings view with 2FA configuration.
     */
    public function showSettings(TwoFactorAuthService $tfaService): View
    {
        $user = Auth::user();
        $secret = $user->two_factor_secret ?: $tfaService->generateSecret();
        $qrUrl = $tfaService->getQrCodeUrl($user->email, $secret);

        return view('profile.security', compact('user', 'secret', 'qrUrl'));
    }

    /**
     * Update user 2FA settings from profile/security panel.
     */
    public function updateSettings(Request $request, TwoFactorAuthService $tfaService): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'two_factor_type' => ['required', 'in:NONE,EMAIL,AUTHENTICATOR'],
            'verify_code' => ['nullable', 'string'],
        ]);

        $newType = $validated['two_factor_type'];

        if ($newType === 'NONE') {
            $user->update([
                'two_factor_type' => 'NONE',
                'two_factor_secret' => null,
                'two_factor_email_code' => null,
                'two_factor_expires_at' => null,
            ]);
            return back()->with('success', 'Two-Factor Authentication has been disabled.');
        }

        if ($newType === 'EMAIL') {
            $user->update([
                'two_factor_type' => 'EMAIL',
            ]);
            return back()->with('success', 'Two-Factor Authentication via Email successfully activated. Security codes will be sent to ' . $user->email);
        }

        if ($newType === 'AUTHENTICATOR') {
            // If secret is not set, generate new
            $secret = $request->input('secret') ?: ($user->two_factor_secret ?: $tfaService->generateSecret());
            $user->update([
                'two_factor_type' => 'AUTHENTICATOR',
                'two_factor_secret' => $secret,
            ]);
            return back()->with('success', 'Google Authenticator 2FA activated. Link your app using the QR code or secret key.');
        }

        return back();
    }
}
