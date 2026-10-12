@php
    $c = $section->content_json ?? [];
    $metrics = $c['metrics'] ?? [
        ['value' => '99.9%', 'label' => 'Audit SLA Compliance', 'icon' => 'bi-shield-check', 'bg' => 'var(--tj-bg-mint)', 'color' => '#059669'],
        ['value' => '45,000+', 'label' => 'Certified Professionals', 'icon' => 'bi-people-fill', 'bg' => 'var(--tj-bg-purple)', 'color' => '#723EC3'],
        ['value' => '100%', 'label' => 'SCORM 1.2 / 2004 Support', 'icon' => 'bi-box-seam-fill', 'bg' => 'var(--tj-bg-cyan)', 'color' => '#0284C7'],
        ['value' => '0.00ms', 'label' => 'Anti-Cheat Proctoring Sync', 'icon' => 'bi-lightning-charge-fill', 'bg' => 'var(--tj-bg-peach)', 'color' => '#FF4D00'],
    ];
@endphp

<!-- Edunex Client & Accreditation Infinite Marquee -->
<div class="py-4 border-bottom" style="background: var(--lms-bg-secondary);">
    <div class="container text-center mb-3">
        <p class="small text-muted fw-bold text-uppercase mb-0" style="letter-spacing: 1.5px;">
            Trusted by over <span class="text-primary fw-bolder">2,000+</span> enterprises, regulatory bodies &amp; accredited institutions
        </p>
    </div>
    <div class="tj-marquee-wrapper">
        <div class="tj-marquee-content">
            <div class="brand-chip"><i class="bi bi-shield-check text-success"></i> ISO/IEC 27001 Certified</div>
            <div class="brand-chip"><i class="bi bi-award text-warning"></i> OSHA Workplace Safety</div>
            <div class="brand-chip"><i class="bi bi-box-seam text-info"></i> SCORM &amp; xAPI Standard</div>
            <div class="brand-chip"><i class="bi bi-cloud text-primary"></i> AWS Partner Network</div>
            <div class="brand-chip"><i class="bi bi-google text-danger"></i> Google Cloud Education</div>
            <div class="brand-chip"><i class="bi bi-cpu text-secondary"></i> IEEE Learning Standards</div>
            <div class="brand-chip"><i class="bi bi-mortarboard text-primary"></i> MIT OpenCourse</div>
            <div class="brand-chip"><i class="bi bi-buildings text-success"></i> Stanford University</div>
            <!-- Duplicate for seamless infinite loop -->
            <div class="brand-chip"><i class="bi bi-shield-check text-success"></i> ISO/IEC 27001 Certified</div>
            <div class="brand-chip"><i class="bi bi-award text-warning"></i> OSHA Workplace Safety</div>
            <div class="brand-chip"><i class="bi bi-box-seam text-info"></i> SCORM &amp; xAPI Standard</div>
            <div class="brand-chip"><i class="bi bi-cloud text-primary"></i> AWS Partner Network</div>
            <div class="brand-chip"><i class="bi bi-google text-danger"></i> Google Cloud Education</div>
            <div class="brand-chip"><i class="bi bi-cpu text-secondary"></i> IEEE Learning Standards</div>
            <div class="brand-chip"><i class="bi bi-mortarboard text-primary"></i> MIT OpenCourse</div>
            <div class="brand-chip"><i class="bi bi-buildings text-success"></i> Stanford University</div>
        </div>
    </div>
</div>

<!-- Metrics Counters Section -->
<section class="py-5" style="background: var(--lms-bg-body);">
    <div class="container">
        <div class="row g-4 text-center">
            @foreach($metrics as $item)
                <div class="col-6 col-lg-3">
                    <div class="lms-card p-4 h-100 lms-card-interactive border-0" style="background: var(--lms-surface-card);">
                        <div class="stat-icon-pill mx-auto" style="background: {{ $item['bg'] ?? 'var(--tj-bg-purple)' }}; color: {{ $item['color'] ?? 'var(--lms-accent)' }};">
                            <i class="bi {{ $item['icon'] ?? 'bi-bar-chart' }}"></i>
                        </div>
                        <div class="display-6 fw-bold metric-number mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            {{ $item['value'] ?? '0' }}
                        </div>
                        <div class="text-secondary small fw-semibold">{{ $item['label'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
