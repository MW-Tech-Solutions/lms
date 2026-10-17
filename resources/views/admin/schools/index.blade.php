@extends('layouts.admin')

@section('title', 'Institutional Schools & Cohorts - Admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Row -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Institutional Schools</li>
                </ol>
            </nav>
            <h2 class="display-7 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-buildings-fill text-warning me-2"></i>Institutional Schools &amp; Cohort Licensure
            </h2>
            <p class="text-secondary small mb-0">
                Manage Cambridge partner schools, auto-generated Corporate Employee codes, and custom student invite links.
            </p>
        </div>
        <button class="tj-btn-primary py-2 px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newSchoolModal">
            <i class="bi bi-plus-circle"></i>
            <span>Register New School</span>
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Schools Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small font-mono" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4">School / Institution</th>
                        <th>Dedicated School URL</th>
                        <th>Auto-Generated Invite Code</th>
                        <th>Students Enrolled</th>
                        <th>Faculty Teachers</th>
                        <th>Licensure Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0"
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: var(--lms-text-primary);">{{ $school->name }}</div>
                                        <div class="small text-muted font-mono" style="font-size: 0.75rem;">
                                            Admin: {{ $school->admin?->name ?? 'System Provisioned' }} ({{ $school->admin?->email ?? 'N/A' }})
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $schoolSlug = $school->slug ?: $school->subdomain;
                                @endphp
                                <a href="{{ route('schools.show', $schoolSlug) }}" target="_blank" class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1.5 text-decoration-none font-mono">
                                    /schools/{{ $schoolSlug }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3 py-1.5 font-mono fw-bold" style="font-size: 0.82rem;">
                                        {{ $school->school_code ?? 'ATC-EMP-' . substr(md5($school->id), 0, 5) }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5" 
                                            onclick="copyToClipboard('{{ url('/register?school_code=' . urlencode($school->school_code)) }}')"
                                            title="Copy Student Invite Link">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 font-mono fw-bold">
                                    {{ $school->students_count ?? 0 }} Students
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-purple-subtle text-purple rounded-pill px-2.5 py-1 font-mono fw-bold" style="background: rgba(114, 62, 195, 0.12); color: #723EC3;">
                                    {{ $school->teachers_count ?? 0 }} Teachers
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 font-mono {{ ($school->subscription_status ?? 'ACTIVE') === 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ $school->subscription_status ?? 'ACTIVE' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.schools.show', $school->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-speedometer2 me-1"></i> School Console
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                                <h6>No Institutional Schools Registered Yet</h6>
                                <p class="small text-secondary mb-3">Click below to enroll your first Cambridge Partner School or Tutoring Center.</p>
                                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#newSchoolModal">
                                    Register School
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schools->hasPages())
            <div class="p-3 border-top">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Register New School -->
<div class="modal fade" id="newSchoolModal" tabindex="-1" aria-labelledby="newSchoolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
            <div class="modal-header border-bottom p-4" style="background: var(--lms-bg-secondary);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-warning" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.15);">
                        <i class="bi bi-buildings-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="newSchoolModalLabel">Register Cambridge Partner School</h5>
                        <span class="small text-muted">Creates school portal, auto-generates Corporate Employee ID code, and sets up principal console.</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.schools.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 vstack gap-3">
                    <div>
                        <label class="form-label small fw-semibold text-secondary">School Official Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Mundra Model School" required>
                    </div>

                    <div>
                        <label class="form-label small fw-semibold text-secondary">School Portal URL Slug <span class="text-muted">(Optional)</span></label>
                        <div class="input-group rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-light text-muted border-0 small font-mono">/schools/</span>
                            <input type="text" name="slug" class="form-control border-0 ps-1 font-mono" placeholder="mundramodelschool">
                        </div>
                        <div class="form-text small text-muted">Example: allthingscambridge.co.uk/schools/mundramodelschool</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Principal / Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" class="form-control rounded-3" placeholder="Mr. David Mundra" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Contact Email <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control rounded-3" placeholder="principal@mundramodel.edu" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-semibold text-secondary">Custom School / Corporate Employee ID <span class="text-muted">(Leave empty to auto-generate)</span></label>
                        <div class="input-group rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-light text-muted border-0 font-mono small">ID CODE</span>
                            <input type="text" name="school_code" class="form-control border-0 font-mono text-uppercase" placeholder="Auto-generated e.g. ATC-EMP-22361">
                        </div>
                        <div class="form-text small text-muted">Students enter this code or click the invite link to unlock all Cambridge materials.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="background: var(--lms-bg-secondary);">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tj-btn-primary py-2 px-4">
                        <i class="bi bi-check2-circle me-1"></i> Register &amp; Generate School Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('School Invite Link copied to clipboard:\n' + text);
    }, function() {
        prompt('Copy this student invite link:', text);
    });
}
</script>
@endsection
