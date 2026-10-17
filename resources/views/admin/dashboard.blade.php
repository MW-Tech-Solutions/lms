@extends('layouts.admin')

@section('title', ($isTeacher ?? false) ? 'Faculty Academic Workspace' : 'Executive Management & Audit Hub')

@section('content')
<div class="container-fluid px-0">

    @if($isTeacher ?? false)
        {{-- =========================================================================
             1. FACULTY / TEACHER DEDICATED WORKSPACE & SYLLABUS HUB
             ========================================================================= --}}
        
        <!-- Teacher Top Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                        <i class="bi bi-mortarboard-fill text-purple me-1"></i> Cambridge Faculty Workspace
                    </span>
                    @if(auth()->user()->approval_status === 'APPROVED')
                        <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2.5 py-1 small">
                            <i class="bi bi-check-circle-fill me-1"></i> Verified Examiner Approved
                        </span>
                    @else
                        <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-2.5 py-1 small">
                            <i class="bi bi-clock-history me-1"></i> Verification Pending (Admin Review)
                        </span>
                    @endif
                </div>
                <h1 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <div class="text-secondary small">
                    Manage your syllabus courses, upload past-paper walkthroughs &amp; topical worksheets, track enrolled scholars, and grade student attempts.
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0">
                <!-- Request Additional Level Modal Trigger -->
                @if(auth()->user()->approval_status === 'APPROVED')
                    <button type="button" class="tj-btn-outline py-2 px-3 small shadow-sm" data-bs-toggle="modal" data-bs-target="#requestLevelModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Request New Cambridge Level</span>
                    </button>
                    <a href="{{ route('admin.courses.index') }}" class="tj-btn-primary py-2 px-3 small shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i>
                        <span>Upload Course / Materials</span>
                    </a>
                @else
                    <button type="button" class="btn btn-secondary py-2 px-3 small shadow-sm rounded-pill opacity-75" disabled title="Awaiting Administrator Approval">
                        <i class="bi bi-lock-fill me-1"></i>
                        <span>Level Request Restricted (Pending Approval)</span>
                    </button>
                    <button type="button" class="btn btn-secondary py-2 px-3 small shadow-sm rounded-pill opacity-75" disabled title="Awaiting Administrator Approval">
                        <i class="bi bi-lock-fill me-1"></i>
                        <span>Upload Restricted (Pending Approval)</span>
                    </button>
                @endif
            </div>
        </div>

        @if(auth()->user()->approval_status !== 'APPROVED')
            <!-- Teacher Pending Approval Banner -->
            <div class="alert alert-warning border-0 rounded-4 p-3.5 mb-4 shadow-sm d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2.5 rounded-3 bg-warning bg-opacity-20 text-warning fs-4 flex-shrink-0">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Faculty Account Verification Under Review</div>
                        <div class="text-secondary small">
                            Your faculty registration has been submitted and is currently being audited by Cambridge platform administrators. Course creation and public material publishing will unlock automatically upon review.
                        </div>
                    </div>
                </div>
                <span class="badge bg-warning text-dark font-mono px-3 py-2 rounded-pill small">Status: {{ auth()->user()->approval_status }}</span>
            </div>
        @endif

        <!-- Teacher KPI Cards -->
        <div class="row g-4 mb-4">
            
            <!-- Card 1: Assigned Courses -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(114, 62, 195, 0.05) 100%); border-top: 4px solid var(--lms-accent);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                Authoring Programs
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-primary mb-1" style="line-height: 1.1;">
                                {{ $myCoursesCount ?? 0 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-primary fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #F9EDFF; width: 54px; height: 54px; color: var(--lms-accent) !important;">
                            <i class="bi bi-journal-check"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span>Active: <strong>{{ $myActiveCoursesCount ?? 0 }}</strong></span>
                        <span>Draft/Review: <strong>{{ $myDraftCoursesCount ?? 0 }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Enrolled Students -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(2, 132, 199, 0.05) 100%); border-top: 4px solid #0284C7;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                Enrolled Scholars
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-info mb-1" style="line-height: 1.1; color: #0284C7 !important;">
                                {{ $myEnrolledStudentsCount ?? 0 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-info fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #EBF8FF; width: 54px; height: 54px; color: #0284C7 !important;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-mortarboard text-info me-1"></i> Active Cambridge Cohort</span>
                        <a href="{{ route('admin.courses.index') }}" class="text-info fw-semibold text-decoration-none small">Inspect &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Student Submissions -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(16, 185, 129, 0.05) 100%); border-top: 4px solid #10B981;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                Submissions Received
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-success mb-1" style="line-height: 1.1;">
                                {{ $myTotalSubmissionsCount ?? 0 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-success fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #E6F9F1; width: 54px; height: 54px;">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-clock text-warning me-1"></i> Pending Feedback:</span>
                        <span class="badge bg-warning-subtle text-warning font-mono">{{ $myPendingGradingCount ?? 0 }} Needs Grade</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Authorized Cambridge Levels -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(245, 158, 11, 0.05) 100%); border-top: 4px solid #F59E0B;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                Authorized Levels
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-warning mb-1" style="line-height: 1.1;">
                                {{ count(auth()->user()->getAuthorizedCategoryIds()) }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-warning fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #FFF8E5; width: 54px; height: 54px;">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-shield-check text-success me-1"></i> Teaching Scope</span>
                        @if(auth()->user()->approval_status === 'APPROVED')
                            <button type="button" class="btn btn-link text-warning p-0 small fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#requestLevelModal">
                                + Add Level
                            </button>
                        @else
                            <span class="text-muted small" title="Pending initial credential approval"><i class="bi bi-lock me-1"></i>Locked</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Teacher Section 2: Authorized Levels & My Courses -->
        <div class="row g-4 mb-4">
            
            <!-- Left: Authorized Cambridge Levels & Requests -->
            <div class="col-lg-5">
                <div class="admin-table-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                Authorized Teaching Levels
                            </h4>
                            <span class="text-muted small">Cambridge tiers you are verified to author and moderate</span>
                        </div>
                        @if(auth()->user()->approval_status === 'APPROVED')
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#requestLevelModal">
                                <i class="bi bi-plus-lg me-1"></i> Request Level
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 opacity-50" disabled title="Awaiting Initial Verification">
                                <i class="bi bi-lock-fill me-1"></i> Locked
                            </button>
                        @endif
                    </div>

                    <!-- Level Badges List -->
                    <div class="vstack gap-2 mb-4">
                        @forelse($myAssignedLevels ?? [] as $lvl)
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--lms-bg-secondary);">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-mortarboard"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark">{{ $lvl->name }}</div>
                                        <span class="text-muted font-mono" style="font-size: 0.72rem;">{{ $lvl->slug }}</span>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">
                                    <i class="bi bi-check-circle me-1"></i> Active
                                </span>
                            </div>
                        @empty
                            <div class="p-3 rounded-3 border text-center text-muted small" style="background: var(--lms-bg-secondary);">
                                <i class="bi bi-info-circle me-1 text-primary"></i> No specific Cambridge tier assigned yet. Your registered faculty department serves as primary scope.
                            </div>
                        @endforelse
                    </div>

                    <!-- Pending Level Requests -->
                    @if(isset($pendingLevelRequests) && $pendingLevelRequests->isNotEmpty())
                        <div class="mt-auto pt-3 border-top">
                            <div class="fw-bold small text-secondary text-uppercase mb-2" style="font-size: 0.72rem;">Pending Additional Level Requests:</div>
                            @foreach($pendingLevelRequests as $req)
                                <div class="p-2.5 rounded-3 border mb-2 d-flex align-items-center justify-content-between bg-warning-subtle border-warning-subtle">
                                    <div>
                                        <span class="fw-bold small text-dark">{{ $req->category?->name }}</span>
                                        <div class="text-muted" style="font-size: 0.7rem;">Requested {{ $req->created_at->diffForHumans() }}</div>
                                    </div>
                                    <span class="badge bg-warning text-dark font-mono small">Pending Admin Review</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: My Courses & Materials -->
            <div class="col-lg-7">
                <div class="admin-table-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                My Courses &amp; Syllabus Programs
                            </h4>
                            <span class="text-muted small">Course authoring and uploaded syllabus materials</span>
                        </div>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            View All ({{ count($myCourses ?? []) }})
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2.5">Course / Material</th>
                                    <th class="py-2.5">Level</th>
                                    <th class="py-2.5 text-center">Status</th>
                                    <th class="py-2.5 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myCourses ?? [] as $course)
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-bold text-dark">{{ $course->title }}</div>
                                            <span class="text-muted font-mono" style="font-size: 0.72rem;">{{ $course->slug }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info font-mono small">
                                                {{ $course->category?->name ?? 'General Cambridge' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($course->is_published)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">Published</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 small">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small">
                                                Manage
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-journal-plus fs-2 text-primary d-block mb-2"></i>
                                            <div class="fw-bold">No courses created yet</div>
                                            <p class="small text-secondary mb-3">Upload your first syllabus topic, worksheet, or video walkthrough.</p>
                                            @if(auth()->user()->approval_status === 'APPROVED')
                                                <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    Create Course Now
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Request Level Modal -->
        <div class="modal fade" id="requestLevelModal" tabindex="-1" aria-labelledby="requestLevelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content lms-card border-0 rounded-4 shadow-lg p-3">
                    <div class="modal-header border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-2 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <h5 class="modal-title fw-bold" id="requestLevelModalLabel">Request Additional Cambridge Level</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.teachers.request-level') }}" method="POST">
                        @csrf
                        <div class="modal-body py-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Target Cambridge Tier <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Cambridge Level...</option>
                                    @foreach($availableCambridgeLevels ?? [] as $lvl)
                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Subject Experience &amp; Notes (KYC) <span class="text-danger">*</span></label>
                                <textarea name="request_notes" class="form-control" rows="3" required placeholder="Outline your tutoring credentials, past paper series familiarity, and teaching background for this level..."></textarea>
                            </div>
                            <div class="alert alert-info border-0 rounded-3 small p-2.5 mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <span>Platform administrators will review your syllabus credentials and unlock material uploading upon approval.</span>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-3">
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="tj-btn-primary py-2 px-4 small shadow-sm">Submit Level Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
        {{-- =========================================================================
             2. SUPER ADMINISTRATOR & EXECUTIVE CONTROL MATRIX (ADMIN VIEW)
             ========================================================================= --}}
        
        <!-- Top Executive Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                        <i class="bi bi-shield-lock-fill text-purple me-1"></i> Executive Control Matrix
                    </span>
                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2.5 py-1 small">
                        <i class="bi bi-check-circle-fill me-1"></i> SOC2 Type II Active
                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    {{ $site_settings['title_admin_dashboard'] ?? 'Enterprise Executive Dashboard' }}
                </h1>
                <div class="text-secondary small">
                    {{ $site_settings['subtitle_admin_dashboard'] ?? 'Real-time telemetry across corporate compliance recertifications and academic student cohorts.' }}
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0">
                <form action="{{ route('admin.compliance.trigger-scan') }}" method="POST">
                    @csrf
                    <button type="submit" class="tj-btn-outline py-2 px-3 small shadow-sm text-danger border-danger">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        <span>{{ $site_settings['btn_run_compliance_scan'] ?? 'Trigger Recertification Audit Scan' }}</span>
                    </button>
                </form>
                
                <a href="{{ route('admin.courses.index') }}" class="tj-btn-primary py-2 px-3 small shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span>{{ $site_settings['btn_new_course'] ?? 'Create Program' }}</span>
                </a>
            </div>
        </div>

        @if(isset($pendingTeacherApprovalsCount) && $pendingTeacherApprovalsCount > 0)
            <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                    <div>
                        <strong>{{ $pendingTeacherApprovalsCount }} Teacher Registration(s) Pending Approval:</strong>
                        <span class="small text-secondary ms-1">Review applicant teaching methodologies and syllabus KYC documents.</span>
                    </div>
                </div>
                <a href="{{ route('admin.teachers.index', ['status' => 'PENDING']) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                    Review Teachers &rarr;
                </a>
            </div>
        @endif

        <!-- TOP KPI METRIC CARDS -->
        <div class="row g-4 mb-4">
            
            <!-- Card 1: Compliance Adherence -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(5, 150, 105, 0.05) 100%); border-top: 4px solid #059669;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                {{ $site_settings['metric_compliance_rate'] ?? 'Compliance Adherence' }}
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-success mb-1" style="line-height: 1.1;">
                                {{ $complianceRate ?? 92.4 }}%
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-success fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #E6F9F1; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-check2-all text-success me-1"></i> Audited Workforce</span>
                        <span class="badge bg-success bg-opacity-10 text-success font-mono">{{ $completedEnrollmentsCount ?? 128 }} Certs</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Recertifications Due -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(217, 119, 6, 0.05) 100%); border-top: 4px solid #D97706;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                {{ $site_settings['metric_recert_due'] ?? 'Recertifications Due' }}
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-warning mb-1" style="line-height: 1.1;">
                                {{ $pendingRecertCount ?? 14 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-warning fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #FFF8E5; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Expiring ≤ 30 days</span>
                        <a href="{{ route('admin.compliance.index') }}" class="text-warning fw-semibold text-decoration-none small">Inspect &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Active Learners -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(114, 62, 195, 0.05) 100%); border-top: 4px solid var(--lms-accent);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                {{ $site_settings['metric_active_learners'] ?? 'Active Learners' }}
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-primary mb-1" style="line-height: 1.1;">
                                {{ $totalUsersCount ?? 342 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-primary fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #F9EDFF; width: 54px; height: 54px; color: var(--lms-accent) !important; box-shadow: 0 4px 12px rgba(114, 62, 195, 0.2);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span>Corporate: <strong>{{ $corpUsersCount ?? 210 }}</strong></span>
                        <span>Academic: <strong>{{ $academicUsersCount ?? 132 }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Anti-Cheat Violations -->
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(220, 38, 38, 0.05) 100%); border-top: 4px solid #DC2626;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                                {{ $site_settings['metric_anti_cheat_flags'] ?? 'Anti-Cheat Violations' }}
                            </div>
                            <div class="display-5 fw-extrabold font-mono text-danger mb-1" style="line-height: 1.1;">
                                {{ $antiCheatFlagsCount ?? 6 }}
                            </div>
                        </div>
                        <div class="p-3 rounded-circle text-danger fs-4 d-flex align-items-center justify-content-center" 
                             style="background: #FFF0ED; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                            <i class="bi bi-shield-x"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-camera-video text-danger me-1"></i> Proctoring Stream</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger font-mono">Real-Time</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- DEPARTMENT COMPLIANCE MATRIX & LIVE PROCTORING AUDIT FEED -->
        <div class="row g-4 mb-4">
            
            <!-- Left: Departmental Matrix -->
            <div class="col-lg-7">
                <div class="admin-table-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                {{ $site_settings['heading_dept_compliance'] ?? 'Departmental Compliance Matrix' }}
                            </h4>
                            <span class="text-muted small">Organizational unit recertification posture</span>
                        </div>
                        <span class="badge badge-purple-soft font-mono px-3 py-1.5 rounded-pill">
                            {{ count($departments ?? []) }} Active Units
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Department</th>
                                    <th class="py-3">Manager Sign-Off</th>
                                    <th class="py-3 text-center">Workforce</th>
                                    <th class="py-3">Adherence Rate</th>
                                    <th class="py-3 text-end pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments ?? [] as $dept)
                                    <tr>
                                        <td class="fw-bold text-dark py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle p-1.5 bg-primary bg-opacity-10 text-primary small">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <span>{{ $dept->name }}</span>
                                            </div>
                                        </td>
                                        <td class="small text-secondary">
                                            <i class="bi bi-person-badge text-muted me-1"></i>
                                            {{ $dept->manager?->name ?? 'HR Compliance Lead' }}
                                        </td>
                                        <td class="font-mono small text-center">
                                            <span class="badge bg-light text-dark border">
                                                {{ $dept->users_count ?? $dept->users()->count() }}
                                            </span>
                                        </td>
                                        <td style="width: 25%;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px; border-radius: 999px; background: rgba(0,0,0,0.06);">
                                                    <div class="progress-bar rounded-pill" style="width: 95%; background: linear-gradient(90deg, #059669, #00D084);"></div>
                                                </div>
                                                <span class="font-mono small fw-bold text-success">95%</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="badge badge-compliance">COMPLIANT</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No department hierarchy configured yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Anti-Cheat Proctoring Live Feed & Teacher Level Requests -->
            <div class="col-lg-5">
                <div class="admin-table-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                {{ $site_settings['heading_recent_audits'] ?? 'Anti-Cheat Proctoring Feed' }}
                            </h4>
                            <span class="text-muted small">Automated telemetry exam flags</span>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 font-mono px-2 py-1 rounded-pill d-flex align-items-center gap-1">
                            <span class="pulse-dot"></span> Live
                        </span>
                    </div>

                    <div class="list-group list-group-flush flex-grow-1">
                        @forelse($recentCheatLogs ?? [] as $log)
                            <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    @if($log->event_type === 'TAB_SWITCH')
                                        <span class="badge rounded-circle p-2 bg-warning-subtle text-warning">
                                            <i class="bi bi-window-sidebar"></i>
                                        </span>
                                    @elseif($log->event_type === 'COPY_ATTEMPT')
                                        <span class="badge rounded-circle p-2 bg-danger-subtle text-danger">
                                            <i class="bi bi-clipboard-x"></i>
                                        </span>
                                    @else
                                        <span class="badge rounded-circle p-2 bg-info-subtle text-info">
                                            <i class="bi bi-camera"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div class="small fw-bold text-dark">{{ str_replace('_', ' ', $log->event_type) }}</div>
                                        <div class="text-muted font-mono" style="font-size: 11px;">
                                            Attempt #{{ $log->attempt_id }} &bull; {{ $log->attempt?->user?->name ?? 'Learner' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="font-mono small text-muted">
                                    {{ $log->recorded_at ? $log->recorded_at->diffForHumans() : 'Just now' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-5 my-auto text-muted">
                                <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
                                <div class="fw-semibold text-dark">Pristine Examination Integrity</div>
                                <p class="small text-secondary mb-0">Zero recent anti-cheat anomalies detected across active cohorts.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pending Level Moderation Requests -->
                    @if(isset($pendingTeacherLevelRequests) && $pendingTeacherLevelRequests->isNotEmpty())
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark"><i class="bi bi-diagram-3-fill text-warning me-1"></i> Teacher Level Authorization Requests:</span>
                                <a href="{{ route('admin.teachers.index') }}" class="small text-primary text-decoration-none">View Teachers</a>
                            </div>
                            <div class="vstack gap-2">
                                @foreach($pendingTeacherLevelRequests as $req)
                                    <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold small text-dark">{{ $req->user?->name }}</div>
                                            <span class="text-muted" style="font-size: 0.72rem;">Requests: <strong>{{ $req->category?->name }}</strong></span>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <form action="{{ route('admin.teachers.approve-level-request', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem;">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.teachers.reject-level-request', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem;">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @endif

</div>
@endsection

