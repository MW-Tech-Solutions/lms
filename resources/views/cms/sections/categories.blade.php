@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Explore Categories';
    $subtitle = $c['subtitle'] ?? 'Browse top categories and find the perfect course to grow your skills.';
    $viewAllText = $c['view_all_text'] ?? 'View All Categories';
    $viewAllUrl = $c['view_all_url'] ?? route('courses.index');
    
    $defaultCategories = [
        [
            'name' => 'Web Development',
            'lessons' => '48+ lessons',
            'icon' => 'code-window',
            'url' => route('courses.index', ['category' => 'web-development']),
        ],
        [
            'name' => 'Data Science',
            'lessons' => '52+ lessons',
            'icon' => 'data-network',
            'url' => route('courses.index', ['category' => 'data-science']),
        ],
        [
            'name' => 'IT & Software',
            'lessons' => '50+ lessons',
            'icon' => 'it-software',
            'url' => route('courses.index', ['category' => 'it-software']),
        ],
        [
            'name' => 'UI/UX Design',
            'lessons' => '38+ lessons',
            'icon' => 'ui-ux',
            'url' => route('courses.index', ['category' => 'ui-ux-design']),
        ],
        [
            'name' => 'Marketing',
            'lessons' => '36+ lessons',
            'icon' => 'marketing',
            'url' => route('courses.index', ['category' => 'marketing']),
        ],
        [
            'name' => 'Cybersecurity',
            'lessons' => '47+ lessons',
            'icon' => 'cybersecurity',
            'url' => route('courses.index', ['category' => 'cybersecurity']),
        ],
        [
            'name' => 'Finance & Accounting',
            'lessons' => '25+ lessons',
            'icon' => 'finance',
            'url' => route('courses.index', ['category' => 'finance-accounting']),
        ],
        [
            'name' => 'Health & Fitness',
            'lessons' => '20+ lessons',
            'icon' => 'health',
            'url' => route('courses.index', ['category' => 'health-fitness']),
        ],
    ];

    $categories = !empty($c['categories']) ? $c['categories'] : $defaultCategories;

    $highlights = $c['highlights'] ?? [
        ['icon' => 'bi-globe2', 'text' => '500+ Expert-led categories'],
        ['icon' => 'bi-star-fill text-warning', 'text' => '94% Career growth rate'],
        ['icon' => 'bi-mortarboard-fill text-primary', 'text' => 'New category every month'],
    ];
@endphp

