@extends('layouts.admin')

@section('title', 'Course Curriculum & Program Manager')

@section('content')
<div class="container-fluid" 
     x-data="{ 
         showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
         showEditModal: false,
         liveSearch: '{{ addslashes($search ?? '') }}',
         editCourse: {
             id: '',
             category_id: '',
             title: '',
             slug: '',
             description: '',
             public_preview_content: '',
             thumbnail: '',
             thumbnail_url: '',
             video_url: '',
             material_file: '',
             material_notes: '',
             target_audience: 'BOTH',
             format: 'SELF_PACED',
             passing_grade: 70,
             recertification_days: '',
             price_cents: 0,
             special_pricing_tag: '',
             currency: 'USD',
             is_published: true,
             action_url: ''
         },
         openEdit(c) {
             this.editCourse = {
                 id: c.id,
                 category_id: c.category_id || '',
                 title: c.title,
                 slug: c.slug,
                 description: c.description || '',
                 public_preview_content: c.public_preview_content || '',
                 thumbnail: c.thumbnail || '',
                 thumbnail_url: (c.thumbnail && (c.thumbnail.startsWith('http') || c.thumbnail.startsWith('/'))) ? c.thumbnail : '',
                 video_url: c.video_url || '',
                 material_file: c.material_file || '',
                 material_notes: c.material_notes || '',
                 target_audience: c.target_audience,
                 format: c.format,
                 passing_grade: c.passing_grade,
                 recertification_days: c.recertification_days || '',
                 price_cents: (c.price_approval_status === 'PENDING' && c.proposed_price_cents !== null) ? c.proposed_price_cents : c.price_cents,
                 special_pricing_tag: c.special_pricing_tag || '',
                 currency: c.currency,
                 is_published: !!c.is_published,
                 action_url: '{{ url('admin/courses') }}/' + c.id
             };
             this.showEditModal = true;
         },
         requestDeletePrompt(courseId, courseTitle) {
             Swal.fire({
                 title: 'Request Course Deletion',
                 html: `<p class='small text-muted mb-3'>You are requesting deletion of <strong>${courseTitle}</strong>. As an approved course/material, an administrator must review and approve this deletion before proceeding.</p>
                        <textarea id='swal-delete-reason' class='form-control text-start' rows='3' placeholder='Please enter the reason for deletion (e.g. syllabus updated, deprecated past paper)...'></textarea>`,
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#DC2626',
                 cancelButtonColor: '#6B7280',
                 confirmButtonText: 'Submit Deletion Request',
                 cancelButtonText: 'Cancel',
                 preConfirm: () => {
                     const reason = document.getElementById('swal-delete-reason').value;
                     if (!reason || reason.trim().length === 0) {
                         Swal.showValidationMessage('Please provide a reason for the deletion request');
                         return false;
                     }
                     return reason;
                 }
             }).then((result) => {
                 if (result.isConfirmed) {
                     const form = document.createElement('form');
                     form.method = 'POST';
                     form.action = `/admin/courses/${courseId}/request-delete`;
                     
                     const csrf = document.createElement('input');
                     csrf.type = 'hidden';
                     csrf.name = '_token';
                     csrf.value = '{{ csrf_token() }}';
                     form.appendChild(csrf);

                     const reasonInput = document.createElement('input');
                     reasonInput.type = 'hidden';
                     reasonInput.name = 'reason';
                     reasonInput.value = result.value;
                     form.appendChild(reasonInput);

                     document.body.appendChild(form);
                     form.submit();
                 }
             });
         }
     }">

    @if(!$isTeacher && $pendingDeletionsCount > 0)
        <!-- Admin Alert: Pending Teacher Deletion Requests -->
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" style="background: rgba(245, 158, 11, 0.12); border-left: 5px solid #F59E0B !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Action Required: {{ $pendingDeletionsCount }} Pending Course / Material Deletion Request(s)</h6>
                    <span class="small text-secondary">Teachers have requested deletion of approved courses or materials. Please review and approve or reject below.</span>
                </div>
            </div>
            <span class="badge bg-warning text-dark font-mono px-3 py-2 rounded-pill">{{ $pendingDeletionsCount }} REQUESTED</span>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                @if($isTeacher)
                    My Courses &amp; Learning Materials
                @else
                    {{ $site_settings['title_courses_manager'] ?? 'Courses & Curriculum Architecture' }}
                @endif
            </h2>
            <div class="text-secondary small">
                @if($isTeacher)
                    Create courses under specific Cambridge levels, upload lecture videos, and attach study materials with inline text notes.
                @else
                    {{ $site_settings['subtitle_courses_manager'] ?? 'Design modular learning paths, upload course thumbnails, set recertification cycles, and configure audiences.' }}
                @endif
            </div>
        </div>
        @if($isTeacher && !auth()->user()->isApprovedTeacher())
            <button type="button" class="btn btn-secondary py-2 px-3 small shadow-sm rounded-pill opacity-75" disabled title="Administrator Approval Required">
                <i class="bi bi-lock-fill me-1"></i> Awaiting Admin Approval
            </button>
        @else
            <button class="tj-btn-primary py-2 px-3 small shadow-sm" @click="showCreateModal = true">
                <i class="bi bi-plus-lg me-1"></i> {{ $isTeacher ? 'Create Course Under Level' : ($site_settings['btn_create_course'] ?? 'Create New Program') }}
            </button>
        @endif
    </div>

    @if($isTeacher && !auth()->user()->isApprovedTeacher())
        <!-- Teacher Pending Verification Notification Banner -->
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" style="background: rgba(245, 158, 11, 0.12); border-left: 5px solid #F59E0B !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Faculty Account Pending Administrator Approval</h6>
                    <span class="small text-secondary">Your instructor account is currently undergoing verification. Once an administrator approves your profile, course authoring and material upload will be unlocked.</span>
                </div>
            </div>
            <span class="badge bg-warning text-dark font-mono px-3 py-2 rounded-pill">PENDING APPROVAL</span>
        </div>
    @endif

    <!-- Courses & Materials Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--lms-surface-card);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.courses.index') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div style="min-width: 220px;">
                        <select name="level_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="0">All Cambridge Levels</option>
                            @foreach($allFilterLevels ?? [] as $lvl)
                                <option value="{{ $lvl->id }}" {{ ($levelFilter ?? 0) == $lvl->id ? 'selected' : '' }}>
                                    Level: {{ $lvl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 240px;">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" x-model="liveSearch" class="form-control border-start-0" placeholder="Search title or instructor...">
                    </div>

                    <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                    @if(!empty($search) || ($levelFilter ?? 0) > 0)
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-link text-muted p-0 ms-1 text-decoration-none">Clear</a>
                    @endif
                </div>

                <div class="small text-muted font-mono">
                    Total Courses Found: <span class="fw-bold text-dark">{{ $courses->total() }}</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Filter Navigation Tabs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.courses.index', array_merge(request()->except('status'), ['status' => 'ALL'])) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ ($statusFilter ?? 'ALL') === 'ALL' ? 'btn-primary shadow-xs' : 'btn-outline-secondary' }}">
                <i class="bi bi-grid-fill me-1"></i> All Courses
                <span class="badge {{ ($statusFilter ?? 'ALL') === 'ALL' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $totalCoursesCount ?? $courses->total() }}</span>
            </a>

            <a href="{{ route('admin.courses.index', array_merge(request()->except('status'), ['status' => 'PENDING'])) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ ($statusFilter ?? 'ALL') === 'PENDING' ? 'btn-warning text-dark shadow-xs' : 'btn-outline-warning text-dark' }}">
                <i class="bi bi-hourglass-split me-1 text-warning"></i> Pending Admin Approval
                <span class="badge {{ ($statusFilter ?? 'ALL') === 'PENDING' ? 'bg-dark text-warning' : 'bg-warning text-dark' }} rounded-pill ms-1 font-mono">{{ $pendingApprovalCount ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.courses.index', array_merge(request()->except('status'), ['status' => 'LIVE'])) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ in_array(($statusFilter ?? 'ALL'), ['LIVE', 'PUBLISHED']) ? 'btn-success shadow-xs' : 'btn-outline-success' }}">
                <i class="bi bi-check-circle-fill me-1 text-success"></i> Approved &amp; Live
                <span class="badge {{ in_array(($statusFilter ?? 'ALL'), ['LIVE', 'PUBLISHED']) ? 'bg-white text-success' : 'bg-success-subtle text-success' }} rounded-pill ms-1 font-mono">{{ $liveCoursesCount ?? 0 }}</span>
            </a>

            @if(($rejectedCoursesCount ?? 0) > 0 || ($statusFilter ?? 'ALL') === 'REJECTED')
                <a href="{{ route('admin.courses.index', array_merge(request()->except('status'), ['status' => 'REJECTED'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ ($statusFilter ?? 'ALL') === 'REJECTED' ? 'btn-danger shadow-xs' : 'btn-outline-danger' }}">
                    <i class="bi bi-x-circle-fill me-1 text-danger"></i> Rejected
                    <span class="badge {{ ($statusFilter ?? 'ALL') === 'REJECTED' ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' }} rounded-pill ms-1 font-mono">{{ $rejectedCoursesCount ?? 0 }}</span>
                </a>
            @endif
        </div>

        @if(!$isTeacher && ($pendingApprovalCount ?? 0) > 0)
            <div class="small text-warning-emphasis fw-semibold d-flex align-items-center gap-1">
                <i class="bi bi-shield-exclamation text-warning fs-6"></i>
                <span>{{ $pendingApprovalCount }} course(s) require review before public listing</span>
            </div>
        @endif
    </div>

    <!-- Courses Table -->
    <div class="lms-card p-4 shadow-sm mb-4" style="min-height: 380px;">
        <div class="table-responsive" style="min-height: 280px; padding-bottom: 70px;">
            <table class="table table-hover align-middle mb-0 w-100" style="table-layout: auto;">
                <thead class="table-light text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th style="width: 50px;">Media</th>
                        <th style="min-width: 180px;">Title &amp; Code</th>
                        <th>Level &amp; Format</th>
                        @if(!$isTeacher)
                            <th>Teacher</th>
                        @endif
                        <th class="text-center" style="width: 85px;">Materials</th>
                        <th style="width: 140px;">Approval Status</th>
                        <th class="text-end" style="min-width: 160px;">Review &amp; Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr class="{{ $course->isPendingDeletion() ? 'table-warning bg-opacity-25' : '' }}"
                            x-show="!liveSearch || '{{ strtolower(addslashes($course->title . ' ' . $course->slug . ' ' . ($course->course_code ?? '') . ' ' . ($course->category?->name ?? '') . ' ' . ($course->instructor?->name ?? ''))) }}'.includes(liveSearch.toLowerCase().trim())">
                            <td>
                                @if($course->thumbnailUrl())
                                    <div class="position-relative rounded-2 overflow-hidden shadow-xs d-flex align-items-center justify-content-center" 
                                         style="width: 44px; height: 32px; min-width: 44px; border: 1px solid var(--lms-border); background: {{ $course->target_audience === 'CORPORATE' ? 'linear-gradient(135deg, #0F172A, #1E293B)' : 'linear-gradient(135deg, #723EC3, #2563EB)' }};">
                                        <img src="{{ $course->thumbnailUrl() }}" 
                                             alt=""
                                             loading="lazy"
                                             referrerpolicy="no-referrer"
                                             class="w-100 h-100 object-fit-cover" 
                                             style="display: block;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="align-items-center justify-content-center text-white small w-100 h-100" style="display: none; font-size: 0.85rem;">
                                            <i class="bi {{ $course->target_audience === 'CORPORATE' ? 'bi-shield-check text-warning' : 'bi-mortarboard-fill text-light' }}"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-2 d-flex align-items-center justify-content-center text-white small" 
                                         style="width: 44px; height: 32px; min-width: 44px; background: {{ $course->target_audience === 'CORPORATE' ? 'linear-gradient(135deg, #0F172A, #1E293B)' : 'linear-gradient(135deg, #723EC3, #2563EB)' }}; font-size: 0.85rem;">
                                        <i class="bi {{ $course->target_audience === 'CORPORATE' ? 'bi-shield-check text-warning' : 'bi-mortarboard-fill text-light' }}"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $course->title }}">{{ $course->title }}</div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    <span class="badge bg-light text-secondary border font-mono px-1.5 py-0.5" style="font-size: 0.65rem;">
                                        {{ $course->course_code ?? 'CAM-' . str_pad((string)$course->id, 3, '0', STR_PAD_LEFT) . '-T' . ($course->instructor_id ?? '1') }}
                                    </span>
                                    <span class="font-mono text-muted small text-truncate" style="font-size: 0.72rem; max-width: 130px;">/{{ $course->slug }}</span>
                                </div>
                                @if($course->special_pricing_tag)
                                    <div class="mt-1">
                                        <span class="badge rounded-pill font-mono px-2 py-0.5" style="font-size: 0.68rem; background: rgba(114, 62, 195, 0.12); color: #723EC3; border: 1px solid rgba(114, 62, 195, 0.3);">
                                            <i class="bi bi-tag-fill me-1"></i> {{ $course->special_pricing_tag }}
                                        </span>
                                    </div>
                                @endif
                                @if($course->price_approval_status === 'PENDING')
                                    <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.68rem;" title="Proposed: {{ $course->currency }} {{ number_format(($course->proposed_price_cents ?? 0) / 100, 2) }}">
                                        <i class="bi bi-hourglass-split me-1"></i> Price Pending: {{ $course->currency }} {{ number_format(($course->proposed_price_cents ?? 0) / 100, 2) }}
                                    </div>
                                @elseif($course->price_approval_status === 'REJECTED')
                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.68rem;" title="{{ $course->rejection_reason ?? 'Non-compliant pricing' }}">
                                        <i class="bi bi-x-circle-fill me-1"></i> Price Rejected
                                    </div>
                                @endif
                                @if($course->isPendingDeletion())
                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill mt-1" style="font-size: 0.68rem;">
                                        <i class="bi bi-hourglass-split me-1"></i> Deletion Requested
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($course->category)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill small fw-semibold d-inline-block text-truncate" style="max-width: 160px;">
                                        {{ $course->category->name }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-0.5 rounded-pill small">Unassigned</span>
                                @endif
                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">{{ ucfirst(strtolower($course->format)) }}</div>
                            </td>
                            @if(!$isTeacher)
                                <td>
                                    @if($course->instructor)
                                        <div class="d-flex align-items-center gap-1.5">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 0.62rem;">
                                                {{ substr($course->instructor->name, 0, 1) }}
                                            </div>
                                            <div class="small fw-semibold text-truncate" style="max-width: 110px;">
                                                {{ $course->instructor->name }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border small" style="font-size: 0.68rem;">Site Faculty</span>
                                    @endif
                                </td>
                            @endif
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1.5">
                                    @if($course->video_url)
                                        <span class="text-success" title="Video Attached"><i class="bi bi-play-circle-fill fs-6"></i></span>
                                    @endif
                                    @if($course->material_file)
                                        <a href="{{ $course->materialUrl() }}" target="_blank" class="text-info" title="Download Material File"><i class="bi bi-file-earmark-arrow-down-fill fs-6"></i></a>
                                    @endif
                                    @if($course->material_notes)
                                        <span class="text-secondary" title="Has Inline Notes"><i class="bi bi-card-text fs-6"></i></span>
                                    @endif
                                    @if(!$course->video_url && !$course->material_file && !$course->material_notes)
                                        <span class="text-muted small" style="font-size: 0.7rem;">None</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($course->is_published)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i> APPROVED &amp; LIVE
                                    </span>
                                @elseif($course->material_approval_status === 'REJECTED')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="{{ $course->material_rejection_reason ?? 'Curriculum non-compliant' }}">
                                        <i class="bi bi-x-circle-fill me-1"></i> REJECTED
                                    </span>
                                    @if($course->material_rejection_reason)
                                        <div class="text-danger small mt-0.5 text-truncate" style="max-width: 130px; font-size: 0.68rem;" title="{{ $course->material_rejection_reason }}">
                                            {{ $course->material_rejection_reason }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                        <i class="bi bi-hourglass-split me-1"></i> PENDING APPROVAL
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    @if(!$isTeacher && !$course->is_published)
                                        <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-bold text-nowrap shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;" title="Approve & Publish Course to Public Catalog" onclick="return confirm('Approve & make course live to public: {{ addslashes($course->title) }}?')">
                                                <i class="bi bi-check-lg"></i>
                                                <span>Approve &amp; Publish</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.74rem;" title="Reject course submission" data-bs-toggle="modal" data-bs-target="#rejectCourseModal{{ $course->id }}">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif

                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary p-1 px-2" 
                                            title="Edit Course"
                                            @click="openEdit({{ json_encode($course) }})">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="{{ route('learning.player', ['course' => $course->slug]) }}" class="btn btn-sm btn-outline-success p-1 px-2" title="Preview Student View" target="_blank">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </a>

                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light border p-1 px-2" type="button" data-bs-toggle="dropdown" data-bs-strategy="fixed" title="More Actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1060;">
                                            <li>
                                                <a class="dropdown-item small" href="{{ route('courses.show', $course->slug) }}" target="_blank">
                                                    <i class="bi bi-box-arrow-up-right me-2 text-primary"></i> Public Page
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if(!$isTeacher)
                                                @if(!$course->is_published)
                                                    <li>
                                                        <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item small text-success fw-bold">
                                                                <i class="bi bi-check2-circle me-2"></i> Approve &amp; Make Live
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item small text-danger" data-bs-toggle="modal" data-bs-target="#rejectCourseModal{{ $course->id }}">
                                                            <i class="bi bi-x-circle me-2"></i> Reject Course Submission
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                @endif
                                                @if($course->price_approval_status === 'PENDING')
                                                    <li>
                                                        <form action="{{ route('admin.courses.approve-pricing', $course->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item small text-success fw-bold">
                                                                <i class="bi bi-tag-fill me-2"></i> Approve Special Price ({{ $course->currency }} {{ number_format(($course->proposed_price_cents ?? 0) / 100, 2) }})
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item small text-danger" data-bs-toggle="modal" data-bs-target="#rejectPricingModal{{ $course->id }}">
                                                            <i class="bi bi-x-circle me-2"></i> Reject Price Proposal
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                @endif
                                                @if($course->isPendingDeletion())
                                                    <li>
                                                        <form action="{{ route('admin.courses.approve-delete', $course->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item small text-danger fw-bold" onclick="return confirm('Approve deletion of course {{ addslashes($course->title) }}?')">
                                                                <i class="bi bi-check2-circle me-2"></i> Approve Deletion
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.courses.reject-delete', $course->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item small text-secondary">
                                                                <i class="bi bi-x-circle me-2"></i> Reject Deletion
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this program: {{ addslashes($course->title) }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item small text-danger">
                                                                <i class="bi bi-trash me-2"></i> Delete Course
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @else
                                                @if($course->is_published || $course->deletion_status === 'APPROVED')
                                                    <li>
                                                        <button type="button" class="dropdown-item small text-warning" @click="requestDelete({{ $course->id }}, '{{ addslashes($course->title) }}')">
                                                            <i class="bi bi-exclamation-triangle me-2"></i> Request Deletion
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete unpublished course: {{ addslashes($course->title) }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item small text-danger">
                                                                <i class="bi bi-trash me-2"></i> Delete Draft
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isTeacher ? 7 : 8 }}" class="text-center py-4 text-muted">
                                @if($isTeacher)
                                    You have not created any courses yet. Click "Create Course Under Level" to begin.
                                @else
                                    No courses created yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="small text-secondary">
                    Showing <span class="fw-bold text-dark">{{ $courses->firstItem() }}</span> to <span class="fw-bold text-dark">{{ $courses->lastItem() }}</span> of <span class="fw-bold text-dark">{{ $courses->total() }}</span> programs
                </div>
                <div>
                    {{ $courses->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- 1. CREATE COURSE MODAL -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showCreateModal }" 
         :style="showCreateModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showCreateModal = false" 
         @keydown.escape.window="showCreateModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">{{ $isTeacher ? 'Create Course Under Cambridge Level' : 'Create New Learning Program' }}</h5>
                        <div class="text-muted small">Configure curriculum parameters, upload lecture video, and attach course study materials.</div>
                    </div>
                    <button type="button" class="btn-close" @click="showCreateModal = false"></button>
                </div>
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <!-- Cambridge Level Selection (Compulsory for Teacher) -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(114, 62, 195, 0.05); border-color: rgba(114, 62, 195, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-diagram-3-fill text-primary"></i> Target Cambridge Level <span class="text-danger">*</span>
                                    </label>
                                    <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                                        Select the exact academic Cambridge level this course belongs to (e.g. Cambridge IGCSE, Cambridge O Level, Cambridge International AS &amp; A Level).
                                    </div>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Select Authorized Cambridge Level (Compulsory) --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }} (Approved Scope)</option>
                                        @endforeach
                                    </select>
                                    @if($isTeacher && $categories->isEmpty())
                                        <div class="alert alert-warning border-0 p-2 mt-2 mb-0 rounded-3 small">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> You do not currently have any approved Cambridge teaching levels. Please <a href="{{ route('admin.dashboard') }}" class="fw-bold text-decoration-underline">request a Cambridge level</a> from your dashboard.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Course Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g., Cambridge IGCSE Mathematics: Paper 2 Core Framework">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">URL Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" required value="{{ old('slug') }}" placeholder="igcse-math-paper-2">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Course Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of syllabus objectives, competencies, and learning outcomes...">{{ old('description') }}</textarea>
                            </div>

                            <!-- Course Lecture Video Section -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-success d-flex align-items-center gap-1">
                                        <i class="bi bi-camera-video-fill text-success"></i> Course Lecture Video (Upload or Embed)
                                    </label>
                                    <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                                        Upload an MP4/WebM video recording or paste an external URL (YouTube / Vimeo / CDN). Students will stream this lecture directly in the course player.
                                    </div>
                                    @php
                                        $preservedVideo = session('preserved_video_file', old('preserved_video_file'));
                                    @endphp
                                    @if($preservedVideo)
                                        <input type="hidden" name="preserved_video_file" value="{{ $preservedVideo }}">
                                        <div class="alert alert-success border-0 py-2 px-3 mb-2 rounded-3 small d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-check-circle-fill me-1 text-success"></i> Video uploaded previously: <code>{{ basename($preservedVideo) }}</code> (retained)</span>
                                            <span class="badge bg-success-subtle text-success">Ready</span>
                                        </div>
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload Video File (MP4, WebM)</label>
                                            <input type="file" name="video_file" class="form-control form-control-sm" accept="video/mp4,video/webm,video/ogg">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Video Link (URL)</label>
                                            <input type="url" name="video_url" class="form-control form-control-sm" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Material Files & Inline Study Text -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-file-earmark-text-fill text-primary"></i> Course Study Materials &amp; Syllabi
                                    </label>
                                    <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                                        Upload document files (PDF, Past Papers, Slides, Zip) and write detailed study notes. Files and notes will format cleanly as native reading materials for students.
                                    </div>
                                    @php
                                        $preservedMaterial = session('preserved_material_file', old('preserved_material_file'));
                                    @endphp
                                    @if($preservedMaterial)
                                        <input type="hidden" name="preserved_material_file" value="{{ $preservedMaterial }}">
                                        <div class="alert alert-success border-0 py-2 px-3 mb-2 rounded-3 small d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-check-circle-fill me-1 text-success"></i> Document uploaded previously: <code>{{ basename($preservedMaterial) }}</code> (retained)</span>
                                            <span class="badge bg-success-subtle text-success">Ready</span>
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Upload Document / Past Paper (PDF, Doc, PPT, Zip)</label>
                                        <input type="file" name="material_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt">
                                    </div>
                                    <div>
                                        <label class="form-label small text-muted">Inline Study Text &amp; Notes (Formatted like normal system texts)</label>
                                        <textarea name="material_notes" class="form-control" rows="4" placeholder="Enter key revision notes, formula guides, paper instructions, or reading text here. It will display seamlessly in the student reader pane...">{{ old('material_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Thumbnail Section -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                    <label class="form-label small fw-bold mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-image text-primary"></i> Course Thumbnail Image
                                    </label>
                                    @php
                                        $preservedThumbnail = session('preserved_thumbnail_file', old('preserved_thumbnail_file'));
                                    @endphp
                                    @if($preservedThumbnail)
                                        <input type="hidden" name="preserved_thumbnail_file" value="{{ $preservedThumbnail }}">
                                        <div class="alert alert-success border-0 py-2 px-3 mb-2 rounded-3 small d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-check-circle-fill me-1 text-success"></i> Thumbnail uploaded previously: <code>{{ basename($preservedThumbnail) }}</code> (retained)</span>
                                            <span class="badge bg-success-subtle text-success">Ready</span>
                                        </div>
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload File (JPG, PNG, WebP, SVG up to 5MB)</label>
                                            <input type="file" name="thumbnail_file" class="form-control form-control-sm" accept="image/*">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Image URL</label>
                                            <input type="url" name="thumbnail_url" class="form-control form-control-sm" value="{{ old('thumbnail_url') }}" placeholder="https://example.com/image.jpg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Target Audience</label>
                                <select name="target_audience" class="form-select">
                                    <option value="PUBLIC" {{ old('target_audience', 'PUBLIC') === 'PUBLIC' ? 'selected' : '' }}>Public &amp; Academic</option>
                                    <option value="BOTH" {{ old('target_audience') === 'BOTH' ? 'selected' : '' }}>Dual-Audience (Both)</option>
                                    <option value="CORPORATE" {{ old('target_audience') === 'CORPORATE' ? 'selected' : '' }}>Corporate Workforce</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Course Format</label>
                                <select name="format" class="form-select">
                                    <option value="SELF_PACED" {{ old('format', 'SELF_PACED') === 'SELF_PACED' ? 'selected' : '' }}>Self-Paced Modular</option>
                                    <option value="WEEKLY" {{ old('format') === 'WEEKLY' ? 'selected' : '' }}>Weekly Cohorts</option>
                                    <option value="TOPICS" {{ old('format') === 'TOPICS' ? 'selected' : '' }}>Thematic Topics</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Passing Threshold (%)</label>
                                <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', 70) }}" min="1" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Recertification Cycle (Days)</label>
                                <input type="number" name="recertification_days" class="form-control" value="{{ old('recertification_days') }}" placeholder="e.g. 365">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Price (Cents)</label>
                                <input type="number" name="price_cents" class="form-control" value="{{ old('price_cents', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Currency</label>
                                <input type="text" name="currency" class="form-control" value="{{ old('currency', 'GBP') }}" maxlength="3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Special Pricing Tag (Optional)</label>
                                <input type="text" name="special_pricing_tag" class="form-control" value="{{ old('special_pricing_tag') }}" placeholder="e.g. Early Bird Revision, Term 1 Special, Cambridge Sprint">
                            </div>
                            <div class="col-12">
                                @if($isTeacher)
                                    <div class="alert alert-info py-2 px-3 small rounded-3 d-flex align-items-center gap-2 mb-0 border-info-subtle">
                                        <i class="bi bi-shield-check fs-5 text-primary"></i>
                                        <div>
                                            <strong>Academic Board Approval Required:</strong> Your new course will be submitted in <em>Draft / Pending</em> status and reviewed by an administrator before becoming publicly visible in the course catalog.
                                        </div>
                                    </div>
                                @else
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="createPublishSwitch" {{ old('is_published', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="createPublishSwitch">
                                            Publish Immediately to Public Catalog
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" @click="showCreateModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">
                            <i class="bi bi-check-lg me-1"></i> Save Course &amp; Materials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. EDIT COURSE & MATERIALS MODAL -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showEditModal }" 
         :style="showEditModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showEditModal = false" 
         @keydown.escape.window="showEditModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Edit Course, Video &amp; Materials</h5>
                        <div class="text-muted small">Update course metadata, change video link, and revise attached materials.</div>
                    </div>
                    <button type="button" class="btn-close" @click="showEditModal = false"></button>
                </div>
                <form :action="editCourse.action_url" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <!-- Cambridge Level Selection -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(114, 62, 195, 0.05); border-color: rgba(114, 62, 195, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-diagram-3-fill text-primary"></i> Target Cambridge Level <span class="text-danger">*</span>
                                    </label>
                                    <select name="category_id" class="form-select" x-model="editCourse.category_id" required>
                                        <option value="">-- Select Cambridge Level (Compulsory) --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Course Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required x-model="editCourse.title">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">URL Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" required x-model="editCourse.slug">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Course Description</label>
                                <textarea name="description" class="form-control" rows="2" x-model="editCourse.description"></textarea>
                            </div>

                            <!-- Course Lecture Video Section -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-success d-flex align-items-center gap-1">
                                        <i class="bi bi-camera-video-fill text-success"></i> Course Lecture Video
                                    </label>
                                    <template x-if="editCourse.video_url">
                                        <div class="mb-2 small font-mono text-success d-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Current Video: <span class="text-truncate" style="max-width: 400px;" x-text="editCourse.video_url"></span>
                                        </div>
                                    </template>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload Replacement Video</label>
                                            <input type="file" name="video_file" class="form-control form-control-sm" accept="video/mp4,video/webm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Video Link URL</label>
                                            <input type="text" name="video_url" class="form-control form-control-sm" x-model="editCourse.video_url" placeholder="https://...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Material Files & Inline Study Text -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.3) !important;">
                                    <label class="form-label small fw-bold mb-1 text-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-file-earmark-text-fill text-primary"></i> Course Study Materials &amp; Syllabi
                                    </label>
                                    <template x-if="editCourse.material_file">
                                        <div class="mb-2 small font-mono text-primary d-flex align-items-center gap-1">
                                            <i class="bi bi-file-earmark-check-fill"></i> Current Material File: <span x-text="editCourse.material_file"></span>
                                        </div>
                                    </template>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Upload New Document File (PDF, Doc, Zip)</label>
                                        <input type="file" name="material_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt">
                                    </div>
                                    <div>
                                        <label class="form-label small text-muted">Inline Study Text &amp; Notes</label>
                                        <textarea name="material_notes" class="form-control" rows="4" x-model="editCourse.material_notes" placeholder="Enter key revision notes, formula guides, paper instructions..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Thumbnail Section in Edit Modal -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                    <label class="form-label small fw-bold mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-image text-primary"></i> Course Thumbnail Image
                                    </label>
                                    <template x-if="editCourse.thumbnail">
                                        <div class="mb-3 d-flex align-items-center gap-3 p-2 bg-white rounded-3 border">
                                            <img :src="editCourse.thumbnail.startsWith('http') || editCourse.thumbnail.startsWith('/') ? editCourse.thumbnail : ('/storage/' + editCourse.thumbnail)" 
                                                 alt="Current Thumbnail" 
                                                 class="rounded-2 object-fit-cover shadow-sm" 
                                                 style="width: 80px; height: 50px;">
                                            <div>
                                                <div class="small fw-semibold text-dark">Current Thumbnail Attached</div>
                                                <div class="text-muted small" style="font-size: 0.72rem;" x-text="editCourse.thumbnail"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload New Image File (JPG, PNG, WebP, SVG up to 5MB)</label>
                                            <input type="file" name="thumbnail_file" class="form-control form-control-sm" accept="image/*">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Set Image URL</label>
                                            <input type="url" name="thumbnail_url" class="form-control form-control-sm" x-model="editCourse.thumbnail_url" placeholder="https://example.com/new-image.jpg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Target Audience</label>
                                <select name="target_audience" class="form-select" x-model="editCourse.target_audience">
                                    <option value="BOTH">Dual-Audience (Both)</option>
                                    <option value="CORPORATE">Corporate Workforce</option>
                                    <option value="PUBLIC">Public &amp; Academic</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Course Format</label>
                                <select name="format" class="form-select" x-model="editCourse.format">
                                    <option value="SELF_PACED">Self-Paced Modular</option>
                                    <option value="WEEKLY">Weekly Cohorts</option>
                                    <option value="TOPICS">Thematic Topics</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Passing Threshold (%)</label>
                                <input type="number" name="passing_grade" class="form-control" min="1" max="100" x-model="editCourse.passing_grade">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Recertification Cycle (Days)</label>
                                <input type="number" name="recertification_days" class="form-control" x-model="editCourse.recertification_days">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Price (Cents)</label>
                                <input type="number" name="price_cents" class="form-control" x-model="editCourse.price_cents">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Currency</label>
                                <input type="text" name="currency" class="form-control" maxlength="3" x-model="editCourse.currency">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Special Pricing Tag</label>
                                <input type="text" name="special_pricing_tag" class="form-control" placeholder="e.g. Early Bird Revision, Term 1 Special" x-model="editCourse.special_pricing_tag">
                            </div>
                            <div class="col-12">
                                @if($isTeacher)
                                    <div class="alert alert-light border py-2 px-3 small rounded-3 d-flex align-items-center gap-2 mb-0">
                                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                        <div>
                                            <span class="fw-semibold">Publication Control:</span> Course public status is governed by Academic Administrators. Re-uploading materials or updating critical sections will place the course under moderation review.
                                        </div>
                                    </div>
                                @else
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="editPublishSwitch" x-model="editCourse.is_published">
                                        <label class="form-check-label fw-semibold" for="editPublishSwitch">Published &amp; Live in Public Catalogs</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" @click="showEditModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals moved outside table markup to avoid table cell layout clipping -->
    @foreach($courses as $course)
        @if(!$isTeacher)
            <!-- Reject Course Modal -->
            <div class="modal fade" id="rejectCourseModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered text-start">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-bottom pb-3">
                            <h5 class="modal-title fw-bold text-danger">
                                <i class="bi bi-shield-x me-2"></i> Reject Course Submission
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.courses.reject', $course->id) }}">
                            @csrf
                            <div class="modal-body py-3">
                                <p class="small text-secondary mb-3">
                                    You are returning <strong>{{ $course->title }}</strong> to draft status. The instructor (<strong>{{ $course->instructor?->name ?? 'Instructor' }}</strong>) will receive a compliance notification to revise their material or syllabus.
                                </p>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Reason for Rejection / Revision Notes <span class="text-danger">*</span></label>
                                    <textarea name="reason" rows="4" class="form-control" placeholder="Specify why this course cannot be approved for public listing (e.g. syllabus incomplete, specimen paper guidance missing)..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top pt-2">
                                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                    <i class="bi bi-x-circle-fill me-1"></i> Reject &amp; Notify Instructor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="modal fade" id="rejectPricingModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered text-start">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom pb-3">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-tag-fill me-2"></i> Reject Pricing Proposal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.reject-pricing', $course->id) }}">
                        @csrf
                        <div class="modal-body py-3">
                            <p class="small text-secondary mb-3">
                                You are rejecting the proposed price ({{ $course->currency }} {{ number_format(($course->proposed_price_cents ?? 0) / 100, 2) }}) and special tag "{{ $course->special_pricing_tag ?? 'None' }}" for <strong>{{ $course->title }}</strong> submitted by <strong>{{ $course->instructor?->name ?? 'Instructor' }}</strong>. Please provide the non-compliance reason below.
                            </p>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Reason for Pricing Non-Compliance <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="4" class="form-control" placeholder="Specify why the pricing or special tag was not compliant (e.g. price outside approved band, discount period invalid)..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-2">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Reject &amp; Email Instructor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- 4. COURSE ASSET & CREATION UPLOAD PROGRESS MODAL -->
    <div class="modal fade" id="courseCreationProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="z-index: 1080; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
            <div class="modal-content border-0 rounded-4 shadow-2xl p-4 text-center" style="background: var(--lms-card-bg, #FFFFFF);">
                <!-- Animated Spinner & Pulse -->
                <div class="mb-3 d-inline-flex align-items-center justify-content-center mx-auto rounded-circle position-relative" style="width: 80px; height: 80px; background: rgba(114, 62, 195, 0.1);">
                    <div id="courseProgressSpinner" class="spinner-border text-primary position-absolute" style="width: 80px; height: 80px; border-width: 3px;" role="status"></div>
                    <i id="courseProgressIcon" class="bi bi-cloud-arrow-up-fill text-primary fs-2"></i>
                </div>

                <h4 class="fw-bold mb-1" id="courseProgressTitle" style="color: #0F172A;">Building Course Curriculum</h4>
                <div class="text-muted small mb-3" id="courseProgressSubtitle">Uploading media assets &amp; generating syllabus structure...</div>

                <!-- Big Percentage Display -->
                <div class="display-5 fw-bold font-mono mb-2" id="courseProgressPercent" style="color: #723EC3;">0%</div>

                <!-- Progress Bar -->
                <div class="progress mb-3 rounded-pill" style="height: 14px; background: #E2E8F0; overflow: hidden;">
                    <div id="courseProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #723EC3, #0284C7); transition: width 0.25s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Milestone Box -->
                <div class="p-2.5 rounded-3 mb-3 text-start small font-mono border" style="background: rgba(248, 250, 252, 0.8); border-color: rgba(226, 232, 240, 0.8) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Live Progress Stage</span>
                        <span id="courseProgressBytes" class="text-secondary fw-semibold" style="font-size: 0.72rem;">0 MB / 0 MB</span>
                    </div>
                    <div id="courseProgressStage" class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">
                        Preparing upload stream...
                    </div>
                </div>

                <div class="text-muted small" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> Please keep this window open while files are uploading and syllabus is being processed.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_THUMBNAIL_BYTES = 5 * 1024 * 1024; // 5 MB
    const MAX_MEDIA_BYTES = 50 * 1024 * 1024;    // 50 MB

    function validateFileInput(input, maxBytes, friendlyName) {
        if (!input || !input.files || input.files.length === 0) return true;
        const file = input.files[0];
        if (file.size > maxBytes) {
            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            const limitMb = (maxBytes / (1024 * 1024)).toFixed(0);
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                html: `<div class="text-start">
                    <p class="mb-2">The selected <strong>${friendlyName}</strong> is <strong>${sizeMb} MB</strong>, which exceeds the maximum allowed size of <strong>${limitMb} MB</strong>.</p>
                    <p class="text-muted small mb-0">Please choose an image under ${limitMb} MB or compress the file before uploading.</p>
                </div>`,
                confirmButtonColor: '#723EC3'
            });
            input.value = '';
            return false;
        }
        return true;
    }

    // Attach change listeners for immediate feedback
    document.querySelectorAll('input[type="file"][name="thumbnail_file"]').forEach(function(input) {
        input.addEventListener('change', function() {
            validateFileInput(this, MAX_THUMBNAIL_BYTES, 'Course Thumbnail');
        });
    });

    document.querySelectorAll('input[type="file"][name="video_file"]').forEach(function(input) {
        input.addEventListener('change', function() {
            validateFileInput(this, MAX_MEDIA_BYTES, 'Lecture Video');
        });
    });

    document.querySelectorAll('input[type="file"][name="material_file"]').forEach(function(input) {
        input.addEventListener('change', function() {
            validateFileInput(this, MAX_MEDIA_BYTES, 'Course Study Material');
        });
    });

    function submitCourseFormWithProgress(form, modeTitle) {
        const modalEl = document.getElementById('courseCreationProgressModal');
        let bsModal = bootstrap.Modal.getInstance(modalEl);
        if (!bsModal) {
            bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
        }

        const titleEl = document.getElementById('courseProgressTitle');
        const subtitleEl = document.getElementById('courseProgressSubtitle');
        const percentEl = document.getElementById('courseProgressPercent');
        const barEl = document.getElementById('courseProgressBar');
        const bytesEl = document.getElementById('courseProgressBytes');
        const stageEl = document.getElementById('courseProgressStage');
        const iconEl = document.getElementById('courseProgressIcon');
        const spinnerEl = document.getElementById('courseProgressSpinner');

        titleEl.textContent = modeTitle || 'Creating Cambridge Course';
        subtitleEl.textContent = 'Uploading media assets & generating syllabus structure...';
        percentEl.textContent = '0%';
        barEl.style.width = '0%';
        bytesEl.textContent = 'Calculating...';
        stageEl.textContent = 'Initiating upload stream...';
        iconEl.className = 'bi bi-cloud-arrow-up-fill text-primary fs-2';
        spinnerEl.style.display = 'inline-block';

        bsModal.show();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        let simInterval = null;

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable && e.total > 0) {
                const rawPercent = (e.loaded / e.total);
                const displayPercent = Math.min(80, Math.round(rawPercent * 80));
                barEl.style.width = displayPercent + '%';
                percentEl.textContent = displayPercent + '%';

                const loadedMb = (e.loaded / (1024 * 1024)).toFixed(1);
                const totalMb = (e.total / (1024 * 1024)).toFixed(1);
                bytesEl.textContent = loadedMb + ' MB / ' + totalMb + ' MB';

                if (rawPercent < 0.35) {
                    stageEl.textContent = 'Uploading media files (video lecture & materials)...';
                } else if (rawPercent < 0.75) {
                    stageEl.textContent = 'Writing assets to secure storage & processing syllabus...';
                } else {
                    stageEl.textContent = 'Verifying curriculum metadata & signatures...';
                }
            }
        });

        xhr.upload.addEventListener('load', function() {
            let sim = 82;
            barEl.style.width = sim + '%';
            percentEl.textContent = sim + '%';
            stageEl.textContent = 'Extracting curriculum framework & generating module hierarchy...';

            simInterval = setInterval(function() {
                if (sim < 96) {
                    sim += 2;
                    barEl.style.width = sim + '%';
                    percentEl.textContent = sim + '%';
                    if (sim >= 90 && sim < 94) {
                        stageEl.textContent = 'Configuring grade boundaries and pass thresholds...';
                    } else if (sim >= 94) {
                        stageEl.textContent = 'Finalizing course indexing & publishing...';
                    }
                }
            }, 500);
        });

        xhr.addEventListener('load', function() {
            if (simInterval) clearInterval(simInterval);

            if (xhr.status >= 200 && xhr.status < 400) {
                barEl.style.width = '100%';
                percentEl.textContent = '100%';
                stageEl.textContent = 'Course saved successfully! Redirecting...';
                iconEl.className = 'bi bi-check-circle-fill text-success fs-2';
                spinnerEl.style.display = 'none';

                let redirectUrl = '{{ route('admin.courses.index') }}';
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.redirect) redirectUrl = res.redirect;
                } catch (err) {
                    if (xhr.responseURL) redirectUrl = xhr.responseURL;
                }

                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 600);
            } else if (xhr.status === 422) {
                bsModal.hide();
                try {
                    const res = JSON.parse(xhr.responseText);
                    let errorHtml = '<div class="text-start"><ul class="text-danger small mb-0 ps-3">';
                    if (res.errors) {
                        Object.values(res.errors).forEach(function(arr) {
                            arr.forEach(function(msg) {
                                errorHtml += '<li>' + msg + '</li>';
                            });
                        });
                    } else if (res.message) {
                        errorHtml += '<li>' + res.message + '</li>';
                    }
                    errorHtml += '</ul></div>';
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorHtml,
                        confirmButtonColor: '#723EC3'
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Error',
                        text: 'Please review your input fields and try again.',
                        confirmButtonColor: '#723EC3'
                    });
                }
            } else {
                bsModal.hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error Saving Course',
                    text: 'An unexpected server error occurred (' + xhr.status + '). Please try again.',
                    confirmButtonColor: '#723EC3'
                });
            }
        });

        xhr.addEventListener('error', function() {
            if (simInterval) clearInterval(simInterval);
            bsModal.hide();
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Upload connection interrupted. Please check your internet connection and retry.',
                confirmButtonColor: '#723EC3'
            });
        });

        xhr.open(form.method || 'POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    }

    // Validate and submit with progress
    const createForm = document.querySelector('#createCourseModal form');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            const thumbInput = createForm.querySelector('input[name="thumbnail_file"]');
            if (thumbInput && !validateFileInput(thumbInput, MAX_THUMBNAIL_BYTES, 'Course Thumbnail')) {
                e.preventDefault();
                return false;
            }
            const videoInput = createForm.querySelector('input[name="video_file"]');
            if (videoInput && !validateFileInput(videoInput, MAX_MEDIA_BYTES, 'Lecture Video')) {
                e.preventDefault();
                return false;
            }
            const matInput = createForm.querySelector('input[name="material_file"]');
            if (matInput && !validateFileInput(matInput, MAX_MEDIA_BYTES, 'Course Study Material')) {
                e.preventDefault();
                return false;
            }

            e.preventDefault();
            submitCourseFormWithProgress(createForm, 'Creating Cambridge Course');
        });
    }

    const editForm = document.querySelector('#editCourseModal form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const thumbInput = editForm.querySelector('input[name="thumbnail_file"]');
            if (thumbInput && !validateFileInput(thumbInput, MAX_THUMBNAIL_BYTES, 'Course Thumbnail')) {
                e.preventDefault();
                return false;
            }
            const videoInput = editForm.querySelector('input[name="video_file"]');
            if (videoInput && !validateFileInput(videoInput, MAX_MEDIA_BYTES, 'Lecture Video')) {
                e.preventDefault();
                return false;
            }
            const matInput = editForm.querySelector('input[name="material_file"]');
            if (matInput && !validateFileInput(matInput, MAX_MEDIA_BYTES, 'Course Study Material')) {
                e.preventDefault();
                return false;
            }

            e.preventDefault();
            submitCourseFormWithProgress(editForm, 'Updating Course & Materials');
        });
    }
});
</script>
@endpush

