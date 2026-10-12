@extends('layouts.app')

@section('title', 'Dual-Audience Registration - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))

@section('content')
<div class="py-5 position-relative overflow-hidden" style="background: var(--lms-bg-secondary); min-height: calc(100vh - 180px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 77, 0, 0.08) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-3" style="z-index: 2;" x-data="{ audience: 'EMPLOYEE' }">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="lms-card p-4 p-md-5 shadow-lg border-0" style="background: var(--lms-surface-card); border-radius: 28px;">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <span class="badge rounded-3 p-3 shadow-sm mb-3 d-inline-block text-white" 
                              style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1.5rem;">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>
                        <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            {{ $site_settings['label_register'] ?? 'Create Enterprise LMS Account' }}
                        </h2>
                        <p class="text-secondary small mb-0">Select your operational profile to configure your personalized learning track.</p>
                    </div>

                    <!-- Dual Audience Persona Selector -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <button type="button" 
                                    class="btn w-100 p-3 text-start border rounded-4 d-flex flex-column transition-all shadow-sm"
                                    :class="audience === 'EMPLOYEE' ? 'btn-primary text-white border-primary shadow' : 'btn-light border text-secondary'"
                                    @click="audience = 'EMPLOYEE'">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-building fs-5"></i>
                                    <span class="fw-bold" style="font-size: 0.95rem;">Corporate Workforce</span>
                                </div>
                                <span class="small" :class="audience === 'EMPLOYEE' ? 'text-white-50' : 'text-muted'" style="font-size: 0.75rem;">
                                    Compliance tracks &amp; automated recertifications
                                </span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" 
                                    class="btn w-100 p-3 text-start border rounded-4 d-flex flex-column transition-all shadow-sm"
                                    :class="audience === 'STUDENT' ? 'btn-primary text-white border-primary shadow' : 'btn-light border text-secondary'"
                                    @click="audience = 'STUDENT'">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-mortarboard fs-5"></i>
                                    <span class="fw-bold" style="font-size: 0.95rem;">Academic Student</span>
                                </div>
                                <span class="small" :class="audience === 'STUDENT' ? 'text-white-50' : 'text-muted'" style="font-size: 0.75rem;">
                                    Public catalog &amp; accredited technical cohorts
                                </span>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="user_type" :value="audience">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Full Legal Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" required value="{{ old('name') }}" placeholder="Dr. Jane Smith">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0" required value="{{ old('email') }}" placeholder="jane@organization.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Corporate Specific Fields -->
                        <div x-show="audience === 'EMPLOYEE'" x-transition class="p-3.5 rounded-4 mb-3 border shadow-sm" style="background: var(--lms-bg-secondary);">
                            <div class="fw-bold small text-primary mb-2">
                                <i class="bi bi-shield-check me-1"></i> Organizational Affiliation
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Corporate Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" name="employee_id" class="form-control font-mono" placeholder="EMP-94021" :required="audience === 'EMPLOYEE'">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Assigned Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Select Department Hierarchy...</option>
                                        @foreach(\App\Models\Department::all() as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="Minimum 8 characters">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold text-secondary">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control border-start-0 ps-0" required placeholder="Repeat password">
                                </div>
                            </div>
                            @error('password')
                                <div class="col-12 text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                            <span>Complete Account Registration</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-secondary">
                        <span>Already hold an enterprise login?</span>
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
