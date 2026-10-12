@php
    $c = $section->content_json ?? [];
@endphp
<section class="bento-showcase-section py-5" style="background: var(--lms-bg-body);">
    <div class="container-fluid px-3 px-xl-5 py-3">
        <div class="row g-4 align-items-stretch">
            
            {{-- 1. LEFT CARD: Secure From the Start --}}
            <div class="col-lg-5 col-xl-4 d-flex">
                <div class="bento-secure-card w-100 shadow-sm">
                    <div>
                        <h2 class="bento-secure-title">Secure From the Start</h2>
                        <p class="bento-secure-desc">
                            Keep your website experience secure and transparent with our thoughtful privacy controls, cookie management.
                        </p>
                    </div>

                    {{-- Privacy Mockup Preview with Central Shield Badge --}}
                    <div class="privacy-mockup-wrapper">
                        {{-- Glowing Blue Shield Icon Badge --}}
                        <div class="privacy-shield-badge">
                            <svg width="34" height="38" viewBox="0 0 34 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 2L3 7.5V17.5C3 26.5 9 33.5 17 37C25 33.5 31 26.5 31 17.5V7.5L17 2Z" fill="white"/>
                                <circle cx="17" cy="17" r="3.5" fill="#0077B6"/>
                                <path d="M15.5 18L14.8 23.5H19.2L18.5 18H15.5Z" fill="#0077B6"/>
                            </svg>
                        </div>

                        {{-- Realistic Privacy UI Frame --}}
                        <div class="privacy-mockup-frame">
                            <div class="row g-2">
                                {{-- Mini Sidebar --}}
                                <div class="col-4 privacy-sidebar-nav border-end pe-2">
                                    <div class="nav-heading">On this page</div>
                                    <div class="nav-item">Information we collect</div>
                                    <div class="nav-item active">How we use your data</div>
                                    <div class="nav-item">What we never do</div>
                                    <div class="nav-item">Cookies & track</div>
                                    <div class="nav-item">Data security & retention</div>
                                    <div class="nav-item">Your rights & choices</div>
                                    <div class="nav-item">Children's privacy updates</div>

                                    {{-- Mini Question Callout --}}
                                    <div class="privacy-help-box">
                                        <div class="fw-bold text-dark d-flex align-items-center mb-1" style="font-size: 0.65rem;">
                                            <i class="bi bi-chat-heart-fill text-purple me-1" style="color: #723EC3;"></i>
                                            Questions about privacy?
                                        </div>
                                        <p class="mb-1 text-muted" style="font-size: 0.58rem; line-height: 1.25;">
                                            Our dedicated team replies within one business day.
                                        </p>
                                        <a href="#enterpriseInquiryModal" data-bs-toggle="modal" class="badge rounded-pill text-white px-2 py-1 text-decoration-none" style="background: #723EC3; font-size: 0.58rem;">
                                            Book session <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Main Policy Content --}}
                                <div class="col-8 privacy-content-preview ps-2">
                                    <div class="d-flex justify-content-end mb-1">
                                        <span class="text-muted" style="font-size: 0.58rem;">Last updated: June 1, 2026 - Version 4.2</span>
                                    </div>

                                    <p class="mb-2 text-secondary" style="font-size: 0.62rem; line-height: 1.35;">
                                        Your trust matters to us. This policy explains — in plain language — what data Edunex collects, why we collect it, and the choices you have. The short version: we collect only what we need to run your learning experience, we never sell your data, and you stay in control.
                                    </p>

                                    {{-- 3 Mini Stat Boxes --}}
                                    <div class="row g-1 mb-2">
                                        <div class="col-4">
                                            <div class="stat-pill text-center">
                                                <div class="fw-bold text-body" style="font-size: 0.6rem;">Minimal collection</div>
                                                <div class="text-muted" style="font-size: 0.52rem; line-height: 1.2;">Only data needed to run courses.</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-pill text-center">
                                                <div class="fw-bold text-body" style="font-size: 0.6rem;">Never sold</div>
                                                <div class="text-muted" style="font-size: 0.52rem; line-height: 1.2;">Never sold or rented to 3rd parties.</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-pill text-center">
                                                <div class="fw-bold text-body" style="font-size: 0.6rem;">You're in control</div>
                                                <div class="text-muted" style="font-size: 0.52rem; line-height: 1.2;">Export or delete anytime in settings.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="fw-bold text-body mb-1" style="font-size: 0.65rem;">01. Information we collect</div>
                                    <p class="text-muted mb-1" style="font-size: 0.58rem; line-height: 1.25;">
                                        We collect information you give us directly and a small amount generated by your use of the platform:
                                    </p>
                                    <ul class="list-unstyled mb-0 text-secondary" style="font-size: 0.56rem; line-height: 1.3;">
                                        <li class="mb-1"><strong class="text-dark">Account details:</strong> Name, email address and login profile.</li>
                                        <li class="mb-1"><strong class="text-dark">Learning activity:</strong> Courses enrolled, completions, quiz telemetry.</li>
                                        <li class="mb-1"><strong class="text-dark">Payment info:</strong> Processed securely via encrypted gateways (Stripe/PayPal).</li>
                                        <li><strong class="text-dark">Device data:</strong> Browser diagnostics to keep system fast and safe.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. RIGHT CARD: Faster Performance --}}
            <div class="col-lg-7 col-xl-8 d-flex">
                <div class="bento-perf-card w-100 shadow-lg">
                    <div class="row g-4 align-items-center h-100">
                        
                        {{-- Left Column: Text & Metrics --}}
                        <div class="col-md-5 d-flex flex-column justify-content-between h-100">
                            <div>
                                {{-- Rotating Sunburst Spinner Rays --}}
                                <div class="mb-3">
                                    <svg width="38" height="38" viewBox="0 0 38 38" fill="none" class="lms-perf-spinner" xmlns="http://www.w3.org/2000/svg">
                                        <g stroke="white" stroke-width="2.5" stroke-linecap="round">
                                            <line x1="19" y1="3" x2="19" y2="8" opacity="1"/>
                                            <line x1="29.6" y1="7.4" x2="26.1" y2="10.9" opacity="0.9"/>
                                            <line x1="34" y1="19" x2="29" y2="19" opacity="0.8"/>
                                            <line x1="29.6" y1="30.6" x2="26.1" y2="27.1" opacity="0.7"/>
                                            <line x1="19" y1="35" x2="19" y2="30" opacity="0.6"/>
                                            <line x1="8.4" y1="30.6" x2="11.9" y2="27.1" opacity="0.5"/>
                                            <line x1="4" y1="19" x2="9" y2="19" opacity="0.4"/>
                                            <line x1="8.4" y1="7.4" x2="11.9" y2="10.9" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </div>

                                <h2 class="bento-perf-title">Faster Performance</h2>
                                <p class="bento-perf-desc">
                                    Give learners a faster way to explore your course with optimized layouts that reduced waiting and keep every interaction smooth.
                                </p>
                            </div>

                            {{-- Benchmark Metric Bar: Grade A, Performance > 95%, Structure > 93% --}}
                            <div class="perf-metrics-strip">
                                <div class="perf-grade-a">A</div>
                                
                                <div class="perf-divider"></div>

                                <div>
                                    <div class="perf-stat-label">PERFORMANCE?</div>
                                    <div class="perf-stat-value">&gt; 95%</div>
                                </div>

                                <div class="perf-divider"></div>

                                <div>
                                    <div class="perf-stat-label">STRCUTURE?</div>
                                    <div class="perf-stat-value">&gt; 93%</div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Gauge Speedometer & Laptop Hardware Mockup --}}
                        <div class="col-md-7 h-100">
                            <div class="bento-laptop-container">
                                
                                {{-- Semi-Circular Speedometer Gauge Meter --}}
                                <div class="speedometer-wrapper">
                                    <svg width="160" height="90" viewBox="0 0 160 90" fill="none" class="speedometer-svg" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <linearGradient id="gaugeArcGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#FF3B30" />
                                                <stop offset="25%" stop-color="#FF9500" />
                                                <stop offset="50%" stop-color="#FFCC00" />
                                                <stop offset="75%" stop-color="#34C759" />
                                                <stop offset="100%" stop-color="#00D084" />
                                            </linearGradient>
                                        </defs>
                                        {{-- Background Arc --}}
                                        <path d="M 18 80 A 62 62 0 0 1 142 80" fill="none" stroke="rgba(255, 255, 255, 0.12)" stroke-width="16" stroke-linecap="round"/>
                                        {{-- Colorful Gradient Arc --}}
                                        <path d="M 18 80 A 62 62 0 0 1 142 80" fill="none" stroke="url(#gaugeArcGradient)" stroke-width="14" stroke-linecap="round"/>
                                        {{-- Dial needle pointing right at 95% zone --}}
                                        <g transform="translate(80, 80)">
                                            <line x1="0" y1="0" x2="38" y2="-40" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round"/>
                                            <polygon points="36,-38 46,-48 40,-34" fill="#FFFFFF"/>
                                            <circle cx="0" cy="0" r="10" fill="#151928" stroke="#FFFFFF" stroke-width="2.5"/>
                                            <circle cx="0" cy="0" r="4.5" fill="#00D084"/>
                                        </g>
                                    </svg>
                                </div>

                                {{-- Sleek Laptop Hardware Showcase --}}
                                <div class="laptop-chassis">
                                    <div class="laptop-lid">
                                        {{-- Webcam Dot --}}
                                        <div class="laptop-webcam"></div>
                                        
                                        {{-- Laptop Screen Frame --}}
                                        <div class="laptop-screen shadow">
                                            {{-- Mini Edunex Nav Bar --}}
                                            <div class="d-flex align-items-center justify-content-between px-2 py-1" style="background: #111422; font-size: 8px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge rounded-circle p-1" style="background: #723EC3;"><i class="bi bi-mortarboard-fill text-white" style="font-size: 7px;"></i></span>
                                                    <span class="fw-bold text-white" style="letter-spacing: -0.3px;">Edunex</span>
                                                </div>
                                                <div class="d-none d-sm-flex align-items-center gap-2 text-muted" style="font-size: 7px;">
                                                    <span class="text-white fw-semibold">Home</span>
                                                    <span>Pages</span>
                                                    <span>Courses</span>
                                                    <span>Blog</span>
                                                    <span>Contact</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge bg-secondary px-1 py-0.5 text-white" style="font-size: 6px;"><i class="bi bi-search"></i> Quick search...</span>
                                                    <span class="rounded-circle bg-white text-dark text-center" style="width: 12px; height: 12px; font-size: 7px; line-height: 12px;"><i class="bi bi-person-fill"></i></span>
                                                </div>
                                            </div>

                                            {{-- Mini Hero Section inside Laptop --}}
                                            <div class="p-2 position-relative" style="background: #1E2235; min-height: 140px;">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-7">
                                                        <span class="badge px-1 py-0.5 rounded-pill mb-1" style="background: rgba(114, 62, 195, 0.25); color: #B388FF; font-size: 6px;">
                                                            <i class="bi bi-star-fill text-warning"></i> #1 FEATURED ONLINE LMS
                                                        </span>
                                                        <div class="fw-bold text-white mb-1" style="font-size: 9.5px; line-height: 1.2; font-family: var(--lms-font-heading);">
                                                            Transform Future Through Online Skill Building.
                                                        </div>
                                                        <div class="d-flex gap-1 mb-1">
                                                            <span class="badge px-2 py-0.5 rounded-pill text-white" style="background: #723EC3; font-size: 6px;">Start Learning Now</span>
                                                            <span class="badge px-2 py-0.5 rounded-pill border border-secondary text-light" style="font-size: 6px;">Explore courses</span>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 5.5px;">
                                                            <i class="bi bi-check-circle-fill text-success"></i> 36K+ active enterprise & academic learners
                                                        </div>
                                                    </div>

                                                    <div class="col-5 position-relative text-center">
                                                        <div class="position-relative d-inline-block">
                                                            <img src="{{ asset('images/hero_learner.jpg') }}" alt="Learner" class="img-fluid rounded-3 shadow-sm" style="max-height: 85px; object-fit: cover; border: 1.5px solid rgba(255,255,255,0.2);">
                                                            
                                                            {{-- Mini floating badge --}}
                                                            <div class="position-absolute shadow-sm px-1 py-0.5 rounded-pill" style="bottom: -4px; left: -10px; background: rgba(15, 19, 33, 0.95); border: 1px solid rgba(255,255,255,0.15); font-size: 5px; color: #00D084; white-space: nowrap;">
                                                                <i class="bi bi-speedometer2"></i> 95% Pass Rate
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Partner Logos Strip at Screen Bottom --}}
                                                <div class="mt-2 pt-1 border-top border-secondary border-opacity-25 text-center">
                                                    <div class="text-muted mb-1" style="font-size: 5.5px;">Trusted by 40,000+ teams and universities worldwide</div>
                                                    <div class="d-flex justify-content-center align-items-center gap-2 text-muted" style="font-size: 6px; opacity: 0.7;">
                                                        <span>JUST EAT</span>
                                                        <span>•</span>
                                                        <span>INSTRUX</span>
                                                        <span>•</span>
                                                        <span>P&amp;G</span>
                                                        <span>•</span>
                                                        <span>opple</span>
                                                        <span>•</span>
                                                        <span>AllSource</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Laptop Bottom Base Edge --}}
                                    <div class="laptop-base">
                                        <div class="laptop-notch"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- Enterprise Deployment Consultation Modal Trigger Banner --}}
        <div class="text-center mt-4">
            <span class="text-muted small">
                Looking for enterprise single-sign on, SCORM clusters, or CBAC role hierarchies?
            </span>
            <button type="button" class="btn btn-link btn-sm text-decoration-none fw-semibold" style="color: var(--tj-color-theme-primary);" data-bs-toggle="modal" data-bs-target="#enterpriseInquiryModal">
                Inquire for Enterprise Deployment <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>
