@extends('layouts.app')

@section('title', $teacher->name . ' - Cambridge International Faculty Profile')
@section('meta_description', 'View Cambridge examiner profile, subject specializations, and curriculum courses taught by ' . $teacher->name . '.')

@section('content')
@php
    $profile = $teacher->profile;
    $avatar = (!empty($profile?->avatar_url) && !str_contains($profile->avatar_url, 'unsplash.com')) ? $profile->avatar_url : asset('images/profile-img.png');
    $jobTitle = $profile?->job_title ?? 'Cambridge Examiner & Curriculum Specialist';
    $bio = $profile?->bio ?? 'Accredited Cambridge International instructor dedicated to deep conceptual mastery, past paper dissection, and personalized diagnostic feedback for students worldwide.';
@endphp

<!-- Ambient Profile Hero Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%); color: #FFF;">
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 450px; height: 450px; background: radial-gradient(circle, rgba(114, 62, 195, 0.3) 0%, transparent 70%); filter: blur(60px); pointer-events: none;"></div>

    <div class="container position-relative py-3" style="z-index: 2;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mentors.index') }}" class="text-white-50 text-decoration-none">Mentors</a></li>
                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">{{ $teacher->name }}</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <!-- Avatar Frame -->
            <div class="col-md-auto text-center text-md-start">
                <div class="position-relative d-inline-block">
                    <img src="{{ $avatar }}" 
                         alt="{{ $teacher->name }}" 
                         class="rounded-4 shadow-lg object-fit-cover" 
                         style="width: 160px; height: 160px; border: 3px solid rgba(255, 255, 255, 0.25);">
                    <span class="position-absolute bottom-0 end-0 badge bg-success rounded-circle p-2 shadow" title="Verified Examiner">
                        <i class="bi bi-patch-check-fill fs-6"></i>
                    </span>
                </div>
            </div>

            <!-- Mentor Bio & Titles -->
            <div class="col-md">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase" 
                          style="background: rgba(114, 62, 195, 0.35); color: #C4B5FD; border: 1px solid rgba(196, 181, 253, 0.3); font-size: 0.74rem;">
                        <i class="bi bi-mortarboard-fill me-1 text-warning"></i> Cambridge Faculty Lead
                    </span>
                    <span class="badge bg-light text-dark font-mono px-2.5 py-1" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success me-1"></i>Verified Identity &amp; Credentials
                    </span>
                </div>

                <h1 class="display-6 fw-bold mb-1 text-white" style="font-family: var(--lms-font-heading); letter-spacing: -0.5px; color: #FFFFFF !important;">
                    {{ $teacher->name }}
                </h1>
                <p class="fs-6 mb-3 fw-semibold" style="color: #E2E8F0 !important;">
                    {{ $jobTitle }}
                </p>

                <div class="d-flex flex-wrap gap-3 small" style="color: #CBD5E1 !important;">
                    <div><i class="bi bi-envelope-fill me-1 text-info"></i> <span class="text-white">{{ $teacher->email }}</span></div>
                    @if($profile?->phone)
                        <div><i class="bi bi-telephone-fill me-1 text-success"></i> <span class="text-white">{{ $profile->phone }}</span></div>
                    @endif
                    <div><i class="bi bi-geo-alt-fill me-1 text-warning"></i> <span class="text-white">Cambridge Assessment International</span></div>
                </div>
            </div>

            <!-- Header Quick Stats -->
            <div class="col-lg-3 text-center text-lg-end">
                <div class="lms-card p-3 rounded-4 shadow-sm border border-light border-opacity-25" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px);">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="fs-4 fw-bold font-mono text-warning">{{ $totalCourses }}</div>
                            <div class="small text-white-50" style="font-size: 0.72rem;">Courses Taught</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-4 fw-bold font-mono text-success">{{ $totalStudents }}+</div>
                            <div class="small text-white-50" style="font-size: 0.72rem;">Scholars Mentored</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Body: Bio & Courses By Cambridge Level -->
