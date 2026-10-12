@extends('layouts.app')

@section('title', 'Cohort Pricing & Enterprise Licensing - ' . ($site_settings['brand_name'] ?? 'Enterprise Academy'))
@section('meta_description', 'Transparent licensing tiers engineered for corporate workforce compliance recertification and accredited student cohorts. Save 20% on annual billing.')

@section('content')
<div x-data="{ 
    billingAnnual: true,
    teamSeats: 50,
    activeFaq: 0,
    get calculatedPrice() {
        let perSeat = this.billingAnnual ? 9.60 : 12.00;
        return (this.teamSeats * perSeat).toFixed(2);
    },
    get annualSavings() {
        return (this.teamSeats * (12.00 - 9.60) * 12).toFixed(0);
    },
    get hoursSaved() {
        return Math.round(this.teamSeats * 2.5);
    }
}" class="pricing-page-wrapper">

    {{-- 1. HERO & BILLING TOGGLE (REDESIGNED DUAL-COLUMN HIGH IMPACT) --}}
    <section class="py-5 position-relative overflow-hidden pricing-hero-section">
        <!-- Ambient Background Glows -->
        <div class="position-absolute top-0 end-0 rounded-circle" 
             style="width: 520px; height: 520px; background: radial-gradient(circle, rgba(114, 62, 195, 0.15) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(30%, -20%);"></div>
        <div class="position-absolute bottom-0 start-0 rounded-circle" 
             style="width: 440px; height: 440px; background: radial-gradient(circle, rgba(255, 77, 0, 0.1) 0%, transparent 70%); filter: blur(60px); pointer-events: none; transform: translate(-30%, 20%);"></div>

        <div class="container position-relative py-3" style="z-index: 2;">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Pricing Plans</li>
                </ol>
            </nav>

            <div class="row align-items-center g-5">
                <!-- Left Column: Headline, Proposition & Interactive Billing Toggle -->
                <div class="col-lg-7 text-center text-lg-start">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-sm border mb-3" 
                         style="background: var(--lms-surface-card); border-color: rgba(114, 62, 195, 0.25) !important;">
                        <i class="bi bi-shield-check text-purple"></i>
                        <span class="small fw-bold" style="color: var(--lms-accent); font-size: 0.76rem; letter-spacing: 0.6px;">
                            TRANSPARENT INSTITUTIONAL LICENSING
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                            TIER MATRIX
                        </span>
                    </div>
                    
                    <h1 class="display-4 fw-extrabold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -1px; line-height: 1.15;">
                        Investment Engineered for <br class="d-none d-md-inline">High-Stakes Compliance &amp; 
                        <span style="background: linear-gradient(135deg, #723EC3 0%, #2563EB 50%, #FF4D00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Academic Distinction.
                        </span>
                    </h1>

                    <p class="lead text-secondary fs-6 mb-4" style="line-height: 1.85; max-width: 620px;">
                        Whether you are certifying an internal workforce of 1,000+ employees or advancing your personal technical credentials, select the operational tier built for zero audit surprises and frictionless learning.
                    </p>

                    <!-- Billing Frequency Switcher -->
                    <div class="d-inline-flex align-items-center gap-2 p-1.5 rounded-pill border shadow-sm mb-3" style="background: var(--lms-surface-card);">
                        <button type="button" 
                                class="btn btn-sm rounded-pill px-4 py-2 fw-bold transition-all"
                                :class="!billingAnnual ? 'btn-primary shadow-sm' : 'text-secondary border-0 bg-transparent'"
                                @click="billingAnnual = false">
                            Monthly Billing
                        </button>
                        
                        <button type="button" 
                                class="btn btn-sm rounded-pill px-4 py-2 fw-bold transition-all d-flex align-items-center gap-2"
                                :class="billingAnnual ? 'btn-primary shadow-sm' : 'text-secondary border-0 bg-transparent'"
                                @click="billingAnnual = true">
                            <span>Annual Billing</span>
                            <span class="badge bg-warning text-dark font-mono px-2 py-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">SAVE 20%</span>
                        </button>
                    </div>

                    <!-- Trust Micro-Badges -->
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-3 text-secondary small pt-1">
                        <span class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-check text-success"></i> 30-Day Money-Back Guarantee
                        </span>
                        <span class="d-none d-sm-inline opacity-25">&bull;</span>
                        <span class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-arrow-repeat text-primary"></i> Cancel Anytime
                        </span>
                        <span class="d-none d-sm-inline opacity-25">&bull;</span>
                        <span class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-lock-fill text-warning"></i> SOC2 Type II Audited
                        </span>
                    </div>
                </div>

                <!-- Right Column: Live Telemetry & ROI Snapshot Bento Card -->
                <div class="col-lg-5">
                    <div class="p-4 rounded-4 shadow-lg position-relative overflow-hidden" 
                         style="background: #111528; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 50px rgba(0,0,0,0.45);">
                        
                        <!-- Header with Status Indicator -->
                        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary border-opacity-25 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="p-2 rounded-circle" style="background: rgba(114, 62, 195, 0.25); color: #A78BFA;">
                                    <i class="bi bi-graph-up-arrow fs-5"></i>
                                </span>
                                <div>
                                    <div class="text-white fw-bold small" style="line-height: 1.2;">Interactive Tier ROI Snapshot</div>
                                    <div class="text-white-50 font-mono" style="font-size: 0.65rem;">Live calculation based on billing cycle</div>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 font-mono small">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Active Model
                            </span>
                        </div>

                        <!-- Dynamic ROI Preview Box -->
                        <div class="p-3 rounded-3 mb-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                <span class="text-white-50 small">Standard Enterprise Cohort (50 Seats)</span>
                                <div class="text-end">
                                    <span class="fs-4 fw-extrabold font-mono text-white" x-text="'$' + calculatedPrice">$480.00</span>
                                    <span class="text-white-50 small">/ mo</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1">
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 font-mono px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-tag-fill me-1"></i> Annual Savings: $<span x-text="annualSavings">1440</span>/yr
                                </span>
                                <span class="text-success small fw-semibold font-mono" style="font-size: 0.75rem;">
                                    <i class="bi bi-check-circle-fill"></i> 2 Months Free
                                </span>
                            </div>
                        </div>

                        <!-- Operational Impact Metrics -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2.5 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                    <div class="text-white-50" style="font-size: 0.68rem;">First-Time Audit Pass</div>
                                    <div class="fs-5 fw-bold text-success font-mono">99.4%</div>
                                    <div class="text-white-50" style="font-size: 0.65rem;">Zero Audit Failures</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2.5 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                    <div class="text-white-50" style="font-size: 0.68rem;">Admin Hours Saved</div>
                                    <div class="fs-5 fw-bold text-white font-mono"><span x-text="hoursSaved">125</span> hrs/mo</div>
                                    <div class="text-info" style="font-size: 0.65rem;">Automated Grading</div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Trigger Button -->
                        <button type="button" 
                                class="btn btn-sm btn-outline-light rounded-pill w-100 py-2 d-flex align-items-center justify-content-center gap-2" 
                                style="border-color: rgba(255,255,255,0.25); background: rgba(255,255,255,0.06);"
                                data-bs-toggle="modal" 
                                data-bs-target="#enterpriseInquiryModal">
                            <i class="bi bi-chat-quote-fill text-warning"></i>
                            <span class="small fw-semibold">Request Custom Multi-Campus Quote</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. THREE PRICING TIERS GRID --}}
    <section class="py-5" style="background: var(--lms-bg-body);">
        <div class="container py-3">
            <div class="row g-4 justify-content-center align-items-stretch">
                
                {{-- Tier 1: Individual Academic Student --}}
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="lms-card p-4 p-xl-5 w-100 d-flex flex-column border shadow-sm" style="background: var(--lms-surface-card); border-radius: 24px;">
                        <div class="mb-3">
                            <span class="badge badge-academic rounded-pill px-3 py-1 text-uppercase small fw-bold">
                                Public Academic
                            </span>
                            <h3 class="fw-bold mt-3 mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                Individual Student Track
                            </h3>
                            <div class="d-flex align-items-baseline gap-1 my-3">
                                <span class="display-4 fw-extrabold font-mono" style="color: var(--lms-text-primary);" x-text="billingAnnual ? '$23' : '$29'">$23</span>
                                <span class="text-muted small">/ month</span>
                            </div>
                            <p class="text-secondary small" style="min-height: 42px;">
                                Ideal for self-paced learners pursuing accredited technical credentials and independent career advancement.
                            </p>
                        </div>

                        <hr class="my-3 opacity-25">

                        <ul class="list-unstyled my-3 flex-grow-1">
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Full catalog access to self-paced accredited courses</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Proctored automated quizzes &amp; practice exam banks</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Cryptographically signed PDF certificates &amp; QR verification</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Interactive video bookmarks &amp; peer forum discussions</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-muted opacity-75">
                                <i class="bi bi-x-circle text-muted fs-6 mt-0.5"></i>
                                <span>Automated corporate recertification cycles</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-muted opacity-75">
                                <i class="bi bi-x-circle text-muted fs-6 mt-0.5"></i>
                                <span>SSO, SAML &amp; manager audit trail dashboards</span>
                            </li>
                        </ul>

                        <div class="pt-3">
                            <a href="{{ route('register') }}" class="tj-btn-outline w-100 py-3 justify-content-center">
                                <span>Get Started Free</span>
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tier 2: Enterprise Compliance Hub (Featured & Recommended) --}}
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="lms-card p-4 p-xl-5 w-100 d-flex flex-column position-relative shadow-lg" 
                         style="background: var(--lms-surface-card); border: 2.5px solid var(--lms-accent) !important; border-radius: 28px; z-index: 3;">
                        
                        <!-- 100% Fully Visible Recommended Header Badge (Zero Half-Cut or Clipping) -->
                        <div class="text-center mb-3">
                            <span class="badge rounded-pill px-3.5 py-1.5 text-white shadow-sm d-inline-flex align-items-center gap-1.5"
                                  style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 0.74rem; font-weight: 700; letter-spacing: 0.8px; box-shadow: 0 4px 14px rgba(114, 62, 195, 0.35);">
                                <i class="bi bi-star-fill text-warning"></i> RECOMMENDED FOR ENTERPRISE
                            </span>
                        </div>

                        <div class="mb-3">
                            <span class="badge badge-compliance rounded-pill px-3 py-1 text-uppercase small fw-bold">
                                Corporate Workforce
                            </span>
                            <h3 class="fw-bold mt-3 mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                Enterprise Compliance Hub
                            </h3>
                            <div class="d-flex align-items-baseline gap-1 my-3">
                                <span class="display-4 fw-extrabold font-mono" style="color: var(--lms-accent);" x-text="billingAnnual ? '$9.60' : '$12'">$9.60</span>
                                <span class="text-muted small">/ seat / month</span>
                            </div>
                            <p class="text-secondary small" style="min-height: 42px;">
                                Engineered for corporate governance, automated recertification loops, and 5-tier CBAC permission compliance.
                            </p>
                        </div>

                        <hr class="my-3 opacity-25">

                        <ul class="list-unstyled my-3 flex-grow-1">
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span><strong>Automated annual recertification loop</strong> (ISO, OSHA, HIPAA)</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span><strong>5-Tier CBAC Hierarchy</strong> &amp; Department Manager Sign-Offs</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>SCORM 1.2 / 2004 Runtime package hosting &amp; CMI tracking</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Anti-cheat live webcam snapshot telemetry proctoring</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Biometric rolling QR geofenced lecture attendance</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Single Sign-On (Okta, Azure AD, SAML 2.0) &amp; CSV/PDF audit logs</span>
                            </li>
                        </ul>

                        <div class="pt-3">
                            <button type="button" class="tj-btn-primary w-100 py-3 justify-content-center shadow" data-bs-toggle="modal" data-bs-target="#enterpriseInquiryModal">
                                <span>Deploy Enterprise Hub</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tier 3: Institutional University & Cluster --}}
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="lms-card p-4 p-xl-5 w-100 d-flex flex-column border shadow-sm" style="background: var(--lms-surface-card); border-radius: 24px;">
                        <div class="mb-3">
                            <span class="badge rounded-pill px-3 py-1 text-uppercase small fw-bold" style="background: #E0E7FF; color: #3730A3;">
                                Higher Education &amp; Gov
                            </span>
                            <h3 class="fw-bold mt-3 mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                Institutional Cluster
                            </h3>
                            <div class="d-flex align-items-baseline gap-1 my-3">
                                <span class="display-4 fw-extrabold font-mono" style="color: var(--lms-text-primary);">Custom</span>
                                <span class="text-muted small">/ dedicated cluster</span>
                            </div>
                            <p class="text-secondary small" style="min-height: 42px;">
                                Tailored for multi-tenant universities, government accreditation bodies, and large-scale public examination boards.
                            </p>
                        </div>

                        <hr class="my-3 opacity-25">

                        <ul class="list-unstyled my-3 flex-grow-1">
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Dedicated isolated MySQL &amp; Redis cluster architecture</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Custom domain, white-label branding &amp; custom CSS tokens</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Canvas, Blackboard &amp; Moodle LTI 1.3 SIS integrations</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Unlimited faculty instructors &amp; proctored testing halls</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Custom grading algorithms &amp; custom weighted rubrics</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-3 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span>Dedicated Technical Account Manager &amp; 99.99% SLA guarantee</span>
                            </li>
                        </ul>

                        <div class="pt-3">
                            <button type="button" class="tj-btn-outline w-100 py-3 justify-content-center" data-bs-toggle="modal" data-bs-target="#enterpriseInquiryModal">
                                <span>Schedule Partnership Call</span>
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 3. INTERACTIVE ROI & SEAT CALCULATOR --}}
    <section class="py-5" style="background: var(--lms-bg-secondary);">
        <div class="container py-3">
            <div class="lms-card p-4 p-md-5 border-0 shadow-lg" style="background: var(--lms-surface-card); border-radius: 28px;">
                <div class="row g-4 align-items-center">
                    
                    <div class="col-lg-6">
                        <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2">
                            <i class="bi bi-calculator-fill me-1"></i> Interactive Team Sizing
                        </span>
                        <h2 class="display-6 fw-bold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            Estimate Your Enterprise Workforce ROI
                        </h2>
                        <p class="text-secondary small mb-4">
                            Slide to calculate your estimated investment, audit hours saved per quarter, and annual compliance dividend.
                        </p>

                        <!-- Slider -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-semibold text-secondary small">Estimated Active Workforce Seats:</label>
                                <span class="badge bg-primary fs-6 px-3 py-1 font-mono rounded-pill" x-text="teamSeats + ' Learners'">50 Learners</span>
                            </div>
                            <input type="range" class="form-range" min="10" max="500" step="5" x-model="teamSeats">
                            <div class="d-flex justify-content-between text-muted small mt-1 font-mono" style="font-size: 0.72rem;">
                                <span>10 Seats</span>
                                <span>100 Seats</span>
                                <span>250 Seats</span>
                                <span>500+ Seats</span>
                            </div>
                        </div>

                        <!-- Highlights -->
                        <div class="row g-2 pt-2">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background: var(--lms-bg-secondary);">
                                    <div class="text-muted small">Quarterly Audit Hours Saved</div>
                                    <div class="fs-4 fw-extrabold text-success font-mono" x-text="'~' + hoursSaved + ' hrs'">~125 hrs</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background: var(--lms-bg-secondary);">
                                    <div class="text-muted small">Annual Bulk Savings</div>
                                    <div class="fs-4 fw-extrabold text-primary font-mono" x-text="'$' + annualSavings">$1,440</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-4 p-md-5 rounded-4 text-center text-white" 
                             style="background: linear-gradient(135deg, #151928 0%, #272F54 100%); box-shadow: 0 15px 35px rgba(15, 19, 33, 0.4);">
                            <div class="text-white-50 small text-uppercase fw-bold letter-spacing-1 mb-2">Estimated Investment</div>
                            <div class="display-3 fw-extrabold font-mono text-white mb-1" x-text="'$' + calculatedPrice">$480.00</div>
                            <div class="text-white-50 small mb-4">
                                <span x-text="billingAnnual ? 'Billed annually ($9.60/seat/mo)' : 'Billed monthly ($12/seat/mo)'">Billed annually</span>
                            </div>

                            <button type="button" class="tj-btn-primary w-100 py-3 shadow-lg justify-content-center" data-bs-toggle="modal" data-bs-target="#enterpriseInquiryModal">
                                <span>Lock In Enterprise Quote</span>
                                <span class="btn-icon"><i class="bi bi-shield-check ms-2"></i></span>
                            </button>
                            <div class="text-white-50 small mt-3">
                                Includes full tenant migration, custom roles &amp; SCORM onboarding.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- 4. DEEP FEATURE COMPARISON MATRIX --}}
    <section class="py-5" style="background: var(--lms-bg-body);">
        <div class="container py-3">
            <div class="text-center mx-auto mb-5" style="max-width: 650px;">
                <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-table me-1"></i> Technical Matrix
                </span>
                <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Compare Capabilities Across Tiers
                </h2>
                <p class="text-secondary small">
                    Every tier includes our core responsive learner portal and strict WCAG 2.1 AA accessibility.
                </p>
            </div>

            <div class="table-responsive lms-card border shadow-sm p-0" style="border-radius: 20px; overflow: hidden; background: var(--lms-surface-card);">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-4 ps-4" style="width: 40%;">Core Capabilities</th>
                            <th class="py-4 text-center" style="width: 20%;">Student Track</th>
                            <th class="py-4 text-center" style="width: 20%; background: rgba(114, 62, 195, 0.08);">
                                <span class="text-primary fw-bold">Enterprise Hub</span>
                            </th>
                            <th class="py-4 text-center pe-4" style="width: 20%;">Institutional Cluster</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Category: Learning & Course Delivery -->
                        <tr class="table-secondary">
                            <td colspan="4" class="fw-bold py-2 ps-4 small text-uppercase letter-spacing-1">1. Learning Experience &amp; SCORM</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Self-Paced Video Player with Resume &amp; Bookmarks</td>
                            <td class="text-center"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">SCORM 1.2 &amp; SCORM 2004 Runtime Bridge</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Downloadable Exercise Files &amp; Lecture Notes</td>
                            <td class="text-center"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>

                        <!-- Category: Compliance & Proctoring -->
                        <tr class="table-secondary">
                            <td colspan="4" class="fw-bold py-2 ps-4 small text-uppercase letter-spacing-1">2. Compliance &amp; Anti-Cheat Proctoring</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Automated Recertification Loop (90d warning, 30d expiry)</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Anti-Cheat Live Webcam Proctoring (Window blur &amp; tab change)</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Cryptographically Signed PDF Certificate with QR Verification</td>
                            <td class="text-center"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Rolling Geofenced QR Code Lecture Attendance</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>

                        <!-- Category: Governance & Security -->
                        <tr class="table-secondary">
                            <td colspan="4" class="fw-bold py-2 ps-4 small text-uppercase letter-spacing-1">3. Governance, Security &amp; Access Control</td>
                        </tr>
                        <tr>
                            <td class="ps-4">5-Tier Context-Based Access Control (CBAC)</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">SSO Integration (Okta, Azure AD, SAML 2.0, OAuth2)</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Department Manager Sign-Off Dashboard &amp; Audit Trail</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center" style="background: rgba(114, 62, 195, 0.04);"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Dedicated Multi-Tenant Cluster &amp; White-Label Domain</td>
                            <td class="text-center text-muted">&bull;</td>
                            <td class="text-center text-muted" style="background: rgba(114, 62, 195, 0.04);">&bull;</td>
                            <td class="text-center pe-4"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- 5. FREQUENTLY ASKED QUESTIONS (ACCORDION) --}}
    <section class="py-5" style="background: var(--lms-bg-secondary);">
        <div class="container py-3" style="max-width: 860px;">
            <div class="text-center mb-5">
                <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-question-circle-fill me-1"></i> Common Inquiries
                </span>
                <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Frequently Asked Questions
                </h2>
                <p class="text-secondary small">
                    Everything you need to know about billing, enterprise deployment, and data governance.
                </p>
            </div>

            <div class="accordion shadow-sm rounded-4 overflow-hidden border-0" id="pricingFaqAccordion">
                
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                            How does the automated compliance recertification engine work?
                        </button>
                    </h2>
                    <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#pricingFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.75;">
                            When an employee passes a compliance track (e.g., ISO 27001 or OSHA Hazard Communication), the system records their certification date and active validity window. Our cron worker evaluates expirations daily. At 90 and 30 days prior to expiration, automated warning reminders are dispatched to the employee and their department manager. When the window expires, their status flips to expired and an automated recertification enrollment module is provisioned.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            Can I add or remove corporate workforce seats mid-cycle?
                        </button>
                    </h2>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#pricingFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.75;">
                            Yes. You can adjust your active employee count at any time via the Admin &gt; Settings panel. Seat additions are prorated to the exact remaining days of your current billing period, and decommissioned employee records retain full immutable audit logs for regulatory compliance.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            Do you support existing SCORM 1.2 or SCORM 2004 packages?
                        </button>
                    </h2>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#pricingFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.75;">
                            Completely. Our SCORM Runtime bridge supports ZIP packages created in Articulate 360, Adobe Captivate, and iSpring. It automatically unzips the manifest, handles `LMSInitialize`, `LMSSetValue`, and `LMSCommit` CMI telemetry, recording lesson status, suspend data, and raw scores directly into MySQL.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            How does the anti-cheat proctoring engine prevent exam fraud?
                        </button>
                    </h2>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#pricingFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.75;">
                            During high-stakes examinations, the learner client captures periodic periodic webcam snapshots and monitors DOM browser focus events. If the learner switches tabs, opens developer tools, or unfocuses the testing window, an anti-cheat log event is dispatched to the audit trail with an automated penalty flag.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading5">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                            Is there an educational institution or non-profit discount?
                        </button>
                    </h2>
                    <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#pricingFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.75;">
                            Yes. Accredited schools, universities, and registered 501(c)(3) non-profit organizations qualify for up to 30% discount on institutional clusters and student licensing. Contact our education team via the inquiry form below to verify your domain.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 6. BOTTOM CTA CARD (PREMIUM EDUNEX REDESIGN WITH 100% CONTRAST) --}}
    <section class="py-5" style="background: var(--lms-bg-body);">
        <div class="container py-3">
            <div class="p-5 rounded-4 text-center text-white position-relative overflow-hidden shadow-lg" 
                 style="background: radial-gradient(circle at 10% 20%, rgba(114, 62, 195, 0.45) 0%, transparent 50%), radial-gradient(circle at 90% 80%, rgba(255, 77, 0, 0.3) 0%, transparent 50%), linear-gradient(135deg, #0A0E1A 0%, #13192B 50%, #1F1B3E 100%); border: 1px solid rgba(255, 255, 255, 0.14); box-shadow: 0 25px 60px rgba(10, 14, 26, 0.5);">
                
                <!-- Ambient Glow Ring -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle"
                     style="width: 600px; height: 600px; background: radial-gradient(circle, rgba(114, 62, 195, 0.2) 0%, transparent 70%); filter: blur(60px); pointer-events: none;"></div>

                <div class="position-relative" style="z-index: 2; max-width: 760px; margin: 0 auto;">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3"
                         style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        <span class="small fw-bold text-white text-uppercase" style="letter-spacing: 0.6px; font-size: 0.75rem;">
                            Rapid 48-Hour Onboarding &bull; Zero Operational Disruption
                        </span>
                    </div>

                    <h2 class="display-5 fw-extrabold mb-3 text-white" 
                        style="font-family: var(--lms-font-heading); color: #FFFFFF !important; letter-spacing: -0.8px; line-height: 1.2;">
                        Ready to Transform Your <br class="d-none d-sm-inline">
                        <span style="background: linear-gradient(135deg, #60A5FA 0%, #A78BFA 50%, #F472B6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Workforce Compliance?
                        </span>
                    </h2>

                    <p class="lead fs-6 mb-4" style="color: rgba(255, 255, 255, 0.82) !important; line-height: 1.85;">
                        Join 2,500+ enterprises, accredited universities, and government bodies running automated recertification loops with zero audit surprises and verified proof logs.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('register') }}" class="tj-btn-primary py-3 px-5 shadow-lg">
                            <span>Get Started Now</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </a>

                        <button type="button" class="btn btn-outline-light py-3 px-4 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" 
                                style="border-color: rgba(255,255,255,0.35); background: rgba(255,255,255,0.08); backdrop-filter: blur(12px);"
                                data-bs-toggle="modal" data-bs-target="#enterpriseInquiryModal">
                            <i class="bi bi-chat-dots-fill text-warning"></i>
                            <span>Speak with an Architect</span>
                        </button>
                    </div>

                    <!-- Trust Checklist Row -->
                    <div class="d-flex flex-wrap justify-content-center gap-4 small mt-4 pt-4 border-top" 
                         style="border-color: rgba(255, 255, 255, 0.12) !important; color: rgba(255, 255, 255, 0.75);">
                        <span><i class="bi bi-shield-check text-success me-1"></i> Dedicated Success Manager</span>
                        <span><i class="bi bi-hdd-network text-info me-1"></i> 99.99% Guaranteed SLA</span>
                        <span><i class="bi bi-file-earmark-lock text-warning me-1"></i> Immutable Audit Logs</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- Enterprise Deployment Consultation Modal --}}
