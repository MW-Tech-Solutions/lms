@extends('layouts.app')

@section('title', 'Reset Password - ' . ($site_settings['brand_name'] ?? 'AllThingsCambridge'))
@section('meta_description', 'Recover access to your Cambridge past papers, Esther Math score matrix tracker, and scholar account.')

@section('content')
<div class="py-4 py-lg-5 position-relative overflow-hidden" style="background: var(--lms-bg-body); min-height: calc(100vh - 160px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 500px; height: 500px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 450px; height: 450px; background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-2" style="z-index: 2;">
        
        <!-- Main Split-Screen Container Card -->
        <div class="card border-0 shadow-2xl rounded-5 overflow-hidden mx-auto" 
             style="max-width: 1040px; background: var(--lms-surface-card); border: 1px solid var(--lms-border) !important;">
            
            <div class="row g-0 align-items-stretch">
                
                <!-- LEFT COLUMN: Academic Prestige & Brand Showcase Panel -->
                <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative overflow-hidden" 
                     style="background: linear-gradient(145deg, #0A0D18 0%, #161D36 50%, #0F1322 100%);">
                    
                    <!-- Ambient Glow Effect inside Left Column -->
                    <div class="position-absolute top-0 end-0 rounded-circle" 
                         style="width: 320px; height: 320px; background: radial-gradient(circle, rgba(114, 62, 195, 0.25) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>
                    <div class="position-absolute bottom-0 start-0 rounded-circle" 
                         style="width: 280px; height: 280px; background: radial-gradient(circle, rgba(2, 132, 199, 0.2) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>

                    <!-- Top: Brand Header & Logo -->
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
                                CIE SECURITY
                            </span>
                            <span class="small text-white-50" style="font-size: 0.75rem;">Identity Recovery Bureau</span>
                        </div>

                        <h2 class="display-6 fw-extrabold text-white mb-3" style="font-family: var(--lms-font-heading); letter-spacing: -0.8px; line-height: 1.2;">
                            Securing Your Academic Journey.
                        </h2>
                        
                        <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                            Regain access to your syllabus question packs, Esther Math performance threshold telemetry, and verified examiner feedback.
                        </p>

                        <!-- Academic Feature Checkpoints -->
                        <div class="vstack gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-shield-check text-success fs-6"></i>
                                <span class="small text-white-50">6-Digit Email Verification Code</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-clock-history text-info fs-6"></i>
                                <span class="small text-white-50">60-Minute Security Expiry Window</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-lock-fill text-warning fs-6"></i>
                                <span class="small text-white-50">End-to-End Encrypted Credential Reset</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Testimonial & Security Pill -->
                    <div class="position-relative pt-3 border-top border-white border-opacity-10" style="z-index: 2;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                                 style="width: 42px; height: 42px; background: linear-gradient(135deg, #0284C7, #0369A1); border: 2px solid rgba(255,255,255,0.2);">
                                CIE
                            </div>
                            <div>
                                <div class="small fw-semibold text-white" style="line-height: 1.3;">
                                    "Your Cambridge student records and diagnostic submissions remain fully encrypted and safe."
                                </div>
                                <span class="text-white-50" style="font-size: 0.7rem;">Official Academic Examination Registry</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Reset Request Form Panel -->
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

                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            <h2 class="display-6 fw-extrabold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.6px;">
                                Reset Password
                            </h2>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 font-mono small">
                                <i class="bi bi-shield-lock me-1"></i> Recovery
                            </span>
                        </div>

                        <p class="text-secondary small mb-4">
                            Enter the email address registered with your account to receive a secure <strong>6-digit verification code</strong> and recovery link.
                        </p>

                        <!-- Status Alert -->
                        @if (session('status'))
                            <div class="alert alert-success border-0 rounded-4 small p-3 mb-4 d-flex flex-column gap-2 shadow-sm" style="background: rgba(16, 185, 129, 0.12); color: #065F46;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                    <span class="fw-semibold">{{ session('status') }}</span>
                                </div>

                                @if(session('dev_reset_code'))
                                    <div class="mt-2 p-3 rounded-3 border border-success border-opacity-25 bg-white bg-opacity-90">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                                <i class="bi bi-envelope-check-fill text-primary"></i> Verification Email Dispatched:
                                            </div>
                                            <span class="badge bg-primary text-white font-mono px-2 py-0.5">Live Code</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 my-2 p-2 bg-light rounded-3 border">
                                            <span class="text-secondary small">Your 6-Digit Code:</span>
                                            <span class="font-mono fw-extrabold fs-4 text-primary letter-spacing-2" style="letter-spacing: 4px;">
                                                {{ session('dev_reset_code') }}
                                            </span>
                                        </div>
                                        @if(session('dev_reset_url'))
                                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                                <span class="text-muted" style="font-size: 0.75rem;">Or proceed directly to step 2:</span>
                                                <a href="{{ session('dev_reset_url') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 text-decoration-none fw-semibold" style="font-size: 0.78rem;">
                                                    Enter Code &amp; New Password &rarr;
                                                </a>
                                            </div>
                                        @endif
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

                        <!-- Password Reset Request Form -->
                        <form method="POST" action="{{ route('password.email') }}" class="needs-validation">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary mb-1.5" for="resetEmail">
                                    Account Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="resetEmail" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6" 
                                           required 
                                           autofocus 
                                           value="{{ old('email', session('submitted_email')) }}" 
                                           placeholder="scholar@example.com">
                                </div>
                                <div class="form-text text-muted small mt-1.5 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle text-primary"></i> A 6-digit confirmation code will be sent immediately to this address.
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="tj-btn-primary w-100 py-3 shadow justify-content-center">
                                <span>Send Recovery Link &amp; Reset Code</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                            </button>
                        </form>

                        <!-- If User Already Has a Reset Code -->
                        <div class="p-3 rounded-4 mt-4 border shadow-xs" style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-key-fill text-primary fs-5"></i>
                                    <div>
                                        <div class="small fw-bold" style="color: var(--lms-text-primary);">Already have a 6-digit code?</div>
                                        <div class="text-secondary" style="font-size: 0.72rem;">Enter your verification code and choose a new password.</div>
                                    </div>
                                </div>
                                <a href="{{ route('password.reset', ['token' => 'code']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold text-nowrap" style="font-size: 0.75rem;">
                                    Enter Code &rarr;
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Options -->
                    <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: var(--lms-border) !important;">
                        <span class="small text-secondary">
                            Remembered your password?
                            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">
                                Return to Sign In
                            </a>
                        </span>
                        <a href="{{ route('register') }}" class="small text-secondary text-decoration-none">
                            Need a new account? <strong class="text-primary">Enroll Free</strong>
                        </a>
                    </div>

                </div>

            </div>

        </div>

        <!-- Trust Badges -->
        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 text-muted small">
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-shield-check text-success"></i> Official Cambridge Syllabus
            </span>
            <span>&bull;</span>
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-lock-fill text-primary"></i> 256-Bit TLS Verification
            </span>
            <span>&bull;</span>
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-clock-history text-info"></i> 60-Minute Expiry
            </span>
        </div>

    </div>
</div>
@endsection

