@extends('layouts.app')

@section('title', 'Auditor Verification & Support - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Auditor verification hub, compliance support tickets, and direct solution architect escalations.')

@section('content')
<div class="py-5" style="background: var(--lms-bg-secondary);">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Auditor Verification &amp; Support</li>
            </ol>
        </nav>

        <div class="lms-card p-4 p-md-5 border-0 shadow-sm" style="background: var(--lms-surface-card); border-radius: 24px;">
            <div class="mb-4 pb-3 border-bottom">
                <span class="badge badge-purple-soft px-3 py-1 rounded-pill mb-2">Auditor Portal &amp; Enterprise Support</span>
                <h1 class="display-6 fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Governance Verification &amp; Priority Helpdesk
                </h1>
                <p class="text-muted small">Dedicated support channels for accreditation boards, HR managers, and enterprise learners.</p>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="p-4 rounded-4 border h-100 d-flex flex-column" style="background: var(--lms-bg-secondary);">
                        <div class="rounded-3 p-3 mb-3 d-inline-block text-white" style="background: var(--lms-accent); width: fit-content;">
                            <i class="bi bi-qr-code-scan fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-body">Certificate Verification</h5>
                        <p class="small text-secondary flex-grow-1">
                            Verify any credential issued across our academic cohorts or compliance recertification loops using the cryptographic verification hub.
                        </p>
                        <a href="{{ route('courses.index') }}" class="tj-btn-outline py-2 px-3 small justify-content-center">
                            <span>Browse Verifiable Tracks</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-4 border h-100 d-flex flex-column" style="background: var(--lms-bg-secondary);">
                        <div class="rounded-3 p-3 mb-3 d-inline-block text-white" style="background: #2563EB; width: fit-content;">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-body">Auditor Portal Access</h5>
                        <p class="small text-secondary flex-grow-1">
                            External compliance auditors with context-level roles can inspect immutably logged manager approvals, SCORM scores, and recertification records.
                        </p>
                        <a href="{{ route('login') }}" class="tj-btn-outline py-2 px-3 small justify-content-center">
                            <span>Auditor Login</span>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-4 border h-100 d-flex flex-column" style="background: var(--lms-bg-secondary);">
                        <div class="rounded-3 p-3 mb-3 d-inline-block text-white" style="background: #059669; width: fit-content;">
                            <i class="bi bi-headset fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-body">Architect Escalation</h5>
                        <p class="small text-secondary flex-grow-1">
                            Enterprise clients with 99.99% SLA receive 24/7 direct access to dedicated technical account architects for custom SSO and SCORM imports.
                        </p>
                        <a href="{{ route('cms.show', ['slug' => 'pricing']) }}" class="tj-btn-primary py-2 px-3 small justify-content-center text-white">
                            <span>View SLA Tiers</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
