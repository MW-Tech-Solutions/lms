@php
    $c = $section->content_json ?? [];
    $plan3m = \App\Models\SubscriptionPlan::where('duration_term', '3_MONTHS')->where('is_active', true)->first();
    $plan12m = \App\Models\SubscriptionPlan::where('duration_term', '12_MONTHS')->where('is_active', true)->first();
    $price3m = $plan3m ? ($plan3m->price_pence / 100) : 45;
    $price12m = $plan12m ? ($plan12m->price_pence / 100) : 125;
@endphp
<section class="py-5" id="pricing-section" style="background: var(--lms-bg-body);" 
         x-data="{ duration: '12m', price3m: {{ $price3m }}, price12m: {{ $price12m }} }">
    <div class="container py-3">
        <!-- Section Header with Billing Toggle -->
        <div class="text-center mx-auto mb-5" style="max-width: 720px;">
            <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2" style="background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                <i class="bi bi-tag-fill me-1"></i> Cambridge Assessment Subscriptions
            </span>
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                Transparent Cambridge Subscriptions in British Pounds (£)
            </h2>
            <p class="text-secondary small">
                Unworked exam question papers are 100% free. Unlock step-by-step worked solutions, Esther Math Matrix Trackers, and topical revision packs per assessment level.
            </p>

            <!-- 3-Month / 12-Month Toggle Switch -->
            <div class="d-inline-flex align-items-center gap-2 p-1.5 rounded-pill mt-3 border shadow-sm" style="background: var(--lms-surface-card);">
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-bold transition-all"
                        :class="duration === '3m' ? 'btn-primary shadow-sm' : 'text-secondary border-0 bg-transparent'"
                        @click="duration = '3m'">
                    3-Month Term
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-bold transition-all d-flex align-items-center gap-2"
                        :class="duration === '12m' ? 'btn-primary shadow-sm' : 'text-secondary border-0 bg-transparent'"
                        @click="duration = '12m'">
                    <span>12-Month Annual Pass</span>
                    <span class="badge bg-warning text-dark font-mono" style="font-size: 0.68rem;">BEST VALUE</span>
                </button>
            </div>
        </div>

        <!-- Pricing Cards Grid: Freemium & Level Tiers -->
        <div class="row g-4 justify-content-center align-items-stretch">
            <!-- Free Tier (Publicly Accessible) -->
            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 p-lg-4 h-100 d-flex flex-column border shadow-sm rounded-4" style="background: var(--lms-surface-card);">
                    <div class="mb-3">
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 text-uppercase small">Open Access</span>
                        <h4 class="fw-bold mt-2 mb-1" style="font-family: var(--lms-font-heading);">Free Study Tier</h4>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-bold font-mono" style="color: var(--lms-text-primary);">£0</span>
                            <span class="text-secondary small">/ forever</span>
                        </div>
                        <p class="text-muted small">Standard unworked past papers and Cambridge syllabus specs for all students.</p>
                    </div>

                    <ul class="list-unstyled my-3 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Download unworked question papers (PDF)</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Cambridge official syllabus specifications</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Community discussion boards &amp; study tips</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-muted">
                            <i class="bi bi-x-circle text-muted fs-6"></i>
                            <span class="text-decoration-line-through">Step-by-step worked solutions</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-muted">
                            <i class="bi bi-x-circle text-muted fs-6"></i>
                            <span class="text-decoration-line-through">Automated Esther Math Tracker</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="tj-btn-outline w-100 py-2.5 justify-content-center">
                        <span>Access Free Papers</span>
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>

            <!-- Single Cambridge Level Pass (Featured Tier) -->
            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 p-lg-4 h-100 d-flex flex-column position-relative shadow-lg border-2 rounded-4" 
                     style="background: var(--lms-surface-card); border-color: var(--lms-accent) !important;">
                    <div class="mb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 text-uppercase small">Per-Level Access</span>
                        <span class="badge rounded-pill px-2.5 py-1 text-white shadow-sm"
                              style="background: linear-gradient(135deg, #0284C7, #0369A1); font-size: 0.68rem; letter-spacing: 0.5px;">
                            POPULAR CHOICE
                        </span>
                    </div>
                    <div class="mb-2">
                        <h4 class="fw-bold mt-2 mb-1" style="font-family: var(--lms-font-heading);">Single Level Pass</h4>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-bold font-mono" style="color: var(--lms-accent);" x-text="duration === '12m' ? ('£' + price12m) : ('£' + price3m)">£{{ $price12m }}</span>
                            <span class="text-secondary small font-mono" x-text="duration === '12m' ? '/ 12 months' : '/ 3 months'"></span>
                        </div>
                        <p class="text-muted small">Choose any 1 Cambridge Assessment Level (Primary, Checkpoint, IGCSE, or A-Level).</p>
                    </div>

                    <ul class="list-unstyled my-3 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Full step-by-step worked solutions</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Esther Math Matrix Tracker</strong> with retake analytics</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Topical past paper packs &amp; revision summaries</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Spaced repetition flashcards (90% known tracking)</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Watermarked DRM-protected PDF notes</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="tj-btn-primary w-100 py-2.5 justify-content-center">
                        <span>Subscribe with Stripe (£)</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Institutional / School License Tier -->
            <div class="col-md-6 col-lg-4">
                <div class="lms-card p-4 p-lg-4 h-100 d-flex flex-column border shadow-sm rounded-4" style="background: var(--lms-surface-card);">
                    <div class="mb-3">
                        <span class="badge bg-warning-subtle text-dark rounded-pill px-3 py-1 text-uppercase small">Schools &amp; Teachers</span>
                        <h4 class="fw-bold mt-2 mb-1" style="font-family: var(--lms-font-heading);">Institutional School Plan</h4>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-bold font-mono" style="color: var(--lms-text-primary);" x-text="duration === '12m' ? '£249' : '£89'">£249</span>
                            <span class="text-secondary small font-mono" x-text="duration === '12m' ? '/ 12 months' : '/ 3 months'"></span>
                        </div>
                        <p class="text-muted small">For Cambridge accredited schools, freelance tutors, and multi-student cohorts.</p>
                    </div>

                    <ul class="list-unstyled my-3 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Teacher assignment dashboard &amp; submission feedback</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Parent portal multi-child link codes (CMP-XXX)</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>CMS draft/publish staging for school educators</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2.5 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>All 5 Cambridge assessment levels included</span>
                        </li>
                    </ul>

                    <a href="{{ route('cms.show', ['slug' => 'support']) }}" class="tj-btn-outline w-100 py-2.5 justify-content-center">
                        <span>Inquire School License</span>
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
