@extends('layouts.app')

@section('title', 'Terms of Service - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Enterprise Terms of Service, CBAC governance agreements, and dual-audience LMS operational conditions.')

@section('content')
<div class="py-5" style="background: var(--lms-bg-secondary);">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Terms of Service</li>
            </ol>
        </nav>

        <div class="lms-card p-4 p-md-5 border-0 shadow-sm" style="background: var(--lms-surface-card); border-radius: 24px;">
            <div class="mb-4 pb-3 border-bottom">
                <span class="badge badge-purple-soft px-3 py-1 rounded-pill mb-2">Governance &amp; Trust</span>
                <h1 class="display-6 fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Enterprise Terms of Service
                </h1>
                <p class="text-muted small">Effective Date: October 1, 2026 &bull; Version 3.4 &bull; WCAG 2.1 AA Compliant</p>
            </div>

            <div class="content-body text-secondary" style="line-height: 1.85;">
                <h4 class="fw-bold text-dark mt-4 mb-2">1. Operational Scope &amp; Dual-Audience Architecture</h4>
                <p>
                    These Terms govern use of the enterprise Learning Management System platform provided by Enterprise Academy. The platform serves two distinct operational audiences: (a) Corporate internal workforce users enrolled in mandatory regulatory compliance and recertification tracks, and (b) Public students accessing self-paced, instructor-led, and accredited technical programs.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">2. Context-Based Access Control (CBAC) &amp; Identity</h4>
                <p>
                    User authentication and permission boundaries are enforced via our 5-tier hierarchical context matrix: System, Tenant, Department, Course, and Section. Users accessing via Corporate SSO (SAML 2.0 / Okta / Azure Active Directory) are subject to automated session termination and manager audit verification. You agree never to share single-sign-on credentials or attempt to circumvent tenant boundary isolation.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">3. Assessment Proctoring &amp; Anti-Cheat Policies</h4>
                <p>
                    High-stakes compliance exams and certified graded quizzes employ client-side anti-cheat telemetry. Telemetry triggers on window defocusing, tab blur, developer tool activation, and periodic webcam validation snapshots. Any verified evasion event incurs an automated audit flag, notification to your department manager, and grade invalidation.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">4. SCORM Runtime Packages &amp; Intellectual Property</h4>
                <p>
                    All SCORM 1.2 and SCORM 2004 courseware uploaded to tenant clusters remain the intellectual property of the originating corporate tenant or accredited instructor. The platform maintains CMI telemetry, suspend state, and completion status under enterprise encryption standards.
                </p>

                <h4 class="fw-bold text-dark mt-4 mb-2">5. Cryptographic Certification &amp; Verification</h4>
                <p>
                    Upon successful course completion and passing gradebook computation, certificates are generated with a unique SHA-256 identifier and verifiable rolling QR code. Certificates are publicly verifiable at <code>/verify/{uuid}</code>. Alteration or forgery of certificate records constitutes grounds for permanent account suspension.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