<section class="py-5" style="background: var(--lms-bg-body); min-height: 60vh;">
    <div class="container py-2">
        <div class="row g-4">
            
            <!-- Left Column: Biography & Background Details -->
            <div class="col-lg-4">
                <div class="lms-card p-4 rounded-4 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>About Instructor
                    </h5>
                    <p class="text-secondary small mb-4" style="line-height: 1.8; font-size: 0.9rem;">
                        {{ $profile?->about_instructor ?: $bio }}
                    </p>

                    <h6 class="fw-bold small text-uppercase font-mono text-muted mb-2">Teaching Methodology</h6>
                    @if(!empty($profile?->teaching_methodology))
                        <div class="text-secondary small mb-4" style="line-height: 1.7; font-size: 0.88rem; white-space: pre-line;">
                            {{ $profile->teaching_methodology }}
                        </div>
                    @else
                        <ul class="list-unstyled small vstack gap-2 text-secondary mb-4">
                            <li><i class="bi bi-check2-circle text-success me-2"></i>Official Cambridge mark scheme breakdowns</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i>Topical diagnostic quizzes &amp; anti-cheat assessments</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i>Real-time step-by-step formula derivations</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i>Personalized learner progress tracking</li>
                        </ul>
                    @endif

                    <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        <div class="small fw-bold text-dark mb-1"><i class="bi bi-award text-warning me-1"></i>Accreditation Tier &amp; Qualifications</div>
                        <div class="small text-muted">{{ $profile?->qualifications ?: 'Senior Cambridge Examiner • Category 1 Syllabus Moderator' }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Courses Grouped by Cambridge Levels -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            Curriculum &amp; Courses by Cambridge Level
                        </h4>
                        <span class="text-muted small">Explore complete course offerings separated by academic level</span>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-2 font-mono">
                        {{ $totalCourses }} Total Courses
                    </span>
                </div>

                @if($coursesByLevel->isEmpty())
                    <div class="lms-card p-5 text-center shadow-sm rounded-4">
                        <i class="bi bi-journal-x fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-secondary">No courses published yet</h5>
                        <p class="text-muted small mb-0">This instructor is currently preparing curriculum modules for upcoming terms.</p>
                    </div>
                @else
                    @foreach($coursesByLevel as $levelName => $courses)
                        <div class="mb-5">
                            <!-- Level Header Ribbon -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3 border shadow-xs" 
                                 style="background: var(--lms-surface-card); border-left: 4px solid var(--lms-accent) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-2 p-1.5 px-2 text-white" style="background: var(--lms-accent); font-size: 0.85rem;">
                                        <i class="bi bi-layers-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                            {{ $levelName }}
                                        </h5>
                                        <span class="text-muted small" style="font-size: 0.75rem;">
                                            {{ $courses->count() }} {{ Str::plural('Course', $courses->count()) }} Available in this Level
                                        </span>
                                    </div>
                                </div>
                                <span class="badge bg-light text-primary border font-mono small">
                                    Level Silo
                                </span>
                            </div>

                            <!-- Courses Grid in this Level -->
                            <div class="row g-3">
                                @foreach($courses as $course)
                                    <div class="col-md-6">
                                        <div class="lms-card h-100 p-3 rounded-4 shadow-sm border transition-all d-flex flex-column justify-content-between"
                                             style="background: var(--lms-surface-card); border-color: var(--lms-border) !important;">
                                            <div>
                                                <!-- Course Thumbnail & Unique Code -->
                                                <div class="position-relative rounded-3 overflow-hidden mb-3" style="height: 140px; background: #0F172A;">
                                                    @if($course->thumbnailUrl())
                                                        <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="w-100 h-100 object-fit-cover">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1E1B4B, #312E81);">
                                                            <i class="bi bi-mortarboard fs-1 text-white-50"></i>
                                                        </div>
                                                    @endif

                                                    <!-- Unique Teacher Course Code Badge -->
                                                    <span class="position-absolute top-2 start-2 badge bg-dark bg-opacity-80 text-white font-mono px-2 py-1 shadow-sm" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                        <i class="bi bi-qr-code me-1 text-warning"></i>{{ $course->course_code }}
                                                    </span>

                                                    <span class="position-absolute bottom-2 end-2 badge bg-primary text-white font-mono px-2 py-0.5" style="font-size: 0.68rem;">
                                                        {{ $course->format }}
                                                    </span>
                                                </div>

                                                <!-- Course Title -->
                                                <h6 class="fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); line-height: 1.4;">
                                                    <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-reset">
                                                        {{ $course->title }}
                                                    </a>
                                                </h6>

                                                <p class="text-secondary small mb-3 line-clamp-2" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ $course->description ?? 'Cambridge curriculum modules featuring worked topical papers and verified assessment rubrics.' }}
                                                </p>
                                            </div>

                                            <!-- Card Bottom Action -->
                                            <div class="pt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--lms-border) !important;">
                                                <div>
                                                    <span class="text-muted small" style="font-size: 0.72rem;">Enrollment</span>
                                                    <div class="fw-bold font-mono text-dark" style="font-size: 0.88rem;">
                                                        @if($course->price_cents > 0)
                                                            ${{ number_format($course->price_cents / 100, 2) }}
                                                        @else
                                                            <span class="text-success">Complimentary</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-primary btn-sm rounded-3 py-1.5 px-3 fw-semibold">
                                                    View Course <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>
</section>
@endsection
