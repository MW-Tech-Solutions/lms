@extends('layouts.app')

@section('title', 'Cambridge Score History & Mastery Tracker')

@section('content')
<div class="container py-5">
    
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-grid-3x3 text-primary me-2"></i> Esther Math Mastery History
            </h1>
            <p class="text-secondary small mb-0">
                Detailed record of all Cambridge past-paper components, mark thresholds, and progress over time.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('scores.report') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-printer me-1"></i> Print / Generate Diagnostic Report
            </a>
            <a href="{{ route('home') }}#esther-math-tracker" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Key In New Score
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase">Total Attempts</div>
                <div class="h3 fw-bold mb-0 text-primary">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase">Mastery (&gt;=75%)</div>
                <div class="h3 fw-bold mb-0 text-success">{{ $stats['mastery'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase">Developing (50-74%)</div>
                <div class="h3 fw-bold mb-0 text-warning">{{ $stats['developing'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background: var(--lms-surface-card);">
                <div class="text-secondary small fw-semibold text-uppercase">Average Score</div>
                <div class="h3 fw-bold mb-0 text-info">{{ $stats['average_pct'] }}%</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Exam Series</th>
                        <th class="py-3">Subject &amp; Component</th>
                        <th class="py-3 text-center">Score Obtained</th>
                        <th class="py-3 text-center">Percentage</th>
                        <th class="py-3 text-center">Mastery Status</th>
                        <th class="pe-4 py-3 text-end">Date Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr>
                            <td class="ps-4 py-3 fw-bold font-mono">
                                {{ $attempt->year }} {{ $attempt->season }} (Var {{ $attempt->variant }})
                            </td>

                            <td class="py-3">
                                <div class="fw-semibold text-body-emphasis small">
                                    {{ $attempt->notes ?? $attempt->course?->title ?? 'Past Paper Component' }}
                                </div>
                            </td>

                            <td class="py-3 text-center font-mono fw-bold">
                                {{ $attempt->score_obtained }} / {{ $attempt->max_score }}
                            </td>

                            <td class="py-3 text-center font-mono fw-bold">
                                {{ $attempt->percentage }}%
                            </td>

                            <td class="py-3 text-center">
                                @switch($attempt->status_color)
                                    @case('GREEN')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Mastery
                                        </span>
                                        @break
                                    @case('AMBER')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Developing
                                        </span>
                                        @break
                                    @case('RED')
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Remedial Focus
                                        </span>
                                        @break
                                @endswitch
                            </td>

                            <td class="pe-4 py-3 text-end text-secondary small">
                                {{ $attempt->created_at ? $attempt->created_at->format('M d, Y • h:i A') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-card-checklist fs-2 d-block mb-2 text-secondary"></i>
                                No score history entries recorded yet. Key in your scores on the homepage to start tracking!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4">
                {{ $attempts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
