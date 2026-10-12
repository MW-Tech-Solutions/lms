@php
    $c = $section->content_json ?? [];
@endphp
<section class="hero-gradient-canvas position-relative py-5 overflow-hidden">
    <!-- Ambient Background Lighting Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 520px; height: 520px; background: radial-gradient(circle, rgba(114, 62, 195, 0.16) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 440px; height: 440px; background: radial-gradient(circle, rgba(255, 77, 0, 0.12) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center g-4 g-lg-5">
            <!-- Left: Hero Headline & Action Strategy -->
            <div class="col-lg-6 text-center text-lg-start">
                <!-- Live Pill Badge -->
                <div class="hero-live-pill mb-3 d-inline-flex align-items-center flex-wrap flex-sm-nowrap gap-2 px-3 py-1 rounded-pill shadow-sm border" 
                     style="background: var(--lms-surface-card); border-color: rgba(114, 62, 195, 0.2) !important; max-width: 100%;">
                    <span class="badge rounded-pill px-2 py-1 flex-shrink-0" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); color: #FFF; font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-lightning-charge-fill me-1"></i> LIVE LMS ENGINE
                    </span>
                    <span class="small fw-semibold text-secondary text-truncate" style="font-size: 0.82rem;">
                        Trusted by 2,500+ Organizations &amp; Accreditation Boards
                    </span>
                </div>

                <!-- Main Display Title -->
                <h1 class="display-4 fw-extrabold mb-3 lh-sm" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -1px; word-break: break-word;">
                    Empower Your Workforce.<br>
                    <span style="background: linear-gradient(135deg, #723EC3 0%, #2563EB 50%, #FF4D00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Certify With Distinction.
                    </span>
                </h1>

                <!-- Clear Value Proposition -->
                <p class="lead text-secondary mb-4 fs-6" style="line-height: 1.85; max-width: 560px;">
                    The dual-audience enterprise learning platform engineered for Next-Gen Workforce Compliance and high-stakes operations. Automate annual corporate recertifications, enforce 5-tier CBAC audit trails, and deliver accredited technical credentials with sub-millisecond anti-cheat verification.
                </p>

                <!-- Dual Action CTAs -->
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-2 gap-sm-3 mb-4">
                    <a href="{{ $c['primary_cta_url'] ?? route('courses.index') }}" class="tj-btn-primary py-2.5 py-sm-3 px-3 px-sm-4 shadow-sm" style="font-size: 0.95rem;">
                        <span>Explore Curriculum</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ $c['secondary_cta_url'] ?? route('corporate.compliance') }}" class="tj-btn-outline py-2.5 py-sm-3 px-3 px-sm-4" style="font-size: 0.95rem;">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        <span>Corporate Compliance Hub</span>
                    </a>
                </div>

                <!-- Verified Key Assurance Badges -->
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 pt-2 text-secondary small fw-medium">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1" style="background: rgba(5, 150, 105, 0.15); color: #059669;">
                            <i class="bi bi-check-lg fw-bold"></i>
                        </span>
                        <span>ISO/IEC 27001 &amp; OSHA Ready</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1" style="background: rgba(5, 150, 105, 0.15); color: #059669;">
                            <i class="bi bi-check-lg fw-bold"></i>
                        </span>
                        <span>SCORM 1.2 / 2004 &amp; xAPI Native</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1" style="background: rgba(5, 150, 105, 0.15); color: #059669;">
                            <i class="bi bi-check-lg fw-bold"></i>
                        </span>
                        <span>365-Day Recertification Loops</span>
                    </div>
                </div>
            </div>

            <!-- Right: Premium Real-World Learner Image & Floating Interactive Badges -->
            <div class="col-lg-6 position-relative">
                <div class="position-relative mx-auto" style="max-width: 530px;">
                    <!-- Central Photograph Frame -->
                    <div class="hero-image-frame position-relative">
                        <img src="{{ asset('images/hero_learner.jpg') }}" alt="Enterprise Learner Showcase" class="img-fluid w-100">

                        <!-- Bottom Glassmorphic Telemetry Ribbon Overlay -->
                        <div class="position-absolute bottom-0 start-0 end-0 p-3 m-3 rounded-3 d-flex align-items-center justify-content-between text-white" 
                             style="background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot"></span>
                                <div class="small fw-semibold" style="font-size: 0.8rem;">
                                    Live SCORM 1.2 &amp; xAPI Telemetry Active
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 font-mono small" style="font-size: 0.72rem;">
                                99.8% SLA
                            </span>
                        </div>
                    </div>

                    <!-- Floating Badge 1: Top-Left Circular Progress Box -->
                    <div class="banner-progress-box animate-float position-absolute d-none d-md-flex align-items-center gap-3 shadow-lg" 
                         style="top: -24px; left: -36px; z-index: 3;">
                        <div class="position-relative" style="width: 54px; height: 54px;">
                            <svg class="circle-progress-svg" width="54" height="54" viewBox="0 0 100 100">
                                <circle class="circle-progress-bg" cx="50" cy="50" r="42"></circle>
                                <circle class="circle-progress-bar" cx="50" cy="50" r="42"></circle>
                            </svg>
                            <span class="position-absolute top-50 start-50 translate-middle fw-bold font-mono" style="color: var(--lms-accent); font-size: 0.8rem;">80%</span>
                        </div>
                        <div>
                            <div class="small fw-bold" style="font-family: var(--lms-font-heading);">Course Progress</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Syllabus Mastery</div>
                        </div>
                    </div>

                    <!-- Floating Badge 2: Bottom-Left Compliance Certified Pill -->
                    <div class="lms-card p-3 shadow-lg position-absolute d-none d-lg-flex align-items-center gap-2 animate-float-slow"
                         style="bottom: 70px; left: -32px; z-index: 3; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 9999px;">
                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-mint); color: #059669;">
                            <i class="bi bi-shield-check fs-6"></i>
                        </span>
                        <div class="pe-2">
                            <div class="small fw-bold text-dark" style="font-size: 0.78rem;">ISO 27001 Audit Cleared</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Annual Recertification Passed</div>
                        </div>
                    </div>

                    <!-- Floating Badge 3: Bottom-Right Certified Community Pill -->
                    <div class="banner-launched-pill animate-float-slow position-absolute d-none d-sm-inline-flex shadow-lg" 
                         style="bottom: -20px; right: -20px; z-index: 3;">
                        <span class="badge rounded-circle p-2" style="background: var(--tj-bg-purple); color: var(--lms-accent);">
                            <i class="bi bi-award-fill"></i>
                        </span>
                        <div class="small">
                            <span class="fw-bold text-body">45,000+ Certified</span>
                            <span class="text-secondary">&bull; 2,500+ Organizations</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
