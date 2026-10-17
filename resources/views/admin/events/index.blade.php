@extends('layouts.admin')

@section('title', 'Upcoming Live Events & Spot Reservations')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Academic Operations</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-calendar-event-fill text-primary me-2"></i> Cambridge Live Events &amp; Spot Reservations
            </h1>
            <p class="text-secondary small mb-0">
                Manage live review sessions, activate/deactivate events, and approve student spot reservations with automated confirmation emails.
            </p>
        </div>

        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#newEventModal">
            <i class="bi bi-plus-lg"></i>
            <span>Schedule New Live Event</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Events</div>
                        <div class="h4 fw-bold mb-0 text-primary">{{ $events->total() }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Active Live Sessions</div>
                        <div class="h4 fw-bold mb-0 text-success">{{ $events->where('is_active', true)->count() }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-broadcast fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Reservations</div>
                        <div class="h4 fw-bold mb-0 text-info">{{ $reservations->total() }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Approvals</div>
                        <div class="h4 fw-bold mb-0 text-warning">{{ $reservations->where('status', 'PENDING')->count() }}</div>
                    </div>
                    <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Live Events Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-broadcast text-primary"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">Scheduled Cambridge Live Masterclasses</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary">{{ $events->total() }} Masterclasses</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Event Title &amp; Level</th>
                        <th class="py-3">Date &amp; Time</th>
                        <th class="py-3">Instructor</th>
                        <th class="py-3 text-center">Capacity / Booked</th>
                        <th class="py-3 text-center">Active Status</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-body-emphasis" style="font-size: 0.92rem;">
                                    {{ $event->title }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small" style="font-size: 0.70rem;">
                                        {{ $event->level_badge }}
                                    </span>
                                    @if($event->meeting_link)
                                        <a href="{{ $event->meeting_link }}" target="_blank" class="small text-decoration-none text-muted">
                                            <i class="bi bi-link-45deg"></i> Link Attached
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold small">
                                    <i class="bi bi-calendar-check text-primary me-1"></i>
                                    {{ $event->formatted_date }}
                                </div>
                                <div class="text-secondary small font-mono" style="font-size: 0.75rem;">
                                    {{ $event->duration_minutes }} minutes
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold text-body-emphasis small">
                                    {{ $event->instructor_name }}
                                </div>
                                <div class="text-secondary small" style="font-size: 0.74rem;">
                                    {{ $event->instructor_role }}
                                </div>
                            </td>

                            <td class="py-3 text-center">
                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold {{ $event->remaining_seats > 10 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $event->reserved_count }} / {{ $event->max_capacity }} booked
                                </span>
                                <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">
                                    {{ $event->remaining_seats }} spots open
                                </div>
                            </td>

                            <td class="py-3 text-center">
                                <form method="POST" action="{{ route('admin.events.toggle', $event->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $event->is_active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size: 0.75rem;">
                                        <i class="bi {{ $event->is_active ? 'bi-check-circle-fill' : 'bi-dash-circle' }} me-1"></i>
                                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEventModal{{ $event->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                No live events currently scheduled. Click above to add your first masterclass!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Student Spot Reservations Table & Email Approvals -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: var(--lms-bg-secondary);">
            <div>
                <span class="fw-bold fs-6" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    <i class="bi bi-inbox-fill text-primary me-2"></i> Student Spot Reservations &amp; Approval Dispatch
                </span>
                <span class="text-muted small ms-2 d-none d-sm-inline">Approve reservations to instantly send meeting links via email.</span>
            </div>

            <!-- Filter Status -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.events.index', ['reservation_status' => 'ALL']) }}" class="btn btn-sm rounded-pill px-3 {{ $reservationStatus === 'ALL' ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('admin.events.index', ['reservation_status' => 'PENDING']) }}" class="btn btn-sm rounded-pill px-3 {{ $reservationStatus === 'PENDING' ? 'btn-warning' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('admin.events.index', ['reservation_status' => 'CONFIRMED']) }}" class="btn btn-sm rounded-pill px-3 {{ $reservationStatus === 'CONFIRMED' ? 'btn-success' : 'btn-outline-secondary' }}">Confirmed</a>
                <a href="{{ route('admin.events.index', ['reservation_status' => 'REJECTED']) }}" class="btn btn-sm rounded-pill px-3 {{ $reservationStatus === 'REJECTED' ? 'btn-danger' : 'btn-outline-secondary' }}">Rejected</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Applicant Name &amp; Contact</th>
                        <th class="py-3">Reserved Masterclass</th>
                        <th class="py-3">Reserved At</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center">Email Dispatch</th>
                        <th class="pe-4 py-3 text-end">Review Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold" style="color: var(--lms-text-primary); font-size: 0.92rem;">
                                    {{ $reservation->name }}
                                </div>
                                <div class="text-secondary small font-mono" style="font-size: 0.76rem;">
                                    <i class="bi bi-envelope me-1"></i>{{ $reservation->email }}
                                    @if($reservation->phone)
                                        <span class="ms-2"><i class="bi bi-telephone me-1"></i>{{ $reservation->phone }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold text-body-emphasis small">
                                    {{ $reservation->event?->title ?? 'Session' }}
                                </div>
                                <div class="text-secondary small" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $reservation->event?->formatted_date }}
                                </div>
                            </td>

                            <td class="py-3 text-secondary small">
                                {{ $reservation->created_at ? $reservation->created_at->format('M d, Y • h:i A') : 'N/A' }}
                            </td>

                            <td class="py-3 text-center">
                                @switch($reservation->status)
                                    @case('CONFIRMED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="bi bi-check-circle-fill me-1"></i> Confirmed
                                        </span>
                                        @break
                                    @case('PENDING')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <i class="bi bi-clock-fill me-1"></i> Pending
                                        </span>
                                        @break
                                    @case('REJECTED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                        </span>
                                        @break
                                @endswitch
                            </td>

                            <td class="py-3 text-center">
                                @if($reservation->approval_email_sent_at)
                                    <span class="text-success small fw-semibold">
                                        <i class="bi bi-send-check-fill me-1"></i> Sent {{ $reservation->approval_email_sent_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-muted small">Not sent yet</span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($reservation->status !== 'CONFIRMED')
                                        <form method="POST" action="{{ route('admin.events.reservations.status', $reservation->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="CONFIRMED">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.76rem;">
                                                <i class="bi bi-check-lg"></i>
                                                <span>Approve &amp; Send Mail</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if($reservation->status !== 'REJECTED')
                                        <form method="POST" action="{{ route('admin.events.reservations.status', $reservation->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="REJECTED">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.76rem;">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                No spot reservations found matching this status filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-secondary">
                    Showing {{ $reservations->firstItem() }} to {{ $reservations->lastItem() }} of {{ $reservations->total() }} reservations
                </div>
                <div>
                    {{ $reservations->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Schedule New Event -->
<div class="modal fade" id="newEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.events.store') }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calendar-plus text-primary me-2"></i> Schedule New Live Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Event Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Cambridge Pure Mathematics 1 Live Masterclass" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Cambridge Level / Badge *</label>
                        <input type="text" name="level_badge" class="form-control" value="Cambridge AS & A Level (9709)" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Course Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- General / Multi-Level --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label small fw-semibold">Date &amp; Time *</label>
                        <input type="datetime-local" name="event_date" class="form-control" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-semibold">Duration (Minutes) *</label>
                        <input type="number" name="duration_minutes" class="form-control" value="60" min="15" max="480" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Instructor Name *</label>
                        <input type="text" name="instructor_name" class="form-control" placeholder="e.g. Darren Treadway" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Instructor Role *</label>
                        <input type="text" name="instructor_role" class="form-control" placeholder="e.g. Pure Math Lead Examiner" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Zoom / Google Meet URL</label>
                    <input type="url" name="meeting_link" class="form-control" placeholder="https://zoom.us/j/...">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Max Seat Capacity *</label>
                        <input type="number" name="max_capacity" class="form-control" value="100" min="5" required>
                    </div>
                    <div class="col-6 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newActive" checked>
                            <label class="form-check-label small fw-semibold" for="newActive">Publish Immediately</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Description / Syllabus Scope</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Topics covered, past paper questions to be solved live..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Publish Live Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals: Edit Live Events (Cleanly isolated from table DOM) -->
@foreach($events as $event)
<div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.events.update', $event->id) }}" class="modal-content border-0 shadow-lg rounded-4 text-start">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Edit Live Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Event Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Level Badge *</label>
                    <input type="text" name="level_badge" class="form-control" value="{{ $event->level_badge }}" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label small fw-semibold">Date &amp; Time *</label>
                        <input type="datetime-local" name="event_date" class="form-control" value="{{ $event->event_date ? $event->event_date->format('Y-m-d\TH:i') : '' }}" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-semibold">Duration (Min)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="{{ $event->duration_minutes }}" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Instructor Name *</label>
                        <input type="text" name="instructor_name" class="form-control" value="{{ $event->instructor_name }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Instructor Role *</label>
                        <input type="text" name="instructor_role" class="form-control" value="{{ $event->instructor_role }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Zoom / Meeting URL</label>
                    <input type="url" name="meeting_link" class="form-control" value="{{ $event->meeting_link }}">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Max Capacity *</label>
                        <input type="number" name="max_capacity" class="form-control" value="{{ $event->max_capacity }}" required>
                    </div>
                    <div class="col-6 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $event->id }}" {{ $event->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="active{{ $event->id }}">Active on Homepage</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update Event</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