<div class="modal fade" id="enterpriseInquiryModal" tabindex="-1" aria-labelledby="enterpriseInquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: var(--lms-surface-card);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <span class="badge badge-purple-soft px-3 py-1 rounded-pill mb-1">
                        <i class="bi bi-chat-quote-fill me-1"></i> Direct Architect Consultation
                    </span>
                    <h4 class="modal-title fw-bold" id="enterpriseInquiryModalLabel" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        Inquire for Enterprise Deployment
                    </h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-4">
                    Our enterprise solutions architects will design your custom CBAC permissions hierarchy, automated recertification cadence, and SSO integration.
                </p>

                <form action="{{ route('cms.form-submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="pricing_page">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control border-start-0 ps-0" required placeholder="Alexander Vance">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Work Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="work_email" class="form-control border-start-0 ps-0" required placeholder="alexander@enterprise.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Organization / University</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                <input type="text" name="organization" class="form-control border-start-0 ps-0" placeholder="Acme Global Corporation">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Estimated Seats / Learners</label>
                            <select name="seats" class="form-select">
                                <option value="50-250">50 - 250 Employees</option>
                                <option value="250-1000">250 - 1,000 Employees</option>
                                <option value="1000+">1,000+ Enterprise Tier (Dedicated Cluster)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Compliance &amp; Technical Scope</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Detail your compliance requirements (e.g., ISO 27001, OSHA, HIPAA), SCORM package volumes, or SSO/LDAP integration needs..."></textarea>
                        </div>
                        <div class="col-12 text-center pt-2">
                            <button type="submit" class="tj-btn-primary px-5 py-3 shadow-sm">
                                <span>Submit Enterprise Inquiry</span>
                                <span class="btn-icon"><i class="bi bi-send-fill ms-2"></i></span>
                            </button>
                            <div class="text-muted small mt-2">
                                <i class="bi bi-shield-lock-fill me-1 text-success"></i> Your inquiries are protected by strict enterprise NDAs.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
