@extends('layouts.app')

@section('title', 'Cambridge Past Papers & Syllabus Specifications')

@section('content')
<!-- Header Banner with Ambient Gradient -->
<div class="py-5 border-bottom position-relative overflow-hidden catalog-header-banner" style="background: var(--lms-bg-body);">
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 480px; height: 480px; background: radial-gradient(circle, rgba(2, 132, 199, 0.12) 0%, transparent 70%); filter: blur(50px); pointer-events: none; transform: translate(30%, -20%);"></div>

    <div class="container position-relative py-2" style="z-index: 2;">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb small mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Cambridge Resources</li>
                    </ol>
                </nav>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-sm border mb-2" 
                     style="background: var(--lms-surface-card); border-color: rgba(2, 132, 199, 0.25) !important;">
                    <i class="bi bi-mortarboard-fill text-primary"></i>
                    <span class="small fw-bold" style="color: var(--lms-accent); font-size: 0.74rem;">
                        BRITISH INTERNATIONAL CURRICULUM
                    </span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                        5 LEVELS
                    </span>
                </div>
                <h1 class="display-5 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Past Papers, Mark Schemes &amp; Syllabus Packs
                </h1>
                <p class="lead text-secondary fs-6 mb-0">
                    Access unworked past papers for free, or unlock step-by-step worked solutions, Esther Math Matrix revision trackers, and topical question packs per Cambridge assessment level.
                </p>
            </div>
            <div class="col-lg-5">
                <!-- Search & Category Filter Box -->
                <form action="{{ route('courses.index') }}" method="GET" class="lms-card p-3 border shadow-sm rounded-4" style="background: var(--lms-surface-card);">
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search papers, syllabus codes (e.g. 9709, 0620)..." value="{{ request('search') }}">
                    </div>
                    <div class="d-flex gap-2">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Cambridge Levels &amp; Subjects</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') === $category->slug || request('level') === $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="tj-btn-primary py-1 px-3 small flex-shrink-0">
                            <span>Filter</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Level Quick-Filter Badges -->
<div class="border-bottom py-3 sticky-top" style="background: var(--lms-surface-card); z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div class="container d-flex align-items-center gap-2 overflow-x-auto text-nowrap pb-1">
        <span class="small fw-bold text-muted text-uppercase me-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Browse Level:</span>
        <a href="{{ route('courses.index') }}" 
           class="badge rounded-pill text-decoration-none px-3 py-2 fw-semibold transition-all {{ !request('category') && !request('level') && !request('search') ? 'bg-primary text-white shadow-sm' : 'bg-body text-secondary border' }}">
            All Cambridge Syllabuses
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('courses.index', ['category' => $cat->slug]) }}" 
               class="badge rounded-pill text-decoration-none px-3 py-2 fw-semibold transition-all {{ (request('category') === $cat->slug || request('level') === $cat->slug) ? 'bg-primary text-white shadow-sm' : 'bg-body text-secondary border' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

