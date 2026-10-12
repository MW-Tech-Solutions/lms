@extends('layouts.app')

@section('title', $site_settings['nav_courses'] ?? 'Course Catalog')

@section('content')
<!-- Header Banner with Ambient Gradient -->
<div class="py-5 border-bottom position-relative overflow-hidden" style="background: radial-gradient(circle at 90% 10%, rgba(114, 62, 195, 0.08) 0%, rgba(255, 242, 229, 0.3) 50%, var(--lms-bg-body) 100%);">
    <div class="container py-2">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Course Catalog</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    {{ $site_settings['catalog_header'] ?? 'Explore All Learning Tracks' }}
                </h1>
                <p class="lead text-secondary fs-6 mb-0">
                    {{ $site_settings['catalog_subhead'] ?? 'Find mandatory corporate compliance tracks, automated annual recertifications, and accredited public academic degrees.' }}
                </p>
            </div>
            <div class="col-lg-5">
                <!-- Search & Audience Filter Box -->
                <form action="{{ route('courses.index') }}" method="GET" class="lms-card p-3 border-0 shadow-sm" style="background: var(--lms-surface-card);">
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search title, skills..." value="{{ request('search') }}">
                    </div>
                    <div class="d-flex gap-2">
                        <select name="audience" class="form-select form-select-sm">
                            <option value="">All Audiences</option>
                            <option value="CORPORATE" {{ request('audience') === 'CORPORATE' ? 'selected' : '' }}>Corporate Workforce</option>
                            <option value="PUBLIC" {{ request('audience') === 'PUBLIC' ? 'selected' : '' }}>Public Academic</option>
                        </select>
                        <button type="submit" class="tj-btn-primary py-1 px-3 small">
                            <span>Filter</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Courses Grid -->
<div class="container py-5">
    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="lms-card lms-card-interactive h-100 d-flex flex-column border-0 shadow-sm" style="background: var(--lms-surface-card);">
                    <!-- Thumbnail Zoom Container -->
                    <div class="course-thumb-wrap position-relative">
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" 
                             style="background: {{ $course->target_audience === 'CORPORATE' ? 'linear-gradient(135deg, #0F172A, #1E293B)' : 'linear-gradient(135deg, #723EC3, #2563EB)' }}; min-height: 180px;">
                            <div class="text-center p-3">
                                <i class="bi {{ $course->target_audience === 'CORPORATE' ? 'bi-shield-check fs-1 text-warning' : 'bi-mortarboard-fill fs-1 text-light' }} d-block mb-1"></i>
                                <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                    {{ $course->category?->name ?? 'Enterprise Program' }}
                                </span>
                            </div>
                        </div>

                        <!-- Ribbon Badges -->
                        <div class="position-absolute top-0 start-0 m-3">
                            @if($course->target_audience === 'CORPORATE')
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill small shadow-sm">
                                    <i class="bi bi-building me-1"></i> Corporate Track
                                </span>
                            @else
                                <span class="badge-popular shadow-sm">
                                    <i class="bi bi-fire me-1"></i> Popular
                                </span>
                            @endif
                        </div>

                        @if($course->recertification_days)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small">
                                    <i class="bi bi-arrow-repeat me-1 text-warning"></i> {{ $course->recertification_days }}d Recert
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <!-- Instructor Row -->
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                                     style="width: 28px; height: 28px; background: var(--lms-accent);">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="small fw-semibold text-secondary">
                                    {{ $course->target_audience === 'CORPORATE' ? 'Chief Compliance Officer' : 'Senior Cloud Architect' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                                <i class="bi bi-star-fill"></i>
                                <span class="text-dark">4.9</span>
                                <span class="text-muted fw-normal">(1.4k)</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h5 class="fw-bold mb-2 flex-grow-1" style="font-family: var(--lms-font-heading); line-height: 1.4;">
                            <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-reset">
                                {{ $course->title }}
                            </a>
                        </h5>

                        <!-- Meta Chips -->
                        <div class="d-flex align-items-center justify-content-between text-muted small my-3 py-2 px-3 rounded-3" 
                             style="background: var(--lms-bg-secondary);">
                            <span><i class="bi bi-journal-text me-1 text-primary"></i> 24 Lessons</span>
                            <span><i class="bi bi-clock me-1 text-primary"></i> 8h 45m</span>
                            <span><i class="bi bi-people me-1 text-primary"></i> 1.8k Enrolled</span>
                        </div>

                        <!-- Price & Action CTA -->
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-auto">
                            <div>
                                @if($course->price_cents > 0)
                                    <span class="text-decoration-line-through text-muted small me-1">$69.00</span>
                                    <span class="fs-4 fw-bold font-mono" style="color: var(--lms-accent);">
                                        {{ $course->formattedPrice() }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                        Included in Corporate Plan
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course->slug) }}" class="tj-btn-primary py-2 px-3 small">
                                <span>{{ $site_settings['label_enroll'] ?? 'Enroll Now' }}</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-search display-3 d-block mb-3 text-secondary opacity-50"></i>
                <h4 class="fw-bold" style="font-family: var(--lms-font-heading);">No courses matched your query.</h4>
                <p class="text-muted">Try adjusting your search terms or audience filter criteria.</p>
                <a href="{{ route('courses.index') }}" class="tj-btn-outline mt-2">Clear All Filters</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