<section class="explore-categories-section py-5 position-relative" style="background: var(--lms-bg-body);">
    <div class="container py-3">
        
        <!-- Header Row -->
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
            <div>
                <h2 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                    {{ $title }}
                </h2>
                <p class="text-secondary small mb-0" style="font-size: 0.95rem;">
                    {{ $subtitle }}
                </p>
            </div>
            <div>
                <a href="{{ $viewAllUrl }}" class="fw-bold text-decoration-none d-inline-flex align-items-center gap-1.5 transition-all text-body-emphasis" style="font-size: 0.95rem;">
                    <span>{{ $viewAllText }}</span>
                    <i class="bi bi-chevron-right" style="font-size: 0.85rem;"></i>
                </a>
            </div>
        </div>

        <!-- 8 Categories Grid -->
        <div class="row g-3 g-md-4 mb-4 pb-2">
            @foreach($categories as $cat)
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="{{ $cat['url'] ?? route('courses.index') }}" 
                       class="lms-category-card d-flex align-items-center justify-content-between p-3.5 p-xl-4 rounded-4 text-decoration-none h-100 transition-all shadow-sm"
                       style="background: var(--lms-bg-secondary, #F8F9FE); border: 1px solid var(--lms-border); min-height: 104px;">
                        
                        <div class="pe-2">
                            <h3 class="h6 fw-bold mb-1 text-truncate" style="color: var(--lms-text-primary); font-size: 1.05rem;">
                                {{ $cat['name'] }}
                            </h3>
                            <div class="text-secondary small font-mono" style="font-size: 0.82rem;">
                                {{ $cat['lessons'] }}
                            </div>
                        </div>

                        <!-- Right Purple Outline Graphic -->
                        <div class="lms-category-icon-box flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; color: var(--lms-accent, #723EC3);">
                            @switch($cat['icon'] ?? '')
                                @case('code-window')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="4" y="6" width="30" height="24" rx="4" stroke="currentColor" stroke-width="2.2"/>
                                        <path d="M12 18L8 21L12 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M22 18L26 21L22 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <line x1="18.5" y1="17" x2="15.5" y2="25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <rect x="18" y="20" width="26" height="22" rx="4" fill="var(--lms-surface-card)" stroke="currentColor" stroke-width="2.2"/>
                                        <line x1="22" y1="28" x2="38" y2="28" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="22" y1="34" x2="32" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    @break

                                @case('data-network')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="24" cy="14" rx="14" ry="5" stroke="currentColor" stroke-width="2.2"/>
                                        <ellipse cx="24" cy="24" rx="14" ry="5" stroke="currentColor" stroke-width="2.2"/>
                                        <ellipse cx="24" cy="34" rx="14" ry="5" stroke="currentColor" stroke-width="2.2"/>
                                        <path d="M10 14V34" stroke="currentColor" stroke-width="2.2"/>
                                        <path d="M38 14V34" stroke="currentColor" stroke-width="2.2"/>
                                        <circle cx="24" cy="24" r="2" fill="currentColor"/>
                                        <path d="M6 24C6 14 42 14 42 24C42 34 6 34 6 24Z" stroke="currentColor" stroke-width="1.8" stroke-dasharray="2 3"/>
                                    </svg>
                                    @break

                                @case('it-software')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="8" y="10" width="32" height="22" rx="3" stroke="currentColor" stroke-width="2.2"/>
                                        <path d="M4 36H44" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        <text x="17" y="26" font-family="monospace" font-size="12" font-weight="bold" fill="currentColor">IT</text>
                                        <circle cx="34" cy="16" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="14" cy="30" r="1.5" fill="currentColor"/>
                                        <line x1="32" y1="18" x2="28" y2="22" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                    @break

                                @case('ui-ux')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="6" y="8" width="36" height="32" rx="4" stroke="currentColor" stroke-width="2.2"/>
                                        <circle cx="12" cy="14" r="1.5" fill="currentColor"/>
                                        <circle cx="17" cy="14" r="1.5" fill="currentColor"/>
                                        <circle cx="22" cy="14" r="1.5" fill="currentColor"/>
                                        <path d="M12 28C18 20 26 36 34 26" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        <circle cx="12" cy="28" r="2" fill="currentColor"/>
                                        <circle cx="34" cy="26" r="2" fill="currentColor"/>
                                        <path d="M30 38L38 30L34 26L26 34L30 38Z" fill="var(--lms-surface-card)" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    @break

                                @case('marketing')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 20V28L14 30V18L8 20Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                                        <path d="M14 18L32 10V38L14 30" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                                        <path d="M18 30V40H22V30" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        <path d="M36 18C39 20 41 24 39 28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                        <path d="M40 14C44 18 45 28 41 34" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                                    </svg>
                                    @break

                                @case('cybersecurity')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M24 6L38 12V24C38 33 28 40 24 42C20 40 10 33 10 24V12L24 6Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                                        <rect x="19" y="22" width="10" height="8" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M21 22V18C21 16.34 22.34 15 24 15C25.66 15 27 16.34 27 18V22" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    @break

                                @case('finance')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="6" y="8" width="26" height="22" rx="3" stroke="currentColor" stroke-width="2.2"/>
                                        <line x1="12" y1="24" x2="12" y2="18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        <line x1="18" y1="24" x2="18" y2="14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        <line x1="24" y1="24" x2="24" y2="20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        <circle cx="34" cy="32" r="10" fill="var(--lms-surface-card)" stroke="currentColor" stroke-width="2.2"/>
                                        <circle cx="34" cy="32" r="5" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="34" cy="32" r="1.5" fill="currentColor"/>
                                    </svg>
                                    @break

                                @case('health')
                                    <svg width="42" height="42" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M24 40S8 29 8 18C8 12.5 12.5 8 18 8C21.5 8 24 10 24 10C24 10 26.5 8 30 8C35.5 8 40 12.5 40 18C40 29 24 40 24 40Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                                        <path d="M12 24H18L21 18L27 30L30 24H36" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break

                                @default
                                    <i class="bi bi-grid-fill fs-2"></i>
                            @endswitch
                        </div>

                    </a>
                </div>
            @endforeach
        </div>

        <!-- Centered Bottom Highlight Badges -->
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 gap-md-3">
            @foreach($highlights as $pill)
                <div class="badge rounded-pill px-3 py-2 border d-flex align-items-center gap-2" 
                     style="background: var(--lms-surface-card); color: var(--lms-text-primary); border-color: var(--lms-border) !important; font-size: 0.82rem; font-weight: 600;">
                    <i class="bi {{ $pill['icon'] ?? 'bi-check-circle' }}" style="color: var(--lms-accent, #723EC3);"></i>
                    <span>{{ $pill['text'] }}</span>
                </div>
            @endforeach
        </div>

    </div>
</section>
