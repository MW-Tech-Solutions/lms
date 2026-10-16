@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Upcoming Cambridge Live Masterclasses';
    $subtitle = $c['subtitle'] ?? 'Join certified Cambridge examiners and educators in real-time syllabus walkthroughs & past paper marking clinics.';
    $viewAllText = $c['view_all_text'] ?? 'View All Events';
    $viewAllUrl = route('events.index');

    // Query active live events from database
    $dbEvents = \App\Models\LiveEvent::where('is_active', true)
        ->where('event_date', '>=', now()->subDays(2))
        ->orderBy('event_date')
        ->take(4)
        ->get();

    $eventsList = [];
    if ($dbEvents->isNotEmpty()) {
        foreach ($dbEvents as $e) {
            $eventsList[] = [
                'id' => $e->id,
                'title' => $e->title,
                'date' => $e->formatted_date,
                'location' => 'Online Live (Zoom / Portal)',
                'level_badge' => $e->level_badge,
                'remaining' => $e->remaining_seats,
                'instructor' => $e->instructor_name,
                'role' => $e->instructor_role,
                'price' => 'Free Pass',
                'price_label' => "/ {$e->remaining_seats} spots left",
            ];
        }
    }

    if (!empty($c['events'])) {
        foreach ($c['events'] as $ce) {
            $eventsList[] = [
                'id' => $ce['id'] ?? null,
                'title' => $ce['title'],
                'date' => $ce['date'],
                'location' => $ce['location'] ?? 'Online Live (Zoom / Portal)',
                'level_badge' => 'Interactive Workshop',
                'remaining' => 15,
                'instructor' => 'Lead Cambridge Examiner',
                'role' => 'Faculty Specialist',
                'price' => $ce['price'] ?? 'Free Pass',
                'price_label' => $ce['price_label'] ?? '',
            ];
        }
    }

    if (empty($eventsList)) {
        $eventsList = [
            [
                'id' => 1,
                'title' => 'Cambridge IGCSE Math (0580): Extended Paper 4 Clinic',
                'date' => 'Jul 02, 2026 • 04:00 PM BST',
                'location' => 'Online Live (Zoom)',
                'level_badge' => 'IGCSE (Years 10–11)',
                'remaining' => 24,
                'instructor' => 'Darren Treadway',
                'role' => 'Senior Cambridge Examiner',
                'price' => 'Free Pass',
                'price_label' => '/ 24 spots left',
            ],
            [
                'id' => 2,
                'title' => 'Pure Mathematics 1 (9709): Calculus & Trigonometry Intensive',
                'date' => 'Jul 10, 2026 • 05:30 PM BST',
                'location' => 'Online Live (Portal)',
                'level_badge' => 'AS Level (Year 12)',
                'remaining' => 18,
                'instructor' => 'Tammi Allison',
                'role' => 'A-Level Specialist Tutor',
                'price' => 'Free Pass',
                'price_label' => '/ 18 spots left',
            ],
            [
                'id' => 3,
                'title' => 'Cambridge Physics (9702): Paper 5 Practical Planning Mastery',
                'date' => 'Jul 20, 2026 • 03:00 PM BST',
                'location' => 'Online Live (Zoom)',
                'level_badge' => 'A Level (Year 13)',
                'remaining' => 32,
                'instructor' => 'Micheal Cabot',
                'role' => 'Physics Examiner',
                'price' => 'Free Pass',
                'price_label' => '/ 32 spots left',
            ],
            [
                'id' => 4,
                'title' => 'Primary Checkpoint English (0844): Fiction & Non-Fiction Analysis',
                'date' => 'Jul 28, 2026 • 02:00 PM BST',
                'location' => 'Online Live (Portal)',
                'level_badge' => 'Primary (Years 1–6)',
                'remaining' => 15,
                'instructor' => 'Sofia Bennett',
                'role' => 'Curriculum Lead',
                'price' => 'Free Pass',
                'price_label' => '/ 15 spots left',
            ],
        ];
    }

    // Enforce strictly max 4 events on homepage; remainder displayed on /events
    $eventsList = array_slice($eventsList, 0, 4);

    $featuredImageUrl = $c['featured_image_url'] ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
@endphp

