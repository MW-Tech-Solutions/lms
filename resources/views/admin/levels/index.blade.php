@extends('layouts.admin')

@section('title', 'Cambridge Levels & Syllabus Upload Approvals')

@section('content')
<div class="container-fluid px-4 py-4" x-data="{ liveSearch: '{{ addslashes($search ?? '') }}' }">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Curriculum Management</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-layers-fill text-primary me-2"></i> Cambridge Level Syllabus Review &amp; Materials
            </h1>
            <p class="text-secondary small mb-0">
                Filter by specific Cambridge Assessment Level to inspect materials uploaded by instructors and approve them for subscriber release.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="tj-btn-primary d-inline-flex align-items-center gap-1.5 rounded-3 shadow-xs py-2 px-3 small" data-bs-toggle="modal" data-bs-target="#createLevelModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add Cambridge Level</span>
            </button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-3 shadow-xs py-2 px-3 small">
                <i class="bi bi-folder-plus"></i>
                <span>Upload New Material Pack</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Managed Levels</div>
                <div class="h4 fw-bold mb-0 text-primary">{{ $stats['total_levels'] }} Levels</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Level Accreditation Requests</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $stats['pending_level_requests'] ?? 0 }} Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Approved &amp; Live Materials</div>
                <div class="h4 fw-bold mb-0 text-success">{{ $stats['approved_materials'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Material Reviews</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $stats['pending_review'] }}</div>
            </div>
        </div>
    </div>

    <!-- Cambridge Level Selector Filter Bar (Clean & Professional) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--lms-surface-card);">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center justify-content-between">
                
                <!-- Left: Level Dropdown & Search -->
                <div class="col-lg-7">
                    <form method="GET" action="{{ route('admin.levels.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">

                        <div class="input-group input-group-sm" style="min-width: 240px; max-width: 320px;">
                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-diagram-3-fill text-primary"></i></span>
                            <select name="level_id" class="form-select border-start-0 fw-semibold" onchange="this.form.submit()">
                                <option value="" {{ empty($selectedLevelId) ? 'selected' : '' }}>All Cambridge Levels ({{ $stats['total_materials'] }} materials)</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ (string)$selectedLevelId === (string)$lvl->id ? 'selected' : '' }}>
                                        {{ $lvl->name }} ({{ $lvl->courses_count ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group input-group-sm" style="min-width: 200px; max-width: 260px;">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ $search ?? '' }}" x-model="liveSearch" class="form-control border-start-0" placeholder="Search title or teacher...">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">Filter</button>
                        @if(!empty($selectedLevelId) || $statusFilter !== 'ALL' || !empty($search))
                            <a href="{{ route('admin.levels.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none">Clear</a>
                        @endif
                    </form>
                </div>

                <!-- Right: Approval Status Filter Tabs -->
                <div class="col-lg-5 d-flex justify-content-lg-end">
                    <div class="d-inline-flex p-1 rounded-pill border" style="background: var(--lms-bg-secondary);">
                        <a href="{{ route('admin.levels.index', ['level_id' => $selectedLevelId, 'status' => 'ALL', 'search' => $search ?? '']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold {{ $statusFilter === 'ALL' ? 'btn-primary shadow-xs' : 'btn-link text-secondary text-decoration-none' }}">
                            All ({{ $stats['total_materials'] }})
                        </a>
                        <a href="{{ route('admin.levels.index', ['level_id' => $selectedLevelId, 'status' => 'PENDING', 'search' => $search ?? '']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold {{ $statusFilter === 'PENDING' ? 'btn-warning text-dark shadow-xs' : 'btn-link text-secondary text-decoration-none' }}">
                            <i class="bi bi-clock-history me-1"></i> Pending ({{ $stats['pending_review'] }})
                        </a>
                        <a href="{{ route('admin.levels.index', ['level_id' => $selectedLevelId, 'status' => 'APPROVED', 'search' => $search ?? '']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold {{ $statusFilter === 'APPROVED' ? 'btn-success text-white shadow-xs' : 'btn-link text-secondary text-decoration-none' }}">
                            <i class="bi bi-check-circle-fill me-1"></i> Approved ({{ $stats['approved_materials'] }})
                        </a>
                    </div>
                </div>

            </div>

            <!-- Quick Tier Filter Chips -->
            <div class="pt-2.5 mt-2.5 border-top d-flex align-items-center gap-1.5 flex-nowrap overflow-x-auto" style="scrollbar-width: thin;">
                <span class="small fw-bold text-muted text-uppercase me-1" style="font-size: 0.68rem; letter-spacing: 0.5px; white-space: nowrap;">QUICK TIERS:</span>
                <a href="{{ route('admin.levels.index', ['status' => $statusFilter]) }}" 
                   class="badge rounded-pill px-2.5 py-1.5 text-decoration-none transition-all {{ empty($selectedLevelId) ? 'bg-primary text-white shadow-xs' : 'bg-light text-secondary border' }}">
                    All
                </a>
                @foreach($levels->take(5) as $quickLvl)
                    <a href="{{ route('admin.levels.index', ['level_id' => $quickLvl->id, 'status' => $statusFilter]) }}" 
                       class="badge rounded-pill px-2.5 py-1.5 text-decoration-none transition-all text-nowrap {{ (string)$selectedLevelId === (string)$quickLvl->id ? 'bg-primary text-white shadow-xs' : 'bg-light text-secondary border' }}">
                        {{ $quickLvl->name }}
                    </a>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Materials Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-collection-fill text-primary"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">
                    Teacher Uploads &amp; Syllabus Submissions
                    @if($selectedLevelId)
                        <span class="badge bg-primary-subtle text-primary ms-1">Filtered by Level</span>
                    @endif
                </span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary">{{ $materials->total() }} Materials Found</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Material Title &amp; Syllabus</th>
                        <th class="py-3">Cambridge Level</th>
                        <th class="py-3">Instructor / Author</th>
                        <th class="py-3 text-center">Modules / Lessons</th>
                        <th class="py-3 text-center">Approval Status</th>
                        <th class="pe-4 py-3 text-end">Review Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr x-show="!liveSearch || '{{ strtolower(addslashes($material->title . ' ' . $material->slug . ' ' . ($material->category?->name ?? '') . ' ' . ($material->instructor?->name ?? '') . ' ' . ($material->approval_status ?? ''))) }}'.includes(liveSearch.toLowerCase().trim())">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 overflow-hidden flex-shrink-0 border shadow-xs" 
                                         style="width: 50px; height: 50px; background: #EEF2F6;">
                                        <img src="{{ !empty($material->thumbnail) && !str_contains($material->thumbnail, 'unsplash.com') ? $material->thumbnail : asset('images/profile-img.png') }}" 
                                             alt="{{ $material->title }}" 
                                             class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-body-emphasis" style="font-size: 0.92rem;">
                                            {{ $material->title }}
                                        </div>
                                        <div class="text-secondary small font-mono" style="font-size: 0.75rem;">
                                            Slug: /courses/{{ $material->slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small">
                                    {{ $material->category?->name ?? 'General Cambridge' }}
                                </span>
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold text-body-emphasis small">
                                    {{ $material->instructor?->name ?? 'Cambridge Faculty' }}
                                </div>
                                <div class="text-secondary small font-mono" style="font-size: 0.74rem;">
                                    {{ $material->instructor?->email ?? 'academic@cambridge.co.uk' }}
                                </div>
                            </td>

                            <td class="py-3 text-center font-mono small">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ $material->sections->count() }} Sections • {{ $material->sections->sum(fn($s) => $s->modules->count()) }} Modules
                                </span>
                            </td>

                            <td class="py-3 text-center">
                                @if($material->material_approval_status === 'REJECTED')
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);" title="{{ $material->material_rejection_reason ?? 'Non-compliant' }}">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                    @if($material->material_rejection_reason)
                                        <div class="small text-danger mt-1 text-truncate" style="max-width: 140px; font-size: 0.68rem;" title="{{ $material->material_rejection_reason }}">
                                            {{ $material->material_rejection_reason }}
                                        </div>
                                    @endif
                                @elseif($material->is_published || $material->material_approval_status === 'APPROVED')
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved &amp; Live
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                                        <i class="bi bi-clock-fill me-1"></i> Pending Approval
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('courses.show', $material->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="Preview Student View">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(!$material->is_published)
                                        <form method="POST" action="{{ route('admin.levels.materials.approve', $material->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.76rem;">
                                                <i class="bi bi-check-lg"></i>
                                                <span>Approve</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#rejectMaterialModal{{ $material->id }}">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#rejectMaterialModal{{ $material->id }}">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                No materials found matching the selected level.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($materials->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-secondary">
                    Showing {{ $materials->firstItem() }} to {{ $materials->lastItem() }} of {{ $materials->total() }} uploaded materials
                </div>
                <div>
                    {{ $materials->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Teacher Additional Cambridge Level Requests Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-warning"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">
                    Teacher Level Accreditation &amp; Approval Requests
                </span>
                @if($pendingRequestsCount > 0)
                    <span class="badge bg-warning text-dark font-mono rounded-pill ms-1">{{ $pendingRequestsCount }} Pending</span>
                @endif
            </div>
            <span class="badge bg-secondary-subtle text-secondary">{{ $levelRequests->total() }} Requests Logged</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Teacher / Instructor</th>
                        <th class="py-3">Requested Cambridge Tier</th>
                        <th class="py-3">Subject KYC &amp; Notes</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Requested On</th>
                        <th class="pe-4 py-3 text-end">Admin Decision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($levelRequests as $req)
                        <tr x-show="!liveSearch || '{{ strtolower(addslashes(($req->user?->name ?? '') . ' ' . ($req->user?->email ?? '') . ' ' . ($req->category?->name ?? '') . ' ' . ($req->status ?? ''))) }}'.includes(liveSearch.toLowerCase().trim())">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle overflow-hidden flex-shrink-0 border" style="width: 36px; height: 36px;">
                                        <img src="{{ !empty($req->user?->profile?->avatar_url) && !str_contains($req->user->profile->avatar_url, 'unsplash.com') ? $req->user->profile->avatar_url : asset('images/profile-img.png') }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark">{{ $req->user?->name ?? 'Unknown Teacher' }}</div>
                                        <div class="text-muted font-mono" style="font-size: 0.72rem;">{{ $req->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold font-mono">
                                    <i class="bi bi-mortarboard-fill me-1"></i> {{ $req->category?->name ?? 'Level #' . $req->category_id }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 280px;" title="{{ $req->request_notes }}">
                                    {{ $req->request_notes }}
                                </div>
                                @if($req->admin_feedback)
                                    <div class="small text-muted font-italic mt-0.5" style="font-size: 0.7rem;">
                                        Feedback: {{ $req->admin_feedback }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($req->status === 'APPROVED')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                @elseif($req->status === 'REJECTED')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Pending Review
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted font-mono">
                                {{ $req->created_at->format('M d, Y') }}
                            </td>
                            <td class="pe-4 text-end">
                                @if($req->status === 'PENDING')
                                    <div class="d-inline-flex gap-1.5">
                                        <form action="{{ route('admin.teachers.approve-level-request', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.76rem;">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.76rem;" data-bs-toggle="modal" data-bs-target="#rejectLevelReqModal{{ $req->id }}">
                                            <i class="bi bi-x-circle me-1"></i> Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No additional level requests submitted.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($levelRequests->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-secondary">
                    Showing {{ $levelRequests->firstItem() }} to {{ $levelRequests->lastItem() }} of {{ $levelRequests->total() }} level requests
                </div>
                <div>
                    {{ $levelRequests->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    <!-- 3. ALL CAMBRIDGE LEVELS & CRUD MANAGEMENT -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">
                    Cambridge Levels &amp; Program Tier Registry (CRUD)
                </span>
            </div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#createLevelModal">
                <i class="bi bi-plus-lg me-1"></i> New Level
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">ID</th>
                        <th class="py-3">Level Name</th>
                        <th class="py-3">Slug / Endpoint</th>
                        <th class="py-3 text-center">Associated Courses / Materials</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($levels as $lvl)
                        <tr x-show="!liveSearch || '{{ strtolower(addslashes($lvl->name . ' ' . $lvl->slug)) }}'.includes(liveSearch.toLowerCase().trim())">
                            <td class="ps-4 py-3 font-mono text-muted small">#{{ $lvl->id }}</td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $lvl->name }}</div>
                            </td>
                            <td class="py-3 font-mono text-secondary small">
                                /courses?level={{ $lvl->slug }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-light text-dark border font-mono px-2.5 py-1 rounded-pill">
                                    {{ $lvl->courses_count ?? $lvl->courses()->count() }} Programs
                                </span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex gap-1.5">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#editLevelModal{{ $lvl->id }}" title="Edit Level">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    @if(($lvl->courses_count ?? $lvl->courses()->count()) === 0)
                                        <form action="{{ route('admin.levels.destroy', $lvl->id) }}" method="POST" onsubmit="return confirm('Delete Cambridge level {{ $lvl->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" title="Delete Level">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 opacity-50" disabled title="Cannot delete level with active courses">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No levels found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Level Modal -->
    <div class="modal fade" id="createLevelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start">
            <form method="POST" action="{{ route('admin.levels.store') }}" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Cambridge Level
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Level / Curriculum Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Cambridge Primary Checkpoint, Cambridge IGCSE...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control font-mono" placeholder="e.g. cambridge-primary-checkpoint">
                        <span class="small text-muted" style="font-size: 0.72rem;">Used in URLs and filters. Defaults to slugified name.</span>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tj-btn-primary px-4 py-2 small">Create Level</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals moved outside table markup to prevent display and layout issues -->
    @foreach($materials as $material)
        <div class="modal fade" id="rejectMaterialModal{{ $material->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered text-start">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom pb-3">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-shield-x me-2"></i> Reject Material &amp; Syllabus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.levels.materials.reject', $material->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body py-3">
                            <p class="small text-secondary mb-3">
                                You are rejecting <strong>{{ $material->title }}</strong> submitted by <strong>{{ $material->instructor?->name ?? 'Instructor' }}</strong>. Please specify the non-compliance reason below. The instructor will receive a formal notification email with an authenticated direct link to revise their submission.
                            </p>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Reason for Non-Compliance <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="4" class="form-control" placeholder="Specify why this syllabus or material file was not compliant (e.g. wrong syllabus code, missing answer keys, incomplete modules)..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-2">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Confirm Rejection &amp; Email Instructor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($levelRequests as $req)
        <div class="modal fade" id="rejectLevelReqModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered text-start">
                <form method="POST" action="{{ route('admin.teachers.reject-level-request', $req->id) }}" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-danger">Decline Level Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3">
                        <p class="small text-secondary mb-2">
                            Decline authorization for <strong>{{ $req->user?->name }}</strong> to tutor <strong>{{ $req->category?->name }}</strong>.
                        </p>
                        <label class="form-label small fw-bold">Feedback / Notes</label>
                        <textarea name="admin_feedback" rows="3" class="form-control" placeholder="Specify why this accreditation was declined..."></textarea>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Confirm Decline</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach($levels as $lvl)
        <div class="modal fade" id="editLevelModal{{ $lvl->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered text-start">
                <form method="POST" action="{{ route('admin.levels.update', $lvl->id) }}" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit Cambridge Level</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Level Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $lvl->name }}" placeholder="e.g. Cambridge IGCSE">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Slug / URL Identifier</label>
                            <input type="text" name="slug" class="form-control font-mono" value="{{ $lvl->slug }}" placeholder="e.g. cambridge-igcse">
                            <span class="small text-muted" style="font-size: 0.72rem;">Leave blank to auto-generate from name.</span>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-3 py-1.5 small">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</div>
@endsection
