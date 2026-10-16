@extends('layouts.app')

@section('title', 'Cambridge Subscription Plans & Pricing (£)')

@section('content')
<div class="py-5 text-center position-relative overflow-hidden" style="background: radial-gradient(circle at 50% 0%, rgba(2, 132, 199, 0.1) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-3">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-sm border mb-3 bg-white">
            <i class="bi bi-shield-check text-primary"></i>
            <span class="small fw-bold text-dark" style="font-size: 0.76rem;">TRANSPARENT BRITISH CURRICULUM PRICING</span>
        </div>
        <h1 class="display-5 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
            Choose Your Cambridge Assessment Level Pass
        </h1>
        <p class="lead text-secondary fs-6 mx-auto mb-4" style="max-width: 680px;">
            Free unworked past papers remain accessible to all. Unlock step-by-step worked solutions, topical question packs, flashcards, and the automated Esther Math Tracker.
        </p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        @foreach($categories as $cat)
            @php
                $catPlans = $plans->get($cat->id);
                $hasActive = $activeSubscriptions->has($cat->id);
            @endphp
            @if($catPlans)
                <div class="col-lg-4 col-md-6">
                    <div class="lms-card h-100 d-flex flex-column border shadow-sm rounded-4 p-4 position-relative bg-white {{ $hasActive ? 'border-success' : '' }}">
                        @if($hasActive)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 small fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i> ACTIVE PASS
                                </span>
                            </div>
                        @endif

                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small fw-bold">
                                {{ $cat->name }}
                            </span>
                        </div>

                        <h4 class="fw-bold mb-2 text-dark" style="font-family: var(--lms-font-heading);">
                            {{ $cat->name }} Pass
                        </h4>
                        <p class="text-secondary small mb-4">
                            Complete access to worked solutions and exam preparation for this assessment stage.
                        </p>

                        <!-- Pricing Options for 3 Months and 12 Months -->
                        <div class="bg-light p-3 rounded-4 mb-4 border">
                            @foreach($catPlans as $p)
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <div class="small fw-bold text-dark">{{ $p->duration_term === '12_MONTHS' ? '12 Months (Annual)' : '3 Months (Term)' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $p->duration_term === '12_MONTHS' ? 'Best value for school year' : 'Ideal for exam season' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-5 fw-bold font-mono text-primary">{{ $p->formattedPrice() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Feature Checklist -->
                        <ul class="list-unstyled small d-flex flex-column gap-2 mb-4 flex-grow-1">
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Unworked past exam papers (Free Tier)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Step-by-step worked solutions &amp; mark schemes</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Esther Math past-paper matrix tracker</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Spaced repetition revision flashcards</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Parent &amp; guardian oversight linkage</span>
                            </li>
                        </ul>

                        <!-- CTA Actions -->
                        @auth
                            @php
                                $plan3m = $catPlans->firstWhere('duration_term', '3_MONTHS');
                                $plan12m = $catPlans->firstWhere('duration_term', '12_MONTHS');
                            @endphp
                            @if($hasActive)
                                <a href="{{ route('courses.index', ['category' => $cat->slug]) }}" class="tj-btn-outline w-100 justify-content-center py-2.5">
                                    <span>Access {{ $cat->name }}</span>
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                <div class="d-flex flex-column gap-2">
                                    @if($plan12m)
                                        <form action="{{ route('subscription.checkout', $plan12m->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="tj-btn-primary w-100 justify-content-center py-2.5 shadow-sm">
                                                <span>Unlock 12-Month Pass ({{ $plan12m->formattedPrice() }})</span>
                                            </button>
                                        </form>
                                    @endif
                                    @if($plan3m)
                                        <form action="{{ route('subscription.checkout', $plan3m->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100 rounded-pill py-2">
                                                <span>3-Month Term Pass ({{ $plan3m->formattedPrice() }})</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login', ['redirect' => url()->full()]) }}" class="tj-btn-primary w-100 justify-content-center py-2.5 shadow-sm">
                                <span>Sign In to Subscribe</span>
                                <i class="bi bi-box-arrow-in-right ms-1"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
