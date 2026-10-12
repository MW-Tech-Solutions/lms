@extends('layouts.app')

@section('title', 'Sign In - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 450px; height: 450px; background: radial-gradient(circle, rgba(114, 62, 195, 0.15) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 380px; height: 380px; background: radial-gradient(circle, rgba(255, 77, 0, 0.1) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-4" style="z-index: 2;" x-data="{
        fillCredentials(email) {
            document.getElementById('loginEmail').value = email;
            document.getElementById('loginPassword').value = 'password123';
        }
    }">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="lms-card p-4 p-md-5 shadow-lg border-0" style="background: var(--lms-surface-card); border-radius: 28px;">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <span class="badge rounded-3 p-3 shadow-sm mb-3 d-inline-block text-white" 
                              style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1.5rem;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </span>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            {{ $site_settings['label_login'] ?? 'Sign In to Enterprise LMS' }}
                        </h2>
                        <p class="text-secondary small mb-0">
                            {{ $site_settings['login_subtext'] ?? 'Enter your corporate SSO or academic credentials to access your dashboard.' }}
                        </p>
                    </div>

                    <!-- Quick Demo Autofill Pills (Interactive convenience) -->
                    <div class="mb-4 p-2.5 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        <div class="text-muted small fw-bold mb-1.5" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Test Sign-In Accounts:
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" @click="fillCredentials('admin@enterpriselms.com')" class="badge bg-primary-subtle text-primary border-0 py-1 px-2 rounded small" title="Root Super Admin">
                                Super Admin
                            </button>
                            <button type="button" @click="fillCredentials('hr.manager@acmeglobal.com')" class="badge bg-success-subtle text-success border-0 py-1 px-2 rounded small" title="Corporate HR Manager">
                                HR Manager
                            </button>
                            <button type="button" @click="fillCredentials('employee@acmeglobal.com')" class="badge bg-warning-subtle text-warning border-0 py-1 px-2 rounded small" title="Corporate Employee">
                                Employee
                            </button>
                            <button type="button" @click="fillCredentials('student@enterpriselms.com')" class="badge bg-info-subtle text-info border-0 py-1 px-2 rounded small" title="Academic Public Student">
                                Student
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">
                                {{ $site_settings['label_email'] ?? 'Work / Student Email' }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="loginEmail" class="form-control border-start-0 ps-0" required autofocus value="{{ old('email') }}" placeholder="name@organization.com">
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">
                                    {{ $site_settings['label_password'] ?? 'Password' }} <span class="text-danger">*</span>
                                </label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="loginPassword" class="form-control border-start-0 ps-0" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                                <label class="form-check-label small text-secondary" for="rememberMe">
                                    {{ $site_settings['label_remember_me'] ?? 'Remember session' }}
                                </label>
                            </div>
                            <span class="text-muted small">Default pw: <code>password123</code></span>
                        </div>

                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>{{ $site_settings['label_sign_in_btn'] ?? 'Sign In to Console' }}</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <span>New to the academy?</span>
                        <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1">Create an Account</a>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="d-flex justify-content-center align-items-center gap-3 mt-4 text-muted small">
                    <span><i class="bi bi-shield-check text-success me-1"></i> SOC2 Type II Audited</span>
                    <span>&bull;</span>
                    <span><i class="bi bi-lock-fill text-primary me-1"></i> 256-Bit TLS Encrypted</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
