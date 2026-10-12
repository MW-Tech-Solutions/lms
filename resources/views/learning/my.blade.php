@extends('layouts.app')

@section('title', 'My Learning & Compliance Dashboard')

@section('content')
<div class="py-5" style="background: var(--lms-bg-body);">
    <div class="container py-2">
        
        <!-- 1. WELCOME PROFILE BANNER -->
        <div class="p-4 p-md-5 rounded-4 mb-5 position-relative overflow-hidden shadow-sm" 
             style="background: linear-gradient(135deg, #151928 0%, #272F54 50%, #723EC3 100%); color: #FFFFFF;">
            
            <div class="position-relative" style="z-index: 2;">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-extrabold display-6 shadow" 
                                 style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--lms-accent), #FF4D00); border: 3px solid rgba(255,255,255,0.3);">
                                {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                            </div>
                            <div>
                                <span class="badge rounded-pill px-3 py-1 mb-1 text-white" style="background: rgba(255,255,255,0.18); font-size: 0.72rem; letter-spacing: 0.5px;">
                                    {{ Auth::user()->user_type === 'EMPLOYEE' ? 'CORPORATE WORKFORCE' : 'ACADEMIC SCHOLAR' }}
                                </span>
                                <h1 class="display-6 fw-bold mb-0 text-white" style="font-family: var(--lms-font-heading);">
                                    Welcome back, {{ Auth::user()->name ?? 'Learner' }}!
                                </h1>
                                <div class="text-white-50 small font-mono mt-1">
                                    ID: {{ Auth::user()->employee_id ?? 'STD-9901' }} &bull; Department: {{ Auth::user()->department?->name ?? 'Enterprise Hub' }}
                                </div>
                            </div>
                        </div>
                        <p class="text-white-50 small mb-0 pe-lg-4" style="line-height: 1.7;">
                            Track enrolled certifications, review passing telemetry, and ensure mandatory compliance recertifications remain audit-ready.
                        </p>
                    </div>

                    <!-- Quick Summary Pills -->
                    <div class="col-lg-5">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-3 rounded-3 text-center" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                                    <div class="display-6 fw-extrabold font-mono text-warning mb-0">{{ count($enrollments) }}</div>
                                    <div class="text-white-50 small" style="font-size: 0.75rem;">Active Courses</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 text-center" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                                    <div class="display-6 fw-extrabold font-mono text-success mb-0">{{ count($certificates) }}</div>
                                    <div class="text-white-50 small" style="font-size: 0.75rem;">Certificates Earned</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SECTION HEADER & ACTIONS -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Active Enrolled Programs
                </h3>
                <p class="text-secondary small mb-0">Continue your curriculum or access self-paced modules.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="tj-btn-outline py-2 px-3 small">
                <i class="bi bi-search me-1"></i>
                <span>Explore Catalog</span>
            </a>
        </div>

        <!-- 3. ENROLLED COURSES CARDS GRID -->
        <div class="row g-4 mb-5">
            @forelse($enrollments as $enrollment)
                <div class="col-md-6 col-lg-4">
                    <div class="lms-card p-4 h-100 d-flex flex-column shadow-sm border position-relative" 
                         style="background: var(--lms-surface-card); border-radius: 20px;">
                        
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                                {{ $enrollment->course->category?->name ?? 'Course' }}
                            </span>
                            @if($enrollment->status === 'COMPLETED')
                                <span class="badge badge-compliance">COMPLETED</span>
                            @elseif($enrollment->status === 'EXPIRED')
                                <span class="badge badge-overdue">RECERTIFICATION EXPIRED</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-bold">ACTIVE</span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">
                            <a href="{{ route('learning.player', ['course' => $enrollment->course->slug]) }}" class="text-decoration-none text-reset">
                                {{ $enrollment->course->title }}
                            </a>
                        </h4>

                        <!-- Recertification Due Badge -->
                        @if($enrollment->expires_at)
                            <div class="mb-3 p-2.5 rounded-3 small font-mono d-flex align-items-center gap-2 {{ $enrollment->isExpired() ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                                <i class="bi bi-clock-history fs-6"></i>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.72rem;">RECERTIFICATION DEADLINE</div>
                                    <div style="font-size: 0.8rem;">{{ $enrollment->expires_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Progress Bar Simulation -->
                        <div class="my-3">
                            <div class="d-flex justify-content-between small text-muted mb-1 font-mono">
                                <span>Curriculum Progress</span>
                                <span class="fw-bold text-dark">{{ $enrollment->status === 'COMPLETED' ? '100%' : '65%' }}</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar rounded-pill" 
                                     style="width: {{ $enrollment->status === 'COMPLETED' ? '100%' : '65%' }}; background: linear-gradient(90deg, #723EC3, #2563EB);"></div>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted font-mono" style="font-size: 0.75rem;">
                                Enrolled: {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'Active' }}
                            </span>
                            <a href="{{ route('learning.player', ['course' => $enrollment->course->slug]) }}" class="tj-btn-primary py-2 px-3 small">
                                <span>Resume</span>
                                <span class="btn-icon"><i class="bi bi-play-fill ms-1"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="lms-card p-5 mx-auto text-center" style="max-width: 500px; border-radius: 24px;">
                        <i class="bi bi-journal-plus display-3 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold">No active enrollments found</h4>
                        <p class="text-secondary small">Explore our catalog of corporate compliance tracks and public accredited certifications.</p>
                        <a href="{{ route('courses.index') }}" class="tj-btn-primary py-2.5 px-4 mx-auto mt-2">
                            <span>Browse Course Catalog</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- 4. ACHIEVED CRYPTOGRAPHIC CERTIFICATES -->
        @if($certificates->isNotEmpty())
            <div class="lms-card p-4 p-md-5 shadow-sm border" style="background: var(--lms-surface-card); border-radius: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            <i class="bi bi-award-fill text-warning me-2"></i>Verified Cryptographic Credentials
                        </h3>
                        <p class="text-secondary small mb-0">Publicly auditable certificates signed with cryptographic SHA-256 tokens.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill font-mono small">
                        <i class="bi bi-patch-check-fill me-1"></i> {{ count($certificates) }} Verified Credentials
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">Program Title</th>
                                <th class="py-3">Certificate UUID</th>
                                <th class="py-3">Issued Date</th>
                                <th class="py-3">Recertification Status</th>
                                <th class="py-3">Verification</th>
                                <th class="py-3 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $cert)
                                <tr>
                                    <td class="fw-bold text-dark py-3">{{ $cert->course->title }}</td>
                                    <td class="font-mono text-muted small">{{ $cert->uuid }}</td>
                                    <td class="font-mono small">{{ $cert->issued_at->format('M d, Y') }}</td>
                                    <td class="font-mono small">
                                        {{ $cert->expires_at ? $cert->expires_at->format('M d, Y') : 'Permanent Validity' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $cert->isValid() ? 'badge-compliance' : 'badge-overdue' }}">
                                            {{ $cert->isValid() ? 'VALID & VERIFIED' : 'EXPIRED' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('certificates.verify', ['uuid' => $cert->uuid]) }}" target="_blank" class="tj-btn-outline py-1 px-2.5 small" title="View Public Verification">
                                                <i class="bi bi-qr-code-scan me-1"></i>
                                                <span>Verify</span>
                                            </a>
                                            <a href="{{ route('certificates.verify', ['uuid' => $cert->uuid]) }}?download=1" class="btn btn-sm btn-light border py-1 px-2.5" title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
