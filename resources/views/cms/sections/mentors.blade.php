@php
    $c = $section->content_json ?? [];
    $title = $c['title'] ?? 'Meet Our Mentors';
    $subtitle = $c['subtitle'] ?? 'Learn from industry experts who bring real-world experience straight to your screen.';
    
    $defaultMentors = [
        [
            'name' => 'Darren Treadway',
            'role' => 'Data Science Instructor',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
            'students' => '28k+',
            'courses' => '8',
            'rating' => '98%',
        ],
        [
            'name' => 'Tammi Allison',
            'role' => 'UI/UX Design Mentor',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
            'students' => '19k+',
            'courses' => '12',
            'rating' => '96%',
        ],
        [
            'name' => 'Micheal Cabot',
            'role' => 'Full-Stack Web Instructor',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
            'students' => '24k+',
            'courses' => '10',
            'rating' => '97%',
        ],
        [
            'name' => 'Sofia Bennett',
            'role' => 'Marketing & Growth Coach',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
            'students' => '31k+',
            'courses' => '9',
            'rating' => '95%',
        ],
    ];

    $mentors = !empty($c['mentors']) ? $c['mentors'] : $defaultMentors;
@endphp

<section class="meet-mentors-section py-5 position-relative" style="background: var(--lms-bg-body);">
    <div class="container py-3">
        
        <!-- Centered Section Header -->
        <div class="text-center mb-5 max-w-700 mx-auto">
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                {{ $title }}
            </h2>
            <p class="text-secondary small mb-0" style="font-size: 0.95rem;">
                {{ $subtitle }}
            </p>
        </div>

        <!-- 4 Mentors Grid -->
        <div class="row g-4 justify-content-center">
            @foreach($mentors as $mentor)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="lms-mentor-card h-100 p-3.5 rounded-4 shadow-sm border transition-all text-center d-flex flex-column justify-content-between"
                         style="background: var(--lms-surface-card); border-color: var(--lms-border) !important; border-radius: 22px;">
                        
                        <!-- Top Avatar with Purple Radial Lighting Backdrop -->
                        <div class="mentor-avatar-wrapper rounded-4 overflow-hidden mb-3 position-relative mx-auto w-100" 
                             style="height: 190px; background: radial-gradient(circle, rgba(114, 62, 195, 0.45) 0%, rgba(94, 50, 166, 0.15) 60%, rgba(240, 235, 255, 0.4) 100%);">
                            
                            <!-- Ambient Glow Pattern Behind Headshot -->
                            <div class="position-absolute top-50 start-50 translate-middle rounded-circle" 
                                 style="width: 140px; height: 140px; background: rgba(114, 62, 195, 0.28); filter: blur(28px); pointer-events: none;"></div>

                            <img src="{{ $mentor['avatar'] }}" 
                                 alt="{{ $mentor['name'] }}" 
                                 class="w-100 h-100 object-fit-cover position-relative"
                                 style="object-position: top center; mix-blend-mode: normal;">
                        </div>

                        <!-- Mentor Info -->
                        <div class="px-2 mb-3">
                            <h3 class="h5 fw-bold mb-1" style="color: var(--lms-text-primary); font-family: var(--lms-font-heading);">
                                {{ $mentor['name'] }}
                            </h3>
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold" 
                                  style="background: rgba(114, 62, 195, 0.09); color: var(--lms-accent, #723EC3); font-size: 0.76rem;">
                                {{ $mentor['role'] }}
                            </span>
                        </div>

                        <!-- 3-Column Stats Row -->
                        <div class="pt-3 border-top d-flex justify-content-around align-items-center" style="border-color: var(--lms-border) !important;">
                            <div>
                                <div class="fw-bold fs-6 font-mono" style="color: var(--lms-accent, #723EC3); line-height: 1.1;">
                                    {{ $mentor['students'] }}
                                </div>
                                <div class="text-secondary small" style="font-size: 0.72rem;">Students</div>
                            </div>
                            <div class="border-end h-75" style="border-color: var(--lms-border) !important;"></div>
                            <div>
                                <div class="fw-bold fs-6 font-mono" style="color: var(--lms-accent, #723EC3); line-height: 1.1;">
                                    {{ $mentor['courses'] }}
                                </div>
                                <div class="text-secondary small" style="font-size: 0.72rem;">Courses</div>
                            </div>
                            <div class="border-end h-75" style="border-color: var(--lms-border) !important;"></div>
                            <div>
                                <div class="fw-bold fs-6 font-mono" style="color: var(--lms-accent, #723EC3); line-height: 1.1;">
                                    {{ $mentor['rating'] }}
                                </div>
                                <div class="text-secondary small" style="font-size: 0.72rem;">5-star</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
