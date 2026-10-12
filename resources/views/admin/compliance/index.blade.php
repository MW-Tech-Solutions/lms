@extends('layouts.admin')

@section('title', 'Corporate Compliance & Recertification Audit')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_compliance_audit'] ?? 'Compliance & Annual Recertification Management' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_compliance_audit'] ?? 'Monitor mandatory corporate training cycles, recertification expirations, and manager sign-offs.' }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.compliance.export-csv') }}" class="btn btn-outline-success btn-sm shadow-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Auditor CSV
            </a>
            <form action="{{ route('admin.compliance.trigger-scan') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm shadow-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-repeat"></i> Scan &amp; Enforce Recertification
                </button>
            </form>
        </div>
    </div>

    <!-- Active Compliance Tracks Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-success me-2"></i>Mandatory Corporate Compliance Programs</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Program Title</th>
                        <th>Target Department</th>
                        <th>Recertification Cycle</th>
                        <th>Passing Threshold</th>
                        <th>Enrolled Workforce</th>
                        <th>Compliance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complianceCourses as $course)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none">
                                    {{ $course->title }}
                                </a>
                            </td>
                            <td>{{ $course->category?->name ?? 'All Departments' }}</td>
                            <td>
                                <span class="badge badge-amber font-mono">
                                    <i class="bi bi-arrow-repeat me-1"></i> {{ $course->recertification_days ?? 365 }} Days Loop
                                </span>
                            </td>
                            <td class="font-mono">{{ $course->passing_grade }}% Required</td>
                            <td class="font-mono">{{ $course->enrollments()->count() }} Active</td>
                            <td>
                                <span class="badge badge-compliance">MANDATORY</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No corporate compliance programs currently configured.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Employee Recertification Roster -->
    <div class="lms-card p-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Workforce Compliance &amp; Recertification Roster</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Program</th>
                        <th>Status</th>
                        <th>Recertification Due</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $enrollment->user->name }}</div>
                                <div class="small text-muted">{{ $enrollment->user->email }}</div>
                            </td>
                            <td class="font-mono small">{{ $enrollment->user->employee_id ?? 'EMP-' . $enrollment->user->id }}</td>
                            <td>{{ $enrollment->user->department?->name ?? 'Corporate Staff' }}</td>
                            <td class="fw-semibold">{{ $enrollment->course->title }}</td>
                            <td>
                                @if($enrollment->status === 'COMPLETED')
                                    <span class="badge badge-compliance">CERTIFIED</span>
                                @elseif($enrollment->status === 'EXPIRED')
                                    <span class="badge badge-overdue">RECERT OVERDUE</span>
                                @else
                                    <span class="badge badge-amber">IN PROGRESS</span>
                                @endif
                            </td>
                            <td class="font-mono small">
                                @if($enrollment->expires_at)
                                    <span class="{{ $enrollment->isExpired() ? 'text-danger fw-bold' : 'text-secondary' }}">
                                        {{ $enrollment->expires_at->format('M d, Y') }}
                                        ({{ $enrollment->expires_at->diffForHumans() }})
                                    </span>
                                @else
                                    <span class="text-muted">Permanent</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.compliance.signoff', ['id' => $enrollment->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Manager Compliance Sign-Off">
                                        <i class="bi bi-pen me-1"></i> Sign Off
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No employee enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
