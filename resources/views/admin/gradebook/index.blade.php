@extends('layouts.admin')

@section('title', 'Weighted Gradebook & Audit Ledger')

@section('content')
<div class="container-fluid" x-data="{ 
    showEditModal: false, 
    editGradeId: null, 
    currentScore: 0, 
    studentName: '', 
    itemName: '' 
}">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_gradebook'] ?? 'Weighted Gradebook & Academic Ledger' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_gradebook'] ?? 'Category weights, drop-lowest calculations, and immutable instructor audit justifications.' }}</div>
        </div>
    </div>

    <!-- Category Weights Overview -->
    <div class="row g-3 mb-4">
        @forelse($categories as $cat)
            <div class="col-md-4">
                <div class="lms-card p-3 border-top border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">{{ $cat->name }}</h6>
                        <span class="badge bg-primary font-mono fs-6">{{ $cat->weight_percentage }}%</span>
                    </div>
                    <div class="small text-muted mt-2">
                        Course: {{ $cat->course->title }} &bull; Drops lowest: {{ $cat->drop_lowest_count }} item(s)
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No weighted grade categories registered yet. Defaulting to standard weighted curve.</div>
            </div>
        @endforelse
    </div>

    <!-- Gradebook Matrix -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-calculator text-primary me-2"></i>Student Master Grade Ledger</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Learner</th>
                        <th>Course</th>
                        <th>Grade Item</th>
                        <th>Category</th>
                        <th>Max Score</th>
                        <th>Final Score</th>
                        <th>Lock Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $grade)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $grade->user->name }}</div>
                                <div class="small text-muted">{{ $grade->user->email }}</div>
                            </td>
                            <td>{{ $grade->item->course->title }}</td>
                            <td class="fw-semibold">{{ $grade->item->item_type }} #{{ $grade->item->item_id ?? $grade->item->id }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary small">
                                    {{ $grade->item->category?->name ?? 'Default' }}
                                </span>
                            </td>
                            <td class="font-mono">{{ $grade->item->max_score }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success fs-6 font-mono px-3 py-1">
                                    {{ $grade->final_score }}
                                </span>
                            </td>
                            <td>
                                @if($grade->is_locked)
                                    <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>LOCKED</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-unlock-fill me-1"></i>EDITABLE</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        @click="
                                            editGradeId = {{ $grade->id }};
                                            currentScore = {{ $grade->final_score }};
                                            studentName = '{{ addslashes($grade->user->name) }}';
                                            itemName = '{{ $grade->item->item_type }}';
                                            showEditModal = true;
                                        ">
                                    <i class="bi bi-pencil-square me-1"></i> Adjust Grade
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No student grade records available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grade Modification Audit Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" x-show="showEditModal" x-cloak style="background: rgba(0,0,0,0.6);" @click.self="showEditModal = false" @keydown.escape.window="showEditModal = false">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-2">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Manual Grade Override &amp; Audit Trail</h5>
                    <button type="button" class="btn-close" @click="showEditModal = false"></button>
                </div>
                <form action="{{ route('admin.gradebook.override') }}" method="POST">
                    @csrf
                    <input type="hidden" name="grade_id" :value="editGradeId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Learner</label>
                            <input type="text" class="form-control" :value="studentName" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Grade Item</label>
                            <input type="text" class="form-control" :value="itemName" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">New Adjusted Score</label>
                            <input type="number" step="0.5" name="new_score" class="form-control font-mono fs-5" x-model="currentScore" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Audit Justification (Required for Compliance)</label>
                            <textarea name="justification" class="form-control" rows="3" required placeholder="State exact pedagogical or compliance reason for grade override (e.g. Oral defense reassessment, verified technical disruption)..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
                        <button type="submit" class="btn btn-accent px-4">Commit Override &amp; Archive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
