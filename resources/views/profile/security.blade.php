@extends('layouts.app')

@section('title', 'Security & Two-Factor Authentication (2FA)')

@section('content')
<div class="py-5" style="background: var(--lms-bg-primary); min-height: 80vh;">
    <div class="container" style="max-width: 900px;">
        
        <!-- Breadcrumbs -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('home') }}" class="text-secondary text-decoration-none small">Home</a>
            <span class="text-muted small">/</span>
            <span class="text-secondary small">Account Settings</span>
            <span class="text-muted small">/</span>
            <span class="text-primary small fw-semibold">Two-Factor Authentication</span>
        </div>

        <!-- Header -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1" style="color: var(--lms-text-primary);">
                    <i class="bi bi-shield-lock-fill text-primary me-2"></i> Two-Factor Authentication (2FA)
                </h1>
                <p class="text-secondary small mb-0">
                    Strengthen your account security by requiring an additional authentication step when logging into Enterprise LMS.
                </p>
            </div>
            <div>
                @if($user->two_factor_type === 'AUTHENTICATOR')
                    <span class="badge rounded-pill px-3 py-2 fw-semibold shadow-xs" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <i class="bi bi-phone-fill me-1"></i> Google Authenticator Active
                    </span>
                @elseif($user->two_factor_type === 'EMAIL')
                    <span class="badge rounded-pill px-3 py-2 fw-semibold shadow-xs" style="background: rgba(59, 130, 246, 0.12); color: #2563EB; border: 1px solid rgba(59, 130, 246, 0.3);">
                        <i class="bi bi-envelope-check-fill me-1"></i> Email OTP Active
                    </span>
                @else
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: rgba(107, 114, 128, 0.12); color: #4B5563; border: 1px solid rgba(107, 114, 128, 0.2);">
                        <i class="bi bi-shield-x me-1"></i> 2FA Disabled
                    </span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
        @endif

        <!-- 2FA Configuration Form with Alpine.js -->
        <div class="lms-card p-4 p-md-5 rounded-4 shadow-sm mb-4" 
             style="background: var(--lms-surface-card); border: 1px solid var(--lms-border);"
             x-data="{ 
                 selectedType: '{{ $user->two_factor_type ?? 'NONE' }}',
                 copied: false,
                 copySecret() {
                     navigator.clipboard.writeText('{{ $secret }}');
                     this.copied = true;
                     setTimeout(() => this.copied = false, 2500);
                 }
             }">

            <form method="POST" action="{{ route('profile.2fa.update') }}">
                @csrf
                <input type="hidden" name="secret" value="{{ $secret }}">

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-3">Choose Authentication Method</label>
                    <div class="row g-3">
                        
                        <!-- Option 1: Disabled -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 rounded-4 border text-start cursor-pointer transition-all"
                                   :style="selectedType === 'NONE' ? 'border: 2px solid var(--lms-primary) !important; background: rgba(114, 62, 195, 0.04);' : 'border-color: var(--lms-border);'"
                                   style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="radio" name="two_factor_type" value="NONE" x-model="selectedType" class="form-check-input mt-0">
                                    <span class="fw-bold small text-dark">Disabled</span>
                                </div>
                                <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                    Standard single-factor sign in with your email and password only.
                                </p>
                            </label>
                        </div>

                        <!-- Option 2: Email OTP -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 rounded-4 border text-start cursor-pointer transition-all"
                                   :style="selectedType === 'EMAIL' ? 'border: 2px solid var(--lms-primary) !important; background: rgba(114, 62, 195, 0.04);' : 'border-color: var(--lms-border);'"
                                   style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="radio" name="two_factor_type" value="EMAIL" x-model="selectedType" class="form-check-input mt-0">
                                    <span class="fw-bold small text-primary"><i class="bi bi-envelope-fill me-1"></i> Email Code (OTP)</span>
                                </div>
                                <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                    Receive a secure 6-digit one-time code to <strong>{{ $user->email }}</strong> upon sign-in.
                                </p>
                            </label>
                        </div>

                        <!-- Option 3: Google Authenticator -->
                        <div class="col-md-4">
                            <label class="card h-100 p-3 rounded-4 border text-start cursor-pointer transition-all"
                                   :style="selectedType === 'AUTHENTICATOR' ? 'border: 2px solid var(--lms-primary) !important; background: rgba(114, 62, 195, 0.04);' : 'border-color: var(--lms-border);'"
                                   style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="radio" name="two_factor_type" value="AUTHENTICATOR" x-model="selectedType" class="form-check-input mt-0">
                                    <span class="fw-bold small text-success"><i class="bi bi-phone-fill me-1"></i> Google Authenticator</span>
                                </div>
                                <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                    Use Google Authenticator, Microsoft Authenticator, or Authy to generate time-based codes.
                                </p>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- Google Authenticator Setup Panel -->
                <div x-show="selectedType === 'AUTHENTICATOR'" x-transition class="p-4 rounded-4 border mb-4" style="background: rgba(114, 62, 195, 0.03); border-color: rgba(114, 62, 195, 0.2) !important;">
                    <h5 class="fw-bold mb-2 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-qr-code-scan text-primary"></i> Link Authenticator App
                    </h5>
                    <p class="text-secondary small mb-3">
                        Scan the QR code below using Google Authenticator on your mobile device, or enter the secret key manually.
                    </p>

                    <div class="row g-4 align-items-center">
                        <div class="col-auto">
                            <div class="p-3 bg-white rounded-3 shadow-sm border d-inline-block text-center">
                                <img src="{{ $qrUrl }}" alt="2FA QR Code" class="img-fluid" style="width: 150px; height: 150px;">
                                <div class="text-muted small mt-1 font-mono" style="font-size: 0.68rem;">Google Authenticator QR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Manual Secret Setup Key</label>
                                <div class="input-group input-group-sm" style="max-width: 380px;">
                                    <input type="text" readonly class="form-control font-mono fw-bold text-primary" value="{{ $secret }}" style="letter-spacing: 2px;">
                                    <button type="button" class="btn btn-outline-primary" @click="copySecret()">
                                        <i class="bi" :class="copied ? 'bi-check-lg text-success' : 'bi-clipboard'"></i>
                                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                    </button>
                                </div>
                                <span class="form-text small text-muted">Account: <strong>{{ $user->email }}</strong> | Issuer: <strong>Enterprise LMS</strong></span>
                            </div>

                            <div class="alert alert-info py-2 px-3 rounded-3 small mb-0 d-inline-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-info"></i>
                                <span>Supported Apps: Google Authenticator, Microsoft Authenticator, 1Password, Authy.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email OTP Notice Panel -->
                <div x-show="selectedType === 'EMAIL'" x-transition class="p-4 rounded-4 border mb-4" style="background: rgba(59, 130, 246, 0.04); border-color: rgba(59, 130, 246, 0.2) !important;">
                    <h5 class="fw-bold mb-1 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check"></i> Email Verification Protection
                    </h5>
                    <p class="text-secondary small mb-0">
                        When you sign in, our platform will generate a cryptographic 6-digit verification code and dispatch it immediately to <strong>{{ $user->email }}</strong>. You will have 10 minutes to input the code.
                    </p>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <button type="submit" class="tj-btn-primary px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Save Security Settings
                    </button>
                </div>
            </form>

        </div>

    </div>
</div>
@endsection
