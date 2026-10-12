@extends('layouts.app')

@section('title', 'Create Account - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Enroll as a corporate workforce professional or academic student scholar.')

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 420px; height: 420px; background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-3" style="z-index: 2;" 
         x-data="{ 
             audience: '{{ old('user_type', 'EMPLOYEE') }}',
             showPassword: false,
             showPasswordConfirm: false
         }">
        
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">

                <!-- Main Register Card -->
                <div class="lms-card p-4 p-md-5 shadow-lg border position-relative" 
                     style="background: var(--lms-surface-card); border-radius: 28px; border-color: var(--lms-border) !important;">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-4 shadow-sm mb-3" 
                             style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); color: #FFFFFF;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                            {{ $site_settings['label_register'] ?? 'Create Your Account' }}
                        </h2>
                        <p class="text-secondary small mb-0">
                            Select your profile to automatically configure your learning track and compliance pathways.
                        </p>
                    </div>

                    <!-- 3-Way Persona Selector -->
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <button type="button" 
                                    class="btn w-100 p-2.5 text-start border rounded-3 d-flex flex-column transition-all shadow-sm h-100"
                                    :class="audience === 'EMPLOYEE' ? 'border-primary bg-primary bg-opacity-10 shadow' : 'border-0 bg-body text-secondary'"
                                    @click="audience = 'EMPLOYEE'">
                                <div class="d-flex align-items-center gap-1.5 mb-1">
                                    <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 24px; height: 24px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                        <i class="bi bi-building" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Corporate</span>
                                </div>
                                <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                    Compliance & audits
                                </span>
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" 
                                    class="btn w-100 p-2.5 text-start border rounded-3 d-flex flex-column transition-all shadow-sm h-100"
                                    :class="audience === 'STUDENT' ? 'border-primary bg-primary bg-opacity-10 shadow' : 'border-0 bg-body text-secondary'"
                                    @click="audience = 'STUDENT'">
                                <div class="d-flex align-items-center gap-1.5 mb-1">
                                    <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 24px; height: 24px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                        <i class="bi bi-mortarboard" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Student</span>
                                </div>
                                <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                    Degrees & cohorts
                                </span>
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" 
                                    class="btn w-100 p-2.5 text-start border rounded-3 d-flex flex-column transition-all shadow-sm h-100"
                                    :class="audience === 'HYBRID' ? 'border-primary bg-primary bg-opacity-10 shadow' : 'border-0 bg-body text-secondary'"
                                    @click="audience = 'HYBRID'">
                                <div class="d-flex align-items-center gap-1.5 mb-1">
                                    <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 24px; height: 24px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                        <i class="bi bi-journal-code" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Faculty</span>
                                </div>
                                <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                    Teaching & grading
                                </span>
                            </button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 small p-3 mb-3">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Registration Error</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="user_type" :value="audience">

                        <!-- Name & Email -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary mb-1.5">
                                    Full Legal Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6" 
                                           required 
                                           value="{{ old('name') }}" 
                                           placeholder="Dr. Jane Smith">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary mb-1.5">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6" 
                                           required 
                                           value="{{ old('email') }}" 
                                           placeholder="jane@organization.com">
                                </div>
                            </div>
                        </div>

                        <!-- 1. Corporate Specific Fields -->
                        <div x-show="audience === 'EMPLOYEE'" x-transition class="p-3.5 rounded-4 mb-3 border" style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                            <div class="d-flex align-items-center gap-2 mb-2.5">
                                <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 28px; height: 28px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                    <i class="bi bi-shield-check" style="font-size: 0.88rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.85rem;">Organizational Context</div>
                                    <div class="text-secondary" style="font-size: 0.68rem;">Verified enterprise affiliation for compliance &amp; certification audits</div>
                                </div>
                            </div>
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Corporate Employee ID</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-card-text"></i>
                                        </span>
                                        <input type="text" 
                                               name="employee_id" 
                                               autocomplete="off"
                                               class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                               placeholder="EMP-94021" 
                                               value="{{ old('employee_id') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Assigned Department</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <select name="department_id" class="form-select border-0 bg-transparent ps-2 fs-6">
                                            <option value="">General Corporate Workforce</option>
                                            @foreach(\App\Models\Department::all() as $dept)
                                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Academic Student Specific Fields -->
                        <div x-show="audience === 'STUDENT'" x-transition class="p-3.5 rounded-4 mb-3 border" style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                            <div class="d-flex align-items-center gap-2 mb-2.5">
                                <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 28px; height: 28px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                    <i class="bi bi-mortarboard" style="font-size: 0.88rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.85rem;">Academic Scholar Context</div>
                                    <div class="text-secondary" style="font-size: 0.68rem;">Higher education accreditation &amp; degree program track</div>
                                </div>
                            </div>
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Student / Scholar ID</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <input type="text" 
                                               name="employee_id" 
                                               autocomplete="off"
                                               class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                               placeholder="STU-2026-99" 
                                               value="{{ old('employee_id') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Academic Program / School</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-book"></i>
                                        </span>
                                        <select name="department_id" class="form-select border-0 bg-transparent ps-2 fs-6">
                                            <option value="">Graduate &amp; Undergraduate Studies</option>
                                            @foreach(\App\Models\Department::all() as $dept)
                                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Faculty / Educator Specific Fields -->
                        <div x-show="audience === 'HYBRID'" x-transition class="p-3.5 rounded-4 mb-3 border" style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                            <div class="d-flex align-items-center gap-2 mb-2.5">
                                <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 28px; height: 28px; background: rgba(var(--tj-color-theme-primary-rgb, 114, 62, 195), 0.12); color: var(--lms-accent);">
                                    <i class="bi bi-journal-code" style="font-size: 0.88rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.85rem;">Faculty &amp; Educator Context</div>
                                    <div class="text-secondary" style="font-size: 0.68rem;">Curriculum authoring, grading authority, and proctoring access</div>
                                </div>
                            </div>
                            <div class="row g-2.5">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Faculty Staff ID</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-person-vcard"></i>
                                        </span>
                                        <input type="text" 
                                               name="employee_id" 
                                               autocomplete="off"
                                               class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                               placeholder="FAC-5001" 
                                               value="{{ old('employee_id') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Assigned Academic Faculty</label>
                                    <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                            <i class="bi bi-building-check"></i>
                                        </span>
                                        <select name="department_id" class="form-select border-0 bg-transparent ps-2 fs-6">
                                            <option value="">General Academic Faculty</option>
                                            @foreach(\App\Models\Department::all() as $dept)
                                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passwords -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary mb-1.5">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                           required 
                                           placeholder="Min 8 characters">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                            title="Toggle password">
                                        <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary mb-1.5">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" style="background: var(--lms-bg-body);">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input :type="showPasswordConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                           required 
                                           placeholder="Repeat password">
                                    <button type="button" 
                                            @click="showPasswordConfirm = !showPasswordConfirm"
                                            class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                            title="Toggle password">
                                        <i :class="showPasswordConfirm ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>Create Account &amp; Enter Academy</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <span>Already registered with your organization?</span>
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">
                            Sign In
                        </a>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="d-flex justify-content-center align-items-center gap-3 mt-4 text-muted small">
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-shield-check text-success"></i> ISO 27001 ISMS
                    </span>
                    <span>&bull;</span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-lock-fill text-primary"></i> 256-Bit TLS
                    </span>
                    <span>&bull;</span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-award text-warning"></i> FERPA &amp; GDPR Compliant
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
