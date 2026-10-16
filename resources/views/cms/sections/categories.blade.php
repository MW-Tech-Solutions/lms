@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Cambridge Assessment Levels & Subjects';
    $subtitle = $c['subtitle'] ?? 'Browse structured resources across the 5 distinct Cambridge International stages with strict level isolation.';
    $viewAllText = $c['view_all_text'] ?? 'View All Cambridge Syllabuses';
    $viewAllUrl = $c['view_all_url'] ?? route('courses.index');
    
    // Fetch dynamic categories from database in exact Cambridge progression order
    $orderedSlugs = [
        'primary-checkpoint', 
        'lower-secondary', 
        'igcse', 
        'as-level', 
        'a-level',
        'cambridge-mathematics',
        'cambridge-physics',
        'cambridge-chemistry',
    ];

    $dbCategories = \App\Models\CourseCategory::whereIn('slug', $orderedSlugs)
        ->get()
        ->sortBy(function($m) use ($orderedSlugs) {
            return array_search($m->slug, $orderedSlugs);
        });

    $iconMap = [
        'primary-checkpoint' => ['icon' => 'ui-ux', 'badge' => 'Years 1–6 Primary Checkpoint', 'color' => '#0284C7', 'bg' => 'rgba(2, 132, 199, 0.12)'],
        'lower-secondary' => ['icon' => 'data-network', 'badge' => 'Years 7–9 Secondary 1', 'color' => '#0D9488', 'bg' => 'rgba(13, 148, 136, 0.12)'],
        'igcse' => ['icon' => 'code-window', 'badge' => 'Years 10–11 Upper Secondary', 'color' => '#2563EB', 'bg' => 'rgba(37, 99, 235, 0.12)'],
        'as-level' => ['icon' => 'it-software', 'badge' => 'Year 12 Advanced Subsidiary', 'color' => '#7C3AED', 'bg' => 'rgba(124, 58, 237, 0.12)'],
        'a-level' => ['icon' => 'marketing', 'badge' => 'Year 13 Advanced Level', 'color' => '#D97706', 'bg' => 'rgba(217, 119, 6, 0.12)'],
        'cambridge-mathematics' => ['icon' => 'finance', 'badge' => 'Pure Math 1-3, Mech & Stats', 'color' => '#059669', 'bg' => 'rgba(5, 150, 105, 0.12)'],
        'cambridge-physics' => ['icon' => 'cybersecurity', 'badge' => 'Theory, MCQ & Practical', 'color' => '#DC2626', 'bg' => 'rgba(220, 38, 38, 0.12)'],
        'cambridge-chemistry' => ['icon' => 'health', 'badge' => 'Inorganic, Organic & Bio', 'color' => '#0891B2', 'bg' => 'rgba(8, 145, 178, 0.12)'],
    ];

    $categories = [];
    if ($dbCategories->isNotEmpty()) {
        foreach ($dbCategories as $cat) {
            $meta = $iconMap[$cat->slug] ?? ['icon' => 'ui-ux', 'badge' => 'Cambridge Resource', 'color' => '#0284C7', 'bg' => 'rgba(2, 132, 199, 0.12)'];
            $coursesCount = \App\Models\Course::where('category_id', $cat->id)->where('is_published', true)->count();
            $categories[] = [
                'name' => $cat->name,
                'lessons' => $coursesCount > 0 ? "{$coursesCount}+ Past Paper Packs" : $meta['badge'],
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'bg' => $meta['bg'],
                'url' => route('courses.index', ['category' => $cat->slug]),
            ];
        }
    } else {
        $categories = [
            ['name' => '1. Primary Checkpoint (Years 1–6)', 'lessons' => 'English, Math & Science', 'icon' => 'ui-ux', 'url' => route('courses.index', ['level' => 'primary-checkpoint'])],
            ['name' => '2. Lower Secondary Checkpoint (Years 7–9)', 'lessons' => 'Curriculum progression & tests', 'icon' => 'data-network', 'url' => route('courses.index', ['level' => 'lower-secondary'])],
            ['name' => '3. Cambridge Upper Secondary / IGCSE', 'lessons' => 'Years 10–11 Core & Extended', 'icon' => 'code-window', 'url' => route('courses.index', ['level' => 'igcse'])],
            ['name' => '4. Cambridge International AS Level', 'lessons' => 'Year 12 Advanced Subsidiary', 'icon' => 'it-software', 'url' => route('courses.index', ['level' => 'as-level'])],
            ['name' => '5. Cambridge International A Level', 'lessons' => 'Year 13 Advanced Level', 'icon' => 'marketing', 'url' => route('courses.index', ['level' => 'a-level'])],
            ['name' => 'Cambridge Mathematics (9709)', 'lessons' => 'Pure Math, Mechanics, Stats', 'icon' => 'finance', 'url' => route('courses.index', ['category' => 'cambridge-mathematics'])],
            ['name' => 'Cambridge Physics (9702)', 'lessons' => 'Theory, Practical & Mark Schemes', 'icon' => 'cybersecurity', 'url' => route('courses.index', ['category' => 'cambridge-physics'])],
            ['name' => 'Cambridge Chemistry & Biology', 'lessons' => 'Organisms, Reactions, Genetics', 'icon' => 'health', 'url' => route('courses.index', ['category' => 'cambridge-chemistry'])],
        ];
    }

    $highlights = $c['highlights'] ?? [
        ['icon' => 'bi-shield-lock-fill text-primary', 'text' => '5-Tier Strict Assessment Isolation'],
        ['icon' => 'bi-file-earmark-pdf-fill text-danger', 'text' => 'Official Past Papers & Mark Schemes'],
        ['icon' => 'bi-calculator-fill text-success', 'text' => 'LaTeX Formula & Esther Math Tracker'],
    ];
