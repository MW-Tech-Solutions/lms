@extends('layouts.admin')

@section('title', 'Assessments & Anti-Cheat Proctoring Engine')

@section('content')
<div class="container-fluid" 
     x-data="{ 
         selectedSnapshot: null,
         showCreateModal: false,
         showEditModal: false,

         // Edit Quiz Object
         editQuiz: {
             id: null,
             title: '',
             time_limit_minutes: 30,
             max_attempts: 1,
             passing_percentage: 70,
             blur_detection: true,
             block_copy_paste: true,
             webcam_snapshots: true,
             shuffle_questions: false,
             is_active: true
         },

         openEditQuiz(quiz) {
             const flags = quiz.anti_cheat_flags || {};
             this.editQuiz = {
                 id: quiz.id,
                 title: quiz.title,
                 time_limit_minutes: quiz.time_limit_minutes,
                 max_attempts: quiz.max_attempts,
                 passing_percentage: quiz.passing_percentage,
                 blur_detection: Boolean(flags.blur_detection ?? true),
                 block_copy_paste: Boolean(flags.block_copy_paste ?? true),
                 webcam_snapshots: Boolean(flags.webcam_snapshots ?? true),
                 shuffle_questions: Boolean(quiz.shuffle_questions),
                 is_active: Boolean(flags.is_active ?? true)
             };
             this.showEditModal = true;
         }
     }">

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                    <i class="bi bi-shield-shaded text-purple me-1"></i> Assessment &amp; Proctoring Matrix
                </span>
                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2.5 py-1 small">
                    <i class="bi bi-camera-video-fill me-1"></i> AI Proctoring Active
                </span>
            </div>
            <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                {{ $site_settings['title_quizzes_manager'] ?? 'Examinations & Anti-Cheat Proctoring' }}
            </h2>
            <div class="text-secondary small">
                {{ $site_settings['subtitle_quizzes_manager'] ?? 'Configure secure exam policies, trial times, time limits, anti-cheat webcam intervals, and audit proctoring logs.' }}
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Courses
            </a>

            <!-- Create Assessment Modal Trigger -->
            <button class="tj-btn-primary py-2 px-3 small shadow-sm" type="button" @click="showCreateModal = true">
                <i class="bi bi-plus-lg me-1"></i>
                <span>Create Assessment / Exam</span>
            </button>
        </div>
    </div>

    <!-- Active Assessments Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    <i class="bi bi-patch-question text-primary me-2"></i>Configured Assessments &amp; Examinations
                </h5>
                <span class="text-muted small">Total Configured Quizzes: {{ $quizzes->count() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Quiz Title</th>
                        <th class="py-3">Time Limit</th>
                        <th class="py-3">Passing Threshold</th>
                        <th class="py-3">Trial Times (Attempts)</th>
                        <th class="py-3">Anti-Cheat Directives</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center">Completed</th>
                        <th class="py-3 text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        @php
                            $isActive = $quiz->isActive();
                            $flags = $quiz->anti_cheat_flags ?? [];
                        @endphp
                        <tr>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $quiz->title }}</div>
                                <div class="small text-muted font-mono" style="font-size: 0.75rem;">
                                    Pool: {{ $quiz->questions()->count() }} Questions &bull; {{ $quiz->shuffle_questions ? 'Shuffled' : 'Sequential' }}
                                </div>
                            </td>
                            <td class="font-mono">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ $quiz->time_limit_minutes }} Mins
                                </span>
                            </td>
                            <td class="font-mono">
                                <span class="fw-bold text-success">{{ $quiz->passing_percentage }}%</span>
                            </td>
                            <td class="font-mono">
                                <span class="badge bg-light text-dark border">
                                    {{ $quiz->max_attempts }} Attempt(s)
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if(!empty($flags['blur_detection'] ?? true))
                                        <span class="badge bg-danger-subtle text-danger font-mono small">Blur Detection</span>
                                    @endif
                                    @if(!empty($flags['block_copy_paste'] ?? true))
                                        <span class="badge bg-warning-subtle text-warning font-mono small">Copy Intercept</span>
                                    @endif
                                    @if(!empty($flags['webcam_snapshots'] ?? true))
                                        <span class="badge bg-info-subtle text-info font-mono small">Webcam Snapshots</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if($isActive)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                        <i class="bi bi-pause-circle-fill me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="font-mono text-center">
                                {{ $quiz->attempts()->count() }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex gap-1.5 justify-content-end">
                                    <!-- Edit Assessment Button -->
                                    <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm"
                                            @click="openEditQuiz({{ json_encode($quiz) }})"
                                            title="Edit Assessment &amp; Time Limits">
                                        <i class="bi bi-pencil-square"></i>
                                        <span class="d-none d-md-inline">Edit</span>
                                    </button>

                                    <!-- Toggle Active / Deactivate Status -->
                                    <form action="{{ route('admin.quizzes.toggle', $quiz->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $isActive ? 'btn-outline-warning' : 'btn-outline-success' }} d-inline-flex align-items-center shadow-sm"
                                                title="{{ $isActive ? 'Deactivate Assessment' : 'Activate Assessment' }}">
                                            <i class="bi {{ $isActive ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Test Exam Button -->
                                    <a href="{{ route('quiz.take', ['quiz' => $quiz->id]) }}" target="_blank" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm">
                                        <i class="bi bi-play-circle"></i>
                                        <span class="d-none d-lg-inline">Test</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No quizzes or exams configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Proctoring Anti-Cheat Event Audit Log -->
    <div class="lms-card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    <i class="bi bi-camera-video-fill text-danger me-2"></i>Proctoring Telemetry &amp; Violation Snapshots
                </h5>
                <span class="text-muted small">Real-time tamper resistance feed</span>
            </div>
            <span class="badge bg-danger font-mono px-3 py-1.5 rounded-pill">{{ $cheatLogs->total() }} Recorded Incidents</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Recorded At</th>
                        <th class="py-3">Attempt ID</th>
                        <th class="py-3">Learner</th>
                        <th class="py-3">Violation Type</th>
                        <th class="py-3 text-end pe-3">Webcam Capture</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cheatLogs as $log)
                        <tr>
                            <td class="font-mono small text-muted py-3">{{ $log->recorded_at->format('Y-m-d H:i:s') }}</td>
                            <td class="font-mono small">#{{ $log->attempt_id }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $log->attempt?->user?->name ?? 'Learner' }}</div>
                                <div class="small text-muted font-mono" style="font-size: 0.75rem;">{{ $log->attempt?->user?->email }}</div>
                            </td>
                            <td>
                                @if($log->event_type === 'TAB_SWITCH')
                                    <span class="badge bg-warning text-dark font-mono"><i class="bi bi-window me-1"></i>TAB SWITCH DETECTED</span>
                                @elseif($log->event_type === 'COPY_ATTEMPT')
                                    <span class="badge bg-danger font-mono"><i class="bi bi-clipboard-x me-1"></i>UNAUTHORIZED COPY</span>
                                @else
                                    <span class="badge bg-info text-dark font-mono"><i class="bi bi-camera me-1"></i>ROUTINE WEBCAM SNAPSHOT</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if($log->snapshot_url)
                                    <button class="btn btn-sm btn-outline-primary shadow-sm" @click="selectedSnapshot = '{{ $log->snapshot_url }}'">
                                        <i class="bi bi-image me-1"></i> Inspect Frame
                                    </button>
                                @else
                                    <span class="text-muted small font-mono">Telemetry Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No anti-cheat violations logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $cheatLogs->links() }}
        </div>
    </div>

    <!-- 1. CREATE ASSESSMENT / EXAM MODAL -->
    <div class="modal fade" id="createQuizModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showCreateModal }" 
         :style="showCreateModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showCreateModal = false" 
         @keydown.escape.window="showCreateModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent);">
                            <i class="bi bi-patch-question-fill"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Create Assessment, Quiz or Exam</h5>
                    </div>
                    <button type="button" class="btn-close" @click="showCreateModal = false"></button>
                </div>
                <form action="{{ route('admin.quizzes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Assessment / Examination Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g. ISO 27001 Annual Recertification Exam">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Link to Program / Course (Optional)</label>
                                <select name="course_id" class="form-select">
                                    <option value="">Independent Exam (Not tied to specific course)</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Time Limit (Minutes) <span class="text-danger">*</span></label>
                                <input type="number" name="time_limit_minutes" class="form-control font-mono" required value="30" min="1" max="480">
                                <div class="form-text small">Maximum duration allowed for each attempt.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Max Attempts / Trial Times <span class="text-danger">*</span></label>
                                <input type="number" name="max_attempts" class="form-control font-mono" required value="2" min="1" max="50">
                                <div class="form-text small">How many times a learner can attempt before locking.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Passing Threshold (%) <span class="text-danger">*</span></label>
                                <input type="number" name="passing_percentage" class="form-control font-mono" required value="80" min="1" max="100">
                                <div class="form-text small">Minimum grade percentage required to pass.</div>
                            </div>

                            <div class="col-12 mt-4 pt-2 border-top">
                                <h6 class="fw-bold small text-uppercase text-secondary font-mono mb-2">Anti-Cheat Directives &amp; Security Protocols</h6>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="blur_detection" value="1" id="createBlur" checked>
                                    <label class="form-check-label fw-semibold small" for="createBlur">
                                        Tab Blur Detection
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="block_copy_paste" value="1" id="createCopy" checked>
                                    <label class="form-check-label fw-semibold small" for="createCopy">
                                        Copy/Paste Intercept
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="webcam_snapshots" value="1" id="createWebcam" checked>
                                    <label class="form-check-label fw-semibold small" for="createWebcam">
                                        Webcam Snapshots
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="shuffle_questions" value="1" id="createShuffle">
                                    <label class="form-check-label fw-semibold small" for="createShuffle">
                                        Shuffle Questions Order
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" value="1" id="createActive" checked>
                                    <label class="form-check-label fw-semibold small" for="createActive">
                                        Activate &amp; Publish Immediately
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">Create &amp; Activate Assessment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. EDIT ASSESSMENT / EXAM MODAL -->
    <div class="modal fade" id="editQuizModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showEditModal }" 
         :style="showEditModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showEditModal = false" 
         @keydown.escape.window="showEditModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">
                            Edit Assessment: <span x-text="editQuiz.title"></span>
                        </h5>
                    </div>
                    <button type="button" class="btn-close" @click="showEditModal = false"></button>
                </div>
                <form :action="'{{ url('/admin/quizzes') }}/' + editQuiz.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Assessment Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required x-model="editQuiz.title">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Time Limit (Minutes) <span class="text-danger">*</span></label>
                                <input type="number" name="time_limit_minutes" class="form-control font-mono" required x-model="editQuiz.time_limit_minutes" min="1" max="480">
                                <div class="form-text small">Duration per exam attempt.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Max Attempts / Trial Times <span class="text-danger">*</span></label>
                                <input type="number" name="max_attempts" class="form-control font-mono" required x-model="editQuiz.max_attempts" min="1" max="50">
                                <div class="form-text small">Permitted trial times.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Passing Threshold (%) <span class="text-danger">*</span></label>
                                <input type="number" name="passing_percentage" class="form-control font-mono" required x-model="editQuiz.passing_percentage" min="1" max="100">
                                <div class="form-text small">Score required to pass.</div>
                            </div>

                            <div class="col-12 mt-4 pt-2 border-top">
                                <h6 class="fw-bold small text-uppercase text-secondary font-mono mb-2">Anti-Cheat Directives &amp; Security Options</h6>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="blur_detection" value="1" id="editBlur" x-model="editQuiz.blur_detection">
                                    <label class="form-check-label fw-semibold small" for="editBlur">
                                        Tab Blur Detection
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="block_copy_paste" value="1" id="editCopy" x-model="editQuiz.block_copy_paste">
                                    <label class="form-check-label fw-semibold small" for="editCopy">
                                        Copy/Paste Intercept
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="webcam_snapshots" value="1" id="editWebcam" x-model="editQuiz.webcam_snapshots">
                                    <label class="form-check-label fw-semibold small" for="editWebcam">
                                        Webcam Snapshots
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="shuffle_questions" value="1" id="editShuffle" x-model="editQuiz.shuffle_questions">
                                    <label class="form-check-label fw-semibold small" for="editShuffle">
                                        Shuffle Questions Order
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch p-2.5 rounded border" style="background: var(--lms-bg-secondary);">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" value="1" id="editActive" x-model="editQuiz.is_active">
                                    <label class="form-check-label fw-semibold small" for="editActive">
                                        Assessment Active &amp; Available
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">Save Assessment Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. SNAPSHOT MODAL -->
    <div class="modal fade" id="snapshotModal" tabindex="-1" role="dialog" 
         :class="{ 'show': !!selectedSnapshot }" 
         :style="selectedSnapshot ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="selectedSnapshot = null" 
         @keydown.escape.window="selectedSnapshot = null">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">
                        <i class="bi bi-camera-fill text-danger me-1"></i> Proctoring AI Snapshot Inspection
                    </h6>
                    <button type="button" class="btn-close" @click="selectedSnapshot = null"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <img :src="selectedSnapshot" alt="Proctoring Snapshot" class="img-fluid rounded border shadow-sm" style="max-height: 380px;">
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
