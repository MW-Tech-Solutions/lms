@extends('layouts.app')

@section('title', 'Create Account - ' . ($site_settings['brand_name'] ?? 'AllThingsCambridge'))
@section('meta_description', 'Enroll as an academic student scholar, corporate professional, or verified Cambridge faculty mentor.')

@php
    $cambridgeLevels = \App\Models\Department::where('name', 'like', 'Cambridge%')->orderBy('id')->get();
    if ($cambridgeLevels->isEmpty()) {
        $cambridgeLevels = \App\Models\Department::all();
    }
    $corporateDepts = \App\Models\Department::where('name', 'not like', 'Cambridge%')->orderBy('id')->get();
    if ($corporateDepts->isEmpty()) {
        $corporateDepts = \App\Models\Department::all();
    }
@endphp

@section('content')
<div class="py-4 py-lg-5 position-relative overflow-hidden" style="background: var(--lms-bg-body); min-height: calc(100vh - 160px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 500px; height: 500px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 450px; height: 450px; background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-2" style="z-index: 2;" 
         x-data="{ 
             audience: '{{ old('user_type', 'STUDENT') }}',
             showPassword: false,
             showPasswordConfirm: false
         }">
        
        <!-- Main Split-Screen Container Card -->
        <div class="card border-0 shadow-2xl rounded-5 overflow-hidden mx-auto" 
             style="max-width: 1160px; background: var(--lms-surface-card); border: 1px solid var(--lms-border) !important;">
            
            <div class="row g-0 align-items-stretch">
                
                <!-- LEFT COLUMN: Academic Prestige & Brand Showcase Panel -->
                <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative overflow-hidden" 
                     style="background: linear-gradient(145deg, #0A0D18 0%, #161D36 50%, #0F1322 100%);">
                    
                    <!-- Ambient Glow Effect inside Left Column -->
                    <div class="position-absolute top-0 end-0 rounded-circle" 
                         style="width: 320px; height: 320px; background: radial-gradient(circle, rgba(114, 62, 195, 0.25) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>
                    <div class="position-absolute bottom-0 start-0 rounded-circle" 
                         style="width: 280px; height: 280px; background: radial-gradient(circle, rgba(2, 132, 199, 0.2) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>

                    <!-- Top: Brand Header -->
                    <div class="position-relative" style="z-index: 2;">
                        <a href="{{ url('/') }}" class="d-inline-flex align-items-center text-decoration-none text-white mb-4" style="gap: 12px;">
                            @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                                <img src="{{ $site_settings['brand_logo_url'] }}" alt="Logo" height="42" class="rounded-2 shadow-sm me-2" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                            @elseif(file_exists(public_path('images/site_logo.png')))
                                <img src="{{ asset('images/site_logo.png') }}" alt="Logo" height="42" class="rounded-2 shadow-sm me-2">
                            @else
                                <span class="badge rounded-3 p-2 shadow-sm me-2" style="background: linear-gradient(135deg, var(--lms-accent, #723EC3), #0284C7); font-size: 1.1rem; color: #FFF;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </span>
                            @endif
                            <div class="ms-1">
                                <span class="fw-extrabold fs-5 d-block text-white" style="font-family: var(--lms-font-heading); letter-spacing: -0.5px; line-height: 1.1;">
                                    {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
                                </span>
                                <span class="text-white-50" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; margin-top: 2px; display: block;">
                                    Cambridge International Platform
                                </span>
                            </div>
                        </a>

                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3 border shadow-xs" 
                             style="background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.15) !important;">
                            <span class="badge rounded-pill px-2 py-0.5 text-white fw-bold" style="background: var(--lms-accent, #723EC3); font-size: 0.68rem;">
                                ENROLLMENT 2026
                            </span>
                            <span class="small text-white-50" style="font-size: 0.75rem;">Verified Syllabus Cohorts</span>
                        </div>

                        <h2 class="display-6 fw-extrabold text-white mb-3" style="font-family: var(--lms-font-heading); letter-spacing: -0.8px; line-height: 1.2;">
                            Empowering Scholars &amp; Certified Faculty.
                        </h2>
                        
                        <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                            Select your academic or professional pathway to automatically tailor your Cambridge exam modules, topical past paper packs, and diagnostic mastery tracking.
                        </p>

                        <!-- Academic Feature Checkpoints -->
                        <div class="vstack gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 24px; height: 24px; background: rgba(34, 197, 94, 0.15);">
                                    <i class="bi bi-check-lg fw-bold" style="font-size: 0.85rem;"></i>
                                </div>
                                <span class="small text-white-50">Primary Checkpoint &bull; Lower Secondary &bull; IGCSE &bull; AS &amp; A Level</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 24px; height: 24px; background: rgba(34, 197, 94, 0.15);">
                                    <i class="bi bi-check-lg fw-bold" style="font-size: 0.85rem;"></i>
                                </div>
                                <span class="small text-white-50">Integrated Esther Math Raw Score Matrix &amp; Printouts</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 24px; height: 24px; background: rgba(34, 197, 94, 0.15);">
                                    <i class="bi bi-check-lg fw-bold" style="font-size: 0.85rem;"></i>
                                </div>
                                <span class="small text-white-50">Secure Family &amp; Parent Guardian Connection</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 24px; height: 24px; background: rgba(34, 197, 94, 0.15);">
                                    <i class="bi bi-check-lg fw-bold" style="font-size: 0.85rem;"></i>
                                </div>
                                <span class="small text-white-50">Verified Examiner KYC &amp; Teaching Methodology Accreditation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Security & Accreditation Footer -->
                    <div class="position-relative pt-3 border-top border-white border-opacity-10" style="z-index: 2;">
                        <div class="d-flex justify-content-between align-items-center text-white-50 small" style="font-size: 0.72rem;">
                            <span><i class="bi bi-shield-check text-success me-1"></i> CIE Syllabus Compliant</span>
                            <span>256-Bit TLS Encrypted</span>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Interactive Registration Form Panel -->
                <div class="col-lg-7 p-4 p-sm-5 d-flex flex-column justify-content-between">
                    
                    <div>
                        <!-- Header & Mobile Logo -->
                        <div class="d-flex d-lg-none align-items-center mb-3" style="gap: 12px;">
                            @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                                <img src="{{ $site_settings['brand_logo_url'] }}" alt="Logo" height="34" class="rounded-2 me-2" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                            @elseif(file_exists(public_path('images/site_logo.png')))
                                <img src="{{ asset('images/site_logo.png') }}" alt="Logo" height="34" class="rounded-2 me-2">
                            @else
                                <span class="badge rounded-2 p-1.5 shadow-sm me-2" style="background: var(--lms-accent, #723EC3); color: #FFF;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </span>
                            @endif
                            <span class="fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
                            </span>
                        </div>

                        <h2 class="display-6 fw-extrabold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.6px;">
                            Create Your Account
                        </h2>
                        <p class="text-secondary small mb-4">
                            Select your academic or professional profile to configure your personalized learning track.
                        </p>

                        <!-- 3-Way Persona Selector Tabs -->
                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <button type="button" 
                                        class="btn w-100 p-3 text-start border rounded-3 d-flex flex-column transition-all shadow-xs h-100"
                                        style="padding: 0.85rem !important;"
                                        :class="audience === 'STUDENT' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body text-secondary'"
                                        @click="audience = 'STUDENT'">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 24px; height: 24px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent, #723EC3);">
                                            <i class="bi bi-mortarboard-fill" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Student</span>
                                    </div>
                                    <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                        Cambridge Scholar
                                    </span>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" 
                                        class="btn w-100 p-3 text-start border rounded-3 d-flex flex-column transition-all shadow-xs h-100"
                                        style="padding: 0.85rem !important;"
                                        :class="audience === 'HYBRID' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body text-secondary'"
                                        @click="audience = 'HYBRID'">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 24px; height: 24px; background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                                            <i class="bi bi-journal-code" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Faculty</span>
                                    </div>
                                    <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                        Mentor &amp; Teacher KYC
                                    </span>
                                </button>
                            </div>
                            <div class="col-4">
                                <button type="button" 
                                        class="btn w-100 p-3 text-start border rounded-3 d-flex flex-column transition-all shadow-xs h-100"
                                        style="padding: 0.85rem !important;"
                                        :class="audience === 'EMPLOYEE' ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-0 bg-body text-secondary'"
                                        @click="audience = 'EMPLOYEE'">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 24px; height: 24px; background: rgba(16, 185, 129, 0.12); color: #10B981;">
                                            <i class="bi bi-building" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <span class="fw-bold text-truncate" style="font-size: 0.82rem;">Corporate</span>
                                    </div>
                                    <span class="small text-muted text-truncate" style="font-size: 0.68rem; line-height: 1.2;">
                                        Compliance &amp; Audits
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Error Alerts -->
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 small p-3 mb-4 shadow-sm">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Registration Error</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Main Registration Form -->
                        <form method="POST" action="{{ route('register') }}" class="vstack gap-3 mb-4">
                            @csrf
                            <input type="hidden" name="user_type" :value="audience">

                            <!-- Name & Email (2-Column Grid) -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1.5">
                                        Full Legal Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                         style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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
                                    <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                         style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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

                            <!-- 1. Academic Student Specific Fields -->
                            <div x-show="audience === 'STUDENT'" x-transition class="rounded-4 border shadow-xs" 
                                 style="padding: 1.5rem !important; background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 32px; height: 32px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent, #723EC3);">
                                        <i class="bi bi-mortarboard" style="font-size: 0.95rem;"></i>
                                    </div>
                                    <div class="ms-1">
                                        <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.88rem;">Academic Scholar Context</div>
                                        <div class="text-secondary" style="font-size: 0.72rem; margin-top: 1px;">Cambridge Level syllabus enrollment &amp; Esther Math tracking</div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small fw-semibold text-secondary mb-0">Student / Scholar ID</label>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Auto-Generated</span>
                                        </div>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-person-badge"></i>
                                            </span>
                                            <input type="text" 
                                                   name="employee_id" 
                                                   autocomplete="off"
                                                   readonly
                                                   :disabled="audience !== 'STUDENT'"
                                                   class="form-control border-0 bg-transparent ps-2 fs-6 font-mono fw-semibold text-primary" 
                                                   placeholder="Student / Scholar ID" 
                                                   value="{{ old('employee_id', $site_settings['generated_student_id'] ?? 'ATC-STU-2026-101') }}"
                                                   style="cursor: default;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary mb-1.5">Enrolled Cambridge Level</label>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-mortarboard"></i>
                                            </span>
                                            <select name="department_id" :disabled="audience !== 'STUDENT'" class="form-select border-0 bg-transparent ps-2 fs-6">
                                                <option value="">Select Cambridge Level...</option>
                                                @foreach($cambridgeLevels as $level)
                                                    <option value="{{ $level->id }}" {{ old('department_id') == $level->id ? 'selected' : '' }}>
                                                        {{ $level->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Faculty / Educator Specific Fields (With Full KYC) -->
                            <div x-show="audience === 'HYBRID'" x-transition class="rounded-4 border shadow-xs" 
                                 style="padding: 1.5rem !important; background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 32px; height: 32px; background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                                        <i class="bi bi-journal-code" style="font-size: 0.95rem;"></i>
                                    </div>
                                    <div class="ms-1">
                                        <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.88rem;">Faculty &amp; Mentor KYC Verification</div>
                                        <div class="text-secondary" style="font-size: 0.72rem; margin-top: 1px;">Syllabus moderation, grading authority, and public profile spotlight</div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small fw-semibold text-secondary mb-0">Faculty Staff ID</label>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Auto-Generated</span>
                                        </div>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-person-vcard"></i>
                                            </span>
                                            <input type="text" 
                                                   name="employee_id" 
                                                   autocomplete="off"
                                                   readonly
                                                   :disabled="audience !== 'HYBRID'"
                                                   class="form-control border-0 bg-transparent ps-2 fs-6 font-mono fw-semibold text-primary" 
                                                   placeholder="Faculty Staff ID" 
                                                   value="{{ old('employee_id', $site_settings['generated_faculty_id'] ?? 'ATC-FAC-5001') }}"
                                                   style="cursor: default;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary mb-1.5">Assigned Cambridge Level / Faculty</label>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-building-check"></i>
                                            </span>
                                            <select name="department_id" :disabled="audience !== 'HYBRID'" class="form-select border-0 bg-transparent ps-2 fs-6">
                                                <option value="">Select Cambridge Level / Faculty...</option>
                                                @foreach($cambridgeLevels as $level)
                                                    <option value="{{ $level->id }}" {{ old('department_id') == $level->id ? 'selected' : '' }}>
                                                        {{ $level->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1.5">
                                            <i class="bi bi-person-lines-fill text-primary me-1"></i> All About Instructor (KYC) <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="about_instructor" 
                                                  class="form-control rounded-3 p-3" 
                                                  rows="3" 
                                                  :disabled="audience !== 'HYBRID'"
                                                  placeholder="Provide your academic pedigree, Cambridge subject specialization, and teaching experience...">{{ old('about_instructor') }}</textarea>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1.5">
                                            <i class="bi bi-lightbulb-fill text-warning me-1"></i> Teaching Methodology (KYC) <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="teaching_methodology" 
                                                  class="form-control rounded-3 p-3" 
                                                  rows="3" 
                                                  :disabled="audience !== 'HYBRID'"
                                                  placeholder="Outline your pedagogical approach, past-paper dissections, diagnostic tests, and student mastery plans...">{{ old('teaching_methodology') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Corporate Specific Fields -->
                            <div x-show="audience === 'EMPLOYEE'" x-transition class="rounded-4 border shadow-xs" 
                                 style="padding: 1.5rem !important; background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10B981;">
                                        <i class="bi bi-shield-check" style="font-size: 0.95rem;"></i>
                                    </div>
                                    <div class="ms-1">
                                        <div class="fw-bold small" style="color: var(--lms-text-primary); font-size: 0.88rem;">Organizational Context</div>
                                        <div class="text-secondary" style="font-size: 0.72rem; margin-top: 1px;">Verified enterprise affiliation for compliance &amp; certification audits</div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <label class="form-label small fw-semibold text-secondary mb-0">Corporate Employee ID</label>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Auto-Generated</span>
                                        </div>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-card-text"></i>
                                            </span>
                                            <input type="text" 
                                                   name="employee_id" 
                                                   autocomplete="off"
                                                   readonly
                                                   :disabled="audience !== 'EMPLOYEE'"
                                                   class="form-control border-0 bg-transparent ps-2 fs-6 font-mono fw-semibold text-primary" 
                                                   placeholder="Corporate Employee ID" 
                                                   value="{{ old('employee_id', $site_settings['generated_corporate_id'] ?? 'ATC-EMP-94021') }}"
                                                   style="cursor: default;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary mb-1.5">Assigned Corporate Department</label>
                                        <div class="input-group rounded-3 border overflow-hidden" style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                                <i class="bi bi-building"></i>
                                            </span>
                                            <select name="department_id" :disabled="audience !== 'EMPLOYEE'" class="form-select border-0 bg-transparent ps-2 fs-6">
                                                <option value="">Select Corporate Department...</option>
                                                @foreach($corporateDepts as $dept)
                                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Passwords (2-Column Grid) -->
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1.5">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                         style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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
                                                title="Toggle password visibility">
                                            <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1.5">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                         style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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
                                                title="Toggle password visibility">
                                            <i :class="showPasswordConfirm ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="tj-btn-primary w-100 py-3 shadow-lg justify-content-center mt-2 text-nowrap" style="font-size: 0.96rem;">
                                <span>Create Account &amp; Enter Academy</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center pt-3 border-top small text-secondary">
                            <span>Already registered with your academic account?</span>
                            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">
                                Sign In
                            </a>
                        </div>

                    </div>

                    <!-- Bottom Trust Badges -->
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 pt-4 mt-3 text-muted small" style="font-size: 0.72rem;">
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
</div>
@endsection
