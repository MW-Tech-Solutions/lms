@extends('layouts.app')

@section('title', 'Esther Math Revision Tracker — ' . $course->title)

@section('content')
<div class="py-4 border-bottom position-relative" style="background: radial-gradient(circle at 85% 10%, rgba(2, 132, 199, 0.08) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">Past Papers</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Esther Math Matrix Tracker</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-sm border mb-2 bg-white">
                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-0.5 small fw-bold">ESTHER MATH MATRIX</span>
                    <span class="small fw-bold text-dark">{{ $course->category?->name ?? 'Cambridge Assessment' }}</span>
                </div>
                <h1 class="display-6 fw-bold mb-1 text-dark" style="font-family: var(--lms-font-heading);">
                    {{ $course->title }} — Revision Tracker
                </h1>
                <p class="text-secondary small mb-0">
                    Track raw scores, calculate percentage mastery, and color-code test performance across Cambridge examination series (2023–2026).
                </p>
            </div>

            <!-- Summary KPI Pill Badges -->
            <div class="d-flex flex-wrap gap-2">
                <div class="p-2.5 px-3 rounded-4 border bg-white shadow-sm text-center">
                    <div class="small text-muted" style="font-size: 0.72rem;">TOTAL ATTEMPTS</div>
                    <div class="fs-5 fw-bold font-mono text-dark">{{ $totalTracked }}</div>
                </div>
                <div class="p-2.5 px-3 rounded-4 border bg-white shadow-sm text-center">
                    <div class="small text-success fw-semibold" style="font-size: 0.72rem;">GREEN (&ge;75%)</div>
                    <div class="fs-5 fw-bold font-mono text-success">{{ $greenCount }}</div>
                </div>
                <div class="p-2.5 px-3 rounded-4 border bg-white shadow-sm text-center">
                    <div class="small text-warning-emphasis fw-semibold" style="font-size: 0.72rem;">AMBER (50-74%)</div>
                    <div class="fs-5 fw-bold font-mono text-warning-emphasis">{{ $amberCount }}</div>
                </div>
                <div class="p-2.5 px-3 rounded-4 border bg-white shadow-sm text-center">
                    <div class="small text-danger fw-semibold" style="font-size: 0.72rem;">RED (&lt;50%)</div>
                    <div class="fs-5 fw-bold font-mono text-danger">{{ $redCount }}</div>
                </div>
                <div class="p-2.5 px-3 rounded-4 border bg-primary text-white shadow-sm text-center">
                    <div class="small text-white-50" style="font-size: 0.72rem;">MASTERY</div>
                    <div class="fs-5 fw-bold font-mono">{{ $overallMastery }}%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5" x-data="estherTracker()">
    <!-- Instructions & Color Threshold Legend -->
    <div class="p-3 mb-4 rounded-4 border shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: var(--lms-accent);">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="small">
                <strong>Color Threshold Rules:</strong> Click any cell to input raw score (e.g. <code>68 / 75</code>). The system auto-calculates percentage and tracks attempts.
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 small">
            <span class="d-flex align-items-center gap-1.5"><span class="badge rounded-pill bg-success px-2 py-1">&ge; 75%</span> Green (Mastery)</span>
            <span class="d-flex align-items-center gap-1.5"><span class="badge rounded-pill bg-warning text-dark px-2 py-1">50% &ndash; 74%</span> Amber (Developing)</span>
            <span class="d-flex align-items-center gap-1.5"><span class="badge rounded-pill bg-danger px-2 py-1">&lt; 50%</span> Red (Needs Retake)</span>
        </div>
    </div>

    <!-- The Esther Math Revision Grid -->
    <div class="lms-card p-0 border shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                <thead style="background: #0F172A; color: #FFF;">
                    <tr>
                        <th style="width: 100px;">Year</th>
                        <th style="width: 110px;">Season</th>
                        <th style="width: 80px;">Var</th>
                        @foreach($components as $comp)
                            <th>
                                <div class="fw-bold">{{ $comp->code }}</div>
                                <div class="small text-white-50 font-normal" style="font-size: 0.72rem;">{{ $comp->name }} (Max: {{ $comp->default_max_score }})</div>
                            </th>
                        @endforeach
                        <th style="width: 160px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($series as $s)
                        <tr>
                            <td class="fw-bold font-mono bg-light text-dark">{{ $s->year }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-mono px-2 py-1">
                                    {{ $s->season }} ({{ $s->name }})
                                </span>
                            </td>
                            <td class="font-mono text-muted">1</td>
                            @foreach($components as $comp)
                                @php
                                    $key = $s->year . '_' . $s->season . '_' . $comp->id;
                                    $attempt = $attempts->get($key);
                                @endphp
                                <td class="p-2 cursor-pointer transition-all"
                                    style="cursor: pointer; {{ $attempt ? ($attempt->status_color === 'GREEN' ? 'background: rgba(16, 185, 129, 0.15);' : ($attempt->status_color === 'AMBER' ? 'background: rgba(245, 158, 11, 0.18);' : 'background: rgba(239, 68, 68, 0.15);')) : '' }}"
                                    @click="openInput({{ $s->year }}, '{{ $s->season }}', {{ $comp->id }}, '{{ $comp->code }}', {{ $comp->default_max_score }}, {{ $attempt ? json_encode($attempt) : 'null' }})">
                                    @if($attempt)
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold font-mono" style="color: {{ $attempt->status_color === 'GREEN' ? '#047857' : ($attempt->status_color === 'AMBER' ? '#B45309' : '#B91C1C') }}; font-size: 0.95rem;">
                                                {{ $attempt->score_obtained }} / {{ $attempt->max_score }}
                                            </span>
                                            <span class="badge rounded-pill small px-2 py-0.5 mt-1 {{ $attempt->status_color === 'GREEN' ? 'bg-success text-white' : ($attempt->status_color === 'AMBER' ? 'bg-warning text-dark' : 'bg-danger text-white') }}" style="font-size: 0.7rem;">
                                                {{ $attempt->percentage }}%
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted small py-2 d-inline-block opacity-50">
                                            <i class="bi bi-pencil-square me-1"></i> Input Score
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <span class="text-muted small">Auto-Calculated</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Score Input Modal -->
    <div class="modal fade" id="scoreModal" tabindex="-1" :class="{ 'show': showModal }" :style="showModal ? 'display: block !important; background: rgba(0,0,0,0.6);' : ''" x-cloak>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Record Paper Score</h5>
                        <div class="small text-muted" x-text="activeCell.year + ' ' + activeCell.season + ' — ' + activeCell.code"></div>
                    </div>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <form @submit.prevent="submitScore()">
                    <div class="modal-body py-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Score Obtained</label>
                                <input type="number" step="0.5" class="form-control form-control-lg font-mono text-center" x-model="activeCell.score_obtained" required placeholder="e.g. 65" min="0" :max="activeCell.max_score">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Maximum Score</label>
                                <input type="number" step="1" class="form-control form-control-lg font-mono text-center bg-light" x-model="activeCell.max_score" required readonly>
                            </div>
                            <div class="col-12 text-center" x-show="calculatedPercentage !== null">
                                <div class="p-3 rounded-3" :class="previewColorClass">
                                    <div class="small text-uppercase fw-bold" x-text="previewLabel"></div>
                                    <div class="display-6 fw-bold font-mono" x-text="calculatedPercentage + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" @click="showModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2" :disabled="isSubmitting">
                            <span x-show="!isSubmitting"><i class="bi bi-check-lg me-1"></i> Save to Matrix</span>
                            <span x-show="isSubmitting"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function estherTracker() {
    return {
        showModal: false,
        isSubmitting: false,
        activeCell: {
            course_id: {{ $course->id }},
            year: null,
            season: '',
            component_id: null,
            code: '',
            score_obtained: '',
            max_score: 75,
        },
        openInput(year, season, componentId, code, defaultMax, existing) {
            this.activeCell = {
                course_id: {{ $course->id }},
                year: year,
                season: season,
                component_id: componentId,
                code: code,
                score_obtained: existing ? existing.score_obtained : '',
                max_score: existing ? existing.max_score : defaultMax,
            };
            this.showModal = true;
        },
        get calculatedPercentage() {
            const score = parseFloat(this.activeCell.score_obtained);
            const max = parseFloat(this.activeCell.max_score);
            if (isNaN(score) || isNaN(max) || max <= 0) return null;
            return Math.min(100, Math.max(0, ((score / max) * 100).toFixed(1)));
        },
        get previewColorClass() {
            const pct = parseFloat(this.calculatedPercentage);
            if (pct >= 75) return 'bg-success-subtle text-success border border-success-subtle';
            if (pct >= 50) return 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
            return 'bg-danger-subtle text-danger border border-danger-subtle';
        },
        get previewLabel() {
            const pct = parseFloat(this.calculatedPercentage);
            if (pct >= 75) return 'Green — Cambridge Mastery';
            if (pct >= 50) return 'Amber — Developing (Review Recommended)';
            return 'Red — Needs Retake';
        },
        submitScore() {
            this.isSubmitting = true;
            fetch('{{ route('tracker.record') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.activeCell)
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error recording score.');
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                alert('Connection error while recording attempt.');
            });
        }
    };
}
</script>
@endsection
