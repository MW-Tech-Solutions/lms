@extends('layouts.app')

@section('title', $school->name . ' — Cambridge Partner School Portal')
@section('meta_description', 'Official Cambridge Assessment revision & learning portal for students and faculty of ' . $school->name)

@section('content')
<!-- School Hero Banner -->
<div class="py-5 border-bottom position-relative overflow-hidden" 
     style="background: radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.12) 0%, rgba(2, 132, 199, 0.08) 50%, var(--lms-bg-body) 100%);">
    <div class="container py-3">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item text-muted">Schools</li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ $school->name }}</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3 py-1 font-mono fw-bold">
                        <i class="bi bi-buildings-fill text-warning me-1"></i> Cambridge Partner Institution
                    </span>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-mono">
                        <i class="bi bi-shield-check me-1"></i> Full School License
                    </span>
                </div>

                <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    {{ $school->name }}
                </h1>
                <p class="lead text-secondary fs-6 mb-4" style="line-height: 1.8; max-width: 720px;">
                    Welcome to the official <strong>{{ $school->name }}</strong> Cambridge Learning Portal. Students and faculty enrolled with our school receive full, unrestricted access to step-by-step worked solutions, past paper topical packs, and Esther Math Matrix analytics.
                </p>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    @guest
                        <a href="{{ $inviteUrl }}" class="tj-btn-primary py-2.5 px-4 shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i>
                            <span>Student Registration (School Access)</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                        </a>
                        <a href="{{ route('login') }}" class="tj-btn-outline py-2.5 px-4">
                            <span>Sign In to School Portal</span>
                        </a>
                    @else
                        <a href="{{ route('my-learning') }}" class="tj-btn-primary py-2.5 px-4 shadow-sm">
                            <i class="bi bi-speedometer2 me-1"></i>
                            <span>Launch My School Dashboard</span>
                        </a>
                    @endguest
                </div>
            </div>

            <!-- School Code & Fast Invite Card (Right) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-center" style="background: var(--lms-surface-card); border: 2px solid var(--lms-accent) !important;">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3 shadow-sm mx-auto"
                         style="width: 58px; height: 58px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">School Enrollment Code</h5>
                    <p class="small text-secondary mb-3">Auto-generated Corporate ID for students of {{ $school->name }}:</p>

                    <div class="p-3 rounded-3 mb-3 border font-mono fw-bold display-7" style="background: var(--lms-bg-secondary); color: var(--lms-accent);">
                        {{ $schoolCode }}
                    </div>

                    <button class="btn btn-outline-primary btn-sm rounded-pill w-100 py-2 fw-semibold" onclick="copySchoolLink('{{ $inviteUrl }}')">
                        <i class="bi bi-clipboard me-1"></i> Copy Direct Student Link
                    </button>
                    <div class="small text-muted mt-2 font-mono" style="font-size: 0.72rem;">
                        {{ $studentCount }} active student(s) enrolled
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Cambridge Levels for this School -->
<div class="py-5" style="background: var(--lms-bg-body);">
    <div class="container py-2">
        <div class="text-center mx-auto mb-5" style="max-width: 680px;">
            <span class="badge badge-purple-soft rounded-pill px-3 py-1 mb-2">
                <i class="bi bi-collection-fill me-1"></i> Included in School Licensure
            </span>
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading);">
                All Cambridge Levels Included
            </h2>
            <p class="text-secondary small">
                Unlike individual subscriptions that unlock one level at a time, your school plan provides students and faculty with access across the complete Cambridge syllabus continuum.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($categories as $cat)
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-all hover-translate" style="background: var(--lms-surface-card);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-primary mb-3" style="width: 44px; height: 44px; background: rgba(2, 132, 199, 0.1);">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">{{ $cat->name }}</h5>
                        <p class="small text-secondary mb-3">{{ $cat->description ?? 'Past papers, step-by-step solutions, and diagnostic test trackers.' }}</p>
                        <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                            <span class="small font-mono text-muted">{{ $cat->courses_count }} Syllabuses</span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">
                                <i class="bi bi-unlock-fill me-1"></i> Included
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Featured Question Packs & Courses -->
<div class="py-5 border-top" style="background: var(--lms-bg-secondary);">
    <div class="container py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">
                    <i class="bi bi-journal-bookmark text-primary me-2"></i>Curriculum &amp; Exam Materials
                </h3>
                <span class="small text-secondary">Browse Cambridge question packs and modules available for {{ $school->name }} students.</span>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 small">
                Explore Full Catalog <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($featuredCourses as $course)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: var(--lms-surface-card);">
                        <div class="p-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge badge-purple-soft rounded-pill px-2.5 py-1 small fw-bold">
                                    {{ $course->category?->name ?? 'Cambridge' }}
                                </span>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> School Access
                                </span>
                            </div>
                            <h5 class="fw-bold mb-2">
                                <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $course->title }}
                                </a>
                            </h5>
                            <p class="small text-secondary mb-3">{{ Str::limit($course->description, 100) }}</p>
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <span class="small text-muted font-mono"><i class="bi bi-qr-code me-1"></i>{{ $course->course_code }}</span>
                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    View Syllabus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">
                    No courses published yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function copySchoolLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('School Direct Registration Link Copied to Clipboard:\n\n' + url);
    }, function() {
        prompt('Copy student registration link:', url);
    });
}
</script>
@endsection
