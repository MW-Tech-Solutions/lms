@extends('layouts.app')

@section('title', 'About AllThingsCambridge')

@section('content')
<!-- Hero Header -->
<div class="py-5 border-bottom position-relative overflow-hidden" style="background: radial-gradient(circle at 85% 15%, rgba(114, 62, 195, 0.08) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-3">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">About Us</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-xs border mb-3 bg-white">
                    <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 small" style="font-size: 0.65rem;">MISSION</span>
                    <span class="small fw-bold text-dark">PIONEERING CAMBRIDGE EXCELLENCE</span>
                </div>
                <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    About AllThingsCambridge
                </h1>
                <p class="lead text-secondary fs-5 mb-0" style="line-height: 1.8;">
                    <strong>AllThingsCambridge</strong> is a comprehensive study platform built for students who want to excel in their exams &mdash; from lower secondary all the way to advanced level.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="p-4 rounded-4 shadow-sm border bg-white d-inline-block text-start" style="max-width: 320px;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Cambridge Curricula</div>
                            <div class="small text-muted">Checkpoint to A-Level</div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Structured revision notes, examiner-approved mark schemes, and adaptive testing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Mission Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="lms-card p-4 p-md-5 border shadow-sm rounded-4 bg-white h-100">
                <h3 class="fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    <i class="bi bi-compass-fill text-primary me-2"></i>Our Mission
                </h3>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.8;">
                    Our mission is to make high-quality study materials accessible to every student, no matter where they are. We provide structured notes, curated past papers, topical questions, and interactive flashcards to help you study smarter and score higher.
                </p>
                <div class="row g-3 pt-2">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-check2-circle text-success me-1.5"></i> Accessible Everywhere</div>
                            <div class="small text-muted">Available 24/7 on desktop, tablet, and mobile devices.</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-graph-up-arrow text-primary me-1.5"></i> Score Smarter</div>
                            <div class="small text-muted">Direct syllabus alignment and Esther Math matrices.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="lms-card p-4 p-md-5 border shadow-sm rounded-4 h-100 text-white position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);">
                <div class="position-absolute top-0 end-0 p-4 opacity-10">
                    <i class="bi bi-award-fill" style="font-size: 8rem;"></i>
                </div>
                <h4 class="fw-bold text-white mb-3" style="font-family: var(--lms-font-heading);">
                    Curriculum Scope
                </h4>
                <p class="small text-white-50 mb-4" style="line-height: 1.7;">
                    From Cambridge Primary Checkpoint, Lower Secondary, IGCSE, through to AS &amp; A Levels, our pedagogical framework guarantees comprehensive revision mastery.
                </p>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-4 small text-light">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cambridge Primary Checkpoint (Years 1-6)</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cambridge Lower Secondary (Years 7-9)</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cambridge IGCSE &amp; O Level (Years 10-11)</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Cambridge International AS &amp; A Level (Years 12-13)</li>
                </ul>
                <a href="{{ route('courses.index') }}" class="tj-btn-primary px-4 py-2 small d-inline-flex align-items-center">
                    <span>Explore Syllabuses</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- What We Offer -->
    <div class="mb-5">
        <div class="text-center mb-4">
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                What We Offer
            </h2>
            <p class="text-secondary small mb-0">Built ground-up with targeted tools engineered for Cambridge exam candidates.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-primary mb-3" style="width: 50px; height: 50px; background: rgba(2, 132, 199, 0.1);">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Revision Notes for Major Subjects</h5>
                    <p class="text-secondary small mb-0">
                        In-depth, concise revision notes meticulously tailored for Cambridge Mathematics, Sciences, Business, and Computer Science.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-success mb-3" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1);">
                        <i class="bi bi-file-earmark-check fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Past Papers with Mark Schemes</h5>
                    <p class="text-secondary small mb-0">
                        Extensive repository of past Cambridge exam papers, examiner reports, and detailed worked solutions across all seasons.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-warning mb-3" style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1);">
                        <i class="bi bi-collection fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Topical Question Banks</h5>
                    <p class="text-secondary small mb-0">
                        Categorized topical problem sets and quizzes allowing students to isolate weak chapters and master individual syllabus objectives.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-info mb-3" style="width: 50px; height: 50px; background: rgba(6, 182, 212, 0.1);">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Spaced-Repetition Flashcards</h5>
                    <p class="text-secondary small mb-0">
                        Interactive spaced-repetition flashcards designed to optimize memory retention for definitions, formulae, and key terms.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-danger mb-3" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.1);">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Community of Students &amp; Teachers</h5>
                    <p class="text-secondary small mb-0">
                        A collaborative learning network bringing together Cambridge instructors, verified tutors, and students worldwide.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white h-100 transition-all hover-shadow">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-purple mb-3" style="width: 50px; height: 50px; background: rgba(114, 62, 195, 0.1);">
                        <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Esther Math Performance Matrix</h5>
                    <p class="text-secondary small mb-0">
                        Dynamic diagnostic RAG matrix tracking student performance, predicting grade thresholds, and highlighting areas needing review.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Team Section -->
    <div class="p-4 p-md-5 border rounded-4 shadow-sm bg-white text-center" style="background: radial-gradient(circle at 50% 10%, rgba(2, 132, 199, 0.04) 0%, #FFFFFF 100%);">
        <div class="d-inline-flex align-items-center justify-content-center text-primary mb-3">
            <i class="bi bi-award fs-1"></i>
        </div>
        <h3 class="fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
            Our Team
        </h3>
        <p class="text-secondary fs-6 mx-auto mb-4" style="max-width: 680px; line-height: 1.8;">
            We are a team of graduates and educators passionate about education technology. If you'd like to get in touch, use our <a href="{{ route('contact') }}" class="fw-bold text-primary text-decoration-none hover-underline">contact page</a>.
        </p>
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
            <a href="{{ route('contact') }}" class="tj-btn-primary px-4 py-2.5">
                <i class="bi bi-envelope-fill me-1.5"></i> Get in Touch with Us
            </a>
            <a href="{{ route('courses.index') }}" class="tj-btn-outline px-4 py-2.5">
                Browse Past Papers
            </a>
        </div>
    </div>
</div>
@endsection
