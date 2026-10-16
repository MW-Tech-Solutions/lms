@php
    $c = $section->content_json ?? [];
@endphp
<footer class="edunex-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            <!-- Col 1: Brand Info & Socials -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center fw-bold fs-4 text-white mb-3" style="gap: 12px;">
                    @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                        <img src="{{ $site_settings['brand_logo_url'] }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="38" class="rounded-2 shadow-sm me-2" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                    @elseif(file_exists(public_path('images/site_logo.png')))
                        <img src="{{ asset('images/site_logo.png') }}" alt="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" height="38" class="rounded-2 shadow-sm me-2">
                    @else
                        <span class="badge rounded-3 p-2 shadow-sm me-2" style="background: linear-gradient(135deg, var(--lms-accent), #FF4D00); font-size: 1.1rem; color: #FFF;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </span>
                    @endif
                    <span style="font-family: var(--lms-font-heading); font-weight: 800; letter-spacing: -0.5px;">
                        {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}
                    </span>
                </div>
                <p class="small text-white-50 pe-lg-4 mb-4" style="line-height: 1.8;">
                    {{ $c['about_text'] ?? ($site_settings['footer_about_text'] ?? 'The next-generation enterprise Learning Management System engineering mandatory corporate compliance recertifications, accredited academic cohorts, and anti-cheat assessment infrastructure.') }}
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ $site_settings['social_linkedin'] ?? '#' }}" target="_blank" class="footer-social-icon" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="{{ $site_settings['social_twitter'] ?? '#' }}" target="_blank" class="footer-social-icon" title="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                    <a href="{{ $site_settings['social_facebook'] ?? '#' }}" target="_blank" class="footer-social-icon" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="{{ $site_settings['social_youtube'] ?? '#' }}" target="_blank" class="footer-social-icon" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="{{ $site_settings['social_instagram'] ?? '#' }}" target="_blank" class="footer-social-icon" title="Instagram"><i class="bi bi-instagram"></i></a>
                </div>

                <!-- Dynamic Administrator Communication & Contact (Managed in Admin Settings) -->
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 small text-white-50">
                    <div class="d-flex flex-wrap align-items-center gap-x-3 gap-y-1 text-white small" style="font-size: 0.8rem;">
                        <a href="mailto:{{ preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $site_settings['admin_contact_email'] ?? 'admin@enterpriselms.com') }}" class="text-white text-decoration-none hover-underline text-nowrap d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-envelope-fill text-primary"></i>
                            <span>{{ preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $site_settings['admin_contact_email'] ?? 'admin@enterpriselms.com') }}</span>
                        </a>
                        <span class="text-secondary d-none d-sm-inline">&bull;</span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $site_settings['admin_contact_phone'] ?? '+44 20 8123 4567') }}" class="text-white text-decoration-none hover-underline text-nowrap d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-telephone-fill text-success"></i>
                            <span>{{ preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $site_settings['admin_contact_phone'] ?? '+44 (0) 20 8123 4567') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Col 2: Cambridge Assessment Silos -->
            <div class="col-lg-3 col-md-6 col-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    Cambridge Levels
                </h5>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('courses.index', ['level' => 'primary-checkpoint']) }}">Primary Checkpoint (Y1-6)</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'lower-secondary']) }}">Lower Secondary (Y7-9)</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'igcse']) }}">Cambridge IGCSE (Y10-11)</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'as-level']) }}">Cambridge AS Level (Y12)</a></li>
                    <li><a href="{{ route('courses.index', ['level' => 'a-level']) }}">Cambridge A Level (Y13)</a></li>
                </ul>
            </div>

            <!-- Col 3: Revision & Features -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    Revision Tools
                </h5>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('scores.history') }}">Esther Math Tracker</a></li>
                    <li><a href="{{ route('scores.report') }}" target="_blank">Diagnostic Reports</a></li>
                    <li><a href="{{ route('courses.index') }}">Worked Solutions</a></li>
                    <li><a href="{{ route('courses.index') }}">Topical Past Papers</a></li>
                    <li><a href="{{ route('cms.show', ['slug' => 'pricing']) }}">Subscription Plans (£)</a></li>
                    <li><a href="{{ route('about') }}">About AllThingsCambridge</a></li>
                    <li><a href="{{ route('contact') }}">Contact &amp; Support</a></li>
                </ul>
            </div>

            <!-- Col 4: Cambridge Series Newsletter -->
            <div class="col-lg-3 col-md-6">
                <h5 class="small text-uppercase fw-bold text-white mb-3" style="letter-spacing: 1px;">
                    Exam Notifications
                </h5>
                <p class="small text-white-50 mb-3">
                    Receive official Cambridge timetable releases, mark scheme alerts, and Esther Math clinic notifications.
                </p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="level_interest" value="All Cambridge Levels">
                    <div class="input-group newsletter-input-group shadow-sm">
                        <input type="email" name="email" class="form-control" placeholder="Enter email for exam alerts..." required>
                        <button class="btn" type="submit" title="Subscribe to Exam Alerts"><i class="bi bi-arrow-right"></i></button>
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
