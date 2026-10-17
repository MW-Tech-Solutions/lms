@extends('layouts.admin')

@section('title', $school->name . ' - School Console')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Row -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.schools.index') }}">Schools</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $school->name }}</li>
                </ol>
            </nav>
            <h2 class="display-7 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-buildings-fill text-warning me-2"></i>{{ $school->name }} — School Console
            </h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3 py-1 font-mono fw-bold">
                    School Code: {{ $school->school_code }}
                </span>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-mono">
                    {{ $school->subscription_status }}
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $portalUrl }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>View School Portal</span>
            </a>
            <button class="btn btn-primary rounded-pill px-3 py-2 d-flex align-items-center gap-2" onclick="copyInviteLink('{{ $inviteUrl }}')">
                <i class="bi bi-clipboard-check"></i>
                <span>Copy Student Invite Link</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Shareable Cohort Link Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.08), rgba(114, 62, 195, 0.08)); border-left: 5px solid var(--lms-accent) !important;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">
                        <i class="bi bi-link-45deg text-primary me-1"></i> Dedicated Student Registration Link
                    </h5>
                    <p class="small text-secondary mb-2">
                        Share this URL with students, parents, or teachers of <strong>{{ $school->name }}</strong>. When they register using this link, they are automatically placed into this school's cohort with full access to all Cambridge study materials.
                    </p>
                    <div class="input-group rounded-3 overflow-hidden border">
                        <input type="text" class="form-control font-mono fs-6 bg-white" readonly value="{{ $inviteUrl }}" id="schoolInviteInput">
                        <button class="btn btn-primary px-4 fw-semibold" onclick="copyInviteLink('{{ $inviteUrl }}')">
                            <i class="bi bi-copy me-1"></i> Copy Link
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="p-3 bg-white rounded-3 shadow-xs border d-inline-block text-start">
                        <div class="small text-muted fw-bold mb-1">AUTO-GENERATED SCHOOL CODE</div>
                        <div class="display-6 font-mono fw-bold text-primary">{{ $school->school_code }}</div>
                        <div class="small text-secondary mt-1">Full All-Levels Institutional Access</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-bold">Active Students</span>
                    <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold font-mono text-dark">{{ count($students) }}</div>
                <div class="small text-muted mt-1">Enrolled across all 5 Cambridge levels</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-bold">Faculty Teachers</span>
                    <div class="rounded-3 p-2 text-white" style="background: linear-gradient(135deg, #723EC3, #9333EA);">
                        <i class="bi bi-person-video3 fs-5"></i>
                    </div>
                </div>
                <div class="display-5 fw-bold font-mono text-dark">{{ count($teachers) }}</div>
                <div class="small text-muted mt-1">Teaching &amp; assignment authors</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--lms-surface-card);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-bold">School Portal Slug</span>
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-globe fs-5"></i>
                    </div>
                </div>
                <div class="h4 fw-bold font-mono text-dark mt-1 text-truncate">/schools/{{ $school->slug }}</div>
                <div class="small text-muted mt-1">Public branded landing portal</div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students and Teachers in this School -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--lms-surface-card);">
        <div class="card-header bg-transparent border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-person-lines-fill text-primary me-2"></i>School Enrolled Roster ({{ count($students) }} Students, {{ count($teachers) }} Teachers)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small font-mono" style="font-size: 0.75rem;">
                    <tr>
                        <th class="ps-4">Member Name</th>
                        <th>Email</th>
                        <th>Student / Faculty ID</th>
                        <th>Persona Role</th>
                        <th>Joined Date</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($school->users as $u)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $u->name }}</div>
                            </td>
                            <td class="font-mono small text-muted">{{ $u->email }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark font-mono px-2.5 py-1">
                                    {{ $u->employee_id ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($u->isTeacher())
                                    <span class="badge bg-purple-subtle text-purple rounded-pill px-2.5 py-1 font-mono" style="background: rgba(114, 62, 195, 0.12); color: #723EC3;">
                                        Teacher
                                    </span>
                                @elseif($u->user_type === 'EMPLOYEE')
                                    <span class="badge bg-warning-subtle text-dark rounded-pill px-2.5 py-1 font-mono">
                                        School Staff
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 font-mono">
                                        Student Scholar
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 font-mono">
                                    ACTIVE
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No students or staff have registered under this school code yet. Send them the link above!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyInviteLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('School Student Invite Link Copied!\n\n' + url + '\n\nSend this to students to register directly into your school.');
    }, function() {
        prompt('Copy student invite link:', url);
    });
}
</script>
@endsection
