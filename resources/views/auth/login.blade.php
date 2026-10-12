@extends('layouts.app')

@section('title', 'Sign In - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Sign in to your enterprise learning portal to access corporate compliance certifications or academic degree cohorts.')

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 420px; height: 420px; background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-3" style="z-index: 2;" 
         x-data="{
             selectedRole: 'admin',
             showPassword: false,
             accounts: {
                 'admin': {
                     name: 'Alexander Vance',
                     role: 'Super Administrator',
                     email: 'admin@enterpriselms.com',
                     badge: 'Admin Console',
                     icon: 'bi-shield-shaded',
                     color: '#723EC3'
                 },
                 'manager': {
                     name: 'Sarah Connor',
                     role: 'Corporate HR Manager',
                     email: 'hr.manager@acmeglobal.com',
                     badge: 'Compliance & Audits',
                     icon: 'bi-briefcase-fill',
                     color: '#059669'
                 },
                 'instructor': {
                     name: 'Dr. Robert Oppenheimer',
                     role: 'Curriculum & Faculty Lead',
                     email: 'instructor@enterpriselms.com',
                     badge: 'Curriculum & Grading',
                     icon: 'bi-journal-code',
                     color: '#D97706'
                 },
                 'employee': {
                     name: 'John Doe',
                     role: 'Corporate Employee',
                     email: 'employee@acmeglobal.com',
                     badge: 'Workforce Learner',
                     icon: 'bi-person-badge',
                     color: '#2563EB'
                 },
                 'student': {
                     name: 'Emma Watson',
                     role: 'Academic Student',
                     email: 'student@enterpriselms.com',
                     badge: 'Degree Scholar',
                     icon: 'bi-mortarboard-fill',
                     color: '#0D9488'
                 }
             },
             applyAccount(key) {
                 this.selectedRole = key;
                 const acc = this.accounts[key];
                 document.getElementById('loginEmail').value = acc.email;
                 document.getElementById('loginPassword').value = 'password123';
             }
         }">
        
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-5">

                <!-- Main Login Card -->
                <div class="lms-card p-4 p-md-5 shadow-lg border position-relative" 
                     style="background: var(--lms-surface-card); border-radius: 28px; border-color: var(--lms-border) !important;">
                    
                    <!-- Brand / Welcome Header -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-4 shadow-sm mb-3" 
                             style="background: linear-gradient(135deg, var(--lms-accent), #2563EB); color: #FFFFFF;">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                            {{ $site_settings['label_login'] ?? 'Welcome Back' }}
                        </h2>
                        <p class="text-secondary small mb-0">
                            {{ $site_settings['login_subtext'] ?? 'Sign in to access your organization dashboard or personal curriculum.' }}
                        </p>
                    </div>

                    <!-- Quick 1-Click Demo Personas -->
                    <div class="mb-4 p-3 rounded-4 border" style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2.5">
                            <span class="small fw-bold text-uppercase text-secondary font-mono" style="font-size: 0.68rem; letter-spacing: 0.6px;">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Test Sign-In (1-Click)
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary font-mono small" style="font-size: 0.62rem;">
                                Pass: password123
                            </span>
                        </div>

                        <!-- Persona Buttons Grid -->
                        <div class="d-grid gap-1.5" style="grid-template-columns: repeat(2, 1fr);">
                            <button type="button" 
                                    @click="applyAccount('admin')"
                                    class="btn btn-sm text-start p-2 rounded-3 border transition-all d-flex align-items-center gap-2"
                                    :class="selectedRole === 'admin' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body'">
                                <i class="bi bi-shield-shaded text-purple fs-6"></i>
                                <div class="text-truncate">
                                    <div class="fw-bold text-body small text-truncate" style="font-size: 0.76rem;">Super Admin</div>
                                    <div class="text-muted text-truncate font-mono" style="font-size: 0.64rem;">admin@enterpriselms</div>
                                </div>
                            </button>

                            <button type="button" 
                                    @click="applyAccount('manager')"
                                    class="btn btn-sm text-start p-2 rounded-3 border transition-all d-flex align-items-center gap-2"
                                    :class="selectedRole === 'manager' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body'">
                                <i class="bi bi-briefcase-fill text-success fs-6"></i>
                                <div class="text-truncate">
                                    <div class="fw-bold text-body small text-truncate" style="font-size: 0.76rem;">HR Manager</div>
                                    <div class="text-muted text-truncate font-mono" style="font-size: 0.64rem;">hr.manager@acme</div>
                                </div>
                            </button>

                            <button type="button" 
                                    @click="applyAccount('instructor')"
                                    class="btn btn-sm text-start p-2 rounded-3 border transition-all d-flex align-items-center gap-2"
                                    :class="selectedRole === 'instructor' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body'">
                                <i class="bi bi-journal-code text-warning fs-6"></i>
                                <div class="text-truncate">
                                    <div class="fw-bold text-body small text-truncate" style="font-size: 0.76rem;">Instructor / Lead</div>
                                    <div class="text-muted text-truncate font-mono" style="font-size: 0.64rem;">instructor@enterpriselms</div>
                                </div>
                            </button>

                            <button type="button" 
                                    @click="applyAccount('employee')"
                                    class="btn btn-sm text-start p-2 rounded-3 border transition-all d-flex align-items-center gap-2"
                                    :class="selectedRole === 'employee' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body'">
                                <i class="bi bi-person-badge text-primary fs-6"></i>
                                <div class="text-truncate">
                                    <div class="fw-bold text-body small text-truncate" style="font-size: 0.76rem;">Employee</div>
                                    <div class="text-muted text-truncate font-mono" style="font-size: 0.64rem;">employee@acme</div>
                                </div>
                            </button>
                        </div>

                        <!-- Student Full-Width Pill -->
                        <div class="mt-1.5">
                            <button type="button" 
                                    @click="applyAccount('student')"
                                    class="btn btn-sm text-start p-2 rounded-3 border transition-all w-100 d-flex align-items-center justify-content-between"
                                    :class="selectedRole === 'student' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body'">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-mortarboard-fill text-info fs-6"></i>
                                    <div>
                                        <div class="fw-bold text-body small" style="font-size: 0.76rem;">Academic Student / Scholar</div>
                                        <div class="text-muted font-mono" style="font-size: 0.64rem;">student@enterpriselms.com &bull; student@university.edu</div>
                                    </div>
                                </div>
                                <span class="badge bg-info-subtle text-info rounded-pill small" style="font-size: 0.62rem;">Learner View</span>
                            </button>
                        </div>
                    </div>

                    <!-- Alert messages -->
                    @if (session('status'))
                        <div class="alert alert-success border-0 rounded-4 small p-3 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 small p-3 mb-3">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Authentication Failed</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Sign In Form -->
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary mb-1.5" for="loginEmail">
                                Work or Academic Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       id="loginEmail" 
                                       class="form-control border-0 bg-transparent ps-2 fs-6" 
                                       required 
                                       autofocus 
                                       value="{{ old('email', 'admin@enterpriselms.com') }}" 
                                       placeholder="name@organization.com">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <label class="form-label small fw-semibold text-secondary mb-0" for="loginPassword">
                                    Security Password <span class="text-danger">*</span>
                                </label>
                            </div>
                            <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="loginPassword" 
                                       class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                       required 
                                       value="password123"
                                       placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                        title="Toggle password visibility">
                                    <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember & Meta -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberMe" checked>
                                <label class="form-check-label small text-secondary" for="rememberMe">
                                    Remember session
                                </label>
                            </div>
                            <span class="text-muted small font-mono" style="font-size: 0.72rem;">
                                Demo: <code>password123</code>
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>{{ $site_settings['label_sign_in_btn'] ?? 'Sign In to Portal' }}</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <span>New learner or corporate employee?</span>
                        <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1">
                            Create Account
                        </a>
                    </div>
                </div>

                <!-- Trust Badges & Compliance Signals -->
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
                        <i class="bi bi-universal-access text-info"></i> WCAG 2.1 AA
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
