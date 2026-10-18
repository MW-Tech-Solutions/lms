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

        @if(!$course->is_published)
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" style="background: rgba(245, 158, 11, 0.15); border-left: 5px solid #F59E0B !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Staff / Faculty Preview Mode</h6>
                        <span class="small text-secondary">This course/study material is currently pending administrator approval and is not visible to public students.</span>
                    </div>
                </div>
                <span class="badge bg-warning text-dark font-mono px-3 py-2 rounded-pill">UNPUBLISHED PREVIEW</span>
            </div>
        @endif

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
                    <span class="badge bg-primary-subtle text-primary border px-2.5 py-1.5 font-mono fw-bold" style="font-size: 0.78rem;">
                        <i class="bi bi-qr-code me-1"></i>{{ $course->course_code }}
                    </span>
                </div>

                <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    {{ $course->title }}
                </h1>
                <p class="lead text-secondary fs-6 mb-4" style="line-height: 1.8;">
                    Structured modular training program engineered with SCORM interoperability, weighted assessments, and official verifiable certifications.
                </p>

                <!-- Instructor & Meta Row -->
                <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4 pt-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                             style="width: 36px; height: 36px; background: var(--lms-accent);">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-dark">{{ $course->instructor_title }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Lead Educator</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1 text-warning small fw-bold border-sm-start ps-sm-3 ps-md-4">
                        <i class="bi bi-star-fill"></i>
                        <span class="text-dark">5.0</span>
                        <span class="text-muted fw-normal">({{ $course->enrolled_count > 0 ? $course->enrolled_count . ' Enrolled' : 'Cambridge Verified' }})</span>
                    </div>

                    <div class="border-sm-start ps-sm-3 ps-md-4 small text-secondary">
                        <i class="bi bi-clock text-primary me-1"></i> {{ ucfirst(strtolower($course->format)) }}
                    </div>

                    <div class="border-sm-start ps-sm-3 ps-md-4 small text-secondary">
                        <i class="bi bi-patch-check text-success me-1"></i> Min Pass: {{ $course->passing_grade }}%
                    </div>
                </div>
            </div>
            @if($course->thumbnailUrl())
                <div class="col-lg-4 text-center text-lg-end">
                    <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="img-fluid rounded-4 shadow-lg object-fit-cover w-100" style="max-height: 220px; border: 2px solid rgba(255,255,255,0.2);">
                </div>
            @endif
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Course Overview & Syllabus (Left) -->
        <div class="col-lg-8">
            <!-- Unpaid / Guest Free Preview Section -->
            @if(!$isEnrolled)
                <div class="lms-card p-4 mb-4 rounded-4 shadow-sm border border-primary-subtle" style="background: radial-gradient(circle at 10% 10%, rgba(59, 130, 246, 0.05) 0%, var(--lms-surface-card) 100%);">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-bold text-white small">
                                <i class="bi bi-unlock-fill me-1"></i> Free Public Preview
                            </span>
                            <span class="text-muted small">No payment required for this overview</span>
                        </div>
                        <a href="{{ route('pricing') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-stars me-1"></i> Unlock Full Course &amp; Subscriptions
                        </a>
                    </div>

                    @if(!empty($course->public_preview_content))
                        <div class="p-3.5 rounded-3 bg-white border mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.8; white-space: pre-line;">
                            {{ $course->public_preview_content }}
                        </div>
                    @else
                        <div class="p-3.5 rounded-3 bg-white border mb-3 text-secondary" style="font-size: 0.92rem; line-height: 1.7;">
                            <p class="mb-2"><strong>Welcome to {{ $course->title }} Free Overview!</strong></p>
                            <p class="mb-1 text-muted">This Cambridge preparatory program provides extensive step-by-step video lectures, verified mark scheme walkthroughs, and diagnostic revision trackers.</p>
                            <p class="mb-0 text-muted">Visitors can preview syllabus topics below. Full video solutions, worked worksheets, and past paper question packs are reserved for active subscribers.</p>
                        </div>
                    @endif

                    <!-- Paywall Notice & Upgrade Callout -->
                    <div class="p-3 rounded-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.4);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill text-warning fs-4"></i>
                            <div>
                                <div class="fw-bold small text-dark">Locked Behind Cambridge Membership</div>
                                <div class="text-muted small" style="font-size: 0.78rem;">Subscribe in British Pounds (£) or enroll individually to gain instant access to all full modules.</div>
                            </div>
                        </div>
                        <a href="{{ route('pricing') }}" class="tj-btn-primary py-2 px-3 small text-nowrap flex-shrink-0">
                            <i class="bi bi-currency-pound me-1"></i> View Plans from £49
                        </a>
                    </div>
                </div>
            @endif

            <!-- Course Lecture Video & Teacher Materials Section -->
            @if($course->video_url || $course->material_file || $course->material_notes)
                <div class="lms-card p-4 p-md-5 mb-5 rounded-4 shadow-sm border">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                <i class="bi bi-collection-play-fill text-primary me-2"></i>Course Lecture &amp; Learning Materials
                            </h4>
                            <div class="text-muted small">Official curriculum multimedia and downloadable syllabus materials uploaded by the instructor.</div>
                        </div>
                        @if($course->material_file)
                            <div class="d-inline-flex align-items-center gap-2 py-1.5 px-3 rounded-pill bg-light border text-muted small">
                                <i class="bi bi-shield-lock-fill text-primary"></i>
                                <span>Curriculum Text Protected</span>
                            </div>
                        @endif
                    </div>

                    @if($course->video_url)
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-2" style="letter-spacing: 0.5px;">
                                <i class="bi bi-play-circle-fill text-success me-1"></i> Course Video Lecture
                            </label>
                            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm bg-dark position-relative course-video-container" oncontextmenu="return false;">
                                @if($course->isVideoEmbed())
                                    <iframe src="{{ $course->videoEmbedUrl() }}" 
                                            title="{{ $course->title }}" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                            allowfullscreen 
                                            class="w-100 h-100 border-0"></iframe>
                                @else
                                    <video controls 
                                           controlsList="nodownload noplaybackrate" 
                                           disablePictureInPicture 
                                           oncontextmenu="return false;"
                                           class="w-100 h-100" 
                                           poster="{{ $course->thumbnailUrl() }}">
                                        <source src="{{ $course->videoUrl() }}" type="video/mp4">
                                        Your browser does not support HTML5 video.
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(!empty($course->extracted_material_html))
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label small fw-bold text-uppercase text-muted mb-0" style="letter-spacing: 0.5px;">
                                    <i class="bi bi-journal-richtext text-primary me-1"></i> Interactive Course Curriculum &amp; Study Text
                                </label>
                                <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill px-2.5 py-1 small">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Read-Only In-System
                                </span>
                            </div>
                            <div class="course-extracted-content p-4 p-md-5 rounded-4 border bg-white shadow-sm user-select-none" 
                                 oncontextmenu="return false;" 
                                 oncopy="return false;" 
                                 oncut="return false;" 
                                 ondragstart="return false;"
                                 style="-webkit-user-select: none; -ms-user-select: none; user-select: none;">
                                {!! $course->extracted_material_html !!}
                            </div>
                        </div>
                    @endif

                    @if($course->material_notes)
                        <div>
                            <label class="form-label small fw-bold text-uppercase text-muted mb-2" style="letter-spacing: 0.5px;">
                                <i class="bi bi-card-text text-primary me-1"></i> Syllabus Study Notes &amp; Reading Material
                            </label>
                            <div class="p-4 rounded-3 border bg-light text-secondary" 
                                 oncontextmenu="return false;" 
                                 style="font-size: 1rem; line-height: 1.8; white-space: pre-line; -webkit-user-select: none; user-select: none;">
                                {{ $course->material_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

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
                                                <span class="curriculum-sidebar-icon">
                                                    @switch($mod->module_type)
                                                        @case('VIDEO')
                                                            <i class="bi bi-play-circle-fill"></i>
                                                            @break
                                                        @case('SCORM')
                                                            <i class="bi bi-box-seam-fill"></i>
                                                            @break
                                                        @case('QUIZ')
                                                            <i class="bi bi-patch-question-fill"></i>
                                                            @break
                                                        @case('ASSIGNMENT')
                                                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                                            @break
                                                        @case('ATTENDANCE')
                                                            <i class="bi bi-qr-code"></i>
                                                            @break
                                                        @case('LESSON')
                                                            <i class="bi bi-diagram-3-fill"></i>
                                                            @break
                                                        @case('FEEDBACK')
                                                            <i class="bi bi-chat-left-heart-fill"></i>
                                                            @break
                                                        @case('CHOICE')
                                                            <i class="bi bi-ui-radios-grid"></i>
                                                            @break
                                                        @case('FORUM')
                                                            <i class="bi bi-chat-square-quote-fill"></i>
                                                            @break
                                                        @case('H5P')
                                                            <i class="bi bi-badge-hd-fill"></i>
                                                            @break
                                                        @case('WORKSHOP')
                                                            <i class="bi bi-people-fill"></i>
                                                            @break
                                                        @case('CHAT')
                                                            <i class="bi bi-chat-dots-fill"></i>
                                                            @break
                                                        @case('GLOSSARY')
                                                            <i class="bi bi-spellcheck"></i>
                                                            @break
                                                        @case('WIKI')
                                                            <i class="bi bi-book-half"></i>
                                                            @break
                                                        @case('PAGE')
                                                            <i class="bi bi-file-earmark-richtext-fill"></i>
                                                            @break
                                                        @case('BOOK')
                                                            <i class="bi bi-journal-bookmark-fill"></i>
                                                            @break
                                                        @case('FILE')
                                                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                                            @break
                                                        @case('FOLDER')
                                                            <i class="bi bi-folder2-open"></i>
                                                            @break
                                                        @case('URL')
                                                            <i class="bi bi-link-45deg"></i>
                                                            @break
                                                        @case('LABEL')
                                                            <i class="bi bi-card-text"></i>
                                                            @break
                                                        @default
                                                            <i class="bi bi-file-text-fill"></i>
                                                    @endswitch
                                                </span>
                                                <div>
                                                    <div class="fw-semibold small text-dark">{{ $mod->title ?? ($mod->module_type . ' Activity') }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $mod->module_type }} &bull; Self-paced interactive completion</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($mod->is_mandatory)
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">Mandatory</span>
                                                @endif
                                                @if(!$isEnrolled)
                                                    <span class="badge bg-secondary-subtle text-muted rounded-pill px-2.5 py-1 small d-flex align-items-center gap-1">
                                                        <i class="bi bi-lock-fill"></i> Locked
                                                    </span>
                                                @else
                                                    <a href="{{ route('learning.player', ['course' => $course->slug]) }}" class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small text-decoration-none">
                                                        <i class="bi bi-play-fill"></i> Access
                                                    </a>
                                                @endif
                                            </div>
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
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="badge badge-purple-soft rounded-pill px-3 py-1 small fw-bold">Enrollment Fee</span>
                        @if($course->special_pricing_tag)
                            <span class="badge rounded-pill px-2.5 py-1 small fw-bold font-mono text-white shadow-xs" style="background: linear-gradient(135deg, #723EC3, #2563EB);">
                                <i class="bi bi-tag-fill me-1"></i> {{ $course->special_pricing_tag }}
                            </span>
                        @endif
                    </div>
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
                        <a href="{{ route('login', ['redirect' => url()->full()]) }}" class="tj-btn-primary w-100 py-3 mb-2 justify-content-center shadow">
                            <span>Sign In to Enroll</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                        </a>
                        <div class="small text-muted">Don't have an account? <a href="{{ route('register', ['redirect' => url()->full()]) }}" class="text-primary fw-semibold">Register Now</a></div>
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
