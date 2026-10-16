@extends('layouts.app')

@section('title', 'Two-Factor Authentication Challenge')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">

            <div class="lms-card p-4 p-md-5 shadow-lg border rounded-4 position-relative" style="background: var(--lms-surface-card);">
                
                <div class="text-center mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                         style="width: 56px; height: 56px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>

                    <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        Two-Factor Authentication
                    </h3>

                    @if($user->two_factor_type === 'EMAIL')
                        <p class="text-secondary small mb-0">
                            A secure 6-digit verification code has been dispatched to your email address:
                            <br><strong class="font-mono text-body">{{ $user->email }}</strong>
                        </p>
                    @else
                        <p class="text-secondary small mb-0">
                            Please open your <strong>Google Authenticator</strong> or TOTP app on your mobile device and key in the active 6-digit code for this account.
                        </p>
                    @endif
                </div>

                @if(session('status'))
                    <div class="alert alert-success border-0 rounded-3 small p-3 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small p-3 mb-3">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Verification Failed</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.2fa.verify') }}" class="mb-3">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary font-mono text-center d-block" style="letter-spacing: 0.8px;">
                            Enter 6-Digit Security Code
                        </label>
                        <input type="text" 
                               name="code" 
                               class="form-control form-control-lg text-center font-mono fw-bold rounded-3" 
                               style="letter-spacing: 8px; font-size: 1.6rem;" 
                               maxlength="8" 
                               autocomplete="one-time-code" 
                               placeholder="------" 
                               required 
                               autofocus>
                        <div class="form-text text-center small text-muted mt-1">
                            Codes cycle every 30 seconds (Authenticator) or 10 minutes (Email).
                        </div>
                    </div>

                    <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                        <i class="bi bi-check-circle me-1"></i>
                        <span>Authenticate &amp; Continue</span>
                    </button>
                </form>

                @if($user->two_factor_type === 'EMAIL')
                    <div class="text-center pt-2 border-top">
                        <form method="POST" action="{{ route('auth.2fa.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-secondary">
                                <i class="bi bi-arrow-repeat me-1"></i> Didn't receive the email? Resend code
                            </button>
                        </form>
                    </div>
                @endif

                <div class="text-center mt-3 pt-2 small text-muted">
                    <a href="{{ route('login') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Return to login screen
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
