@extends('layouts.admin')

@section('title', 'Assessments & Anti-Cheat Proctoring Engine')

@section('content')
<div class="container-fluid" x-data="{ selectedSnapshot: null }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_quizzes_manager'] ?? 'Examinations & Anti-Cheat Proctoring' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_quizzes_manager'] ?? 'Configure secure exam policies, shuffle pools, webcam intervals, and audit proctoring logs.' }}</div>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Courses
        </a>
    </div>

    <!-- Active Assessments -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-patch-question text-primary me-2"></i>Configured Assessments</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Quiz Title</th>
                        <th>Time Limit</th>
                        <th>Passing Threshold</th>
                        <th>Max Attempts</th>
                        <th>Anti-Cheat Directives</th>
                        <th>Completed Attempts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td class="fw-bold">{{ $quiz->title }}</td>
                            <td class="font-mono">{{ $quiz->time_limit_minutes }} Mins</td>
                            <td class="font-mono">{{ $quiz->passing_percentage }}%</td>
                            <td class="font-mono">{{ $quiz->max_attempts }} Attempt(s)</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="badge bg-danger-subtle text-danger font-mono small">Blur Detection</span>
                                    <span class="badge bg-warning-subtle text-warning font-mono small">Copy Intercept</span>
                                    <span class="badge bg-info-subtle text-info font-mono small">Webcam Snapshots</span>
                                </div>
                            </td>
                            <td class="font-mono">{{ $quiz->attempts()->count() }}</td>
                            <td>
                                <a href="{{ route('quiz.take', ['quiz' => $quiz->id]) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-play-circle me-1"></i> Test Exam
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No quizzes configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Proctoring Anti-Cheat Event Audit Log -->
    <div class="lms-card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-camera-video-fill text-danger me-2"></i>Proctoring Telemetry &amp; Violation Snapshots</h5>
            <span class="badge bg-danger font-mono">{{ $cheatLogs->count() }} Recorded Incidents</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Recorded At</th>
                        <th>Attempt ID</th>
                        <th>Learner</th>
                        <th>Violation Type</th>
                        <th>Webcam Capture</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cheatLogs as $log)
                        <tr>
                            <td class="font-mono small text-muted">{{ $log->recorded_at->format('Y-m-d H:i:s') }}</td>
                            <td class="font-mono small">#{{ $log->attempt_id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $log->attempt?->user?->name ?? 'Learner' }}</div>
                                <div class="small text-muted">{{ $log->attempt?->user?->email }}</div>
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
                            <td>
                                @if($log->snapshot_url)
                                    <button class="btn btn-sm btn-outline-primary" @click="selectedSnapshot = '{{ $log->snapshot_url }}'">
                                        <i class="bi bi-image me-1"></i> Inspect Frame
                                    </button>
                                @else
                                    <span class="text-muted small">Telemetry Only</span>
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
    </div>

    <!-- Snapshot Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" x-show="selectedSnapshot" x-cloak style="background: rgba(0,0,0,0.6);" @click.self="selectedSnapshot = null" @keydown.escape.window="selectedSnapshot = null">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-3">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Proctoring AI Snapshot Inspection</h6>
                    <button type="button" class="btn-close" @click="selectedSnapshot = null"></button>
                </div>
                <div class="modal-body text-center">
                    <img :src="selectedSnapshot" alt="Proctoring Snapshot" class="img-fluid rounded border shadow-sm" style="max-height: 380px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
