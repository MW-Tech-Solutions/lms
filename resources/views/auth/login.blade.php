@extends('layouts.app')

@section('title', 'Sign In - ' . ($site_settings['brand_name'] ?? 'AllThingsCambridge'))
@section('meta_description', 'Sign in to access your Cambridge past papers, Esther Math score matrix tracker, and examiner-led solutions.')

@section('content')
<div class="py-4 py-lg-5 position-relative overflow-hidden" style="background: var(--lms-bg-body); min-height: calc(100vh - 160px);">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 500px; height: 500px; background: radial-gradient(circle, rgba(114, 62, 195, 0.12) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 450px; height: 450px; background: radial-gradient(circle, rgba(2, 132, 199, 0.1) 0%, transparent 70%); filter: blur(70px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-2" style="z-index: 2;" 
         x-data="{
             selectedRole: '',
             showPassword: false,
             showQuickAccounts: false,
             accounts: {
                 'admin': {
                     name: 'Alexander Vance',
                     role: 'Super Administrator',
                     email: 'admin@enterpriselms.com',
                     badge: 'Super Admin',
                     icon: 'bi-shield-shaded'
                 },
                 'teacher': {
                     name: 'Marcus Aurelius',
                     role: 'Cambridge Senior Faculty',
                     email: 'teacher.editing@enterpriselms.com',
                     badge: 'Verified Examiner',
                     icon: 'bi-mortarboard-fill'
                 },
                 'student': {
                     name: 'Emma Watson',
                     role: 'Academic Scholar',
                     email: 'student@enterpriselms.com',
                     badge: 'Cambridge Scholar',
                     icon: 'bi-person-check-fill'
                 },
                 'parent': {
                     name: 'Sarah Connor',
                     role: 'Parent / Guardian',
                     email: 'parent@enterpriselms.com',
                     badge: 'Parent Portal',
                     icon: 'bi-people-fill'
                 },
                 'guest': {
                     name: 'Guest Visitor',
                     role: 'Guest Account',
                     email: 'guest@enterpriselms.com',
                     badge: 'Public Guest',
                     icon: 'bi-incognito'
                 }
             },
             applyAccount(key) {
                 this.selectedRole = key;
                 const acc = this.accounts[key];
                 if (acc) {
                     const emailInput = document.getElementById('loginEmail');
                     const passInput = document.getElementById('loginPassword');
                     if (emailInput && passInput) {
                         emailInput.value = acc.email;
                         passInput.value = 'password123';
                         emailInput.dispatchEvent(new Event('input'));
                         passInput.dispatchEvent(new Event('input'));
                     }
                 }
             }
         }">
        
        <!-- Main Split-Screen Container Card -->
        <div class="card border-0 shadow-2xl rounded-5 overflow-hidden mx-auto" 
             style="max-width: 1080px; background: var(--lms-surface-card); border: 1px solid var(--lms-border) !important;">
            
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
                                CIE 2026
                            </span>
                            <span class="small text-white-50" style="font-size: 0.75rem;">Mastery Portal &bull; Isolated Silos</span>
                        </div>

                        <h2 class="display-6 fw-extrabold text-white mb-3" style="font-family: var(--lms-font-heading); letter-spacing: -0.8px; line-height: 1.2;">
                            Excellence in Cambridge Qualifications.
                        </h2>
                        
                        <p class="text-white-50 small mb-4" style="line-height: 1.8;">
                            Access official past paper mark scheme breakdowns, KaTeX step-by-step formula derivations, and the real-time Esther Math diagnostic tracking engine.
                        </p>

                        <!-- Academic Feature Checkpoints -->
                        <div class="vstack gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <span class="small text-white-50">5 Isolated Syllabus Levels (Primary to A-Level)</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <span class="small text-white-50">Esther Math Raw Mark Database Matrix</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <span class="small text-white-50">Senior Syllabus Moderators &amp; Examiner Mentorship</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Testimonial & Metrics Pill -->
                    <div class="position-relative pt-3 border-top border-white border-opacity-10" style="z-index: 2;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                                 style="width: 42px; height: 42px; background: linear-gradient(135deg, #0284C7, #0369A1); border: 2px solid rgba(255,255,255,0.2);">
                                CIE
                            </div>
                            <div>
                                <div class="small fw-semibold text-white" style="line-height: 1.3;">
                                    "Real-time diagnostic percentage calculation ensures students target exact weak paper components."
                                </div>
                                <span class="text-white-50" style="font-size: 0.7rem;">Senior Mathematics Moderator, May/June Series</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Interactive Sign In Form Panel -->
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

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <h2 class="display-6 fw-extrabold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.6px;">
                                Sign In
                            </h2>
                            <button type="button" 
                                    @click="showQuickAccounts = !showQuickAccounts" 
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold shadow-xs" 
                                    style="font-size: 0.74rem;">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
                                <span x-text="showQuickAccounts ? 'Hide Demo Logins' : '1-Click Demo Logins'"></span>
                            </button>
                        </div>

                        <p class="text-secondary small mb-4">
                            Enter your credentials to access your Cambridge learning space and past paper analytics.
                        </p>

                        <!-- Quick 1-Click Demo Personas (Collapsible Drawer) -->
                        <div x-show="showQuickAccounts" x-transition class="p-3 rounded-4 mb-4 border shadow-sm" 
                             style="background: var(--lms-bg-secondary); border-color: var(--lms-border) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2.5">
                                <span class="small fw-bold text-uppercase text-secondary font-mono" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-person-badge-fill text-primary me-1"></i> Fast Switch Roles (Password: password123)
                                </span>
                                <span class="badge bg-success-subtle text-success small font-mono">1-Click Auto Fill</span>
                            </div>
                            <div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                                <template x-for="(acc, key) in accounts" :key="key">
                                    <button type="button" 
                                            @click="applyAccount(key)"
                                            class="btn btn-sm text-start p-2 rounded-3 border transition-all d-flex align-items-center gap-2"
                                            :class="selectedRole === key ? 'border-primary bg-primary bg-opacity-10 shadow-xs' : 'border-0 bg-body'">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                             style="width: 26px; height: 26px; background: rgba(114, 62, 195, 0.1); color: var(--lms-accent, #723EC3);">
                                            <i class="bi" :class="acc.icon" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-bold text-body small text-truncate" style="font-size: 0.72rem;" x-text="acc.badge"></div>
                                            <div class="text-muted text-truncate font-mono" style="font-size: 0.62rem;" x-text="acc.email"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Error Alerts -->
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 small p-3 mb-4 shadow-sm">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Sign-In Failed</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Main Login Form -->
                        <form method="POST" action="{{ route('login') }}" class="vstack gap-3 mb-4">
                            @csrf

                            <!-- Email Address -->
                            <div>
                                <label for="loginEmail" class="form-label small fw-semibold text-secondary mb-1.5">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
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
                                           placeholder="scholar@cambridge.edu">
                                </div>
                            </div>

                            <!-- Password Field with Interactive Toggle -->
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <label for="loginPassword" class="form-label small fw-semibold text-secondary mb-0">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-primary small text-decoration-none fw-semibold">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>
                                <div class="input-group input-group-lg rounded-3 border overflow-hidden" 
                                     style="background: var(--lms-bg-body); border-color: var(--lms-border) !important;">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           id="loginPassword" 
                                           class="form-control border-0 bg-transparent ps-2 fs-6 font-mono" 
                                           required 
                                           placeholder="••••••••">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="input-group-text bg-transparent border-0 text-muted pe-3" 
                                            title="Toggle password visibility">
                                        <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="d-flex align-items-center justify-content-between pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                                    <label class="form-check-label small text-secondary" for="rememberMe">
                                        Keep me signed in on this device
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="tj-btn-primary w-100 py-3 shadow-lg justify-content-center mt-2 text-nowrap" style="font-size: 0.96rem;">
                                <span>Sign In to Learning Portal</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                            </button>
                        </form>

                        <!-- Link to Registration -->
                        <div class="text-center pt-3 border-top small text-secondary">
                            <span>Don't have an academic account yet?</span>
                            <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1">
                                Create Account / Enroll Now
                            </a>
                        </div>

                    </div>

                    <!-- Bottom Security Trust Badges -->
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 pt-4 mt-3 text-muted small" style="font-size: 0.72rem;">
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-success"></i> ISO 27001 Certified
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