<style>
    .lms-event-card {
        background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
        border: 1px solid rgba(2, 132, 199, 0.15) !important;
        border-radius: 22px;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .lms-event-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08) !important;
        border-color: rgba(2, 132, 199, 0.35) !important;
    }
    .lms-event-badge {
        background: #FFFFFF;
        color: #475569;
        border: 1px solid #E2E8F0;
        font-size: 0.74rem;
    }
    .lms-event-title {
        color: #0F172A;
    }

    [data-bs-theme="dark"] .lms-event-card,
    body.dark-mode .lms-event-card,
    .dark .lms-event-card {
        background: linear-gradient(135deg, #151928 0%, #0F1322 100%) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
    }
    [data-bs-theme="dark"] .lms-event-card:hover,
    body.dark-mode .lms-event-card:hover,
    .dark .lms-event-card:hover {
        border-color: var(--lms-accent, #723EC3) !important;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.6) !important;
    }
    [data-bs-theme="dark"] .lms-event-badge,
    body.dark-mode .lms-event-badge,
    .dark .lms-event-badge {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #CBD5E1 !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
    }
    [data-bs-theme="dark"] .lms-event-title,
    body.dark-mode .lms-event-title,
    .dark .lms-event-title {
        color: #F8FAFC !important;
    }
    [data-bs-theme="dark"] .lms-event-modal-content,
    body.dark-mode .lms-event-modal-content,
    .dark .lms-event-modal-content {
        background: #151928 !important;
        color: #F8FAFC !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }
    [data-bs-theme="dark"] .lms-event-modal-box,
    body.dark-mode .lms-event-modal-box,
    .dark .lms-event-modal-box {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #CBD5E1 !important;
    }
</style>

<section class="upcoming-events-section py-5 position-relative" style="background: var(--lms-bg-body);" id="upcoming-events">
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
                    @foreach($eventsList as $event)
                        <div class="col-12 col-md-6">
                            <div class="lms-event-card p-4 rounded-4 shadow-sm border h-100 d-flex flex-column justify-content-between transition-all">
                                
                                <div>
                                    <!-- Meta Badges: Date & Location -->
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge rounded-pill px-2.5 py-1.5 lms-event-badge fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
                                            <i class="bi bi-calendar3" style="color: var(--lms-accent, #723EC3);"></i>
                                            <span>{{ $event['date'] }}</span>
                                        </span>
                                        <span class="badge rounded-pill px-2.5 py-1.5 lms-event-badge fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
                                            <i class="bi bi-broadcast" style="color: var(--lms-accent, #723EC3);"></i>
                                            <span>{{ $event['location'] }}</span>
                                        </span>
                                    </div>

                                    @if(!empty($event['level_badge']))
                                        <div class="mb-2">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.70rem;">
                                                {{ $event['level_badge'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Event Title -->
                                    <h3 class="h5 fw-bold mb-3 lms-event-title" style="font-family: var(--lms-font-heading); line-height: 1.3; font-size: 1.05rem;">
                                        {{ $event['title'] }}
                                    </h3>
                                </div>

                                <!-- Bottom Row: Price & Reserve Action Modal Trigger -->
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="fw-extrabold fs-6 font-mono" style="color: var(--lms-accent, #723EC3);">
                                            {{ $event['price'] }}
                                        </span>
                                        <span class="text-secondary small font-mono" style="font-size: 0.76rem;">
                                            {{ $event['price_label'] }}
                                        </span>
                                    </div>

                                    @php $modalId = $event['id'] ?? ($loop->index + 1); @endphp
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-xs"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reserveModal{{ $modalId }}"
                                            style="font-size: 0.82rem;">
                                        <span>Reserve Spot</span>
                                        <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- Modal: Reserve Spot for Event -->
                        <div class="modal fade" id="reserveModal{{ $modalId }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST" action="{{ route('events.reserve', $modalId) }}" class="modal-content lms-event-modal-content border-0 shadow rounded-4 text-start">
                                    @csrf
                                    <div class="modal-header border-0 pb-0">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill mb-1" style="font-size: 0.70rem;">Cambridge Live Spot Reservation</span>
                                            <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); font-size: 1.1rem;">
                                                {{ $event['title'] }}
                                            </h5>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        <div class="p-3 rounded-3 lms-event-modal-box border mb-3 small">
                                            <div class="mb-1"><i class="bi bi-calendar-event text-primary me-2"></i><strong>Session Time:</strong> {{ $event['date'] }}</div>
                                            <div><i class="bi bi-person-video3 text-primary me-2"></i><strong>Tutor:</strong> {{ $event['instructor'] ?? 'Cambridge Lead Specialist' }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Your Full Name *</label>
                                            <input type="text" name="name" class="form-control" value="{{ auth()->user()?->name ?? '' }}" placeholder="e.g. Student or Parent Name" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Email Address for Zoom Invite *</label>
                                            <input type="email" name="email" class="form-control" value="{{ auth()->user()?->email ?? '' }}" placeholder="student@example.co.uk" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Phone / WhatsApp (Optional)</label>
                                            <input type="text" name="phone" class="form-control" placeholder="+44 7911 123456">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Specific Past Paper Questions or Topics of Concern</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. November 2024 Question 7 on Differential Equations..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                            <i class="bi bi-check-circle me-1"></i> Confirm Reservation
                                        </button>
                                    </div>
                                </form>
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
