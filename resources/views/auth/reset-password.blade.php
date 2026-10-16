@extends('layouts.app')

@section('title', 'Set New Password - ' . ($site_settings['brand_name'] ?? 'AllThingsCambridge'))
@section('meta_description', 'Set a new secure password for your Cambridge academic account.')

@section('content')
<div class="py-4 py-lg-5 position-relative overflow-hidden" style="background: var(--lms-bg-body); min-height: calc(100vh - 160px);"
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
                            <span class="badge rounded-pill px-2 py-0.5 text-white fw-bold" style="background: #10B981; font-size: 0.68rem;">
                                VERIFIED CIPHER
                            </span>
                            <span class="small text-white-50" style="font-size: 0.75rem;">Credential Re-authentication</span>
                        </div>

                        <h2 class="display-6 fw-extrabold text-white mb-3" style="font-family: var(--lms-font-heading); letter-spacing: -0.8px; line-height: 1.2;">
                            Set New Password.
                        </h2>
                        
                        <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                            Create a fresh, resilient password to re-enter your syllabus question bank and continuous Esther score tracker.
                        </p>

                        <!-- Academic Feature Checkpoints -->
                        <div class="vstack gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                <span class="small text-white-50">Minimum 8 characters length policy</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                <span class="small text-white-50">6-Digit verification code authorization</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                <span class="small text-white-50">Instant cryptographic session invalidation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Security Info -->
                    <div class="position-relative pt-3 border-top border-white border-opacity-10" style="z-index: 2;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                                 style="width: 42px; height: 42px; background: linear-gradient(135deg, #10B981, #059669); border: 2px solid rgba(255,255,255,0.2);">
                                <i class="bi bi-key-fill"></i>
                            </div>
                            <div>
                                <div class="small fw-semibold text-white" style="line-height: 1.3;">
                                    "Passwords are protected with Argon2id / Bcrypt multi-round salting."
                                </div>
                                <span class="text-white-50" style="font-size: 0.7rem;">Cambridge Standards &amp; Cyber Governance</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Reset Form Panel -->
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
                                Set New Password
                            </h2>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 font-mono small">
                                <i class="bi bi-shield-check me-1"></i> Security Key
                            </span>
                        </div>

                        <p class="text-secondary small mb-4">
                            Enter the <strong>6-digit code</strong> sent to your email along with your desired new password.
                        </p>

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

                        <!-- Password Update Form -->
                        <form method="POST" action="{{ route('password.update') }}" class="needs-validation">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Email Input -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1.5" for="resetUserEmail">
                                    Account Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="resetUserEmail" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6" 
                                           required 
                                           value="{{ old('email', $email) }}" 
                                           placeholder="scholar@example.com">
                                </div>
                            </div>

                            <!-- 6-Digit Verification Code -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label class="form-label small fw-semibold text-secondary mb-0" for="resetCode">
                                        6-Digit Verification Code
                                    </label>
                                    <span class="text-muted" style="font-size: 0.72rem;">From email or direct link</span>
                                </div>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="text" 
                                           name="reset_code" 
                                           id="resetCode" 
                                           maxlength="6"
                                           class="form-control border-0 bg-transparent ps-2 fs-6 font-mono fw-bold text-primary" 
                                           value="{{ old('reset_code') }}" 
                                           placeholder="e.g. 548291"
                                           style="letter-spacing: 2px;">
                                </div>
                                <div class="form-text text-muted small mt-1" style="font-size: 0.73rem;">
                                    If you opened the reset link from your email, this code may be verified automatically via the secure token.
                                </div>
                            </div>

                            <!-- New Password Input -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1.5" for="newPassword">
                                    New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-check2-circle"></i>
                                    </span>
                                    <input :type="showConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           id="confirmPassword" 
                                           x-model="confirmPassword"
                                           class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                           required 
                                           placeholder="Re-enter new password">
                                    <button type="button" 
                                            @click="showConfirm = !showConfirm"
                                            class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                            title="Toggle password visibility">
                                        <i :class="showConfirm ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                    </button>
                                </div>

                                <!-- Live Requirements Checklist -->
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
                                <span>Update Password &amp; Sign In</span>
                                <span class="btn-icon"><i class="bi bi-check-lg ms-2"></i></span>
                            </button>
                        </form>

                    </div>

                    <!-- Footer Options -->
                    <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: var(--lms-border) !important;">
                        <a href="{{ route('password.request') }}" class="small text-secondary text-decoration-none">
                            <i class="bi bi-arrow-repeat me-1"></i> Request a new code
                        </a>
                        <a href="{{ route('login') }}" class="small text-primary fw-bold text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
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
                <i class="bi bi-shield-lock-fill text-info"></i> End-to-End Encryption
            </span>
        </div>

    </div>
</div>
@endsection

