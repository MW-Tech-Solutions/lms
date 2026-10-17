@extends('layouts.admin')

@section('title', 'Teacher Approvals & Directory')

@section('content')
<div class="container-fluid px-4 py-4" x-data="{ liveSearch: '{{ addslashes($search) }}' }">

    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.users.index') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Users & CBAC
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Teacher Management</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-person-badge-fill text-primary me-2"></i> Teacher Approvals & Directory
            </h1>
            <p class="text-secondary small mb-0">
                Review, filter, approve, or reject Cambridge instructors and tutor applications.
            </p>
        </div>

        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#newTeacherModal">
            <i class="bi bi-plus-lg"></i>
            <span>Register New Teacher</span>
        </button>
    </div>

    <!-- Filter Nav Tabs & Status Counts -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--lms-surface-card);">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                
                <!-- Status Pills -->
                <ul class="nav nav-pills gap-1">
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $status === 'ALL' ? 'active' : '' }}" 
                           href="{{ route('admin.teachers.index', ['status' => 'ALL', 'search' => $search, 'sort' => $sortBy]) }}">
                            All Teachers <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $counts['all'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $status === 'APPROVED' ? 'active text-white' : '' }}" 
                           style="{{ $status === 'APPROVED' ? 'background: #059669;' : '' }}"
                           href="{{ route('admin.teachers.index', ['status' => 'APPROVED', 'search' => $search, 'sort' => $sortBy]) }}">
                            Approved <span class="badge bg-success-subtle text-success ms-1">{{ $counts['approved'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $status === 'PENDING' ? 'active text-dark' : '' }}" 
                           style="{{ $status === 'PENDING' ? 'background: #F59E0B;' : '' }}"
                           href="{{ route('admin.teachers.index', ['status' => 'PENDING', 'search' => $search, 'sort' => $sortBy]) }}">
                            Pending <span class="badge bg-warning-subtle text-warning ms-1">{{ $counts['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $status === 'UNAPPROVED' ? 'active' : '' }}" 
                           href="{{ route('admin.teachers.index', ['status' => 'UNAPPROVED', 'search' => $search, 'sort' => $sortBy]) }}">
                            Unapproved <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $counts['unapproved'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1.5 px-3 rounded-pill fw-semibold {{ $status === 'REJECTED' ? 'active' : '' }}" 
                           style="{{ $status === 'REJECTED' ? 'background: #DC2626;' : '' }}"
                           href="{{ route('admin.teachers.index', ['status' => 'REJECTED', 'search' => $search, 'sort' => $sortBy]) }}">
                            Rejected <span class="badge bg-danger-subtle text-danger ms-1">{{ $counts['rejected'] }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Search and Sort Form -->
                <form method="GET" action="{{ route('admin.teachers.index') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group input-group-sm" style="width: 240px;">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" x-model="liveSearch" class="form-control border-start-0" placeholder="Search teacher...">
                    </div>
                    
                    <select name="sort" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ $sortBy === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                    @if($search !== '' || $status !== 'ALL')
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-link text-muted p-0 ms-1 text-decoration-none">Clear</a>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <!-- Teachers Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Instructor / Mentor</th>
                        <th class="py-3">Specialization &amp; Bio</th>
                        <th class="py-3">Role &amp; Scope</th>
                        <th class="py-3 text-center">Approval Status</th>
                        <th class="py-3">Registered</th>
                        <th class="pe-4 py-3 text-end">Status Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr x-show="!liveSearch || '{{ strtolower(addslashes($teacher->name . ' ' . $teacher->email . ' ' . ($teacher->profile?->job_title ?? '') . ' ' . ($teacher->approval_status ?? ''))) }}'.includes(liveSearch.toLowerCase().trim())">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden flex-shrink-0 border shadow-xs" 
                                         style="width: 44px; height: 44px; background: #EEF2F6;">
                                        <img src="{{ !empty($teacher->profile?->avatar_url) && !str_contains($teacher->profile->avatar_url, 'unsplash.com') ? $teacher->profile->avatar_url : asset('images/profile-img.png') }}" 
                                             alt="{{ $teacher->name }}" 
                                             class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: var(--lms-text-primary); font-size: 0.92rem;">
                                            {{ $teacher->name }}
                                        </div>
                                        <div class="text-secondary small font-mono" style="font-size: 0.76rem;">
                                            {{ $teacher->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold text-body-emphasis small">
                                    {{ $teacher->profile?->job_title ?? 'Cambridge Specialist Tutor' }}
                                </div>
                                <div class="text-secondary small text-truncate" style="max-width: 240px; font-size: 0.76rem;">
                                    {{ $teacher->profile?->about_instructor ?: ($teacher->profile?->bio ?? 'Verified Cambridge curriculum syllabus creator.') }}
                                </div>
                                @if(!empty($teacher->profile?->teaching_methodology))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-1" style="font-size: 0.65rem;">
                                        <i class="bi bi-patch-check me-1"></i> KYC: Methodology Provided
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill mt-1" style="font-size: 0.65rem;">
                                        <i class="bi bi-clock me-1"></i> KYC: Methodology Pending
                                    </span>
                                @endif
                            </td>

                            <td class="py-3">
                                @forelse($teacher->roleAssignments as $ra)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small">
                                        {{ $ra->role?->name ?? 'TEACHER' }}
                                    </span>
                                @empty
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small">EMPLOYEE</span>
                                @endforelse
                            </td>

                            <td class="py-3 text-center">
                                @switch($teacher->approval_status ?? 'APPROVED')
                                    @case('APPROVED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="bi bi-check-circle-fill me-1"></i> Approved
                                        </span>
                                        @break
                                    @case('PENDING')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <i class="bi bi-clock-fill me-1"></i> Pending
                                        </span>
                                        @break
                                    @case('UNAPPROVED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(100, 116, 139, 0.12); color: #475569; border: 1px solid rgba(100, 116, 139, 0.3);">
                                            <i class="bi bi-dash-circle-fill me-1"></i> Unapproved
                                        </span>
                                        @break
                                    @case('REJECTED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                        </span>
                                        @if($teacher->rejection_reason)
                                            <div class="small text-danger mt-1 text-truncate" style="max-width: 130px; font-size: 0.68rem;" title="{{ $teacher->rejection_reason }}">
                                                <i class="bi bi-info-circle me-1"></i> {{ Str::limit($teacher->rejection_reason, 20) }}
                                            </div>
                                        @endif
                                        @break
                                    @default
                                        <span class="badge bg-secondary rounded-pill">{{ $teacher->approval_status }}</span>
                                @endswitch
                            </td>

                            <td class="py-3 text-secondary small">
                                {{ $teacher->created_at ? $teacher->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Change Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="approval_status" value="APPROVED">
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-success">
                                                    <i class="bi bi-check-circle"></i> Set as Approved
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="approval_status" value="PENDING">
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-warning">
                                                    <i class="bi bi-clock"></i> Set as Pending
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="approval_status" value="UNAPPROVED">
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-secondary">
                                                    <i class="bi bi-dash-circle"></i> Set as Unapproved
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $teacher->id }}">
                                                <i class="bi bi-x-circle"></i> Reject with Reason...
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                                No teachers found matching this criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-secondary">
                    Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} instructors
                </div>
                <div>
                    {{ $teachers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Register New Teacher -->
<div class="modal fade" id="newTeacherModal" tabindex="-1" aria-labelledby="newTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.teachers.store') }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="newTeacherModalLabel">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i> Register New Teacher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Arthur Pendelton" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="instructor@cambridge.co.uk" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Temporary Password *</label>
                    <input type="password" name="password" class="form-control" value="Password123!" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Specialization / Job Title</label>
                    <input type="text" name="job_title" class="form-control" placeholder="e.g. Senior Cambridge IGCSE Math Examiner">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Avatar Image URL</label>
                    <input type="url" name="avatar_url" class="form-control" placeholder="https://images.unsplash.com/photo-...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Initial Approval Status *</label>
                    <select name="approval_status" class="form-select" required>
                        <option value="PENDING" selected>Pending (Awaiting Review)</option>
                        <option value="APPROVED">Approved (Instant Access)</option>
                        <option value="UNAPPROVED">Unapproved (Incomplete Application)</option>
                        <option value="REJECTED">Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">All About Instructor *</label>
                    <textarea name="about_instructor" class="form-control" rows="2" placeholder="Comprehensive background, examiner accreditation, and subject history..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Teaching Methodology *</label>
                    <textarea name="teaching_methodology" class="form-control" rows="2" placeholder="Describe pedagogical approaches, mark scheme dissections, and diagnostic strategies..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Register Teacher</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals: Teacher Rejection Reason (Cleanly isolated from table DOM) -->
@foreach($teachers as $teacher)
<div class="modal fade" id="rejectModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}" class="modal-content border-0 shadow-lg rounded-4 text-start">
            @csrf
            @method('PATCH')
            <input type="hidden" name="approval_status" value="REJECTED">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill mb-1">Registration Compliance Rejection</span>
                    <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        Reject {{ $teacher->name }}
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="small text-secondary mb-3">
                    Please specify the non-compliance rationale. This reason will be stored in the audit trail and emailed to <strong class="text-dark">{{ $teacher->email }}</strong> along with an authentication link.
                </p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Reason for Non-Compliance / Rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Incomplete credentials, unverified accreditation, or syllabus mismatch."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                    <i class="bi bi-x-circle me-1"></i> Confirm &amp; Dispatch Rejection
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
