@extends('layouts.admin')

@section('title', 'Attendance Sessions & Rolling QR Geofencing')

@section('content')
<div class="container-fluid" x-data="{ showCreateModal: false }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_attendance_manager'] ?? 'Attendance & Rolling QR Geofencing' }}</h2>
            <div class="text-secondary small">
                @if($isTeacher)
                    Manage rolling QR attendance sessions for your assigned Cambridge classes and courses.
                @else
                    {{ $site_settings['subtitle_attendance_manager'] ?? 'Configure session timeframes, geofencing boundary radiuses, and dynamic rotating QR tokens.' }}
                @endif
            </div>
        </div>
        @if(!$isTeacher || $isApproved)
            <button class="tj-btn-primary py-2 px-3 small shadow-sm" @click="showCreateModal = true" data-bs-toggle="modal" data-bs-target="#createAttendanceModal">
                <i class="bi bi-plus-lg me-1"></i> Create Attendance Session
            </button>
        @else
            <button class="btn btn-secondary py-2 px-3 small shadow-sm opacity-50" disabled title="Pending Administrator Approval">
                <i class="bi bi-lock-fill me-1"></i> Awaiting Approval
            </button>
        @endif
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($isTeacher && !$isApproved)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 p-3 d-flex align-items-center gap-3">
            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
            <div>
                <strong>Faculty Account Pending Moderation:</strong> Your teacher credentials are currently being verified by the Cambridge Academic Administrator. Once approved, you can generate geofenced attendance sessions for your designated Cambridge level and classes.
            </div>
        </div>
    @endif

    <!-- Attendance Sessions Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check text-success me-2"></i>Attendance Sessions</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Session Date</th>
                        <th>Geofence Center</th>
                        <th>Radius</th>
                        <th>Attendees</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td class="fw-bold">{{ $session->title }}</td>
                            <td>{{ $session->course->title }}</td>
                            <td class="font-mono">{{ $session->session_date->format('Y-m-d') }}</td>
                            <td class="font-mono small">
                                @if($session->lat_boundary)
                                    {{ $session->lat_boundary }}, {{ $session->lng_boundary }}
                                @else
                                    <span class="text-muted">Unbounded</span>
                                @endif
                            </td>
                            <td class="font-mono">{{ $session->radius_meters }}m</td>
                            <td class="font-mono">{{ $session->records()->count() }} Recorded</td>
                            <td>
                                <a href="{{ route('attendance.qr', ['course_id' => $session->course_id, 'session_id' => $session->id]) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-qr-code me-1"></i> Open Rolling QR
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No attendance sessions registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Session Modal -->
    <div class="modal fade" id="createAttendanceModal" tabindex="-1" role="dialog" :class="{ 'show': showCreateModal }" :style="showCreateModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" x-cloak @click.self="showCreateModal = false" @keydown.escape.window="showCreateModal = false">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-2">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">New Geofenced Attendance Session</h5>
                    <button type="button" class="btn-close" @click="showCreateModal = false" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Course Program</label>
                            <select name="course_id" class="form-select" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Session Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="Lecture Hall B - Week 4 Hands-on Lab">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Session Date</label>
                            <input type="date" name="session_date" class="form-control font-mono" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Latitude</label>
                                <input type="number" step="0.0000001" name="lat_boundary" class="form-control font-mono" placeholder="37.774929">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Longitude</label>
                                <input type="number" step="0.0000001" name="lng_boundary" class="form-control font-mono" placeholder="-122.419416">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Geofence Radius (Meters)</label>
                            <input type="number" name="radius_meters" class="form-control font-mono" value="50" min="5" max="5000">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
                        <button type="submit" class="btn btn-accent px-4">Generate Session &amp; Secret</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