</section>

{{-- Enterprise Deployment Modal (Preserves backend lead submission & consultation form) --}}
<div class="modal fade" id="enterpriseInquiryModal" tabindex="-1" aria-labelledby="enterpriseInquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: var(--lms-surface-card);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <span class="badge badge-purple-soft px-3 py-1 rounded-pill mb-1">
                        <i class="bi bi-chat-quote-fill me-1"></i> Direct Architect Consultation
                    </span>
                    <h4 class="modal-title fw-bold" id="enterpriseInquiryModalLabel" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                        {{ $c['title'] ?? ($site_settings['form_contact_title'] ?? 'Inquire for Enterprise Deployment') }}
                    </h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-4">
                    {{ $c['subtitle'] ?? ($site_settings['form_contact_subtitle'] ?? 'Our enterprise solutions architects will design your custom CBAC permissions hierarchy, automated recertification cadence, and SSO integration.') }}
                </p>

                <form action="{{ route('cms.form-submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">{{ $site_settings['label_form_name'] ?? 'Full Name' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control border-start-0 ps-0" required placeholder="Alexander Vance">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">{{ $site_settings['label_form_work_email'] ?? 'Work Email' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="work_email" class="form-control border-start-0 ps-0" required placeholder="alexander@enterprise.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">{{ $site_settings['label_form_company'] ?? 'Organization / University' }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                <input type="text" name="organization" class="form-control border-start-0 ps-0" placeholder="Acme Global Corporation">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">{{ $site_settings['label_form_seats'] ?? 'Estimated Seats / Learners' }}</label>
                            <select name="seats" class="form-select">
                                <option value="50-250">50 - 250 Employees</option>
                                <option value="250-1000">250 - 1,000 Employees</option>
                                <option value="1000+">1,000+ Enterprise Tier (Dedicated Cluster)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">{{ $site_settings['label_form_requirements'] ?? 'Compliance & Technical Scope' }}</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Detail your compliance requirements (e.g., ISO 27001, OSHA, HIPAA), SCORM package volumes, or SSO/LDAP integration needs..."></textarea>
                        </div>
                        <div class="col-12 text-center pt-2">
                            <button type="submit" class="tj-btn-primary px-5 py-3 shadow-sm">
                                <span>{{ $c['submit_label'] ?? ($site_settings['btn_submit_inquiry'] ?? 'Submit Enterprise Inquiry') }}</span>
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
