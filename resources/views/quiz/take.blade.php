@extends('layouts.exam')

@section('title', $quiz->title)
@section('exam_title', $quiz->title)

@section('header_timer')
    <div class="d-flex align-items-center gap-2">
        <span class="text-white-50 small text-uppercase fw-semibold">{{ $site_settings['label_time_remaining'] ?? 'Time Remaining' }}:</span>
        <span class="badge bg-danger fs-6 timer-badge px-3 py-2" x-text="formatTimer()"></span>
    </div>
@endsection

@section('content')
<div x-data="{
    attemptId: {{ $attempt->id }},
    totalSeconds: {{ $quiz->time_limit_minutes * 60 }},
    currentQuestionIndex: 0,
    totalQuestions: {{ $questions->count() }},
    answers: {},
    tabSwitches: 0,
    autosaveNotice: '',
    isSubmitting: false,

    init() {
        // Countdown timer
        const timerInterval = setInterval(() => {
            if (this.totalSeconds > 0) {
                this.totalSeconds--;
            } else {
                clearInterval(timerInterval);
                this.submitExam(true);
            }
        }, 1000);

        // Autosave periodic loop (every 15s)
        setInterval(() => {
            this.autosave();
        }, 15000);

        // Anti-Cheat Page Visibility API (Tab Switch detection)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.tabSwitches++;
                this.reportViolation('TAB_SWITCH');
            }
        });

        // Anti-Cheat Copy-Paste interception
        document.addEventListener('copy', (e) => {
            e.preventDefault();
            this.reportViolation('COPY_ATTEMPT');
            alert('Warning: Copying exam content is strictly prohibited.');
        });

        document.addEventListener('paste', (e) => {
            e.preventDefault();
            this.reportViolation('COPY_ATTEMPT');
        });

        // Periodic Webcam Snapshot (every 45s)
        setInterval(() => {
            this.captureWebcamSnapshot();
        }, 45000);
    },

    formatTimer() {
        const m = Math.floor(this.totalSeconds / 60);
        const s = this.totalSeconds % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    },

    reportViolation(type, snapshotBase64 = null) {
        fetch('{{ route('api.anti-cheat.log') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                attempt_id: this.attemptId,
                event_type: type,
                snapshot_base64: snapshotBase64
            })
        });
    },

    captureWebcamSnapshot() {
        const video = document.getElementById('proctoring-webcam');
        if (!video || !video.videoWidth) return;

        const canvas = document.createElement('canvas');
        canvas.width = 320;
        canvas.height = 240;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.6);

        this.reportViolation('WEBCAM_FLAG', dataUrl);
    },

    autosave() {
        this.autosaveNotice = 'Saving...';
        fetch('{{ route('api.quiz.autosave') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                attempt_id: this.attemptId,
                answers: this.answers
            })
        }).then(r => r.json()).then(data => {
            this.autosaveNotice = 'Progress saved ' + new Date().toLocaleTimeString();
            setTimeout(() => { this.autosaveNotice = ''; }, 3000);
        });
    },

    submitExam(force = false) {
        if (!force && !confirm('Are you sure you want to finalize and submit this exam? This action cannot be undone.')) {
            return;
        }
        this.isSubmitting = true;
        document.getElementById('examSubmitForm').submit();
    }
}">

    <!-- Top Banner Alerts -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary font-mono px-3 py-2">
                Question <span x-text="currentQuestionIndex + 1"></span> of {{ $questions->count() }}
            </span>
            <span class="badge bg-danger font-mono px-2 py-1" x-show="tabSwitches > 0">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <span x-text="tabSwitches"></span> Tab Switch Violations Logged
            </span>
        </div>
        <div class="text-info small font-mono" x-text="autosaveNotice"></div>
    </div>

    <form id="examSubmitForm" action="{{ route('quiz.submit', ['quiz' => $quiz->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

        <!-- Questions List Loop -->
        @foreach($questions as $index => $q)
            <div class="lms-card p-4 p-md-5 mb-4 shadow" x-show="currentQuestionIndex === {{ $index }}" x-cloak>
                <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                    <span class="badge bg-primary-subtle text-primary text-uppercase font-mono">
                        {{ str_replace('_', ' ', $q->type) }} &bull; {{ $q->points }} Points
                    </span>
                    <span class="text-muted small font-mono">Item #{{ $index + 1 }}</span>
                </div>

                <h4 class="fw-bold mb-4" style="line-height: 1.5;">
                    {{ $q->question_text }}
                </h4>

                <!-- Render options depending on Question Type -->
                @if(in_array($q->type, ['SINGLE_CHOICE', 'TRUE_FALSE']))
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($q->options as $opt)
                            <label class="lms-card p-3 d-flex align-items-center gap-3 cursor-pointer hover-surface" style="cursor: pointer;">
                                <input type="radio" 
                                       name="answers[{{ $q->id }}]" 
                                       value="{{ $opt->id }}"
                                       @change="answers['{{ $q->id }}'] = '{{ $opt->id }}'; autosave();"
                                       class="form-check-input mt-0 fs-5">
                                <span class="fs-6">{{ $opt->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($q->type === 'MULTI_CHOICE')
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($q->options as $opt)
                            <label class="lms-card p-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer;">
                                <input type="checkbox" 
                                       name="answers[{{ $q->id }}][]" 
                                       value="{{ $opt->id }}"
                                       @change="
                                            if (!Array.isArray(answers['{{ $q->id }}'])) answers['{{ $q->id }}'] = [];
                                            if ($el.checked) { answers['{{ $q->id }}'].push('{{ $opt->id }}'); }
                                            else { answers['{{ $q->id }}'] = answers['{{ $q->id }}'].filter(id => id !== '{{ $opt->id }}'); }
                                            autosave();
                                       "
                                       class="form-check-input mt-0 fs-5">
                                <span class="fs-6">{{ $opt->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($q->type === 'SHORT_ANSWER' || $q->type === 'FILL_BLANK')
                    <div class="mb-4">
                        <label class="form-label small text-muted">Your Answer:</label>
                        <input type="text" 
                               name="answers[{{ $q->id }}]" 
                               class="form-control form-control-lg font-mono" 
                               placeholder="Type precise answer..."
                               @input.debounce.500ms="answers['{{ $q->id }}'] = $el.value; autosave();">
                    </div>
                @elseif($q->type === 'ESSAY')
                    <div class="mb-4">
                        <label class="form-label small text-muted">Comprehensive Response:</label>
                        <textarea name="answers[{{ $q->id }}]" 
                                  rows="6" 
                                  class="form-control" 
                                  placeholder="Type your essay response with justifications..."
                                  @input.debounce.1000ms="answers['{{ $q->id }}'] = $el.value; autosave();"></textarea>
                    </div>
                @endif

                <!-- Navigation Controls -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                    <button type="button" 
                            class="btn btn-outline-secondary px-4" 
                            :disabled="currentQuestionIndex === 0"
                            @click="currentQuestionIndex--">
                        <i class="bi bi-arrow-left me-1"></i> Previous
                    </button>

                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-primary px-4" 
                                x-show="currentQuestionIndex < totalQuestions - 1"
                                @click="currentQuestionIndex++">
                            Next <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                        <button type="button" 
                                class="btn btn-danger px-4 fw-bold" 
                                x-show="currentQuestionIndex === totalQuestions - 1"
                                :disabled="isSubmitting"
                                @click="submitExam()">
                            <i class="bi bi-check2-circle me-1"></i> Finalize &amp; Submit Exam
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Question Grid Navigation Palette -->
        <div class="lms-card p-3 mt-4">
            <div class="small fw-bold text-muted mb-2 text-uppercase">Question Palette</div>
            <div class="d-flex flex-wrap gap-2">
                @for($i = 0; $i < $questions->count(); $i++)
                    <button type="button" 
                            class="btn btn-sm font-mono" 
                            :class="currentQuestionIndex === {{ $i }} ? 'btn-primary' : (answers['{{ $questions[$i]->id }}'] ? 'btn-success' : 'btn-outline-secondary')"
                            style="width: 40px; height: 38px;"
                            @click="currentQuestionIndex = {{ $i }}">
                        {{ $i + 1 }}
                    </button>
                @endfor
            </div>
        </div>
    </form>
</div>
@endsection
