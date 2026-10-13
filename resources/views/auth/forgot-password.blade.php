@extends('layouts.app')

@section('title', 'Forgot Password - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Recover access to your enterprise learning and academic scholar account.')

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 420px; height: 420px; background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-3" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7 col-xl-6">

                <!-- Main Card -->
                <div class="lms-card p-4 p-md-5 shadow-lg border position-relative" 
                     style="background: var(--lms-surface-card); border-radius: 28px; border-color: var(--lms-border) !important;">
                    
                    <!-- Icon / Header -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-4 shadow-sm mb-3" 
                             style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--lms-accent, #723EC3), #2563EB); color: #FFF; font-size: 1.75rem;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                            Reset Password
                        </h2>
                        <p class="text-secondary small mb-0 px-md-3">
                            Enter the email associated with your LMS account and we'll generate a secure identity link to reset your credentials.
                        </p>
                    </div>

                    <!-- Status Alerts -->
                    @if (session('status'))
                        <div class="alert alert-success border-0 rounded-4 small p-3 mb-4 d-flex flex-column gap-2 shadow-sm" style="background: rgba(16, 185, 129, 0.12); color: #065F46;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                <span class="fw-semibold">{{ session('status') }}</span>
                            </div>

                            @if(session('dev_reset_url'))
                                <div class="mt-2 p-2.5 rounded-3 border border-success border-opacity-25 bg-white bg-opacity-75">
                                    <div class="fw-bold text-dark small mb-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Dev/Demo Mode Action:
                                    </div>
                                    <p class="mb-2 text-muted" style="font-size: 0.76rem;">
                                        In local demo mode without an active SMTP server, you can directly open your generated reset link below:
                                    </p>
                                    <a href="{{ session('dev_reset_url') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 font-mono text-decoration-none" style="font-size: 0.78rem;">
                                        Open Password Reset Form &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 small p-3 mb-4" style="background: rgba(239, 68, 68, 0.12); color: #991B1B;">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Request Notice</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary mb-1.5" for="resetEmail">
                                Account Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       id="resetEmail" 
                                       class="form-control border-0 bg-transparent ps-2 fs-6" 
                                       required 
                                       autofocus 
                                       value="{{ old('email') }}" 
                                       placeholder="name@organization.com">
                            </div>
                            <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                                Works for Super Admins, Instructors, Corporate Managers, and Students.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>Send Recovery Link</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <span>Remembered your password?</span>
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">
                            Back to Sign In
                        </a>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="d-flex justify-content-center align-items-center gap-3 mt-4 text-muted small">
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-shield-check text-success"></i> SOC2 Type II
                    </span>
                    <span>&bull;</span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-lock-fill text-primary"></i> 256-Bit TLS
                    </span>
                    <span>&bull;</span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-clock-history text-info"></i> 60-Min Expiry
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
