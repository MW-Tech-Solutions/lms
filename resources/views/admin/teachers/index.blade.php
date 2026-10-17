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
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- View Details Modal Trigger Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 rounded-pill px-2.5" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewTeacherModal{{ $teacher->id }}"
                                            title="View Full Teacher Profile &amp; Credentials">
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-md-inline">View</span>
                                    </button>

                                    <!-- Status Change Dropdown -->
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-2.5" type="button" data-bs-toggle="dropdown">
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

<!-- Modals: Complete Teacher Details View & Status Actions -->
@foreach($teachers as $teacher)
<div class="modal fade" id="viewTeacherModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="viewTeacherModalLabel{{ $teacher->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-start" style="background: var(--lms-surface-card);">
            <!-- Modal Header with Ambient Gradient -->
            <div class="modal-header border-bottom p-4 position-relative" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.08) 0%, rgba(114, 62, 195, 0.08) 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle overflow-hidden border border-2 border-white shadow-sm flex-shrink-0" style="width: 56px; height: 56px; background: #EEF2F6;">
                        <img src="{{ !empty($teacher->profile?->avatar_url) && !str_contains($teacher->profile->avatar_url, 'unsplash.com') ? $teacher->profile->avatar_url : asset('images/profile-img.png') }}" 
                             alt="{{ $teacher->name }}" 
                             class="w-100 h-100 object-fit-cover">
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="modal-title fw-bold mb-0" id="viewTeacherModalLabel{{ $teacher->id }}" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                {{ $teacher->name }}
                            </h5>
                            @switch($teacher->approval_status ?? 'APPROVED')
                                @case('APPROVED')
                                    <span class="badge rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                    @break
                                @case('PENDING')
                                    <span class="badge rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(245, 158, 11, 0.15); color: #D97706;">
                                        <i class="bi bi-clock-fill me-1"></i> Pending Review
                                    </span>
                                    @break
                                @case('UNAPPROVED')
                                    <span class="badge rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(100, 116, 139, 0.15); color: #475569;">
                                        <i class="bi bi-dash-circle-fill me-1"></i> Unapproved
                                    </span>
                                    @break
                                @case('REJECTED')
                                    <span class="badge rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(239, 68, 68, 0.15); color: #DC2626;">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                    @break
                            @endswitch
                        </div>
                        <div class="small text-secondary font-mono d-flex flex-wrap align-items-center gap-2">
                            <span><i class="bi bi-envelope me-1"></i>{{ $teacher->email }}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-person-badge me-1"></i>{{ $teacher->employee_id ?? 'ATC-FAC-' . $teacher->id }}</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body: Comprehensive Teacher Dossier -->
            <div class="modal-body p-4 vstack gap-4">
                
                <!-- If rejected, display the rejection notice -->
                @if($teacher->approval_status === 'REJECTED' && !empty($teacher->rejection_reason))
                    <div class="alert alert-danger border-0 rounded-3 p-3 shadow-xs mb-0">
                        <div class="fw-bold small text-danger mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Non-Compliance / Rejection Rationale</div>
                        <div class="small text-dark">{{ $teacher->rejection_reason }}</div>
                    </div>
                @endif

                <!-- Section 1: Specialization & Bio -->
                <div>
                    <h6 class="fw-bold text-uppercase small text-secondary font-mono mb-2" style="letter-spacing: 0.5px;">
                        <i class="bi bi-person-vcard text-primary me-1"></i> Specialization &amp; Professional Background
                    </h6>
                    <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        <div class="fw-semibold text-dark mb-1">{{ $teacher->profile?->job_title ?? 'Cambridge Specialist Tutor' }}</div>
                        <div class="text-secondary small" style="line-height: 1.6;">
                            {{ $teacher->profile?->about_instructor ?: ($teacher->profile?->bio ?? 'No detailed background provided.') }}
                        </div>
                    </div>
                </div>

                <!-- Section 2: KYC & Teaching Methodology -->
                <div>
                    <h6 class="fw-bold text-uppercase small text-secondary font-mono mb-2" style="letter-spacing: 0.5px;">
                        <i class="bi bi-journal-check text-success me-1"></i> Teaching Methodology &amp; KYC Verification
                    </h6>
                    <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                        @if(!empty($teacher->profile?->teaching_methodology))
                            <div class="text-secondary small" style="line-height: 1.6; white-space: pre-line;">{{ $teacher->profile->teaching_methodology }}</div>
                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-check-circle me-1"></i> KYC Submitted
                                </span>
                                @if($teacher->profile->kyc_submitted_at)
                                    <span class="small text-muted font-mono" style="font-size: 0.7rem;">
                                        Submitted on {{ \Carbon\Carbon::parse($teacher->profile->kyc_submitted_at)->format('M d, Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="text-muted small fst-italic">
                                <i class="bi bi-clock me-1 text-warning"></i> Pedagogical methodology has not yet been submitted by this educator.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section 3: Authorized Cambridge Teaching Levels & Department -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase small text-secondary font-mono mb-2" style="letter-spacing: 0.5px;">
                            <i class="bi bi-diagram-3-fill text-warning me-1"></i> Authorized Cambridge Levels
                        </h6>
                        <div class="p-3 rounded-3 border h-100" style="background: var(--lms-bg-secondary);">
                            <div class="d-flex flex-wrap gap-1.5">
                                @forelse($teacher->teachingLevels as $tl)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 small font-mono">
                                        {{ $tl->name }}
                                    </span>
                                @empty
                                    @if($teacher->department)
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 small font-mono">
                                            {{ $teacher->department->name }} (Primary)
                                        </span>
                                    @else
                                        <span class="text-muted small fst-italic">No Cambridge levels currently assigned</span>
                                    @endif
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase small text-secondary font-mono mb-2" style="letter-spacing: 0.5px;">
                            <i class="bi bi-journal-bookmark-fill text-info me-1"></i> Assigned Courses &amp; Modules
                        </h6>
                        <div class="p-3 rounded-3 border h-100" style="background: var(--lms-bg-secondary);">
                            <div class="fw-bold font-mono h4 mb-0 text-dark">{{ $teacher->courses->count() }}</div>
                            <span class="small text-muted">Course(s) authored and assigned to this instructor</span>
                        </div>
                    </div>
                </div>

                <!-- Section 4: System Roles & Audit Metadata -->
                <div class="p-3 rounded-3 border bg-light d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="small text-secondary">
                        <span class="fw-bold text-dark">CBAC Persona:</span>
                        @foreach($teacher->roleAssignments as $ra)
                            <span class="badge bg-primary-subtle text-primary rounded-pill ms-1">{{ $ra->role?->name ?? 'TEACHER' }}</span>
                        @endforeach
                    </div>
                    <div class="small text-muted font-mono" style="font-size: 0.75rem;">
                        Registered: {{ $teacher->created_at ? $teacher->created_at->format('M d, Y h:i A') : 'N/A' }}
                    </div>
                </div>

            </div>

            <!-- Modal Footer: Action Buttons (Approve, Pending, Unapprove, Reject) -->
            <div class="modal-footer border-top p-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--lms-bg-secondary);">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                    Close
                </button>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Set Approved -->
                    @if($teacher->approval_status !== 'APPROVED')
                        <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="approval_status" value="APPROVED">
                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Approve Teacher
                            </button>
                        </form>
                    @endif

                    <!-- Set Pending -->
                    @if($teacher->approval_status !== 'PENDING')
                        <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="approval_status" value="PENDING">
                            <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark">
                                <i class="bi bi-clock me-1"></i> Mark Pending
                            </button>
                        </form>
                    @endif

                    <!-- Set Unapproved -->
                    @if($teacher->approval_status !== 'UNAPPROVED')
                        <form method="POST" action="{{ route('admin.teachers.status', $teacher->id) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="approval_status" value="UNAPPROVED">
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-dash-circle me-1"></i> Mark Unapproved
                            </button>
                        </form>
                    @endif

                    <!-- Trigger Reject Modal -->
                    @if($teacher->approval_status !== 'REJECTED')
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold"
                                data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $teacher->id }}">
                            <i class="bi bi-x-circle me-1"></i> Reject...
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Teacher Rejection Reason -->
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
