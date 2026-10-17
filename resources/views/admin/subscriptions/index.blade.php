@extends('layouts.admin')

@section('title', 'Manage Cambridge Subscriptions (£ GBP)')

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
                <span class="text-muted small">Billing &amp; Monetization</span>
            </div>
            <h1 class="h3 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                <i class="bi bi-currency-pound text-primary me-2"></i> Cambridge Subscription Plans (£ GBP)
            </h1>
            <p class="text-secondary small mb-0">
                Configure dynamic pricing in British Pounds (£) per Cambridge assessment level for 3-Month and 12-Month recurring passes.
            </p>
        </div>

        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#newPlanModal">
            <i class="bi bi-plus-lg"></i>
            <span>Create New Plan</span>
        </button>
    </div>

    <!-- Plans Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--lms-bg-secondary);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-card-checklist text-primary"></i>
                <span class="fw-bold" style="font-family: var(--lms-font-heading);">Configured Subscription Passes</span>
            </div>
            <span class="badge bg-secondary-subtle text-secondary">{{ $plans->total() }} Total Plans</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Plan Name &amp; Syllabus Silo</th>
                        <th class="py-3">Duration Term</th>
                        <th class="py-3 text-center">Price (£ GBP)</th>
                        <th class="py-3">Features Included</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-body-emphasis" style="font-size: 0.92rem;">
                                    {{ $plan->name }}
                                </div>
                                <div class="text-secondary small mt-0.5">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        {{ $plan->category?->name ?? 'All Cambridge Levels' }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-3">
                                @if($plan->duration_term === '12_MONTHS')
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                        <i class="bi bi-star-fill text-warning me-1"></i> 12-Month Annual Pass
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                        <i class="bi bi-calendar3 me-1"></i> 3-Month Access Pass
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                <span class="h5 fw-bold font-mono text-dark mb-0">
                                    £{{ number_format($plan->price_pence / 100, 2) }}
                                </span>
                            </td>

                            <td class="py-3">
                                @if(is_array($plan->features) && count($plan->features) > 0)
                                    <div class="small text-secondary text-truncate" style="max-width: 280px;">
                                        {{ implode(' • ', $plan->features) }}
                                    </div>
                                @else
                                    <span class="text-muted small">Standard worked solutions &amp; tracker access</span>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                @if($plan->is_active)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                        <i class="bi bi-dash-circle me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $plan->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.subscriptions.destroy', $plan->id) }}" onsubmit="return confirm('Delete this subscription plan?');">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-tag fs-2 d-block mb-2 text-secondary"></i>
                                No subscription plans found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($plans->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4">
                {{ $plans->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create New Plan -->
<div class="modal fade" id="newPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-currency-pound text-primary me-2"></i> Create Cambridge Subscription Plan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Plan Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Cambridge IGCSE 3-Month Access Pass" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Price in GBP (£) *</label>
                        <div class="input-group">
                            <span class="input-group-text">£</span>
                            <input type="number" step="0.01" name="price_pounds" class="form-control font-mono fw-bold" placeholder="45.00" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Duration Term *</label>
                        <select name="duration_term" class="form-select" required>
                            <option value="3_MONTHS" selected>3 Months Access</option>
                            <option value="12_MONTHS">12 Months Annual Pass</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Cambridge Level / Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- All Levels / Site-wide --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Included Features (One per line)</label>
                    <textarea name="features" class="form-control" rows="3" placeholder="Full step-by-step worked solutions&#10;Esther Math Matrix Tracker&#10;Topical past paper packs"></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newActivePlan" checked>
                    <label class="form-check-label small fw-semibold" for="newActivePlan">Publish Immediately</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Create Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals: Edit Subscription Plans (Cleanly isolated from table DOM) -->
@foreach($plans as $plan)
<div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.subscriptions.update', $plan->id) }}" class="modal-content border-0 shadow-lg rounded-4 text-start">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Edit Subscription Plan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Plan Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Price in British Pounds (£) *</label>
                        <div class="input-group">
                            <span class="input-group-text">£</span>
                            <input type="number" step="0.01" name="price_pounds" class="form-control font-mono fw-bold" value="{{ number_format($plan->price_pence / 100, 2, '.', '') }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Duration Term *</label>
                        <select name="duration_term" class="form-select" required>
                            <option value="3_MONTHS" {{ $plan->duration_term === '3_MONTHS' ? 'selected' : '' }}>3 Months Access</option>
                            <option value="12_MONTHS" {{ $plan->duration_term === '12_MONTHS' ? 'selected' : '' }}>12 Months Annual Pass</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Associated Cambridge Level</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- All Levels / Site-wide --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $plan->category_id === $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Plan Features (One per line)</label>
                    <textarea name="features" class="form-control" rows="4">{{ is_array($plan->features) ? implode("\n", $plan->features) : '' }}</textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $plan->id }}" {{ $plan->is_active ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="active{{ $plan->id }}">Active for Purchase</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Update Plan</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
