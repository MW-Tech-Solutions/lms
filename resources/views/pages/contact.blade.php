@extends('layouts.app')

@section('title', 'Contact Us & Community')

@section('content')
<!-- Hero Header -->
<div class="py-5 border-bottom position-relative overflow-hidden" style="background: radial-gradient(circle at 85% 15%, rgba(2, 132, 199, 0.08) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-3">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Contact &amp; Support</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-xs border mb-3 bg-white">
                    <i class="bi bi-headset text-primary"></i>
                    <span class="small fw-bold text-dark">GET IN TOUCH</span>
                </div>
                <h1 class="display-5 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Contact Us
                </h1>
                <p class="lead text-secondary fs-5 mb-0" style="line-height: 1.8;">
                    Have a question or feedback? We'd love to hear from you.
                </p>
            </div>
            <div class="col-lg-4 text-start text-lg-end">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 font-mono small">
                    <i class="bi bi-clock-history me-1"></i> Typical response: Under 24h
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-4" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Message Sent Successfully!</h6>
                    <span class="small">{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-5">
        <!-- Left Column: Contact Form -->
        <div class="col-lg-7">
            <div class="lms-card p-4 p-md-5 border shadow-sm rounded-4 bg-white">
                <div class="mb-4">
                    <h3 class="fw-bold mb-1 text-dark" style="font-family: var(--lms-font-heading);">
                        Send Us a Request
                    </h3>
                    <p class="text-secondary small mb-0">
                        Fill out the inquiry form below. Our academic and technical support staff will receive your message and respond directly to your email address.
                    </p>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="name@example.com" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject / Syllabus Focus</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Cambridge IGCSE Math Worked Solutions question" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Your Message / Feedback <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control" required placeholder="Type your inquiry, feedback, or curriculum question here...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="tj-btn-primary px-4 py-2.5 shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                <span>Submit Request</span>
                            </button>
                            <span class="small text-muted ms-3">We never share your email with third parties.</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Contact Information & Community Info -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">
                
                <!-- Email Card -->
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; background: rgba(2, 132, 199, 0.1);">
                            <i class="bi bi-envelope-at-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Email Support</h5>
                            <div class="small text-muted">Direct academic &amp; technical inquiries</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="small text-muted mb-1">Official Support Desk:</div>
                        <a href="mailto:support@allthingscambridge.com" class="fw-bold font-mono text-primary text-decoration-none fs-6">
                            support@allthingscambridge.com
                        </a>
                    </div>
                </div>

                <!-- Community Card -->
                <div class="lms-card p-4 border shadow-sm rounded-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-chat-quote-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Community Forums</h5>
                            <div class="small text-muted">Collaborate with peers &amp; tutors</div>
                        </div>
                    </div>
                    <p class="small text-secondary mb-3" style="line-height: 1.7;">
                        Join the forums to ask questions, discuss tricky past-paper questions, share revision notes, and connect with fellow Cambridge candidates.
                    </p>
                    <a href="{{ route('courses.index') }}" class="tj-btn-outline w-100 justify-content-center py-2 text-decoration-none">
                        <i class="bi bi-people-fill me-1.5"></i>
                        <span>Visit Academic Discussion Forums</span>
                    </a>
                </div>

                <!-- FAQ Quick Assist -->
                <div class="p-4 rounded-4 border bg-white shadow-sm" style="background: linear-gradient(135deg, rgba(114, 62, 195, 0.04), rgba(2, 132, 199, 0.04));">
                    <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-question-circle text-primary"></i>
                        <span>Looking for Quick Answers?</span>
                    </div>
                    <p class="small text-secondary mb-3">
                        Check out our student FAQ section or explore the revision notes and past paper repository.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('about') }}" class="btn btn-sm btn-light border text-dark rounded-pill px-3">About Us</a>
                        <a href="{{ route('cms.show', ['slug' => 'pricing']) }}" class="btn btn-sm btn-light border text-dark rounded-pill px-3">Pricing (£)</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
