@extends('layouts.admin')

@section('title', 'Manage FAQs')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Content Management</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-question-circle-fill text-primary me-2"></i> Frequently Asked Questions (FAQ) Manager
            </h1>
            <p class="text-secondary small mb-0">
                Create, organize, and publish Cambridge assessment and platform questions displayed on the homepage.
            </p>
        </div>

        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#newFaqModal">
            <i class="bi bi-plus-lg"></i>
            <span>Add New FAQ</span>
        </button>
    </div>

    <!-- FAQs Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-chat-quote-fill text-primary"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">All Published FAQs</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary">{{ $faqs->total() }} Total Questions</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">Order</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Question &amp; Answer Preview</th>
                        <th class="py-3 text-center">Published</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td class="ps-4 py-3 font-mono fw-bold text-muted">
                                #{{ $faq->sort_order }}
                            </td>

                            <td class="py-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small">
                                    {{ $faq->category }}
                                </span>
                            </td>

                            <td class="py-3">
                                <div class="fw-bold text-body-emphasis mb-1" style="font-size: 0.92rem;">
                                    {{ $faq->question }}
                                </div>
                                <div class="text-secondary small text-truncate" style="max-width: 500px; font-size: 0.80rem;">
                                    {{ $faq->answer }}
                                </div>
                            </td>

                            <td class="py-3 text-center">
                                @if($faq->is_published)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Published
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                        <i class="bi bi-dash-circle me-1"></i> Draft
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}" onsubmit="return confirm('Delete this FAQ question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-question-circle fs-2 d-block mb-2 text-secondary"></i>
                                No FAQs registered in database yet. Click above to create one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-secondary">
                    Showing {{ $faqs->firstItem() }} to {{ $faqs->lastItem() }} of {{ $faqs->total() }} questions
                </div>
                <div>
                    {{ $faqs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add New FAQ -->
<div class="modal fade" id="newFaqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.faqs.store') }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-question-circle-fill text-primary me-2"></i> Add New FAQ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label small fw-semibold">Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Question *</label>
                    <input type="text" name="question" class="form-control" placeholder="e.g. How does Cambridge Assessment Isolation work?" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Answer *</label>
                    <textarea name="answer" class="form-control" rows="5" placeholder="Clear, detailed explanation for learners..." required></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="newPub" checked>
                    <label class="form-check-label small fw-semibold" for="newPub">Publish Immediately</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Create FAQ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals: Edit FAQs (Cleanly isolated from table DOM) -->
@foreach($faqs as $faq)
<div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}" class="modal-content border-0 shadow-lg rounded-4 text-start">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Edit FAQ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label small fw-semibold">Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $faq->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $faq->sort_order }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Question *</label>
                    <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Answer *</label>
                    <textarea name="answer" class="form-control" rows="5" required>{{ $faq->answer }}</textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="pub{{ $faq->id }}" {{ $faq->is_published ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="pub{{ $faq->id }}">Published on Homepage</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
