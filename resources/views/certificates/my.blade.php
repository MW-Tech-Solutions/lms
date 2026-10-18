@extends('layouts.app')

@section('title', 'My Certificates & Verified Credentials')

@section('content')
<div class="py-5" style="background: var(--lms-bg-body, #F8FAFC); min-height: 80vh;">
    <div class="container py-2">

        <!-- 1. HEADER BANNER -->
        <div class="p-4 p-md-5 rounded-4 mb-5 position-relative overflow-hidden shadow-sm" 
             style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #4338CA 100%); color: #FFFFFF;">
            <div class="position-relative" style="z-index: 2;">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-xs border mb-3"
                             style="background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.15) !important;">
                            <span class="badge rounded-pill px-2.5 py-1 text-white" style="background: var(--lms-accent, #723EC3); font-size: 0.72rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> VERIFIED CREDENTIALS
                            </span>
                            <span class="small text-white-50" style="font-size: 0.78rem;">
                                Tamper-Proof Cryptographic Verification
                            </span>
                        </div>

                        <h1 class="display-6 fw-bold mb-2 text-white" style="font-family: var(--lms-font-heading);">
                            My Earned Certificates
                        </h1>
                        <p class="text-white-50 small mb-0 pe-lg-4" style="line-height: 1.7; max-width: 650px;">
                            Review all earned credentials, download high-resolution certificates, and share public verification URLs for institutional or corporate audit verification.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <div class="d-inline-flex align-items-center gap-3 p-3 rounded-3" 
                             style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                 style="width: 52px; height: 52px; background: linear-gradient(135deg, #10B981, #059669);">
                                <i class="bi bi-award fs-3"></i>
                            </div>
                            <div class="text-start">
                                <div class="display-6 fw-extrabold font-mono text-white mb-0" style="line-height: 1;">
                                    {{ count($certificates) }}
                                </div>
                                <div class="text-white-50 small" style="font-size: 0.75rem;">
                                    {{ count($certificates) === 1 ? 'Credential Earned' : 'Credentials Earned' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BREADCRUMBS & NAVIGATION CONTROLS -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('my-learning') }}" class="text-decoration-none">My Learning</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Certificates</li>
                </ol>
            </nav>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('my-learning') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5">
                    <i class="bi bi-arrow-left me-1"></i> Return to Learning Dashboard
                </a>
            </div>
        </div>

        <!-- CERTIFICATES LIST -->
        @if($certificates->isNotEmpty())
            <div class="row g-4 mb-5">
                @foreach($certificates as $cert)
                    <div class="col-lg-6">
                        <div class="lms-card p-4 rounded-4 h-100 border shadow-sm d-flex flex-column"
                             style="background: var(--lms-surface-card, #FFFFFF); border-radius: 20px;">
                            
                            <!-- Header Badges -->
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                                    {{ $cert->course->category?->name ?? 'Accredited Course' }}
                                </span>
                                <span class="badge {{ $cert->isValid() ? 'badge-compliance' : 'badge-overdue' }} rounded-pill px-2.5 py-1 font-mono small">
                                    <i class="bi {{ $cert->isValid() ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill' }} me-1"></i>
                                    {{ $cert->isValid() ? 'VALID & VERIFIED' : 'RECERTIFICATION DUE' }}
                                </span>
                            </div>

                            <!-- Course Title -->
                            <h4 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">
                                <a href="{{ route('courses.show', $cert->course->slug) }}" class="text-decoration-none text-reset">
                                    {{ $cert->course->title }}
                                </a>
                            </h4>

                            <!-- Metadata Grid -->
                            <div class="p-3 rounded-3 bg-light border mb-4 mt-2">
                                <div class="row g-2 small font-mono">
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">ISSUED ON:</span>
                                        <strong class="text-dark">{{ $cert->issued_at ? $cert->issued_at->format('M d, Y') : 'N/A' }}</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">VALIDITY STATUS:</span>
                                        <strong class="{{ $cert->isValid() ? 'text-success' : 'text-danger' }}">
                                            {{ $cert->expires_at ? $cert->expires_at->format('M d, Y') : 'Permanent Validity' }}
                                        </strong>
                                    </div>
                                    <div class="col-12 pt-2 border-top">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">CERTIFICATE RECORD UUID:</span>
                                        <div class="d-flex align-items-center justify-content-between gap-2 mt-0.5">
                                            <span class="text-truncate text-secondary" style="font-size: 0.78rem;">{{ $cert->uuid }}</span>
                                            <button type="button" class="btn btn-xs btn-outline-secondary border-0 p-0 text-muted" 
                                                    onclick="navigator.clipboard.writeText('{{ $cert->uuid }}'); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy', 2000);"
                                                    title="Copy UUID">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons Footer -->
                            <div class="mt-auto pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <a href="{{ route('certificates.verify', ['uuid' => $cert->uuid]) }}" target="_blank" class="tj-btn-outline py-2 px-3 small">
                                    <i class="bi bi-qr-code-scan me-1"></i>
                                    <span>Verify Credential</span>
                                </a>

                                <a href="{{ route('certificates.verify', ['uuid' => $cert->uuid]) }}?download=1" class="tj-btn-primary py-2 px-3 small">
                                    <i class="bi bi-download me-1"></i>
                                    <span>Download PDF</span>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="lms-card p-5 mx-auto text-center border shadow-sm rounded-4 my-5" style="max-width: 580px; background: var(--lms-surface-card, #FFFFFF);">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" 
                     style="width: 84px; height: 84px; background: rgba(114, 62, 195, 0.1); color: var(--lms-accent, #723EC3);">
                    <i class="bi bi-award display-4"></i>
                </div>

                <h3 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">
                    No Certificates Earned Yet
                </h3>
                
                <p class="text-secondary small mb-4" style="line-height: 1.7;">
                    Complete all syllabus modules, tests, and mandatory compliance tasks in your enrolled courses to automatically unlock official, cryptographically verified credentials.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('my-learning') }}" class="tj-btn-primary py-2.5 px-4 small">
                        <span>Go to My Learning</span>
                        <span class="btn-icon"><i class="bi bi-play-circle ms-1"></i></span>
                    </a>

                    <a href="{{ route('courses.index') }}" class="tj-btn-outline py-2.5 px-4 small">
                        <i class="bi bi-search me-1"></i>
                        <span>Explore Courses</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
