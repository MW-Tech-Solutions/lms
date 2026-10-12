@php
    $c = $section->content_json ?? [];
@endphp
<footer class="edunex-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            <!-- Col 1: Brand Info & Socials -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 fw-bold fs-4 text-white mb-3">
                    <span class="badge rounded-3 p-2 shadow-sm" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1.1rem; color: #FFF;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span style="font-family: var(--lms-font-heading); font-weight: 800; letter-spacing: -0.5px;">
                        {{ $site_settings['brand_name'] ?? 'Edunex LMS' }}
                    </span>
                </div>
                <p class="small text-white-50 pe-lg-4 mb-4" style="line-height: 1.8;">
                    {{ $c['about_text'] ?? ($site_settings['footer_about_text'] ?? 'The next-generation enterprise Learning Management System engineering mandatory corporate compliance recertifications, accredited academic cohorts, and anti-cheat assessment infrastructure.') }}
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="footer-social-icon" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="footer-social-icon" title="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="footer-social-icon" title="GitHub"><i class="bi bi-github"></i></a>
                    <a href="#" class="footer-social-icon" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Col 2: Courses & Specializations -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    {{ $site_settings['footer_col_platform'] ?? 'Curriculum' }}
                </h5>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('courses.index') }}">Course Catalog</a></li>
                    <li><a href="{{ route('corporate.compliance') }}">Compliance Tracks</a></li>
                    <li><a href="{{ route('courses.index') }}">Cloud &amp; DevSecOps</a></li>
                    <li><a href="{{ route('courses.index') }}">ISO/IEC 27001</a></li>
                    <li><a href="{{ route('cms.show', ['slug' => 'pricing']) }}">Cohort Pricing</a></li>
                </ul>
            </div>

            <!-- Col 3: Compliance & Architecture -->
            <div class="col-lg-3 col-md-6 col-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    {{ $site_settings['footer_col_legal'] ?? 'Governance & Trust' }}
                </h5>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('cms.show', ['slug' => 'terms']) }}">Terms of Service</a></li>
                    <li><a href="{{ route('cms.show', ['slug' => 'privacy']) }}">Privacy Policy</a></li>
                    <li><a href="{{ route('cms.show', ['slug' => 'support']) }}">Auditor Verification</a></li>
                    <li><a href="{{ route('corporate.compliance') }}">Manager Audit Trail</a></li>
                    <li><a href="{{ route('login') }}">SSO Single Sign-On</a></li>
                </ul>
            </div>

            <!-- Col 4: Newsletter & Mobile Apps (Edunex Signature) -->
            <div class="col-lg-3 col-md-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    Newsletter &amp; Mobile
                </h5>
                <p class="small text-white-50 mb-3">
                    Stay ahead with quarterly compliance briefings and new certification releases.
                </p>
                <form action="{{ route('cms.form-submit') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="full_name" value="Newsletter Subscriber">
                    <input type="hidden" name="message" value="Subscribed from footer newsletter.">
                    <div class="input-group newsletter-input-group">
                        <input type="email" name="work_email" class="form-control" placeholder="Your work email..." required>
                        <button class="btn" type="submit"><i class="bi bi-arrow-right"></i></button>
                    </div>
                </form>

                <!-- Mobile Badges -->
                <div class="d-flex align-items-center gap-2 pt-1">
                    <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25 px-2 py-1 rounded small">
                        <i class="bi bi-apple me-1"></i> iOS App
                    </span>
                    <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25 px-2 py-1 rounded small">
                        <i class="bi bi-google-play me-1"></i> Android
                    </span>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Row -->
        <div class="pt-4 mt-2 border-top border-secondary border-opacity-25 d-flex flex-wrap justify-content-between align-items-center gap-3 small text-white-50">
            <div>
                &copy; {{ date('Y') }} {{ $site_settings['brand_name'] ?? 'Edunex LMS' }}. {{ $site_settings['footer_copyright'] ?? 'All rights reserved. WCAG 2.1 AA & Section 508 Compliant.' }}
            </div>
            <div class="d-flex gap-3">
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1">
                    <i class="bi bi-shield-check me-1"></i> SOC2 Type II Audited
                </span>
                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1">
                    <i class="bi bi-cpu me-1"></i> 99.99% Uptime SLA
                </span>
            </div>
        </div>
    </div>
</footer>
