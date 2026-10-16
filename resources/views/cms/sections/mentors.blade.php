@php
    $c = $section->content_json ?? [];
    $title = 'Meet Our Mentors';
    $subtitle = 'Learn from industry experts who bring real-world experience straight to your screen.';
    
    // Fetch dynamically approved teachers from database
    $dbApprovedTeachers = \App\Models\User::where('approval_status', 'APPROVED')
        ->where(function ($q) {
            $q->whereHas('roleAssignments.role', function ($rq) {
                $rq->whereIn('name', ['TEACHER', 'TEACHER_NON_EDITING', 'INSTRUCTOR', 'COURSE_CREATOR']);
            })->orWhere('user_type', 'EMPLOYEE');
        })
        ->with(['profile', 'courses'])
        ->latest()
        ->take(4)
        ->get();

    $mentors = [];

    // 1. CMS section customized mentors
    if (!empty($c['mentors'])) {
        foreach ($c['mentors'] as $cm) {
            $mentors[] = [
                'id' => $cm['id'] ?? 1,
                'name' => $cm['name'],
                'role' => $cm['role'] ?? 'Data Science & Pure Math Specialist',
                'avatar' => $cm['avatar'] ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'students' => $cm['students'] ?? '28k+',
                'courses' => $cm['courses'] ?? '8',
                'rating' => $cm['rating'] ?? '98%',
            ];
        }
    }

    // 2. Dynamically approved teachers from database
    if ($dbApprovedTeachers->isNotEmpty()) {
        foreach ($dbApprovedTeachers as $teacher) {
            if (!collect($mentors)->contains('name', $teacher->name)) {
                $mentors[] = [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'role' => $teacher->profile?->job_title ?? 'Cambridge Faculty Examiner',
                    'avatar' => $teacher->profile?->avatar_url ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                    'students' => '240+',
                    'courses' => $teacher->courses->count() > 0 ? $teacher->courses->count() : '4+',
                    'rating' => '99%',
                ];
            }
        }
    }

    // Fallback seed mentors if database has fewer than 4
    $fallbackMentors = [
        [
            'id' => 1,
            'name' => 'Darren Treadway',
            'role' => 'Data Science & Pure Math Specialist',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
            'students' => '28k+',
            'courses' => '8',
            'rating' => '98%',
        ],
        [
            'id' => 2,
            'name' => 'Prof. Elena Rostova',
            'role' => 'Cambridge Biology & Chemistry Senior Examiner',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
            'students' => '980',
            'courses' => '6',
            'rating' => '98%',
        ],
        [
            'id' => 3,
            'name' => 'Alexander Vance',
            'role' => 'Cambridge Computer Science (9618) Specialist',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
            'students' => '1.5k',
            'courses' => '10',
            'rating' => '100%',
        ],
        [
            'id' => 4,
            'name' => 'Sofia Bennett',
            'role' => 'Checkpoint English & Humanities Moderator',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80',
            'students' => '850',
            'courses' => '5',
            'rating' => '97%',
        ],
    ];

    while (count($mentors) < 4) {
        $idx = count($mentors);
        if (isset($fallbackMentors[$idx])) {
            $mentors[] = $fallbackMentors[$idx];
        } else {
            break;
        }
    }

    // Strictly cap to 4 mentors in 1 row
    $mentors = array_slice($mentors, 0, 4);
@endphp

<section class="meet-mentors-section py-5 position-relative" style="background: var(--lms-bg-body);">
    <div class="container py-3">
        
        <!-- Centered Section Header -->
        <div class="text-center mb-5 max-w-700 mx-auto">
            <div class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase mb-2" 
                 style="background: rgba(114, 62, 195, 0.1); color: var(--lms-accent, #723EC3); font-size: 0.76rem; letter-spacing: 0.8px;">
                <i class="bi bi-award-fill me-1"></i> Verified Cambridge Faculty
            </div>
            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary); letter-spacing: -0.5px;">
                {{ $title }}
            </h2>
            <p class="text-secondary small mb-0" style="font-size: 1rem;">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Mentors Row: Exactly 4 in a single line on desktop (col-lg-3, col-md-6, col-6) -->
        <div class="row g-3 g-lg-4 justify-content-center">
            @foreach($mentors as $mentor)
                <div class="col-6 col-md-3 col-lg-3">
                    <a href="{{ route('mentors.show', $mentor['id']) }}" class="text-decoration-none text-reset d-block h-100">
                        <div class="lms-mentor-card h-100 p-3 p-xl-3.5 rounded-4 shadow-sm border transition-all text-center d-flex flex-column justify-content-between"
                             style="background: var(--lms-surface-card); border-color: var(--lms-border) !important; border-radius: 20px;">
                            
                            <!-- Top Avatar Frame -->
                            <div class="mentor-avatar-wrapper rounded-4 overflow-hidden mb-3 position-relative mx-auto w-100 shadow-xs" 
                                 style="height: 190px; background: linear-gradient(180deg, #F8FAFC 0%, #EDE9FE 100%); border: 1px solid rgba(114, 62, 195, 0.12);">
                                
                                <div class="position-absolute top-50 start-50 translate-middle rounded-circle" 
                                     style="width: 120px; height: 120px; background: rgba(114, 62, 195, 0.20); filter: blur(24px); pointer-events: none;"></div>

                                <img src="{{ $mentor['avatar'] }}" 
                                     alt="{{ $mentor['name'] }}" 
                                     class="w-100 h-100"
                                     style="object-fit: cover; object-position: center 15%;">
                                
                                <span class="position-absolute bottom-2 end-2 badge bg-dark bg-opacity-75 text-white font-mono rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    <i class="bi bi-star-fill text-warning me-0.5"></i>{{ $mentor['rating'] }}
                                </span>
                            </div>

                            <!-- Mentor Info -->
                            <div class="px-1 mb-2">
                                <h4 class="fw-bold mb-1 text-truncate" style="color: var(--lms-text-primary); font-family: var(--lms-font-heading); font-size: 1.05rem;">
                                    {{ $mentor['name'] }}
                                </h4>
                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-truncate d-inline-block w-100" 
                                      style="background: rgba(114, 62, 195, 0.08); color: var(--lms-accent, #723EC3); font-size: 0.7rem;">
                                    {{ Str::limit($mentor['role'], 30) }}
                                </span>
                            </div>

                            <!-- Mini Stats -->
                            <div class="pt-2 border-top d-flex justify-content-around align-items-center" style="border-color: var(--lms-border) !important;">
                                <div>
                                    <div class="fw-bold small font-mono" style="color: var(--lms-accent, #723EC3); line-height: 1;">
                                        {{ $mentor['students'] }}
                                    </div>
                                    <div class="text-secondary" style="font-size: 0.65rem;">Scholars</div>
                                </div>
                                <div class="border-end h-75" style="border-color: var(--lms-border) !important;"></div>
                                <div>
                                    <div class="fw-bold small font-mono" style="color: var(--lms-accent, #723EC3); line-height: 1;">
                                        {{ $mentor['courses'] }}
                                    </div>
                                    <div class="text-secondary" style="font-size: 0.65rem;">Courses</div>
                                </div>
                            </div>

                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Prominent View More Button -->
        <div class="text-center mt-5">
            <a href="{{ route('mentors.index') }}" class="tj-btn-primary py-2.5 px-4 shadow-sm text-decoration-none">
                <span>View All Mentors</span>
                <span class="btn-icon ms-1"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

    </div>
</section>
