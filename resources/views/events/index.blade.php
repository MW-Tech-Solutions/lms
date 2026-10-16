@extends('layouts.app')

@section('title', 'Cambridge Live Masterclasses & Online Events')
@section('meta_description', 'Join certified Cambridge examiners and educators in real-time syllabus walkthroughs, specimen past paper clinics, and exam preparation masterclasses.')

@section('content')
<!-- Events Hero Section -->
<div class="py-5 border-bottom position-relative overflow-hidden" 
     style="background: radial-gradient(circle at 85% 15%, rgba(114, 62, 195, 0.12) 0%, rgba(255, 242, 229, 0.35) 50%, var(--lms-bg-body) 100%);">
    <div class="container py-3">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Upcoming Events</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge badge-purple-soft rounded-pill px-3 py-1.5 small fw-bold mb-2">
                    <i class="bi bi-broadcast me-1"></i> Interactive Live Clinics
                </span>
                <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Upcoming Cambridge Masterclasses &amp; Live Events
                </h1>
                <p class="lead text-secondary fs-6 mb-0" style="line-height: 1.8;">
                    Reserve your interactive seat with official Cambridge syllabus examiners. All sessions include live Q&amp;A, step-by-step mark scheme breakdowns, and downloadable diagnostic worksheets.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex align-items-center gap-3 p-3 rounded-4 shadow-sm bg-white border">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--lms-accent), #FF4D00);">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <div class="text-start">
                        <div class="fw-bold fs-5 text-dark font-mono">{{ $events->total() }} Sessions</div>
                        <div class="text-muted small">Live across 2026 Academic Series</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Level Filter Bar -->
<div class="py-4 border-bottom" style="background: var(--lms-bg-secondary);">
    <div class="container">
        <form method="GET" action="{{ route('events.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group search-input-group shadow-xs">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0 py-2.5" placeholder="Search by event title, tutor name, or subject...">
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <select name="level" class="form-select py-2.5 shadow-xs" onchange="this.form.submit()">
                    <option value="">-- All Cambridge Assessment Levels --</option>
                    <option value="Primary" {{ $level === 'Primary' ? 'selected' : '' }}>Cambridge Primary Checkpoint</option>
                    <option value="Secondary" {{ $level === 'Secondary' ? 'selected' : '' }}>Cambridge Lower Secondary</option>
                    <option value="IGCSE" {{ $level === 'IGCSE' ? 'selected' : '' }}>Cambridge IGCSE (Years 10–11)</option>
                    <option value="AS Level" {{ $level === 'AS Level' ? 'selected' : '' }}>Cambridge AS Level (Year 12)</option>
                    <option value="A Level" {{ $level === 'A Level' ? 'selected' : '' }}>Cambridge A Level (Year 13)</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-3 d-flex gap-2">
                <button type="submit" class="tj-btn-primary py-2.5 px-4 w-100 justify-content-center">
                    <span>Filter Sessions</span>
                </button>
                @if($search || $level)
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary py-2.5 px-3 rounded-pill" title="Reset Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Events Grid -->
<div class="container py-5">
    <div class="row g-4">
        @forelse($events as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column transition-all hover-lift" 
                     style="background: var(--lms-surface-card);">
                    
                    <!-- Card Top Accent Banner -->
                    <div class="p-3 d-flex align-items-center justify-content-between" style="background: rgba(114, 62, 195, 0.05); border-bottom: 1px solid var(--lms-border);">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                            {{ $event->level_badge }}
                        </span>
                        <div class="small fw-bold text-muted font-mono" style="font-size: 0.78rem;">
                            <i class="bi bi-people me-1 text-primary"></i> {{ $event->remaining_seats }} spots left
                        </div>
                    </div>

                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <!-- Date Badge -->
                        <div class="d-flex align-items-center gap-2 mb-2 text-secondary small font-mono">
                            <i class="bi bi-calendar-event text-danger"></i>
                            <span>{{ $event->formatted_date }}</span>
                        </div>

                        <!-- Title -->
                        <h3 class="h5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); line-height: 1.35;">
                            {{ $event->title }}
                        </h3>

                        <!-- Description Excerpt -->
                        <p class="text-secondary small mb-4 flex-grow-1" style="line-height: 1.65;">
                            {{ Str::limit($event->description ?? 'Comprehensive Cambridge syllabus walkthrough and examiner discussion for high-scoring strategies.', 120) }}
                        </p>

                        <!-- Speaker Info -->
                        <div class="d-flex align-items-center gap-3 p-2.5 rounded-3 mb-4 bg-light border">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 38px; height: 38px; background: linear-gradient(135deg, var(--lms-accent), #2563EB); font-size: 0.85rem;">
                                {{ substr($event->instructor_name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold small text-dark text-truncate">{{ $event->instructor_name }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $event->instructor_role }}</div>
                            </div>
                        </div>

                        <!-- Price & Reserve CTA -->
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                <div class="fw-bold text-success font-mono">{{ $event->formatted_fee }}</div>
                                <div class="text-muted" style="font-size: 0.70rem;">Interactive Access</div>
                            </div>
                            <button type="button" 
                                    class="tj-btn-primary py-2 px-3 small"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#reserveModal{{ $event->id }}">
                                <span>Reserve Spot</span>
                                <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reserve Spot Modal -->
                <div class="modal fade" id="reserveModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('events.reserve', $event->id) }}" class="modal-content border-0 shadow-lg rounded-4 text-start">
                            @csrf
                            <div class="modal-header border-bottom pb-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill mb-1 small">Cambridge Spot Reservation</span>
                                    <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                        {{ $event->title }}
                                    </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body py-3">
                                <div class="p-3 rounded-3 bg-light border mb-3 small">
                                    <div class="mb-1"><i class="bi bi-calendar-event text-danger me-2"></i><strong>Session Time:</strong> {{ $event->formatted_date }}</div>
                                    <div><i class="bi bi-person-video3 text-primary me-2"></i><strong>Examiner:</strong> {{ $event->instructor_name }} ({{ $event->instructor_role }})</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()?->name ?? '' }}" placeholder="Student or Guardian Name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Email for Session Link &amp; Materials <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ auth()->user()?->email ?? '' }}" placeholder="candidate@example.co.uk" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Phone / WhatsApp Number (Optional)</label>
                                    <input type="text" name="phone" class="form-control" placeholder="+44 7700 900077">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Specific Topics or Past Paper Questions to Cover</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Please discuss 2024 Oct/Nov Paper 4 Question 7 on calculus"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top pt-3">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="tj-btn-primary px-4 py-2">
                                    <i class="bi bi-check2-circle me-1"></i> Submit Spot Reservation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 rounded-4 border bg-white shadow-xs max-w-lg mx-auto" style="max-width: 500px;">
                    <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">No Live Sessions Found</h4>
                    <p class="text-secondary small mb-4">No events match your current search criteria. Please check back soon or reset your filters.</p>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                        Reset All Filters
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="mt-5 d-flex justify-content-center">
        {{ $events->links() }}
    </div>
</div>
@endsection
