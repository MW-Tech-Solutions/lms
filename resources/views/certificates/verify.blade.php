@extends('layouts.app')

@section('title', 'Public Credential Verification - ' . $certificate->uuid)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="lms-card p-4 p-md-5 shadow-lg border-2 {{ $certificate->isValid() ? 'border-success' : 'border-danger' }} text-center position-relative">
                <div class="mb-4">
                    @if($certificate->isValid())
                        <div class="display-3 text-success mb-2"><i class="bi bi-patch-check-fill"></i></div>
                        <h2 class="fw-bold text-success">Official Credential Verified</h2>
                        <div class="badge bg-success-subtle text-success fs-6 font-mono px-3 py-1">CRYPTOGRAPHICALLY AUTHENTICATED</div>
                    @else
                        <div class="display-3 text-danger mb-2"><i class="bi bi-exclamation-octagon-fill"></i></div>
                        <h2 class="fw-bold text-danger">Recertification Required (Expired)</h2>
                        <div class="badge bg-danger-subtle text-danger fs-6 font-mono px-3 py-1">EXPIRED COMPLIANCE STATUS</div>
                    @endif
                </div>

                <div class="bg-body-tertiary p-4 rounded-3 text-start mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Awarded To</span>
                            <span class="fw-bold fs-5">{{ $certificate->user->name }}</span>
                            @if($certificate->user->employee_id)
                                <span class="d-block small text-muted font-mono">Employee ID: {{ $certificate->user->employee_id }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Issuing Authority</span>
                            <span class="fw-bold fs-5">{{ $site_settings['brand_name'] ?? 'Enterprise Academy & Compliance' }}</span>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Demonstrated Competency Track</span>
                            <span class="fw-bold fs-4 text-primary">{{ $certificate->course->title }}</span>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Issued Date</span>
                            <span class="font-mono fw-semibold">{{ $certificate->issued_at->format('F d, Y') }}</span>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Recertification Deadline</span>
                            <span class="font-mono fw-semibold {{ $certificate->isValid() ? 'text-secondary' : 'text-danger fw-bold' }}">
                                {{ $certificate->expires_at ? $certificate->expires_at->format('F d, Y') : 'Permanent Accreditation' }}
                            </span>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Official Certificate ID</span>
                            <span class="font-mono fw-bold text-dark fs-6">{{ $certificate->certificate_number }}</span>
                        </div>
                        <div class="col-sm-6 border-top pt-2 font-mono small text-muted">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Record UUID</span>
                            <span class="text-secondary text-truncate d-block">{{ $certificate->uuid }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @if($certificate->pdf_url)
                        <a href="{{ $certificate->pdf_url }}" target="_blank" class="btn btn-accent px-4 py-2">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Download Official Certificate PDF
                        </a>
                    @endif
                    <a href="{{ route('courses.show', $certificate->course->slug) }}" class="btn btn-outline-secondary px-4 py-2">
                        View Program Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
