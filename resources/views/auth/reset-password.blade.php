@extends('layouts.app')

@section('title', 'Set New Password - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Set a new secure password for your LMS account.')

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);"
     x-data="{
         showPassword: false,
         showConfirm: false,
         password: '',
         confirmPassword: '',
         get isValidLength() { return this.password.length >= 8; },
         get isMatching() { return this.password && this.password === this.confirmPassword; }
     }">
    
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
                             style="width: 64px; height: 64px; background: linear-gradient(135deg, #10B981, #2563EB); color: #FFF; font-size: 1.75rem;">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                            Set New Password
                        </h2>
                        <p class="text-secondary small mb-0 px-md-3">
                            Create a strong, new security password to regain instant access to your learning credentials.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 small p-3 mb-4" style="background: rgba(239, 68, 68, 0.12); color: #991B1B;">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Update Notice</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Reset Password Form -->
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary mb-1.5" for="resetUserEmail">
                                Account Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       id="resetUserEmail" 
                                       class="form-control border-0 bg-transparent ps-2 fs-6" 
                                       required 
                                       value="{{ old('email', $email) }}" 
                                       placeholder="name@organization.com">
                            </div>
                        </div>

                        <!-- New Password Input -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary mb-1.5" for="newPassword">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="newPassword" 
                                       x-model="password"
                                       class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                       required 
                                       placeholder="Enter at least 8 characters">
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                        title="Toggle password visibility">
                                    <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary mb-1.5" for="confirmPassword">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-check2-circle"></i>
                                </span>
                                <input :type="showConfirm ? 'text' : 'password'" 
                                       name="password_confirmation" 
                                       id="confirmPassword" 
                                       x-model="confirmPassword"
                                       class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                       required 
                                       placeholder="Re-enter password">
                                <button type="button" 
                                        @click="showConfirm = !showConfirm"
                                        class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                        title="Toggle password visibility">
                                    <i :class="showConfirm ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                </button>
                            </div>

                            <!-- Live Password Requirements Checklist -->
                            <div class="d-flex flex-wrap gap-3 mt-2 px-1">
                                <div class="small d-flex align-items-center gap-1.5" :class="isValidLength ? 'text-success' : 'text-muted'">
                                    <i :class="isValidLength ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i>
                                    <span style="font-size: 0.76rem;">At least 8 characters</span>
                                </div>
                                <div class="small d-flex align-items-center gap-1.5" :class="isMatching ? 'text-success' : 'text-muted'">
                                    <i :class="isMatching ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i>
                                    <span style="font-size: 0.76rem;">Passwords match</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>Update Password & Sign In</span>
                            <span class="btn-icon"><i class="bi bi-check-lg ms-2"></i></span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
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
                        <i class="bi bi-shield-lock-fill text-info"></i> End-to-End Encryption
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