@endphp

<section class="explore-categories-section py-5 position-relative" id="cambridge-levels" style="background: var(--lms-bg-body);">
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

        <!-- Categories Grid -->
        <div class="row g-3 g-md-3 g-lg-4 mb-4 pb-2">
            @foreach($categories as $cat)
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="{{ $cat['url'] ?? route('courses.index') }}" 
                       class="lms-category-card d-flex align-items-center gap-3 p-3 rounded-4 text-decoration-none h-100 transition-all shadow-sm"
                       style="background: var(--lms-surface-card); border: 1px solid var(--lms-border); min-height: 84px;">
                        
                        <!-- Icon Box: Ample breathing space, perfectly centered, never touches borders -->
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 shadow-xs" 
                             style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; background: {{ $cat['bg'] ?? 'rgba(2, 132, 199, 0.12)' }}; color: {{ $cat['color'] ?? '#0284C7' }}; border: 1px solid rgba(0,0,0,0.06); border-radius: 12px;">
                            @switch($cat['icon'] ?? '')
                                @case('code-window')
                                    <i class="bi bi-mortarboard" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('data-network')
                                    <i class="bi bi-diagram-3-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('it-software')
                                    <i class="bi bi-journal-bookmark-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('ui-ux')
                                    <i class="bi bi-backpack-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('marketing')
                                    <i class="bi bi-award-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('cybersecurity')
                                    <i class="bi bi-lightning-charge-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('finance')
                                    <i class="bi bi-calculator-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @case('health')
                                    <i class="bi bi-droplet-fill" style="font-size: 1.25rem; line-height: 1;"></i>
                                    @break

                                @default
                                    <i class="bi bi-book-half" style="font-size: 1.25rem; line-height: 1;"></i>
                            @endswitch
                        </div>

                        <!-- Card Text Details: cleanly wraps without scattering -->
                        <div class="min-w-0 flex-grow-1">
                            <h3 class="fw-bold mb-1" style="color: var(--lms-text-primary); font-size: 0.90rem; font-family: var(--lms-font-heading); line-height: 1.3; word-break: break-word;">
                                {{ $cat['name'] }}
                            </h3>
                            <div class="text-secondary small font-mono" style="font-size: 0.74rem; line-height: 1.2;">
                                {{ $cat['lessons'] }}
                            </div>
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
