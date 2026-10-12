@extends('layouts.app')

@section('title', 'Privacy Policy & Data Protection - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Enterprise Privacy Policy, GDPR/CCPA data governance, cookie transparent controls, and learner telemetry retention rules.')

@section('content')
<div class="py-5" style="background: var(--lms-bg-secondary);">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Privacy Policy</li>
            </ol>
        </nav>

        <div class="lms-card p-4 p-md-5 border-0 shadow-sm" style="background: var(--lms-surface-card); border-radius: 24px;">
            <div class="mb-4 pb-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge badge-purple-soft px-3 py-1 rounded-pill mb-2">Data Protection &amp; Governance</span>
                    <h1 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        Privacy Policy &amp; Security Standards
                    </h1>
                    <p class="text-muted small mb-0">Last updated: June 1, 2026 &bull; Version 4.2 &bull; SOC2 Type II Audited</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> GDPR &amp; CCPA Compliant
                    </span>
                </div>
            </div>

            <div class="content-body text-secondary" style="line-height: 1.85;">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i> Minimal Collection</div>
                            <div class="small text-muted">We only collect telemetry required to authenticate, calculate grades, and fulfill compliance recertification audits.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill text-primary me-1"></i> Never Sold</div>
                            <div class="small text-muted">Personal details, course submissions, and company departmental data are never sold or rented to 3rd parties.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-gear-wide-connected text-warning me-1"></i> You're in Control</div>
                            <div class="small text-muted">Learners and tenant admins can export complete data records or trigger compliant data sanitization.</div>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold text-dark mt-4 mb-2">1. Information We Collect</h4>
                <p>
                    We collect credentials supplied during registration (Full Name, Corporate Email, Employee ID, Department Hierarchy), SCORM runtime CMI logs, quiz submission timestamps, exam webcam telemetry flags, and rolling QR check-in geographic coordinates.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">2. How Data is Utilized</h4>
                <p>
                    Data is processed exclusively to: (a) Verify attendance and compute weighted gradebook outcomes, (b) Maintain automated corporate recertification schedules, (c) Deliver cryptographically signed certificates, and (d) Provide department managers with regulatory audit export reports.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">3. Data Retention &amp; Cryptographic Security</h4>
                <p>
                    All database records are stored in encrypted MySQL 8.0+ databases with Redis session caching. Session tokens and password hashes leverage Bcrypt encryption. High-stakes audit trail logs are kept immutable according to corporate tenant compliance policy (typically 3 to 7 years per ISO/IEC 27001).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
