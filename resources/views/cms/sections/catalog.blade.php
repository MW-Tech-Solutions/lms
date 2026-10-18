@php
    $c = $section->content_json ?? [];
    $courses = \App\Models\Course::with(['category', 'instructor', 'sections.modules', 'enrollments'])
        ->where('is_published', true)
        ->orderBy('id', 'desc')
        ->take(6)
        ->get();
@endphp
<section class="py-5 catalog-section-wrapper" id="featured-catalog" x-data="{ activeFilter: 'all' }">
    <div class="container py-3">
        <!-- Section Heading & Filter Tabs -->
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 pb-2 border-bottom">
            <div>
                <span class="badge badge-purple-soft px-3 py-2 rounded-pill mb-2" style="background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                    <i class="bi bi-mortarboard-fill me-1"></i> Cambridge Syllabus Specifications
                </span>
                <h2 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Featured Past Papers &amp; Worked Solutions
                </h2>
                <p class="text-secondary small mb-0">Official Cambridge past question papers, verified mark schemes, and topic packs.</p>
            </div>
            
            <!-- Category Filter Pills -->
            <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0">
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold" 
                        :class="activeFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
                        @click="activeFilter = 'all'">
                    All Cambridge Levels
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold" 
                        :class="activeFilter === 'a-level' ? 'btn-primary' : 'btn-outline-secondary'"
                        @click="activeFilter = 'a-level'">
                    <i class="bi bi-award me-1"></i> A Level &amp; AS
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold" 
                        :class="activeFilter === 'igcse' ? 'btn-primary' : 'btn-outline-secondary'"
                        @click="activeFilter = 'igcse'">
                    <i class="bi bi-globe me-1"></i> IGCSE &amp; Checkpoint
                </button>
            </div>
        </div>

        <!-- Course Cards Grid (Edunex Card Design) -->
        <div class="row g-4">
            @forelse($courses as $course)
                <div class="col-md-6 col-lg-4" 
                     x-show="activeFilter === 'all' || (activeFilter === 'a-level' && ('{{ $course->category?->slug }}' === 'a-level' || '{{ $course->category?->slug }}' === 'as-level')) || (activeFilter === 'igcse' && ('{{ $course->category?->slug }}' === 'igcse' || '{{ $course->category?->slug }}' === 'lower-secondary' || '{{ $course->category?->slug }}' === 'primary-checkpoint'))"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="lms-card lms-card-interactive h-100 d-flex flex-column border-0 shadow-sm" style="background: var(--lms-surface-card);">
                        <!-- Thumbnail Zoom Container -->
                        <div class="course-thumb-wrap position-relative">
                            @if($course->thumbnailUrl())
                                <div class="w-100 h-100 overflow-hidden position-relative">
                                    <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" 
                                     style="background: {{ $course->target_audience === 'CORPORATE' ? 'linear-gradient(135deg, #0F172A, #1E293B)' : 'linear-gradient(135deg, #723EC3, #2563EB)' }}; min-height: 100%;">
                                    <div class="text-center p-3">
                                        <i class="bi {{ $course->target_audience === 'CORPORATE' ? 'bi-shield-check fs-1 text-warning' : 'bi-mortarboard-fill fs-1 text-light' }} d-block mb-1"></i>
                                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                                            {{ $course->category?->name ?? 'Enterprise Program' }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Ribbon / Audience Badges -->
                            <div class="position-absolute top-0 start-0 m-3">
                                @if($course->target_audience === 'CORPORATE')
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill small shadow-sm">
                                        <i class="bi bi-building me-1"></i> Corporate Track
                                    </span>
                                @else
                                    <span class="badge-popular shadow-sm">
                                        <i class="bi bi-fire me-1"></i> Popular
                                    </span>
                                @endif
                            </div>

                            @if($course->recertification_days)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small">
                                        <i class="bi bi-arrow-repeat me-1 text-warning"></i> {{ $course->recertification_days }}d Recert
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <!-- Author / Instructor Row -->
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white small fw-bold" 
                                         style="width: 28px; height: 28px; background: var(--lms-accent);">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="small fw-semibold text-secondary text-truncate" style="max-width: 170px;">
                                        {{ $course->instructor_title }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-dark">5.0</span>
                                    <span class="text-muted fw-normal">({{ $course->enrolled_count > 0 ? $course->enrolled_count : 'New' }})</span>
                                </div>
                            </div>

                            <!-- Course Title -->
                            <h5 class="fw-bold mb-2 flex-grow-1" style="font-family: var(--lms-font-heading); line-height: 1.4;">
                                <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-reset">
                                    {{ $course->title }}
                                </a>
                            </h5>

                            <!-- Course Meta Info (Dynamic counts from system) -->
                            <div class="d-flex align-items-center justify-content-between text-muted small my-3 py-2 px-3 rounded-3" 
                                 style="background: var(--lms-bg-secondary);">
                                <span><i class="bi bi-journal-text me-1 text-primary"></i> {{ $course->lessons_count }} {{ Str::plural('Lesson', $course->lessons_count) }}</span>
                                <span><i class="bi bi-clock me-1 text-primary"></i> {{ $course->estimated_duration }}</span>
                                <span><i class="bi bi-people me-1 text-primary"></i> {{ $course->formattedEnrolledCount() }} Enrolled</span>
                            </div>

                            <!-- Price & Action CTA -->
                            <div class="d-flex justify-content-between align-items-center gap-2 pt-2 mt-auto">
                                <div>
                                    @if($course->price_cents > 0)
                                        <div class="d-flex align-items-baseline gap-1.5 flex-wrap">
                                            <span class="fs-4 fw-bold font-mono text-nowrap" style="color: var(--lms-accent);">
                                                {{ $course->formattedPrice() }}
                                            </span>
                                            <span class="text-decoration-line-through text-muted small text-nowrap">$69.00</span>
                                        </div>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            Included in Corporate Plan
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}" class="tj-btn-primary py-2 px-3 small text-nowrap flex-shrink-0">
                                    <span>View Details</span>
                                    <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                    {{ $site_settings['msg_no_courses_yet'] ?? 'No published courses currently available.' }}
                </div>
            @endforelse
        </div>

        <!-- View All Link -->
        <div class="text-center mt-5">
            <a href="{{ route('courses.index') }}" class="tj-btn-outline explore-all-btn py-2 px-4 shadow-sm">
                <span>{{ $site_settings['label_view_all_courses'] ?? 'Explore All Available Tracks' }}</span>
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
