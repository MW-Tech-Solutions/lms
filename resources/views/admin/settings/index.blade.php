@extends('layouts.admin')

@section('title', 'Dynamic System Settings & Visual Theme Studio')

@section('content')
<div class="container-fluid" x-data="{
    selectedPreset: '{{ $activePreset ?? 'default' }}',
    primaryColor: '{{ $site_settings['brand_primary_color'] ?? '#0F1321' }}',
    accentColor: '{{ $site_settings['brand_accent_color'] ?? '#723EC3' }}',
    secondaryColor: '{{ $site_settings['brand_secondary_color'] ?? '#F8F9FD' }}',
    selectTemplate(key, primary, accent, secondary) {
        this.selectedPreset = key;
        this.primaryColor = primary;
        this.accentColor = accent;
        this.secondaryColor = secondary;
        document.getElementById('primaryHex').value = primary;
        document.getElementById('primaryPicker').value = primary;
        document.getElementById('accentHex').value = accent;
        document.getElementById('accentPicker').value = accent;
        document.getElementById('secondaryHex').value = secondary;
        document.getElementById('secondaryPicker').value = secondary;
    }
}">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-2">
                <i class="bi bi-sliders me-1"></i> Enterprise Whitelabel &amp; Theme Studio
            </span>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_settings_manager'] ?? 'Dynamic System Settings & Theme Customization' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_settings_manager'] ?? 'Zero-Hardcoding Compliance Console: Configure brand assets, logo, favicon, session security, and dynamic color templates.' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="settingsForm" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill fw-semibold">
                <i class="bi bi-check2-circle me-1.5"></i> Save &amp; Broadcast System Changes
            </button>
        </div>
    </div>

    <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- SECTION 1: 10 ENTERPRISE COLOR TEMPLATES SELECTOR -->
        <div class="lms-card p-4 shadow-sm mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-palette-fill text-primary me-2"></i>Global Enterprise Color Templates (10 Presets)
                    </h5>
                    <div class="text-secondary small">
                        Choose a curated color system to completely override buttons, navbars, badges, gradients, and cards across all learner, workforce, and admin interfaces.
                    </div>
                </div>
                <div class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-1.5 font-mono small">
                    <i class="bi bi-broadcast me-1"></i> Live Dynamic Theming
                </div>
            </div>

            <input type="hidden" name="color_template_preset" :value="selectedPreset">

            <div class="row g-3">
                @foreach($templates as $key => $tpl)
                    <div class="col-xl-4 col-md-6">
                        <div class="p-3 rounded-4 border transition-all position-relative cursor-pointer h-100 d-flex flex-column"
                             style="cursor: pointer;"
                             :class="selectedPreset === '{{ $key }}' ? 'border-primary shadow-sm ring-2 ring-primary' : 'border-light-subtle hover-border-secondary'"
                             :style="selectedPreset === '{{ $key }}' ? 'border-width: 2px !important; background: var(--lms-surface-card);' : 'background: var(--lms-surface-card);'"
                             @click="selectTemplate('{{ $key }}', '{{ $tpl['primary'] }}', '{{ $tpl['accent'] }}', '{{ $tpl['secondary'] }}')">
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge rounded-pill px-2.5 py-1 text-uppercase font-mono" 
                                      style="font-size: 0.65rem; background: {{ $tpl['accent'] }}; color: #FFF;">
                                    {{ $tpl['badge'] }}
                                </span>
                                
                                <span x-show="selectedPreset === '{{ $key }}'" class="badge bg-primary text-white rounded-pill px-2 py-0.5 small">
                                    <i class="bi bi-check-circle-fill me-1"></i> ACTIVE
                                </span>
                            </div>

                            <div class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                                {{ $tpl['name'] }}
                            </div>
                            
                            <div class="text-secondary small mb-3 flex-grow-1" style="font-size: 0.78rem; line-height: 1.4;">
                                {{ $tpl['description'] }}
                            </div>

                            <!-- Color Palette Swatches -->
                            <div class="d-flex align-items-center gap-2 pt-2 border-top">
                                <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                    <!-- Primary Swatch -->
                                    <div class="rounded-circle shadow-sm border" style="width: 22px; height: 22px; background: {{ $tpl['primary'] }};" title="Primary: {{ $tpl['primary'] }}"></div>
                                    <!-- Accent Swatch -->
                                    <div class="rounded-circle shadow-sm border" style="width: 22px; height: 22px; background: {{ $tpl['accent'] }};" title="Accent: {{ $tpl['accent'] }}"></div>
                                    <!-- Hover Swatch -->
                                    <div class="rounded-circle shadow-sm border" style="width: 22px; height: 22px; background: {{ $tpl['accent_hover'] }};" title="Accent Hover: {{ $tpl['accent_hover'] }}"></div>
                                    <!-- Secondary Surface Swatch -->
                                    <div class="rounded-circle shadow-sm border" style="width: 22px; height: 22px; background: {{ $tpl['secondary'] }};" title="Secondary: {{ $tpl['secondary'] }}"></div>
                                </div>
                                <span class="font-mono text-muted small" style="font-size: 0.7rem;">{{ $tpl['accent'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row g-4">
            <!-- SECTION 2: BRAND ASSETS (NAME, LOGO & FAVICON) -->
            <div class="col-lg-6">
                <div class="lms-card p-4 h-100 shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-brush text-primary me-2"></i>Brand Identity &amp; System Graphics
                    </h5>

                    <!-- System Brand Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Platform Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="settings[brand_name]" class="form-control" value="{{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}" required placeholder="AllThingsCambridge">
                        <div class="form-text small">Broadcast to header logos, automated emails, certificate seals, and page titles.</div>
                    </div>

                    <!-- System Global Currency (USD / EUR / GBP) -->
                    <div class="mb-3 p-3 rounded-3 border" style="background: var(--lms-surface-card); border-color: rgba(2, 132, 199, 0.25) !important;">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-cash-coin text-primary me-1.5"></i>Global Platform Currency</span>
                            <span class="badge bg-primary-subtle text-primary font-mono small">
                                Current: {{ $site_settings['system_currency'] ?? 'USD' }}
                            </span>
                        </label>
                        <div class="text-secondary small mb-2" style="font-size: 0.78rem;">
                            Set the primary currency used for Cambridge level passes, course checkout, and invoice totals (select USD or EUR).
                        </div>
                        <select name="settings[system_currency]" class="form-select fw-semibold" style="border-radius: 10px;">
                            <option value="USD" {{ ($site_settings['system_currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>
                                USD ($) — United States Dollar (Recommended)
                            </option>
                            <option value="EUR" {{ ($site_settings['system_currency'] ?? '') === 'EUR' ? 'selected' : '' }}>
                                EUR (€) — Euro (European Union)
                            </option>
                            <option value="GBP" {{ ($site_settings['system_currency'] ?? '') === 'GBP' ? 'selected' : '' }}>
                                GBP (£) — British Pound Sterling (Cambridge UK)
                            </option>
                        </select>
                    </div>

                    <!-- System Administration Communication & Contacts -->
                    <div class="mb-3 p-3 rounded-3 border" style="background: var(--lms-surface-card); border-color: rgba(16, 185, 129, 0.25) !important;">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center mb-1">
                            <span><i class="bi bi-headset text-success me-1.5"></i>Administrator Contact &amp; Support Info</span>
                            <span class="badge bg-success-subtle text-success small font-mono">Footer Broadcast</span>
                        </label>
                        <div class="text-secondary small mb-3" style="font-size: 0.78rem;">
                            Dynamic contact information displayed across public footers, system notifications, and inquiries.
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Admin Support Email</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="settings[admin_contact_email]" class="form-control" 
                                           value="{{ $site_settings['admin_contact_email'] ?? 'admin@enterpriselms.com' }}" 
                                           placeholder="admin@enterpriselms.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Admin Phone / WhatsApp</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="settings[admin_contact_phone]" class="form-control" 
                                           value="{{ $site_settings['admin_contact_phone'] ?? '+44 (0) 20 8123 4567' }}" 
                                           placeholder="+44 20 8123 4567" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Logo (File Upload & URL) -->
                    <div class="mb-3 p-3 rounded-3" style="background: var(--lms-bg-secondary); border: 1px dashed var(--lms-border);">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            <span>System Logo</span>
                            @if(!empty($site_settings['brand_logo_url']))
                                <span class="badge bg-success bg-opacity-10 text-success">Active Logo Configured</span>
                            @endif
                        </label>

                        @if(!empty($site_settings['brand_logo_url']) && !str_contains($site_settings['brand_logo_url'], 'example.com'))
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2 bg-white border">
                                <img src="{{ $site_settings['brand_logo_url'] }}" alt="Logo Preview" height="36" class="rounded" onerror="this.onerror=null; this.src='{{ asset('images/site_logo.png') }}';">
                                <div class="small text-truncate text-muted font-mono" style="font-size: 0.75rem;">
                                    {{ $site_settings['brand_logo_url'] }}
                                </div>
                            </div>
                        @elseif(file_exists(public_path('images/site_logo.png')))
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2 bg-white border">
                                <img src="{{ asset('images/site_logo.png') }}" alt="Default Logo Preview" height="36" class="rounded">
                                <div class="small text-truncate text-muted font-mono" style="font-size: 0.75rem;">
                                    {{ asset('images/site_logo.png') }} (Default System Asset)
                                </div>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label small text-secondary">Upload New Logo Image (PNG, SVG, JPG, WebP)</label>
                            <input type="file" name="logo_file" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <div>
                            <label class="form-label small text-secondary">Or Enter Direct Logo Image URL</label>
                            <input type="text" name="settings[brand_logo_url]" class="form-control form-control-sm font-mono" value="{{ $site_settings['brand_logo_url'] ?? '' }}" placeholder="https://your-domain.com/logo.png">
                        </div>
                    </div>

                    <!-- System Favicon (File Upload & URL) -->
                    <div class="mb-3 p-3 rounded-3" style="background: var(--lms-bg-secondary); border: 1px dashed var(--lms-border);">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            <span>Browser Favicon</span>
                            @if(!empty($site_settings['brand_favicon_url']))
                                <span class="badge bg-success bg-opacity-10 text-success">Active Favicon Configured</span>
                            @endif
                        </label>

                        @if(!empty($site_settings['brand_favicon_url']))
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2 bg-white border">
                                <img src="{{ $site_settings['brand_favicon_url'] }}" alt="Favicon Preview" width="24" height="24" class="rounded">
                                <div class="small text-truncate text-muted font-mono" style="font-size: 0.75rem;">
                                    {{ $site_settings['brand_favicon_url'] }}
                                </div>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label small text-secondary">Upload New Favicon (.ico, .png, .svg)</label>
                            <input type="file" name="favicon_file" class="form-control form-control-sm" accept=".ico,image/png,image/svg+xml,image/x-icon">
                        </div>

                        <div>
                            <label class="form-label small text-secondary">Or Enter Direct Favicon URL</label>
                            <input type="text" name="settings[brand_favicon_url]" class="form-control form-control-sm font-mono" value="{{ $site_settings['brand_favicon_url'] ?? '' }}" placeholder="https://your-domain.com/favicon.ico">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: FINE-TUNED COLOR TOKENS & SECURITY CONTROLS -->
            <div class="col-lg-6">
                <div class="lms-card p-4 h-100 shadow-sm d-flex flex-column">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-sliders2 text-primary me-2"></i>Theme Color Tokens &amp; Security Controls
                    </h5>

                    <!-- Primary & Accent Colors -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Primary Theme Token</label>
                            <div class="input-group">
                                <input type="color" id="primaryPicker" class="form-control form-control-color" :value="primaryColor" @input="primaryColor = $event.target.value; document.getElementById('primaryHex').value = $event.target.value">
                                <input type="text" id="primaryHex" name="settings[brand_primary_color]" class="form-control font-mono" :value="primaryColor" @input="primaryColor = $event.target.value; document.getElementById('primaryPicker').value = $event.target.value">
                            </div>
                            <div class="form-text small" style="font-size: 0.7rem;">Headers, dark sections, cards</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Interactive Accent Token</label>
                            <div class="input-group">
                                <input type="color" id="accentPicker" class="form-control form-control-color" :value="accentColor" @input="accentColor = $event.target.value; document.getElementById('accentHex').value = $event.target.value">
                                <input type="text" id="accentHex" name="settings[brand_accent_color]" class="form-control font-mono" :value="accentColor" @input="accentColor = $event.target.value; document.getElementById('accentPicker').value = $event.target.value">
                            </div>
                            <div class="form-text small" style="font-size: 0.7rem;">Buttons, highlights, active tabs</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Secondary Surface Tint</label>
                            <div class="input-group">
                                <input type="color" id="secondaryPicker" class="form-control form-control-color" :value="secondaryColor" @input="secondaryColor = $event.target.value; document.getElementById('secondaryHex').value = $event.target.value">
                                <input type="text" id="secondaryHex" name="settings[brand_secondary_color]" class="form-control font-mono" :value="secondaryColor" @input="secondaryColor = $event.target.value; document.getElementById('secondaryPicker').value = $event.target.value">
                            </div>
                            <div class="form-text small" style="font-size: 0.7rem;">Subtle background sections</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Interactive Hover Token</label>
                            <input type="text" name="settings[brand_accent_hover_color]" class="form-control font-mono" value="{{ $site_settings['brand_accent_hover_color'] ?? '#5E32A6' }}">
                            <div class="form-text small" style="font-size: 0.7rem;">Darkened hover state for buttons</div>
                        </div>
                    </div>

                    <hr class="my-3 opacity-25">

                    <!-- Enterprise Session Security -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-lock-fill text-warning"></i>
                            <span>Enterprise Session Inactivity Timeout (Minutes)</span>
                        </label>
                        <select name="settings[session_timeout_minutes]" class="form-select">
                            <option value="15" {{ ($site_settings['session_timeout_minutes'] ?? '30') == '15' ? 'selected' : '' }}>15 Minutes (Strict SOC-2 / Healthcare / Banking)</option>
                            <option value="30" {{ ($site_settings['session_timeout_minutes'] ?? '30') == '30' ? 'selected' : '' }}>30 Minutes (Recommended Corporate Standard)</option>
                            <option value="60" {{ ($site_settings['session_timeout_minutes'] ?? '30') == '60' ? 'selected' : '' }}>60 Minutes (Academic &amp; Student Portals)</option>
                            <option value="120" {{ ($site_settings['session_timeout_minutes'] ?? '30') == '120' ? 'selected' : '' }}>120 Minutes (Extended Dev/Test Session)</option>
                        </select>
                        <div class="form-text small">Automatically signs idle users out and writes an immutable audit log timestamp.</div>
                    </div>

                    <!-- Live Theme Preview Pill -->
                    <div class="mt-auto p-3 rounded-3 border" style="background: var(--lms-surface-card);">
                        <div class="small fw-semibold text-muted text-uppercase mb-2" style="font-size: 0.68rem;">Live Button &amp; Accent Preview</div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button type="button" class="btn text-white rounded-pill px-3 py-1.5 fw-semibold small shadow-sm" :style="'background: ' + accentColor + '; border: none;'">
                                Primary Action
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold" :style="'border: 1.5px solid ' + accentColor + '; color: ' + accentColor + ';'">
                                Outline Button
                            </button>
                            <span class="badge rounded-pill px-2.5 py-1 text-white" :style="'background: ' + accentColor + ';'">
                                Active Badge
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: GLOBAL NAVIGATION LABELS -->
            <div class="col-12">
                <div class="lms-card p-4 shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-link-45deg text-primary me-2"></i>Global Navigation Labels
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Courses Navigation Label</label>
                            <input type="text" name="settings[nav_courses]" class="form-control" value="{{ $site_settings['nav_courses'] ?? 'Course Catalog' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Compliance Hub Navigation Label</label>
                            <input type="text" name="settings[nav_compliance]" class="form-control" value="{{ $site_settings['nav_compliance'] ?? 'Corporate Compliance' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Pricing &amp; Cohorts Navigation Label</label>
                            <input type="text" name="settings[nav_pricing]" class="form-control" value="{{ $site_settings['nav_pricing'] ?? 'Pricing & Cohorts' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">My Learning Navigation Label</label>
                            <input type="text" name="settings[nav_my_learning]" class="form-control" value="{{ $site_settings['nav_my_learning'] ?? 'My Learning' }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4B: ANNOUNCEMENT & COUNTDOWN TOPBAR BANNER -->
            <div class="col-12" x-data="{
                topbarEnabled: '{{ $site_settings['topbar_enabled'] ?? '1' }}',
                badgeText: '{{ $site_settings['topbar_badge_text'] ?? 'SPECIAL EVENT' }}',
                promoText: '{{ $site_settings['topbar_promo_text'] ?? 'Get 50% Off Annual Enterprise Compliance & Accredited Certifications' }}',
                btnText: '{{ $site_settings['topbar_btn_text'] ?? 'Claim Offer' }}',
                btnUrl: '{{ $site_settings['topbar_btn_url'] ?? '/p/pricing' }}',
                days: '{{ $site_settings['topbar_countdown_days'] ?? '04' }}',
                hours: '{{ $site_settings['topbar_countdown_hours'] ?? '18' }}',
                minutes: '{{ $site_settings['topbar_countdown_minutes'] ?? '35' }}',
                seconds: '{{ $site_settings['topbar_countdown_seconds'] ?? '30' }}'
            }">
                <div class="lms-card p-4 shadow-sm border">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="bi bi-megaphone-fill text-warning me-2"></i>Announcement &amp; Countdown Topbar Banner
                            </h5>
                            <div class="text-secondary small">
                                Configure the global countdown bar, promotional offer, badge, and CTA link displayed across all public and marketing pages.
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5">
                            <input type="hidden" name="settings[topbar_enabled]" :value="topbarEnabled">
                            <input class="form-check-input" type="checkbox" role="switch" id="topbarSwitch" 
                                   :checked="topbarEnabled === '1'"
                                   @change="topbarEnabled = $event.target.checked ? '1' : '0'">
                            <label class="form-check-label fs-6 fw-bold" for="topbarSwitch" x-text="topbarEnabled === '1' ? 'Banner Active' : 'Banner Disabled'"></label>
                        </div>
                    </div>

                    <!-- Live Visual Preview of Topbar Banner -->
                    <div class="mb-4 p-3 rounded-4" style="background: linear-gradient(90deg, #120A2A 0%, #1E1242 50%, #2A175C 100%); color: #FFF; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex justify-content-between align-items-center small text-white-50 mb-2 font-mono" style="font-size: 0.7rem;">
                            <span><i class="bi bi-eye-fill me-1"></i> LIVE BANNER PREVIEW</span>
                            <span :class="topbarEnabled === '1' ? 'text-success' : 'text-danger'" x-text="topbarEnabled === '1' ? '● LIVE ON SITE' : '○ HIDDEN'"></span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 rounded-3" style="background: rgba(0,0,0,0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="countdown-wrap d-flex align-items-center gap-1 font-mono fw-bold" style="font-size: 0.78rem;">
                                    <span class="px-2 py-1 rounded text-white" style="background: rgba(255,255,255,0.12);" x-text="days + 'd'">04d</span>
                                    <span>:</span>
                                    <span class="px-2 py-1 rounded text-white" style="background: rgba(255,255,255,0.12);" x-text="hours + 'h'">18h</span>
                                    <span>:</span>
                                    <span class="px-2 py-1 rounded text-white" style="background: rgba(255,255,255,0.12);" x-text="minutes + 'm'">35m</span>
                                    <span>:</span>
                                    <span class="px-2 py-1 rounded text-white" style="background: rgba(255,255,255,0.12);" x-text="seconds + 's'">30s</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 small">
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill">
                                        <i class="bi bi-fire me-1"></i> <span x-text="badgeText">SPECIAL EVENT</span>
                                    </span>
                                    <span class="fw-semibold text-truncate" style="max-width: 480px;" x-text="promoText">
                                        Get 50% Off Annual Enterprise Compliance & Accredited Certifications
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge px-3 py-1.5 rounded-pill fw-bold text-dark" style="background: #FF9800;">
                                    <span x-text="btnText">Claim Offer</span> <i class="bi bi-arrow-right ms-1"></i>
                                </span>
                                <span class="text-white-50"><i class="bi bi-x-lg"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration Fields Grid -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Pill Badge Label</label>
                            <input type="text" name="settings[topbar_badge_text]" class="form-control" x-model="badgeText" placeholder="SPECIAL EVENT">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Announcement / Offer Headline</label>
                            <input type="text" name="settings[topbar_promo_text]" class="form-control" x-model="promoText" placeholder="Get 50% Off Annual Enterprise Compliance & Accredited Certifications">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">CTA Button Label</label>
                            <input type="text" name="settings[topbar_btn_text]" class="form-control" x-model="btnText" placeholder="Claim Offer">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">CTA Button Link Destination (URL)</label>
                            <input type="text" name="settings[topbar_btn_url]" class="form-control font-mono" x-model="btnUrl" placeholder="/p/pricing">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Countdown Clock Duration (Days / Hours / Mins / Secs)</label>
                            <div class="input-group font-mono">
                                <input type="number" name="settings[topbar_countdown_days]" class="form-control text-center" x-model="days" min="0" max="99" placeholder="04">
                                <span class="input-group-text">Days</span>
                                <input type="number" name="settings[topbar_countdown_hours]" class="form-control text-center" x-model="hours" min="0" max="23" placeholder="18">
                                <span class="input-group-text">Hrs</span>
                                <input type="number" name="settings[topbar_countdown_minutes]" class="form-control text-center" x-model="minutes" min="0" max="59" placeholder="35">
                                <span class="input-group-text">Min</span>
                                <input type="number" name="settings[topbar_countdown_seconds]" class="form-control text-center" x-model="seconds" min="0" max="59" placeholder="30">
                                <span class="input-group-text">Sec</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: DYNAMIC 3-SLIDE HERO MANAGEMENT SYSTEM -->
            <div class="col-12" x-data="{ activeSlideTab: 1 }">
                <div class="lms-card p-4 shadow-sm border rounded-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4 gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--lms-text-primary);">
                                <i class="bi bi-images text-primary me-2"></i>Homepage Hero Slider (3 Master Dynamic Slides)
                            </h5>
                            <span class="text-secondary small">
                                Configure copy, image assets, overlays, and CTAs for all 3 brand-consistent hero slides.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                                    :class="activeSlideTab === 1 ? 'btn-primary shadow-xs' : 'btn-outline-secondary'"
                                    @click="activeSlideTab = 1">
                                <i class="bi bi-1-circle me-1"></i> Slide 1: Primary Value
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                                    :class="activeSlideTab === 2 ? 'btn-primary shadow-xs' : 'btn-outline-secondary'"
                                    @click="activeSlideTab = 2">
                                <i class="bi bi-2-circle me-1"></i> Slide 2: STEM Lab
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                                    :class="activeSlideTab === 3 ? 'btn-primary shadow-xs' : 'btn-outline-secondary'"
                                    @click="activeSlideTab = 3">
                                <i class="bi bi-3-circle me-1"></i> Slide 3: Mentorship
                            </button>
                        </div>
                    </div>

                    {{-- SLIDE 1 --}}
                    <div x-show="activeSlideTab === 1" x-transition class="p-3 rounded-4 border bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fw-bold">SLIDE 1: PRIMARY VALUE PROPOSITION</span>
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="settings[hero_slide_1_enabled]" value="0">
                                <input class="form-check-input" type="checkbox" name="settings[hero_slide_1_enabled]" value="1" 
                                       id="slide1Enabled" {{ ($site_settings['hero_slide_1_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="slide1Enabled">Enable Slide 1</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Image Preview & Upload -->
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Desktop Hero Image Asset</label>
                                <div class="rounded-3 overflow-hidden border mb-2 position-relative shadow-xs" style="height: 180px; background: #0F1321;">
                                    <img src="{{ !empty($site_settings['hero_slide_1_image']) ? $site_settings['hero_slide_1_image'] : asset('images/hero/hero-slide-1.webp') }}" 
                                         alt="Slide 1 Preview" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Upload New Image (WebP, JPG, PNG)</label>
                                    <input type="file" name="hero_slide_1_file" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div>
                                    <label class="form-label small text-muted">Or Image Absolute / CDN URL</label>
                                    <input type="text" name="settings[hero_slide_1_image]" class="form-control form-control-sm font-mono" 
                                           value="{{ $site_settings['hero_slide_1_image'] ?? asset('images/hero/hero-slide-1.webp') }}">
                                </div>
                            </div>

                            <!-- Content Inputs -->
                            <div class="col-md-7">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Eyebrow / Badge Text</label>
                                        <input type="text" name="settings[hero_slide_1_badge]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_1_badge'] ?? 'CAMBRIDGE INTERNATIONAL PLATFORM' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Overlay Opacity (0.2 - 0.9)</label>
                                        <input type="number" step="0.05" min="0" max="1" name="settings[hero_slide_1_overlay]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_1_overlay'] ?? '0.55' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Main Heading</label>
                                        <input type="text" name="settings[hero_slide_1_heading]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_1_heading'] ?? 'Master Cambridge Curriculum with Verified Solutions.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Highlighted Heading Phrase (Gradient)</label>
                                        <input type="text" name="settings[hero_slide_1_highlight]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_1_highlight'] ?? 'Diagnostic Past Papers & Automated Trackers.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Supporting Description</label>
                                        <textarea name="settings[hero_slide_1_desc]" class="form-control form-control-sm" rows="2">{{ $site_settings['hero_slide_1_desc'] ?? 'The premier academic hub featuring 5 isolated Cambridge levels, LaTeX formula rendering, official mark scheme breakdowns, and real-time student mastery analytics.' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Primary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_1_cta1_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_1_cta1_text'] ?? 'Explore Past Papers' }}">
                                            <input type="text" name="settings[hero_slide_1_cta1_url]" class="form-control font-mono" placeholder="/courses" 
                                                   value="{{ $site_settings['hero_slide_1_cta1_url'] ?? route('courses.index') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Secondary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_1_cta2_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_1_cta2_text'] ?? 'Try Esther Math Tracker' }}">
                                            <input type="text" name="settings[hero_slide_1_cta2_url]" class="form-control font-mono" placeholder="#esther-tracker" 
                                                   value="{{ $site_settings['hero_slide_1_cta2_url'] ?? '#esther-tracker' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SLIDE 2 --}}
                    <div x-show="activeSlideTab === 2" x-transition class="p-3 rounded-4 border bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success px-3 py-1.5 rounded-pill fw-bold">SLIDE 2: STEM &amp; ADVANCED MATHEMATICS</span>
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="settings[hero_slide_2_enabled]" value="0">
                                <input class="form-check-input" type="checkbox" name="settings[hero_slide_2_enabled]" value="1" 
                                       id="slide2Enabled" {{ ($site_settings['hero_slide_2_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="slide2Enabled">Enable Slide 2</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Image Preview & Upload -->
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Desktop Hero Image Asset</label>
                                <div class="rounded-3 overflow-hidden border mb-2 position-relative shadow-xs" style="height: 180px; background: #0F1321;">
                                    <img src="{{ !empty($site_settings['hero_slide_2_image']) ? $site_settings['hero_slide_2_image'] : asset('images/hero/hero-slide-2.webp') }}" 
                                         alt="Slide 2 Preview" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Upload New Image (WebP, JPG, PNG)</label>
                                    <input type="file" name="hero_slide_2_file" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div>
                                    <label class="form-label small text-muted">Or Image Absolute / CDN URL</label>
                                    <input type="text" name="settings[hero_slide_2_image]" class="form-control form-control-sm font-mono" 
                                           value="{{ $site_settings['hero_slide_2_image'] ?? asset('images/hero/hero-slide-2.webp') }}">
                                </div>
                            </div>

                            <!-- Content Inputs -->
                            <div class="col-md-7">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Eyebrow / Badge Text</label>
                                        <input type="text" name="settings[hero_slide_2_badge]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_2_badge'] ?? 'STEM & SCIENTIFIC RIGOR' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Overlay Opacity (0.2 - 0.9)</label>
                                        <input type="number" step="0.05" min="0" max="1" name="settings[hero_slide_2_overlay]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_2_overlay'] ?? '0.50' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Main Heading</label>
                                        <input type="text" name="settings[hero_slide_2_heading]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_2_heading'] ?? 'Accelerate Calculus, Physics & Chemistry Mastery.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Highlighted Heading Phrase (Gradient)</label>
                                        <input type="text" name="settings[hero_slide_2_highlight]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_2_highlight'] ?? 'Step-by-Step Derivations & LaTeX Formulas.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Supporting Description</label>
                                        <textarea name="settings[hero_slide_2_desc]" class="form-control form-control-sm" rows="2">{{ $site_settings['hero_slide_2_desc'] ?? 'Dissect complex exam permutations with clear KaTeX formula derivations, topical question packs, and interactive Cambridge examiner guidance.' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Primary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_2_cta1_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_2_cta1_text'] ?? 'Open Esther Tracker' }}">
                                            <input type="text" name="settings[hero_slide_2_cta1_url]" class="form-control font-mono" placeholder="#esther-tracker" 
                                                   value="{{ $site_settings['hero_slide_2_cta1_url'] ?? '#esther-tracker' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Secondary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_2_cta2_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_2_cta2_text'] ?? 'Print Diagnostic Report' }}">
                                            <input type="text" name="settings[hero_slide_2_cta2_url]" class="form-control font-mono" placeholder="/scores/report" 
                                                   value="{{ $site_settings['hero_slide_2_cta2_url'] ?? route('scores.report') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SLIDE 3 --}}
                    <div x-show="activeSlideTab === 3" x-transition class="p-3 rounded-4 border bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-info text-dark px-3 py-1.5 rounded-pill fw-bold">SLIDE 3: ACCREDITED EXAMINERS &amp; FACULTY</span>
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="settings[hero_slide_3_enabled]" value="0">
                                <input class="form-check-input" type="checkbox" name="settings[hero_slide_3_enabled]" value="1" 
                                       id="slide3Enabled" {{ ($site_settings['hero_slide_3_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="slide3Enabled">Enable Slide 3</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Image Preview & Upload -->
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Desktop Hero Image Asset</label>
                                <div class="rounded-3 overflow-hidden border mb-2 position-relative shadow-xs" style="height: 180px; background: #0F1321;">
                                    <img src="{{ !empty($site_settings['hero_slide_3_image']) ? $site_settings['hero_slide_3_image'] : asset('images/hero/hero-slide-3.webp') }}" 
                                         alt="Slide 3 Preview" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted">Upload New Image (WebP, JPG, PNG)</label>
                                    <input type="file" name="hero_slide_3_file" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div>
                                    <label class="form-label small text-muted">Or Image Absolute / CDN URL</label>
                                    <input type="text" name="settings[hero_slide_3_image]" class="form-control form-control-sm font-mono" 
                                           value="{{ $site_settings['hero_slide_3_image'] ?? asset('images/hero/hero-slide-3.webp') }}">
                                </div>
                            </div>

                            <!-- Content Inputs -->
                            <div class="col-md-7">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Eyebrow / Badge Text</label>
                                        <input type="text" name="settings[hero_slide_3_badge]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_3_badge'] ?? 'VERIFIED EXAMINER NETWORK' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Overlay Opacity (0.2 - 0.9)</label>
                                        <input type="number" step="0.05" min="0" max="1" name="settings[hero_slide_3_overlay]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_3_overlay'] ?? '0.50' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Main Heading</label>
                                        <input type="text" name="settings[hero_slide_3_heading]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_3_heading'] ?? 'Learn Directly from Accredited Cambridge Mentors.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Highlighted Heading Phrase (Gradient)</label>
                                        <input type="text" name="settings[hero_slide_3_highlight]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['hero_slide_3_highlight'] ?? '1-on-1 Feedback, Diagnostic Clinic & Global Trust.' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Supporting Description</label>
                                        <textarea name="settings[hero_slide_3_desc]" class="form-control form-control-sm" rows="2">{{ $site_settings['hero_slide_3_desc'] ?? 'Connect with experienced syllabus moderators, review individualized assessment profiles, and prepare with confidence for upcoming May/June exam series.' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Primary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_3_cta1_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_3_cta1_text'] ?? 'Meet Cambridge Mentors' }}">
                                            <input type="text" name="settings[hero_slide_3_cta1_url]" class="form-control font-mono" placeholder="/mentors" 
                                                   value="{{ $site_settings['hero_slide_3_cta1_url'] ?? route('mentors.index') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Secondary CTA Text &amp; Destination</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="settings[hero_slide_3_cta2_text]" class="form-control" placeholder="Button Text" 
                                                   value="{{ $site_settings['hero_slide_3_cta2_text'] ?? 'View Subscription Plans' }}">
                                            <input type="text" name="settings[hero_slide_3_cta2_url]" class="form-control font-mono" placeholder="/p/pricing" 
                                                   value="{{ $site_settings['hero_slide_3_cta2_url'] ?? route('cms.show', ['slug' => 'pricing']) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SECTION 6: SOCIAL MEDIA CHANNELS -->
            <div class="col-12">
                <div class="lms-card p-4 shadow-sm border">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-share-fill text-primary me-2"></i>Official Social Media Handles &amp; Profiles
                    </h5>
                    <div class="text-secondary small mb-3">
                        Configure official social channel links displayed across the footer and contact sections of all public pages.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-semibold"><i class="bi bi-linkedin text-primary me-1"></i> LinkedIn Profile URL</label>
                            <input type="url" name="settings[social_linkedin]" class="form-control" value="{{ $site_settings['social_linkedin'] ?? 'https://linkedin.com/company/allthingscambridge' }}" placeholder="https://linkedin.com/company/...">
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-semibold"><i class="bi bi-twitter-x text-dark me-1"></i> Twitter / X Profile URL</label>
                            <input type="url" name="settings[social_twitter]" class="form-control" value="{{ $site_settings['social_twitter'] ?? 'https://x.com/allthingscambridge' }}" placeholder="https://x.com/...">
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label small fw-semibold"><i class="bi bi-facebook text-primary me-1"></i> Facebook Page URL</label>
                            <input type="url" name="settings[social_facebook]" class="form-control" value="{{ $site_settings['social_facebook'] ?? 'https://facebook.com/allthingscambridge' }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <label class="form-label small fw-semibold"><i class="bi bi-youtube text-danger me-1"></i> YouTube Channel URL</label>
                            <input type="url" name="settings[social_youtube]" class="form-control" value="{{ $site_settings['social_youtube'] ?? 'https://youtube.com/@allthingscambridge' }}" placeholder="https://youtube.com/@...">
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <label class="form-label small fw-semibold"><i class="bi bi-instagram text-danger me-1"></i> Instagram Profile URL</label>
                            <input type="url" name="settings[social_instagram]" class="form-control" value="{{ $site_settings['social_instagram'] ?? 'https://instagram.com/allthingscambridge' }}" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: OFFICIAL CERTIFICATE & ACCREDITATION SETTINGS -->
            <div class="col-12">
                <div class="lms-card p-4 shadow-sm border rounded-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4 gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--lms-text-primary);">
                                <i class="bi bi-award-fill text-primary me-2"></i>Official Certificate &amp; Accreditation Configuration (Landscape Template)
                            </h5>
                            <span class="text-secondary small">
                                Configure the official landscape diploma layout, authorized verifier signature, logo branding, dynamic certificate numbers, and test passing thresholds.
                            </span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small">
                            <i class="bi bi-patch-check-fill me-1"></i> A4 Landscape &bull; Dynamic QR
                        </span>
                    </div>

                    <div class="row g-4">
                        <!-- Left Column: Signature Asset & Signatory Meta -->
                        <div class="col-lg-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <label class="form-label small fw-bold d-flex justify-content-between align-items-center mb-2">
                                    <span>Authorized Verifier Signature</span>
                                    @php
                                        $sigUrl = $site_settings['certificate_signature_url'] ?? (file_exists(public_path('images/certificate_signature.png')) ? asset('images/certificate_signature.png') : null);
                                    @endphp
                                    @if($sigUrl)
                                        <span class="badge bg-success bg-opacity-10 text-success">Active Signature Configured</span>
                                    @endif
                                </label>

                                @if($sigUrl)
                                    <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded-2 bg-white border">
                                        <img src="{{ $sigUrl }}" alt="Signature Preview" height="42" class="rounded object-fit-contain">
                                        <div class="small text-truncate text-muted font-mono" style="font-size: 0.75rem;">
                                            {{ $sigUrl }}
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label small text-secondary">Upload New Signature File (PNG with Transparent Background Recommended, SVG, JPG)</label>
                                    <input type="file" name="signature_file" class="form-control form-control-sm" accept="image/*">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-secondary">Or Signature Image URL</label>
                                    <input type="text" name="settings[certificate_signature_url]" class="form-control form-control-sm font-mono" 
                                           value="{{ $site_settings['certificate_signature_url'] ?? '' }}" placeholder="/uploads/branding/signature_... or https://...">
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Signatory Title</label>
                                        <input type="text" name="settings[certificate_signatory_title]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['certificate_signatory_title'] ?? 'Head of E-Learning' }}" placeholder="Head of E-Learning">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Signatory Organization</label>
                                        <input type="text" name="settings[certificate_signatory_sub]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['certificate_signatory_sub'] ?? ($site_settings['brand_name'] ?? 'AllThingsCambridge') }}" placeholder="AllThingsCambridge">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Certificate Dynamic Identifiers & Thresholds -->
                        <div class="col-lg-6">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <label class="form-label small fw-bold mb-2">Dynamic Identifiers &amp; Compliance Thresholds</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Certificate ID Prefix</label>
                                        <input type="text" name="settings[certificate_prefix]" class="form-control form-control-sm font-mono text-uppercase" 
                                               value="{{ $site_settings['certificate_prefix'] ?? 'ATC' }}" placeholder="ATC" maxlength="10">
                                        <span class="text-muted" style="font-size: 0.72rem;">Format: <code>PREFIX-YYYY-MMDD-SERIAL</code></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Final Test Passing Score (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" min="50" max="100" name="settings[certificate_min_score]" class="form-control font-mono fw-bold" 
                                                   value="{{ $site_settings['certificate_min_score'] ?? '80' }}">
                                            <span class="input-group-text">% Required</span>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.72rem;">Enforces 80% minimum score before certificate access</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Top-Right Tagline 1</label>
                                        <input type="text" name="settings[certificate_tagline_1]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['certificate_tagline_1'] ?? 'Better Learning' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Top-Right Tagline 2</label>
                                        <input type="text" name="settings[certificate_tagline_2]" class="form-control form-control-sm" 
                                               value="{{ $site_settings['certificate_tagline_2'] ?? 'Brighter Future' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Official Website Display URL (Footer Left)</label>
                                        <input type="text" name="settings[certificate_website_url]" class="form-control form-control-sm font-mono" 
                                               value="{{ $site_settings['certificate_website_url'] ?? 'www.allthingscambridge.com' }}" placeholder="www.allthingscambridge.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 7: FOOTER & COMPLIANCE STATEMENTS -->
            <div class="col-12">
                <div class="lms-card p-4 shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-shield-check text-primary me-2"></i>Legal, Compliance &amp; Footer Directives
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Footer Copyright Line</label>
                            <input type="text" name="settings[footer_copyright]" class="form-control" value="{{ $site_settings['footer_copyright'] ?? 'All rights reserved. WCAG 2.1 AA Compliant.' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Auditor Verification Support Label</label>
                            <input type="text" name="settings[footer_compliance_label]" class="form-control" value="{{ $site_settings['footer_compliance_label'] ?? 'Immutable SHA-256 Audit Trails Active' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end pt-2">
                <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm rounded-pill fw-bold">
                    <i class="bi bi-save2 me-1.5"></i> Save &amp; Broadcast System Configuration
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
