@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Upcoming Live Events';
    $subtitle = $c['subtitle'] ?? 'Join mentors, industry experts, and global learners in real-time sessions.';
    $viewAllText = $c['view_all_text'] ?? 'View All Event';
    $viewAllUrl = $c['view_all_url'] ?? route('courses.index');

    $defaultEvents = [
        [
            'title' => "Web3 Builder's Meetup",
            'date' => 'Jul 02, 2026',
            'location' => 'Singapore',
            'price' => '$79',
            'price_label' => '/ networking',
            'reserve_url' => route('courses.index'),
        ],
        [
            'title' => 'Data Science Bootcamp',
            'date' => 'Jul 10, 2026',
            'location' => 'Online (Zoom + Discord)',
            'price' => 'Free',
            'price_label' => '/ limited seats',
            'reserve_url' => route('courses.index'),
        ],
        [
            'title' => 'UI/UX Design Hackathon',
            'date' => 'Jul 20, 2026',
            'location' => 'Berlin & Online',
            'price' => '$49',
            'price_label' => '/ early bird',
            'reserve_url' => route('courses.index'),
        ],
        [
            'title' => 'No-Code Hackathon',
            'date' => 'Jul 28, 2026',
            'location' => 'Virtual + Global',
            'price' => '$15',
            'price_label' => '/ student discount',
            'reserve_url' => route('courses.index'),
        ],
    ];

    $events = !empty($c['events']) ? $c['events'] : $defaultEvents;
    $featuredImageUrl = $c['featured_image_url'] ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
@endphp

<section class="upcoming-events-section py-5 position-relative" style="background: var(--lms-bg-body);">
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

        <!-- 2 Columns: Left 2x2 Grid + Right Graphic Showcase -->
        <div class="row g-4 align-items-center">
            
            <!-- Left: 4 Event Cards (2x2 Grid) -->
            <div class="col-lg-8">
                <div class="row g-3 g-md-4">
                    @foreach($events as $event)
                        <div class="col-12 col-md-6">
                            <div class="lms-event-card p-4 rounded-4 shadow-sm border h-100 d-flex flex-column justify-content-between transition-all"
                                 style="background: linear-gradient(135deg, rgba(245, 243, 255, 0.75) 0%, var(--lms-surface-card) 100%); border-color: var(--lms-border) !important; border-radius: 22px;">
                                
                                <div>
                                    <!-- Meta Badges: Date & Location -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge rounded-pill px-2.5 py-1.5 border bg-white bg-opacity-75 text-secondary fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.74rem;">
                                            <i class="bi bi-calendar3" style="color: var(--lms-accent, #723EC3);"></i>
                                            <span>{{ $event['date'] }}</span>
                                        </span>
                                        <span class="badge rounded-pill px-2.5 py-1.5 border bg-white bg-opacity-75 text-secondary fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.74rem;">
                                            <i class="bi bi-geo-alt-fill" style="color: var(--lms-accent, #723EC3);"></i>
                                            <span>{{ $event['location'] }}</span>
                                        </span>
                                    </div>

                                    <!-- Event Title -->
                                    <h3 class="h5 fw-bold mb-3" style="color: var(--lms-text-primary); font-family: var(--lms-font-heading); line-height: 1.3;">
                                        {{ $event['title'] }}
                                    </h3>
                                </div>

                                <!-- Bottom Row: Price & Reserve Action -->
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="fw-extrabold fs-5 font-mono" style="color: var(--lms-accent, #723EC3);">
                                            {{ $event['price'] }}
                                        </span>
                                        <span class="text-secondary small font-mono" style="font-size: 0.78rem;">
                                            {{ $event['price_label'] }}
                                        </span>
                                    </div>

                                    <a href="{{ $event['reserve_url'] ?? route('courses.index') }}" 
                                       class="fw-bold small text-decoration-none d-inline-flex align-items-center gap-1 text-body transition-all hover-translate-x" 
                                       style="font-size: 0.86rem;">
                                        <span>Reserve Spot</span>
                                        <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Engaging Illustration with Geometric Purple Arc Background -->
            <div class="col-lg-4 text-center">
                <div class="position-relative d-inline-block mx-auto" style="max-width: 340px;">
                    
                    <!-- Decorative Purple Geometric Cross Star Arc -->
                    <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 1; pointer-events: none; transform: translate(-50%, -50%);">
                        <svg width="320" height="320" viewBox="0 0 320 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="160" cy="160" r="140" fill="rgba(114, 62, 195, 0.15)"/>
                            <path d="M160 20C170 110 210 150 300 160C210 170 170 210 160 300C150 210 110 170 20 160C110 150 150 110 160 20Z" fill="var(--lms-accent, #723EC3)" opacity="0.85"/>
                        </svg>
                    </div>

                    <!-- Cutout Photo / Portrait -->
                    <div class="position-relative overflow-hidden rounded-4 shadow-lg border" 
                         style="z-index: 2; border-color: rgba(255, 255, 255, 0.4) !important; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px);">
                        <img src="{{ $featuredImageUrl }}" 
                             alt="Student and Event Speaker" 
                             class="img-fluid object-fit-cover w-100" 
                             style="max-height: 380px; object-position: top center;">
                        
                        <!-- Floating Spotlight Badge -->
                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 px-3 py-2 rounded-pill shadow d-flex align-items-center gap-2 border" 
                             style="background: rgba(15, 19, 33, 0.9); border-color: rgba(255, 255, 255, 0.2) !important; color: #FFF; white-space: nowrap; font-size: 0.78rem;">
                            <span class="badge rounded-circle p-1 bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                            <span class="fw-semibold">Interactive Q&amp;A Included</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>
