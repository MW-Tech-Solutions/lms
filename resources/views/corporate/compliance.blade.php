@extends('layouts.app')

@section('title', 'Corporate Compliance & Workforce Recertification')

@section('content')
<!-- Header Banner with High-Impact Dual-Column Layout -->
<div class="py-5 border-bottom position-relative overflow-hidden" style="background: radial-gradient(circle at 85% 15%, rgba(5, 150, 105, 0.12) 0%, rgba(114, 62, 195, 0.08) 40%, var(--lms-bg-body) 100%);">
    <!-- Ambient Background Glow Orbs -->
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(5, 150, 105, 0.15) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(30%, -20%);"></div>
    <div class="position-absolute bottom-0 start-0 rounded-circle" 
         style="width: 380px; height: 380px; background: radial-gradient(circle, rgba(114, 62, 195, 0.1) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(-30%, 20%);"></div>

    <div class="container position-relative py-3" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <!-- Left: Headline, Value Proposition & Actions -->
            <div class="col-lg-7 text-center text-lg-start">
                <div class="mb-3 d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-sm border" 
                     style="background: var(--lms-surface-card); border-color: rgba(5, 150, 105, 0.25) !important;">
                    <span class="pulse-dot"></span>
                    <span class="small fw-bold text-success" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                        AUTOMATED 365-DAY AUDIT ENGINE ACTIVE
                    </span>
                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                        SOC2 TYPE II
                    </span>
                </div>

                <h1 class="display-4 fw-extrabold mb-3" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -1px; line-height: 1.15;">
                    Workforce Compliance &amp;<br>
                    <span style="background: linear-gradient(135deg, #059669 0%, #2563EB 50%, #723EC3 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Recertification Engine.
                    </span>
                </h1>

                <p class="lead text-secondary fs-6 mb-4" style="line-height: 1.85; max-width: 600px;">
                    Continuous regulatory automation ensuring your multi-tenant workforce satisfies ISO/IEC 27001, OSHA, HIPAA, and GDPR regulatory mandates with immutable manager sign-offs, automated 90-day warning loops, and sub-second anti-cheat verification.
                </p>

                <!-- Action CTAs -->
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-3 mb-4">
                    <a href="{{ route('admin.compliance.index') }}" class="tj-btn-primary py-3 px-4 shadow-sm">
                        <span>Manager Audit Portal</span>
                        <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('cms.show', ['slug' => 'pricing']) }}" class="tj-btn-outline py-3 px-4">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        <span>Explore Compliance Tiers</span>
                    </a>
                </div>

                <!-- Verified Framework Pills -->
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 pt-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill small">
                        <i class="bi bi-check-circle-fill me-1"></i> ISO/IEC 27001
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1.5 rounded-pill small">
                        <i class="bi bi-shield-lock-fill me-1"></i> OSHA Certified
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-pill small">
                        <i class="bi bi-hospital-fill me-1"></i> HIPAA Ready
                    </span>
                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill small">
                        <i class="bi bi-lock-fill me-1"></i> GDPR Compliant
                    </span>
                </div>
            </div>

            <!-- Right: Interactive Compliance Shield & Telemetry Bento Card -->
            <div class="col-lg-5">
                <div class="p-4 rounded-4 shadow-lg position-relative overflow-hidden" 
                     style="background: #151928; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 20px 50px rgba(0,0,0,0.4);">
                    
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary border-opacity-25 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-2 rounded-circle" style="background: rgba(5, 150, 105, 0.2); color: #00D084;">
                                <i class="bi bi-shield-check fs-5"></i>
                            </span>
                            <div>
                                <div class="text-white fw-bold small" style="line-height: 1.2;">Live Regulatory Audit Telemetry</div>
                                <div class="text-white-50 font-mono" style="font-size: 0.65rem;">Updated: Daily at 00:00 UTC</div>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 font-mono small">
                            <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Active Loop
                        </span>
                    </div>

                    <!-- Main Progress Stat -->
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="text-white-50 small">Workforce Compliance Rate</span>
                            <span class="display-6 fw-extrabold font-mono text-success">98.4%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 999px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar rounded-pill" style="width: 98.4%; background: linear-gradient(90deg, #059669, #00D084);"></div>
                        </div>
                        <div class="d-flex justify-content-between text-white-50 small mt-1 font-mono" style="font-size: 0.68rem;">
                            <span>Target: 95.0% Required</span>
                            <span>Audit Risk: Zero Flags</span>
                        </div>
                    </div>

                    <!-- Mini 2-col Stats -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="text-white-50" style="font-size: 0.68rem;">Manager Sign-Offs</div>
                                <div class="fs-5 fw-bold text-white font-mono">142 / 142</div>
                                <div class="text-success" style="font-size: 0.65rem;"><i class="bi bi-check-all"></i> 100% Verified</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="text-white-50" style="font-size: 0.68rem;">Recertification Loop</div>
                                <div class="fs-5 fw-bold text-white font-mono">365 Days</div>
                                <div class="text-warning" style="font-size: 0.65rem;"><i class="bi bi-bell"></i> 90d/30d Auto Notice</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center text-white-50 small">
                        <span><i class="bi bi-file-earmark-lock text-info me-1"></i> SHA-256 Audit Trail</span>
                        <a href="{{ route('admin.compliance.index') }}" class="text-success fw-bold text-decoration-none" style="font-size: 0.75rem;">
                            Inspect Live Logs &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Feature Pillars -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="lms-card p-4 h-100 border-0 shadow-sm lms-card-interactive" style="background: var(--lms-surface-card);">
                <div class="stat-icon-pill" style="background: var(--tj-bg-mint); color: #059669;">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h5 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">Automated Annual Recertification</h5>
                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                    Configure custom recertification loops (e.g. 365 days). The automated scheduler expires stale credentials, archives history, and triggers re-enrollment notification alerts 30 days prior.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="lms-card p-4 h-100 border-0 shadow-sm lms-card-interactive" style="background: var(--lms-surface-card);">
                <div class="stat-icon-pill" style="background: var(--tj-bg-purple); color: var(--lms-accent);">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <h5 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">Department CBAC Hierarchies</h5>
                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                    5-tier Context-Based Access Control traverses from module up to department and organization. Managers view real-time department adherence metrics and execute sign-offs.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="lms-card p-4 h-100 border-0 shadow-sm lms-card-interactive" style="background: var(--lms-surface-card);">
                <div class="stat-icon-pill" style="background: var(--tj-bg-peach); color: #FF4D00;">
                    <i class="bi bi-file-earmark-lock-fill"></i>
                </div>
                <h5 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">Immutable Audit Trails</h5>
                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                    Full instructor grade modifications require pedagogical justification notes logged to immutable audit archives for third-party regulatory inspection.
                </p>
            </div>
        </div>
    </div>

    <!-- Active Compliance Tracks for Current User / Workforce -->
    <div class="lms-card p-4 p-md-5 border-0 shadow-sm mb-5" style="background: var(--lms-surface-card);">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">Mandatory Compliance Programs</h4>
                <div class="text-secondary small">Current corporate tracks requiring verified completion and manager sign-off.</div>
            </div>
            <a href="{{ route('admin.compliance.index') }}" class="tj-btn-outline py-2 px-3 small">
                <i class="bi bi-speedometer2 me-1"></i> Manager Audit Portal
            </a>
        </div>

        <div class="row g-4">
            @forelse($complianceTracks ?? [] as $track)
                <div class="col-md-6">
                    <div class="p-4 border rounded-4 lms-card-interactive h-100 d-flex flex-column" style="background: var(--lms-surface-card); border-color: var(--lms-border) !important;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-compliance rounded-pill px-3 py-1 small">
                                <i class="bi bi-building me-1"></i> {{ $track->category?->name ?? 'Corporate Governance' }}
                            </span>
                            <span class="badge bg-warning text-dark font-mono px-3 py-1 rounded-pill small fw-bold">
                                <i class="bi bi-arrow-repeat me-1"></i> {{ $track->recertification_days }} Days Loop
                            </span>
                        </div>

                        <h5 class="fw-bold mb-2 flex-grow-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            <a href="{{ route('courses.show', $track->slug) }}" class="text-decoration-none" style="color: var(--lms-text-primary);">
                                {{ $track->title }}
                            </a>
                        </h5>

                        <div class="d-flex align-items-center justify-content-between small my-3 py-2 px-3 rounded-3" style="background: var(--lms-bg-body); border: 1px solid var(--lms-border); color: var(--lms-text-secondary);">
                            <span><i class="bi bi-shield-check text-success me-1"></i> Min Pass: {{ $track->passing_grade }}%</span>
                            <span><i class="bi bi-clock text-primary me-1"></i> Self-Paced Modules</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 mt-auto">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                                Mandatory Enrollment
                            </span>
                            <a href="{{ route('courses.show', $track->slug) }}" class="tj-btn-primary py-2 px-3 small">
                                <span>Start Track</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill text-success display-4 d-block mb-3"></i>
                    <h5 class="fw-bold">All mandatory compliance tracks are up to date.</h5>
                    <p class="small">No pending corporate recertification cycles scheduled at this time.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
