@extends('layouts.app')

@section('title', 'Official Credential Verification - ' . ($certificate->certificate_number ?? $certificate->uuid))

@section('content')
@php
    $issuingSchool = $certificate->course->tenant?->name 
        ?? $site_settings['certificate_school_name'] 
        ?? $site_settings['brand_name'] 
        ?? config('app.name', 'AllThingsCambridge');

    if (empty($issuingSchool) || str_contains(strtolower($issuingSchool), 'enterprise academy')) {
        $issuingSchool = 'AllThingsCambridge';
    }
@endphp
<div class="container py-4 py-md-5">
    <div style="max-width: 680px; margin: 0 auto;">
        <div class="lms-card p-4 p-md-5 shadow-sm border rounded-4 text-center position-relative" style="background: var(--lms-card-bg, #FFFFFF);">
            <!-- Verification Status Header -->
            <div class="mb-4">
                @if($certificate->isValid())
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-xs" 
                         style="width: 72px; height: 72px; background: rgba(16, 185, 129, 0.12); color: #10B981; font-size: 2.4rem;">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <h2 class="fw-bold mb-2" style="color: #0F172A; letter-spacing: -0.5px; font-size: 1.75rem;">Official Credential Verified</h2>
                    <span class="badge rounded-pill font-mono px-3 py-1.5 fw-semibold" 
                          style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.78rem; letter-spacing: 0.8px;">
                        <i class="bi bi-shield-check me-1"></i> CRYPTOGRAPHICALLY AUTHENTICATED
                    </span>
                @else
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-xs" 
                         style="width: 72px; height: 72px; background: rgba(239, 68, 68, 0.12); color: #EF4444; font-size: 2.4rem;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <h2 class="fw-bold mb-2 text-danger" style="letter-spacing: -0.5px; font-size: 1.75rem;">Recertification Required (Expired)</h2>
                    <span class="badge rounded-pill font-mono px-3 py-1.5 fw-semibold" 
                          style="background: rgba(239, 68, 68, 0.12); color: #DC2626; font-size: 0.78rem; letter-spacing: 0.8px;">
                        <i class="bi bi-clock-history me-1"></i> EXPIRED COMPLIANCE STATUS
                    </span>
                @endif
            </div>

            <!-- Credential Details Grid Card -->
            <div class="p-3 p-md-4 rounded-3 text-start mb-4 border" style="background: rgba(248, 250, 252, 0.85); border-color: rgba(226, 232, 240, 0.8) !important;">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Awarded To</span>
                        <span class="fw-bold fs-5 text-dark">{{ $certificate->user->name }}</span>
                        @if($certificate->user->employee_id)
                            <span class="d-block small text-muted font-mono" style="font-size: 0.75rem;">ID: {{ $certificate->user->employee_id }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Issuing Authority</span>
                        <span class="fw-bold fs-5 text-dark">{{ $issuingSchool }}</span>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Demonstrated Competency Track</span>
                        <span class="fw-bold fs-5" style="color: #0284C7;">{{ $certificate->course->title }}</span>
                    </div>
                    <div class="col-sm-6 border-top pt-3">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Issued Date</span>
                        <span class="font-mono fw-semibold text-dark">{{ $certificate->issued_at->format('F d, Y') }}</span>
                    </div>
                    <div class="col-sm-6 border-top pt-3">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Recertification Deadline</span>
                        <span class="font-mono fw-semibold {{ $certificate->isValid() ? 'text-secondary' : 'text-danger fw-bold' }}">
                            {{ $certificate->expires_at ? $certificate->expires_at->format('F d, Y') : 'Permanent Accreditation' }}
                        </span>
                    </div>
                    <div class="col-sm-6 border-top pt-3">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Official Certificate ID</span>
                        <span class="font-mono fw-bold text-dark fs-6">{{ $certificate->certificate_number ?? $certificate->uuid }}</span>
                    </div>
                    <div class="col-sm-6 border-top pt-3 font-mono small text-muted">
                        <span class="small text-muted text-uppercase fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Record UUID</span>
                        <span class="text-secondary text-truncate d-block font-mono" style="font-size: 0.78rem;">{{ $certificate->uuid }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons: Download PDF and View Program Details -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                <a href="{{ route('certificates.verify', ['uuid' => $certificate->uuid, 'download' => 1]) }}" 
                   class="btn btn-success px-4 py-2.5 rounded-pill shadow-sm fw-bold d-inline-flex align-items-center gap-2"
                   style="background: #10B981; border-color: #10B981;">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    <span>Download Official Certificate PDF</span>
                </a>
                <a href="{{ route('courses.show', $certificate->course->slug) }}" class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-semibold">
                    <i class="bi bi-journal-text me-1"></i> View Program Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