<div class="container py-5">
    @if(isset($groupedCourses) && $groupedCourses !== null)
        {{-- SECTION-BY-SECTION CATEGORIZED PROGRAM LAYOUT --}}
        @forelse($groupedCourses as $categoryName => $catCourses)
            <div class="mb-5 category-program-section">
                <!-- Category Section Header -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" 
                             style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284C7, #0369A1); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--lms-font-heading); font-size: 1.5rem;">
                                    {{ $categoryName }}
                                </h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    {{ $catCourses->count() }} {{ Str::plural('Program', $catCourses->count()) }}
                                </span>
                            </div>
                            <div class="text-secondary small mt-0.5">
                                Official Cambridge assessment syllabuses, unworked test papers, and Esther Math solution packs.
                            </div>
                        </div>
                    </div>

                    @if($catCourses->first() && $catCourses->first()->category)
                        <a href="{{ route('courses.index', ['category' => $catCourses->first()->category->slug]) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                            <span>Filter {{ $categoryName }}</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>

                <!-- Cards Grid for this specific Category -->
                <div class="row g-4">
                    @foreach($catCourses as $course)
                        <div class="col-md-6 col-lg-4">
                            <div class="lms-card lms-card-interactive h-100 d-flex flex-column border shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
                                <!-- Thumbnail Container -->
                                <div class="course-thumb-wrap position-relative">
                                    @if($course->thumbnailUrl())
                                        <div class="w-100 h-100 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0284C7, #0369A1);">
                                            <img src="{{ $course->thumbnailUrl() }}" 
                                                 alt=""
                                                 loading="lazy"
                                                 referrerpolicy="no-referrer"
                                                 class="w-100 h-100 object-fit-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="align-items-center justify-content-center text-white w-100 h-100" style="display: none;">
                                                <div class="text-center p-3">
                                                    <i class="bi bi-mortarboard-fill fs-1 text-light d-block mb-1"></i>
                                                    <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                                        {{ $course->category?->name ?? 'Cambridge Resource' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" 
                                             style="background: linear-gradient(135deg, #0284C7, #0369A1); min-height: 100%;">
                                            <div class="text-center p-3">
                                                <i class="bi bi-mortarboard-fill fs-1 text-light d-block mb-1"></i>
                                                <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                                    {{ $course->category?->name ?? 'Cambridge Resource' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Ribbon Badges -->
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-primary text-white fw-bold px-2.5 py-1 rounded-pill small shadow-sm">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ $course->category?->name ?? 'Cambridge Level' }}
                                        </span>
                                    </div>

                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-grid-3x3 text-warning me-1"></i> Esther Math Ready
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <!-- Syllabus Specification Row -->
                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                                                 style="width: 28px; height: 28px; background: var(--lms-accent);">
                                                <i class="bi bi-book-half"></i>
                                            </div>
                                            <span class="small fw-semibold text-secondary">
                                                Cambridge International
                                            </span>
                                            <span class="badge bg-light text-primary font-mono border px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                                                {{ $course->course_code }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                                            <i class="bi bi-star-fill"></i>
                                            <span class="text-dark">5.0</span>
                                            <span class="text-muted fw-normal">(Cambridge Verified)</span>
                                        </div>
                                    </div>

                                    <!-- Course Title -->
                                    <h5 class="fw-bold mb-2 flex-grow-1" style="font-family: var(--lms-font-heading); line-height: 1.4;">
                                        <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-reset">
                                            {{ $course->title }}
                                        </a>
                                    </h5>

                                    <p class="text-secondary small mb-3 line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $course->description ?? 'Cambridge past paper packs with marking keys, topical worked solutions, and KaTeX mathematical notation.' }}
                                    </p>

                                    <!-- Meta Chips -->
                                    <div class="d-flex align-items-center justify-content-between text-muted small my-2 py-2 px-3 rounded-3" 
                                         style="background: var(--lms-bg-secondary); font-size: 0.76rem;">
                                        <span><i class="bi bi-file-earmark-pdf me-1 text-primary"></i> Worked Solutions</span>
                                        <span><i class="bi bi-clock-history me-1 text-primary"></i> 2023–2026 Series</span>
                                        <span><i class="bi bi-calculator me-1 text-primary"></i> KaTeX Formulas</span>
                                    </div>

                                    <!-- Elegant Divider -->
                                    <hr class="my-3 opacity-10">

                                    <!-- Price & Action CTA Card Footer -->
                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
                                        <div class="min-w-0">
                                            @if($course->price_cents > 0)
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Level Pass</span>
                                                    <span class="fs-4 fw-bold font-mono lh-1" style="color: var(--lms-accent, #723EC3);">
                                                        {{ $course->formattedPrice() }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Access</span>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold font-mono">
                                                        Free Unworked
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <a href="{{ route('courses.show', $course->slug) }}" 
                                           class="btn btn-sm text-white rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none transition-all"
                                           style="background: linear-gradient(135deg, var(--lms-accent, #0284C7), #0369A1); white-space: nowrap; font-size: 0.82rem;">
                                            <span>Explore Papers</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x display-3 d-block mb-3 text-secondary opacity-50"></i>
                <h4 class="fw-bold" style="font-family: var(--lms-font-heading);">No Cambridge programs available currently.</h4>
                <p class="text-muted">Check back soon as new curriculum materials and past papers are added regularly.</p>
            </div>
        @endforelse

    @else
        {{-- FILTERED VIEW (BY CATEGORY OR SEARCH) --}}
        @if($selectedCategory)
            <div class="p-4 rounded-4 mb-4 border shadow-sm" style="background: var(--lms-surface-card);">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" 
                             style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                            <i class="bi bi-bookmark-check-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold mb-1">
                                FILTERED CATEGORY
                            </span>
                            <h3 class="fw-bold mb-0 text-dark" style="font-family: var(--lms-font-heading);">
                                {{ $selectedCategory->name }}
                            </h3>
                            <div class="text-secondary small mt-0.5">Showing all verified syllabus modules and revision packs under this level.</div>
                        </div>
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> View All Categories
                    </a>
                </div>
            </div>
        @endif

        <div class="row g-4">
            @forelse($courses as $course)
                <div class="col-md-6 col-lg-4">
                    <div class="lms-card lms-card-interactive h-100 d-flex flex-column border shadow-sm rounded-4 overflow-hidden" style="background: var(--lms-surface-card);">
                        <!-- Thumbnail Container -->
                        <div class="course-thumb-wrap position-relative">
                            @if($course->thumbnailUrl())
                                <div class="w-100 overflow-hidden position-relative" style="height: 180px;">
                                    <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" 
                                     style="background: linear-gradient(135deg, #0284C7, #0369A1); min-height: 180px;">
                                    <div class="text-center p-3">
                                        <i class="bi bi-mortarboard-fill fs-1 text-light d-block mb-1"></i>
                                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                            {{ $course->category?->name ?? 'Cambridge Resource' }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Ribbon Badges -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary text-white fw-bold px-2.5 py-1 rounded-pill small shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ $course->category?->name ?? 'Cambridge Level' }}
                                </span>
                            </div>

                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2.5 py-1 small">
                                    <i class="bi bi-grid-3x3 text-warning me-1"></i> Esther Math Ready
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <!-- Syllabus Specification Row -->
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                                         style="width: 28px; height: 28px; background: var(--lms-accent);">
                                        <i class="bi bi-book-half"></i>
                                    </div>
                                    <span class="small fw-semibold text-secondary">
                                        Cambridge International
                                    </span>
                                    <span class="badge bg-light text-primary font-mono border px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                                        {{ $course->course_code }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-dark">5.0</span>
                                    <span class="text-muted fw-normal">(Cambridge Verified)</span>
                                </div>
                            </div>

                            <!-- Course Title -->
                            <h5 class="fw-bold mb-2 flex-grow-1" style="font-family: var(--lms-font-heading); line-height: 1.4;">
                                <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-reset">
                                    {{ $course->title }}
                                </a>
                            </h5>

                            <p class="text-secondary small mb-3 line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $course->description ?? 'Cambridge past paper packs with marking keys, topical worked solutions, and KaTeX mathematical notation.' }}
                            </p>

                            <!-- Meta Chips -->
                            <div class="d-flex align-items-center justify-content-between text-muted small my-2 py-2 px-3 rounded-3" 
                                 style="background: var(--lms-bg-secondary); font-size: 0.76rem;">
                                <span><i class="bi bi-file-earmark-pdf me-1 text-primary"></i> Worked Solutions</span>
                                <span><i class="bi bi-clock-history me-1 text-primary"></i> 2023–2026 Series</span>
                                <span><i class="bi bi-calculator me-1 text-primary"></i> KaTeX Formulas</span>
                            </div>

                            <!-- Elegant Divider -->
                            <hr class="my-3 opacity-10">

                            <!-- Price & Action CTA Card Footer -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
                                <div class="min-w-0">
                                    @if($course->price_cents > 0)
                                        <div class="d-flex flex-column">
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Level Pass</span>
                                            <span class="fs-4 fw-bold font-mono lh-1" style="color: var(--lms-accent, #723EC3);">
                                                {{ $course->formattedPrice() }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column">
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Access</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold font-mono">
                                                Free Unworked
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('courses.show', $course->slug) }}" 
                                   class="btn btn-sm text-white rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none transition-all"
                                   style="background: linear-gradient(135deg, var(--lms-accent, #0284C7), #0369A1); white-space: nowrap; font-size: 0.82rem;">
                                    <span>Explore Papers</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-search display-3 d-block mb-3 text-secondary opacity-50"></i>
                    <h4 class="fw-bold" style="font-family: var(--lms-font-heading);">No Cambridge resources matched your filter.</h4>
                    <p class="text-muted">Try selecting a different Cambridge level or clearing your search criteria.</p>
                    <a href="{{ route('courses.index') }}" class="tj-btn-outline mt-2">Clear All Filters</a>
                </div>
            @endforelse
        </div>

        @if($courses->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $courses->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
