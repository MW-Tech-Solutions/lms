@php
    $c = $section->content_json ?? [];
@endphp
<section class="py-5" style="background: var(--lms-bg-body);" x-data="{ billingAnnual: false }">
    <div class="container py-3">
        <!-- Section Header with Billing Toggle -->
        <div class="text-center mx-auto mb-5" style="max-width: 680px;">
            <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-tag-fill me-1"></i> {{ $c['badge'] ?? 'Transparent Pricing' }}
            </span>
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                {{ $c['title'] ?? 'Enterprise Compliance or Individual Cohorts' }}
            </h2>
            <p class="text-secondary">
                {{ $c['subtitle'] ?? 'Select the operational model tailored to your organizational scale and compliance commitments.' }}
            </p>

            <!-- Annual / Monthly Toggle Switch -->
            <div class="d-inline-flex align-items-center gap-3 p-1 rounded-pill mt-3 border" style="background: var(--lms-bg-secondary);">
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold transition-all"
                        :class="!billingAnnual ? 'btn-primary shadow-sm' : 'btn-light text-secondary border-0'"
                        @click="billingAnnual = false">
                    Monthly Billing
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold transition-all d-flex align-items-center gap-2"
                        :class="billingAnnual ? 'btn-primary shadow-sm' : 'btn-light text-secondary border-0'"
                        @click="billingAnnual = true">
                    <span>Annual Billing</span>
                    <span class="badge bg-warning text-dark font-mono" style="font-size: 0.7rem;">SAVE 20%</span>
                </button>
            </div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="row g-4 justify-content-center align-items-stretch">
            <!-- Individual Student Tier -->
            <div class="col-md-6 col-lg-5">
                <div class="lms-card p-4 p-lg-5 h-100 d-flex flex-column border-0 shadow-sm" style="background: var(--lms-surface-card);">
                    <div class="mb-3">
                        <span class="badge badge-academic rounded-pill px-3 py-1 text-uppercase small">Public Academic</span>
                        <h4 class="fw-bold mt-2 mb-2" style="font-family: var(--lms-font-heading);">Individual Student Track</h4>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-bold font-mono" style="color: var(--lms-text-primary);" x-text="billingAnnual ? '$23' : '$29'">$29</span>
                            <span class="text-secondary small">/ month</span>
                        </div>
                        <p class="text-muted small">Ideal for self-paced learners pursuing accredited technical credentials.</p>
                    </div>

                    <ul class="list-unstyled my-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Full catalog access to self-paced learning modules</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Proctored automated quizzes &amp; exam simulations</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Cryptographically signed PDF certificates &amp; QR verification</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Peer forum discussions and cohort scheduling</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="tj-btn-outline w-100 py-3 justify-content-center">
                        <span>Get Started Free</span>
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>

            <!-- Enterprise Corporate Workforce Tier (Featured) -->
            <div class="col-md-6 col-lg-5">
                <div class="lms-card p-4 p-lg-5 h-100 d-flex flex-column position-relative shadow-lg border-2" 
                     style="background: var(--lms-surface-card); border-color: var(--lms-accent) !important;">
                    <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="badge badge-compliance rounded-pill px-3 py-1 text-uppercase small">Corporate Workforce</span>
                        <span class="badge rounded-pill px-3 py-1.5 text-white shadow-sm"
                              style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="bi bi-star-fill text-warning me-1"></i> RECOMMENDED FOR ENTERPRISE
                        </span>
                    </div>
                    <div class="mb-3">
                        <h4 class="fw-bold mt-2 mb-2" style="font-family: var(--lms-font-heading);">Enterprise Compliance Hub</h4>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-bold font-mono" style="color: var(--lms-accent);" x-text="billingAnnual ? '$9.60' : '$12'">$12</span>
                            <span class="text-secondary small">/ seat / month</span>
                        </div>
                        <p class="text-muted small">Designed for corporate governance, annual audit cycles, and automated employee tracks.</p>
                    </div>

                    <ul class="list-unstyled my-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>Automated annual recertification loop</strong> (ISO, OSHA, HIPAA)</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span><strong>5-Tier CBAC Hierarchy</strong> &amp; Department Manager Sign-Offs</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>SCORM 1.2 / 2004 Runtime package hosting &amp; CMI tracking</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>Anti-cheat live webcam snapshot telemetry proctoring</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 small text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-6"></i>
                            <span>SSO Authentication &amp; Auditor CSV/PDF Export trails</span>
                        </li>
                    </ul>

                    <a href="{{ route('cms.show', ['slug' => 'contact']) }}" class="tj-btn-primary w-100 py-3 justify-content-center">
                        <span>Deploy Enterprise Hub</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
