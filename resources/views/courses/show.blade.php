@extends('layouts.app')

@section('title', $course->title)

@section('content')
<!-- Course Header with Ambient Gradient -->
<div class="py-5 border-bottom position-relative overflow-hidden" style="background: radial-gradient(circle at 90% 10%, rgba(114, 62, 195, 0.08) 0%, rgba(255, 242, 229, 0.3) 50%, var(--lms-bg-body) 100%);">
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">Courses</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ Str::limit($course->title, 40) }}</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="mb-3">
                    <span class="badge badge-purple-soft rounded-pill px-3 py-1 small fw-bold">
                        {{ $course->category?->name ?? 'Program' }}
                    </span>
                    @if($course->target_audience === 'CORPORATE')
                        <span class="badge badge-compliance rounded-pill px-3 py-1 ms-1 small fw-bold">
                            <i class="bi bi-shield-check me-1"></i> Mandatory Corporate Track
                        </span>
                    @else
                        <span class="badge-popular ms-1 small shadow-sm">
                            <i class="bi bi-fire me-1"></i> Public Academic
                        </span>
                    @endif
                </div>

                <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    {{ $course->title }}
                </h1>
                <p class="lead text-secondary fs-6 mb-4" style="line-height: 1.8;">
                    Structured modular training program engineered with SCORM interoperability, weighted assessments, and official verifiable certifications.
                </p>

                <!-- Instructor & Meta Row -->
                <div class="d-flex flex-wrap align-items-center gap-4 pt-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                             style="width: 36px; height: 36px; background: var(--lms-accent);">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-dark">{{ $course->target_audience === 'CORPORATE' ? 'Corporate Compliance Board' : 'Senior Cloud Architect' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Lead Instructor</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1 text-warning small fw-bold border-start ps-4">
                        <i class="bi bi-star-fill"></i>
                        <span class="text-dark">4.9</span>
                        <span class="text-muted fw-normal">(1.4k Reviews)</span>
                    </div>

                    <div class="border-start ps-4 small text-secondary">
                        <i class="bi bi-clock text-primary me-1"></i> {{ ucfirst(strtolower($course->format)) }}
                    </div>

                    <div class="border-start ps-4 small text-secondary">
                        <i class="bi bi-patch-check text-success me-1"></i> Min Pass: {{ $course->passing_grade }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Course Overview & Syllabus (Left) -->
        <div class="col-lg-8">
            <!-- Curriculum Sections Accordion -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading);">
                    <i class="bi bi-journal-text text-primary me-2"></i>Curriculum Syllabus
                </h4>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">
                    {{ count($course->sections) }} Sections
                </span>
            </div>

            <div class="accordion mb-5" id="courseSyllabus">
                @forelse($course->sections as $sIdx => $sec)
                    <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden" style="background: var(--lms-surface-card);">
                        <h2 class="accordion-header" id="secHeading{{ $sec->id }}">
                            <button class="accordion-button py-3 px-4 {{ $sIdx === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#secCollapse{{ $sec->id }}" style="background: var(--lms-bg-secondary);">
                                <div>
                                    <span class="small text-muted fw-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Section {{ $sIdx + 1 }}</span>
                                    <span class="fw-bold fs-6" style="font-family: var(--lms-font-heading);">{{ $sec->title }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="secCollapse{{ $sec->id }}" class="accordion-collapse collapse {{ $sIdx === 0 ? 'show' : '' }}" data-bs-parent="#courseSyllabus">
                            <div class="accordion-body p-4">
                                <ul class="list-group list-group-flush">
                                    @foreach($sec->modules as $mod)
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 py-3 border-bottom">
                                            <div class="d-flex align-items-center gap-3">
                                                @switch($mod->module_type)
                                                    @case('VIDEO')
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-rose); color: #DC2626;"><i class="bi bi-play-circle-fill"></i></span>
                                                        @break
                                                    @case('SCORM')
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-peach); color: #FF4D00;"><i class="bi bi-box-seam-fill"></i></span>
                                                        @break
                                                    @case('QUIZ')
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-cyan); color: #0284C7;"><i class="bi bi-patch-question-fill"></i></span>
                                                        @break
                                                    @case('ASSIGNMENT')
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-purple); color: var(--lms-accent);"><i class="bi bi-file-earmark-arrow-up-fill"></i></span>
                                                        @break
                                                    @case('ATTENDANCE')
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-mint); color: #059669;"><i class="bi bi-qr-code"></i></span>
                                                        @break
                                                    @default
                                                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-purple); color: var(--lms-accent);"><i class="bi bi-file-text-fill"></i></span>
                                                @endswitch
                                                <div>
                                                    <div class="fw-semibold small text-dark">{{ $mod->module_type }} Activity (Module {{ $mod->sort_order + 1 }})</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">Self-paced interactive completion</div>
                                                </div>
                                            </div>
                                            @if($mod->is_mandatory)
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">Mandatory</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info rounded-4">Syllabus modules are currently being finalized.</div>
                @endforelse
            </div>
        </div>

        <!-- Checkout & Enrollment Card (Right) -->
        <div class="col-lg-4">
            <div class="lms-card p-4 p-lg-5 shadow-lg sticky-top border-0 rounded-4" style="top: 100px; background: var(--lms-surface-card);">
                <div class="mb-3">
                    <span class="badge badge-purple-soft rounded-pill px-3 py-1 small fw-bold">Enrollment Fee</span>
                    <div class="display-5 fw-bold font-mono mt-2" style="color: var(--lms-accent);">
                        {{ $course->formattedPrice() }}
                    </div>
                </div>

                <hr class="my-4">

                @auth
                    @if($isEnrolled)
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-4">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div class="fw-medium">You are actively enrolled in this program!</div>
                        </div>
                        <a href="{{ route('learning.player', ['course' => $course->slug]) }}" class="tj-btn-primary w-100 py-3 justify-content-center shadow">
                            <i class="bi bi-play-circle-fill me-2"></i> Launch Learning Player
                        </a>
                    @else
                        <!-- Enrollment or Multi-currency Checkout -->
                        <form action="{{ route('enrollment.store', ['course' => $course->id]) }}" method="POST">
                            @csrf
                            @if($course->price_cents > 0)
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-secondary">Select Payment Gateway</label>
                                    <div class="d-flex gap-2">
                                        <label class="btn btn-outline-primary btn-sm flex-grow-1 rounded-pill">
                                            <input type="radio" name="payment_gateway" value="STRIPE" checked>
                                            <i class="bi bi-credit-card me-1"></i> Stripe
                                        </label>
                                        <label class="btn btn-outline-primary btn-sm flex-grow-1 rounded-pill">
                                            <input type="radio" name="payment_gateway" value="PAYPAL">
                                            <i class="bi bi-paypal me-1"></i> PayPal
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="tj-btn-primary w-100 py-3 justify-content-center shadow">
                                <i class="bi bi-check-circle me-2"></i> {{ $course->price_cents > 0 ? 'Pay & Instant Enroll' : 'Claim Free Enrollment' }}
                            </button>
                        </form>
                    @endif
                @else
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="tj-btn-primary w-100 py-3 mb-2 justify-content-center shadow">
                            <span>Sign In to Enroll</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                        </a>
                        <div class="small text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-semibold">Register Now</a></div>
                    </div>
                @endauth

                <div class="mt-4 pt-3 border-top small text-secondary">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        <span>Includes official certificate of completion</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-phone text-primary"></i>
                        <span>Accessible on desktop, tablet, and mobile</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
