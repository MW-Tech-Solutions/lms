@extends('layouts.app')

@section('title', 'Academic Progress — ' . $student->name)

@section('content')
<div class="py-4 border-bottom position-relative" style="background: radial-gradient(circle at 85% 10%, rgba(2, 132, 199, 0.08) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none text-muted">Guardian Portal</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ $student->name }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-1 text-dark" style="font-family: var(--lms-font-heading);">
                    {{ $student->name }} — Academic Tracking
                </h1>
                <p class="text-secondary small mb-0">
                    Real-time performance analytics across past-paper attempts and syllabus competencies.
                </p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm bg-white" onclick="window.print()">
                    <i class="bi bi-printer-fill text-primary"></i>
                    <span class="fw-semibold">Print Progress Report</span>
                </button>
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
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="lms-card p-0 border shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="p-3 border-bottom bg-light d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">Esther Math Past-Paper Attempts &amp; Scores</h5>
            <span class="badge bg-secondary-subtle text-secondary small">Read-Only Guardian Oversight</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Syllabus / Course</th>
                        <th>Year</th>
                        <th>Series</th>
                        <th>Score Obtained</th>
                        <th>Percentage</th>
                        <th>Mastery Status</th>
                        <th>Date Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $att)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $att->course?->title ?? 'Cambridge Examination' }}</td>
                            <td class="font-mono">{{ $att->year }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary font-mono">{{ $att->season }}</span></td>
                            <td class="font-mono fw-bold">{{ $att->score_obtained }} / {{ $att->max_score }}</td>
                            <td class="font-mono fw-bold fs-6" style="color: {{ $att->status_color === 'GREEN' ? '#059669' : ($att->status_color === 'AMBER' ? '#D97706' : '#DC2626') }};">
                                {{ $att->percentage }}%
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 small {{ $att->status_color === 'GREEN' ? 'bg-success text-white' : ($att->status_color === 'AMBER' ? 'bg-warning text-dark' : 'bg-danger text-white') }}">
                                    {{ $att->status_color === 'GREEN' ? 'Green (Mastery)' : ($att->status_color === 'AMBER' ? 'Amber (Developing)' : 'Red (Retake Recommended)') }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $att->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No past-paper attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
