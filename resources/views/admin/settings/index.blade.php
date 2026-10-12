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
                        <input type="text" name="settings[brand_name]" class="form-control" value="{{ $site_settings['brand_name'] ?? 'Enterprise Academy' }}" required placeholder="Enterprise Academy">
                        <div class="form-text small">Broadcast to header logos, automated emails, certificate seals, and page titles.</div>
                    </div>

                    <!-- System Logo (File Upload & URL) -->
                    <div class="mb-3 p-3 rounded-3" style="background: var(--lms-bg-secondary); border: 1px dashed var(--lms-border);">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            <span>System Logo</span>
                            @if(!empty($site_settings['brand_logo_url']))
                                <span class="badge bg-success bg-opacity-10 text-success">Active Logo Configured</span>
                            @endif
                        </label>

                        @if(!empty($site_settings['brand_logo_url']))
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2 bg-white border">
                                <img src="{{ $site_settings['brand_logo_url'] }}" alt="Logo Preview" height="36" class="rounded">
                                <div class="small text-truncate text-muted font-mono" style="font-size: 0.75rem;">
                                    {{ $site_settings['brand_logo_url'] }}
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

            <!-- SECTION 5: HOMEPAGE HERO SECTION DIRECTIVES -->
            <div class="col-12">
                <div class="lms-card p-4 shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-layout-text-window-reverse text-primary me-2"></i>Homepage Hero Section Directives
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Hero Badge Label</label>
                            <input type="text" name="settings[hero_badge]" class="form-control" value="{{ $site_settings['hero_badge'] ?? 'Dual-Audience Enterprise LMS' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Hero Headline</label>
                            <input type="text" name="settings[hero_headline]" class="form-control" value="{{ $site_settings['hero_headline'] ?? 'Next-Gen Workforce Compliance & Academic Excellence' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Hero Subheadline Lead Text</label>
                            <textarea name="settings[hero_subheadline]" class="form-control" rows="2">{{ $site_settings['hero_subheadline'] ?? 'Unified platform for corporate recertification audits and public accredited student cohorts. Zero friction, fully compliant.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: FOOTER & COMPLIANCE STATEMENTS -->
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
