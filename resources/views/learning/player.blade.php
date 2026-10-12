@extends('layouts.app')

@section('title', $course->title . ' - Player')

@section('content')
<div class="container-fluid px-0" 
     x-data="{
         sidebarOpen: true,
         currentModuleId: {{ $activeModule ? $activeModule->id : 0 }},
         currentModuleType: '{{ $activeModule ? $activeModule->module_type : 'LESSON' }}',
         completedModules: {{ json_encode($completedModuleIds ?? []) }},
         scormCmiStatus: 'not attempted',
         markCompleted(moduleId) {
             if (!this.completedModules.includes(moduleId)) {
                 this.completedModules.push(moduleId);
             }
             fetch('{{ route('learning.module-complete') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                 body: JSON.stringify({ module_id: moduleId })
             }).then(r => r.json()).then(data => {
                 if (data.all_completed) {
                     window.location.reload();
                 }
             });
         }
     }">
    <div class="row g-0">
        <!-- Curriculum Sidebar (Left) -->
        <div class="col-lg-3 col-md-4 player-sidebar p-3" x-show="sidebarOpen">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <a href="{{ route('my-learning') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> {{ $site_settings['label_my_courses'] ?? 'My Learning' }}
                </a>
                <span class="badge bg-primary-subtle text-primary">
                    <span x-text="completedModules.length"></span> / {{ $totalModulesCount ?? 0 }} {{ $site_settings['label_completed'] ?? 'Done' }}
                </span>
            </div>

            <h6 class="fw-bold text-truncate mb-3" title="{{ $course->title }}">{{ $course->title }}</h6>

            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 6px;">
                <div class="progress-bar bg-success" 
                     :style="'width: ' + ({{ $totalModulesCount > 0 ? (100 / $totalModulesCount) : 0 }} * completedModules.length) + '%'"></div>
            </div>

            <!-- Curriculum Accordion -->
            <div class="accordion accordion-flush" id="curriculumAccordion">
                @foreach($course->sections as $sectionIndex => $sec)
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header" id="headingSec{{ $sec->id }}">
                            <button class="accordion-button py-2 px-3 {{ $activeModule && $activeModule->section_id === $sec->id ? '' : 'collapsed' }}" 
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseSec{{ $sec->id }}">
                                <div class="text-truncate pe-2">
                                    <span class="small text-muted d-block">Section {{ $sectionIndex + 1 }}</span>
                                    <span class="fw-semibold">{{ $sec->title }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseSec{{ $sec->id }}" class="accordion-collapse collapse {{ $activeModule && $activeModule->section_id === $sec->id ? 'show' : '' }}" data-bs-parent="#curriculumAccordion">
                            <div class="accordion-body p-1">
                                <ul class="list-group list-group-flush">
                                    @foreach($sec->modules as $mod)
                                        <a href="{{ route('learning.player', ['course' => $course->slug, 'module_id' => $mod->id]) }}" 
                                           class="list-group-item list-group-item-action py-2 px-3 d-flex align-items-center justify-content-between border-0 rounded {{ $activeModule && $activeModule->id === $mod->id ? 'active' : '' }}">
                                            <div class="d-flex align-items-center gap-2 text-truncate">
                                                <span class="curriculum-sidebar-icon">
                                                    @switch($mod->module_type)
                                                        @case('VIDEO')
                                                            <i class="bi bi-play-circle"></i>
                                                            @break
                                                        @case('SCORM')
                                                            <i class="bi bi-box-seam"></i>
                                                            @break
                                                        @case('QUIZ')
                                                            <i class="bi bi-patch-question"></i>
                                                            @break
                                                        @case('ASSIGNMENT')
                                                            <i class="bi bi-file-earmark-arrow-up"></i>
                                                            @break
                                                        @case('LESSON')
                                                            <i class="bi bi-diagram-3"></i>
                                                            @break
                                                        @case('FEEDBACK')
                                                            <i class="bi bi-chat-left-heart"></i>
                                                            @break
                                                        @case('CHOICE')
                                                            <i class="bi bi-ui-radios"></i>
                                                            @break
                                                        @case('ATTENDANCE')
                                                            <i class="bi bi-qr-code-scan"></i>
                                                            @break
                                                        @case('FORUM')
                                                            <i class="bi bi-chat-square-quote"></i>
                                                            @break
                                                        @case('H5P')
                                                            <i class="bi bi-badge-hd"></i>
                                                            @break
                                                        @case('WORKSHOP')
                                                            <i class="bi bi-people"></i>
                                                            @break
                                                        @case('CHAT')
                                                            <i class="bi bi-chat-dots"></i>
                                                            @break
                                                        @case('GLOSSARY')
                                                            <i class="bi bi-spellcheck"></i>
                                                            @break
                                                        @case('WIKI')
                                                            <i class="bi bi-book-half"></i>
                                                            @break
                                                        @case('PAGE')
                                                            <i class="bi bi-file-earmark-richtext"></i>
                                                            @break
                                                        @case('BOOK')
                                                            <i class="bi bi-journal-bookmark"></i>
                                                            @break
                                                        @case('FILE')
                                                            <i class="bi bi-file-earmark-arrow-down"></i>
                                                            @break
                                                        @case('FOLDER')
                                                            <i class="bi bi-folder2-open"></i>
                                                            @break
                                                        @case('URL')
                                                            <i class="bi bi-link-45deg"></i>
                                                            @break
                                                        @case('LABEL')
                                                            <i class="bi bi-card-text"></i>
                                                            @break
                                                        @default
                                                            <i class="bi bi-file-text"></i>
                                                    @endswitch
                                                </span>
                                                <span class="small text-truncate">Module {{ $mod->sort_order + 1 }}: {{ $mod->title ?? $mod->module_type }}</span>
                                            </div>
                                            <span x-show="completedModules.includes({{ $mod->id }})">
                                                <i class="bi bi-check-circle-fill text-success small"></i>
                                            </span>
                                        </a>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Active Module Viewport (Right) -->
        <div class="col-lg-9 col-md-8 player-content p-4 p-md-5 d-flex flex-column justify-content-between">
            <div>
                <!-- Top Toolbar -->
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <button class="btn btn-outline-secondary btn-sm" @click="sidebarOpen = !sidebarOpen" type="button">
                        <i :class="sidebarOpen ? 'bi bi-chevron-bar-left' : 'bi bi-chevron-bar-right'"></i>
                        <span x-text="sidebarOpen ? '{{ $site_settings['label_collapse_menu'] ?? 'Collapse Menu' }}' : '{{ $site_settings['label_expand_menu'] ?? 'Expand Menu' }}'"></span>
                    </button>

                    @if($activeModule)
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm" 
                                    :class="completedModules.includes(currentModuleId) ? 'btn-success' : 'btn-outline-success'"
                                    @click="markCompleted(currentModuleId)">
                                <i class="bi" :class="completedModules.includes(currentModuleId) ? 'bi-check2-circle' : 'bi-circle'"></i>
                                <span x-text="completedModules.includes(currentModuleId) ? '{{ $site_settings['label_completed'] ?? 'Completed' }}' : '{{ $site_settings['label_mark_done'] ?? 'Mark as Done' }}'"></span>
                            </button>
                        </div>
                    @endif
                </div>

                @if($activeModule)
                    <!-- Active Module Viewport Container -->
                    <div class="viewport-box">
                        @switch($activeModule->module_type)
                            @case('VIDEO')
                                <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm mb-4 bg-dark">
                                    <video controls class="w-100 h-100" poster="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200">
                                        <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
                                        Your browser does not support HTML5 video.
                                    </video>
                                </div>
                                <h4 class="fw-bold mb-2">{{ $course->title }}: Video Lecture</h4>
                                <p class="text-secondary">Watch this required instructional module to advance to compliance verification.</p>
                                @break

                            @case('SCORM')
                                <div class="scorm-iframe-container mb-4 shadow">
                                    <!-- SCORM Runtime Bridge iframe -->
                                    <iframe src="{{ route('scorm.launch', ['package_id' => $activeModule->instance_id ?? 1]) }}" 
                                            allow="fullscreen" 
                                            id="scormFrame"></iframe>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 lms-card">
                                    <div>
                                        <div class="fw-bold">{{ $site_settings['label_scorm_runtime'] ?? 'SCORM 1.2 / 2004 Telemetry Interface' }}</div>
                                        <div class="small text-muted">Tracking real-time CMI scores and lesson completion states.</div>
                                    </div>
                                    <span class="badge badge-compliance">cmi.core.lesson_status: <span x-text="scormCmiStatus"></span></span>
                                </div>
                                @break

                            @case('QUIZ')
                                @php
                                    $quiz = \App\Models\Quiz::find($activeModule->instance_id) ?? \App\Models\Quiz::first();
                                @endphp
                                <div class="lms-card p-5 text-center my-4 shadow-sm">
                                    <i class="bi bi-shield-shaded display-3 text-primary mb-3 d-block"></i>
                                    <h3 class="fw-bold mb-2">{{ $quiz ? $quiz->title : 'Modular Knowledge Assessment' }}</h3>
                                    <p class="text-secondary max-w-600 mx-auto mb-4">
                                        {{ $site_settings['msg_quiz_prep'] ?? 'This assessment is monitored by real-time anti-cheat telemetry. Ensure your webcam is enabled and avoid switching browser tabs during the test.' }}
                                    </p>
                                    <div class="d-flex justify-content-center gap-4 text-start font-mono small mb-4">
                                        <div><i class="bi bi-clock me-1 text-primary"></i> {{ $quiz ? $quiz->time_limit_minutes : 30 }} Minutes</div>
                                        <div><i class="bi bi-award me-1 text-success"></i> Passing: {{ $quiz ? $quiz->passing_percentage : 70 }}%</div>
                                        <div><i class="bi bi-eye me-1 text-danger"></i> AI Proctor: Active</div>
                                    </div>
                                    @if($quiz)
                                        <a href="{{ route('quiz.take', ['quiz' => $quiz->id]) }}" class="btn btn-accent btn-lg px-5 shadow">
                                            <i class="bi bi-play-circle-fill me-2"></i> {{ $site_settings['label_start_exam'] ?? 'Launch Examination' }}
                                        </a>
                                    @endif
                                </div>
                                @break

                            @case('ASSIGNMENT')
                                @php
                                    $assignment = \App\Models\Assignment::find($activeModule->instance_id) ?? \App\Models\Assignment::first();
                                @endphp
                                <div class="lms-card p-4 my-3">
                                    <h4 class="fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up text-primary me-2"></i>{{ $assignment ? $assignment->title : 'Practical Submission' }}</h4>
                                    <p class="text-secondary mb-4">{{ $assignment ? $assignment->instructions : 'Upload your required compliance audit evidence or research paper.' }}</p>
                                    
                                    <form action="{{ route('assignments.submit') }}" method="POST" enctype="multipart/form-data" class="border-top pt-3">
                                        @csrf
                                        <input type="hidden" name="assignment_id" value="{{ $assignment ? $assignment->id : 1 }}">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Submission Notes</label>
                                            <textarea name="submission_text" class="form-control" rows="3" placeholder="Provide notes or justifications for your submission..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Upload Document (PDF, DOCX, ZIP)</label>
                                            <input type="file" name="submission_file" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-accent">Submit for Instructor Grading</button>
                                    </form>
                                </div>
                                @break

                            @case('ATTENDANCE')
                                <div class="lms-card p-5 text-center my-4">
                                    <i class="bi bi-qr-code-scan display-4 text-success mb-3 d-block"></i>
                                    <h3 class="fw-bold mb-2">{{ $site_settings['label_attendance_checkin'] ?? 'Geofenced Rolling QR Check-In' }}</h3>
                                    <p class="text-secondary mb-4">Validate your classroom or corporate session attendance using rolling QR codes.</p>
                                    <a href="{{ route('attendance.qr', ['course_id' => $course->id]) }}" class="btn btn-success btn-lg px-4" target="_blank">
                                        <i class="bi bi-camera me-2"></i> Open Rolling QR Scanner
                                    </a>
                                </div>
                                @break

                            @case('LESSON')
                                @php
                                    $lesson = \App\Models\CourseLesson::find($activeModule->instance_id) ?? \App\Models\CourseLesson::first();
                                    $firstPage = $lesson?->pages()->first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-primary-subtle text-primary fw-bold font-mono">mod_lesson</span>
                                        <span class="badge bg-secondary-subtle text-secondary">Adaptive Branching</span>
                                    </div>
                                    <h3 class="fw-bold mb-3">{{ $lesson ? $lesson->title : 'Adaptive Instructional Lesson' }}</h3>
                                    <p class="text-secondary mb-4">{{ $lesson ? $lesson->intro : 'Engage with this adaptive lesson tree: your responses determine branching pathways.' }}</p>
                                    
                                    <div class="p-4 rounded-3 border mb-4" style="background: var(--lms-bg-secondary);">
                                        <h5 class="fw-bold mb-2">{{ $firstPage ? $firstPage->title : 'Core Concept Branch' }}</h5>
                                        <div class="text-secondary mb-4">
                                            {{ $firstPage ? $firstPage->contents : 'Review standard operational governance procedures before choosing how your department responds to critical compliance triggers.' }}
                                        </div>
                                        @if($firstPage && !empty($firstPage->answers_json))
                                            <div class="list-group">
                                                @foreach($firstPage->answers_json as $ans)
                                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                                        <span>{{ $ans['text'] ?? 'Branch Decision Option' }}</span>
                                                        <i class="bi bi-arrow-right-circle text-primary"></i>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> Review Prerequisite</button>
                                                <button type="button" class="btn btn-accent"><i class="bi bi-arrow-right me-1"></i> Advance to Next Branch</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @break

                            @case('FEEDBACK')
                                @php
                                    $feedback = \App\Models\CourseFeedback::find($activeModule->instance_id) ?? \App\Models\CourseFeedback::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-info-subtle text-info fw-bold font-mono">mod_feedback</span>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $feedback && $feedback->is_anonymous ? 'Anonymous Evaluation' : 'Identified Response' }}</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $feedback ? $feedback->title : 'Course Feedback & Survey' }}</h3>
                                    <p class="text-secondary mb-4">{{ $feedback ? $feedback->intro : 'Please rate the instructional delivery, clarity of materials, and relevance to your certification targets.' }}</p>
                                    
                                    <form class="p-4 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">1. Rate instructional pacing & module structure:</label>
                                            <div class="d-flex gap-3">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="rate_pacing" id="rate_{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="rate_{{ $i }}">{{ $i }} {{ $i === 5 ? '(Excellent)' : '' }}</label>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">2. Qualitative feedback or suggested improvements:</label>
                                            <textarea class="form-control" rows="3" placeholder="Provide anonymous course feedback..."></textarea>
                                        </div>
                                        <button type="button" class="btn btn-accent" @click="alert('Feedback response submitted successfully!')">Submit Survey Response</button>
                                    </form>
                                </div>
                                @break

                            @case('CHOICE')
                                @php
                                    $choice = \App\Models\CourseChoice::find($activeModule->instance_id) ?? \App\Models\CourseChoice::first();
                                    $options = $choice && is_array($choice->options_json) ? $choice->options_json : ['Morning Cohort (09:00 UTC)', 'Afternoon Cohort (14:00 UTC)', 'Async Self-Paced Track'];
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-success-subtle text-success fw-bold font-mono">mod_choice</span>
                                        <span class="badge bg-secondary-subtle text-secondary">Single Question Poll</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $choice ? $choice->title : 'Quick Cohort Selection Poll' }}</h3>
                                    <p class="text-secondary mb-4">{{ $choice ? $choice->intro : 'Select your cohort preference to reserve your virtual proctoring slot.' }}</p>
                                    
                                    <div class="p-4 rounded-3 border mb-3" style="background: var(--lms-bg-secondary);">
                                        @foreach($options as $idx => $opt)
                                            <div class="form-check p-3 rounded mb-2 border" style="background: var(--lms-surface-card);">
                                                <input class="form-check-input" type="radio" name="course_choice" id="choice_{{ $idx }}" value="{{ $opt }}" {{ $idx === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold ms-2" for="choice_{{ $idx }}">{{ is_array($opt) ? ($opt['text'] ?? 'Option') : $opt }}</label>
                                            </div>
                                        @endforeach
                                        <button type="button" class="btn btn-accent mt-2" @click="alert('Choice recorded successfully!')">Save My Selection</button>
                                    </div>
                                </div>
                                @break

                            @case('FORUM')
                                @php
                                    $forum = \App\Models\CourseForum::find($activeModule->instance_id) ?? \App\Models\CourseForum::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary-subtle text-primary fw-bold font-mono">mod_forum</span>
                                            <span class="badge bg-secondary-subtle text-secondary">Mode: {{ $forum?->forum_type ?? 'STANDARD' }}</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-circle me-1"></i> Add Discussion Topic</button>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $forum ? $forum->title : 'Course Discussion & Q&A Forum' }}</h3>
                                    <p class="text-secondary mb-4">{{ $forum ? $forum->intro : 'Collaborate with instructors and cohort peers on course topics and compliance scenarios.' }}</p>
                                    
                                    <div class="list-group shadow-sm">
                                        <div class="list-group-item p-3 border-0 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="fw-bold mb-0 text-primary">Best practices for multi-cloud security audits</h6>
                                                <span class="small text-muted font-mono">Yesterday &bull; 6 replies</span>
                                            </div>
                                            <div class="small text-secondary">Started by Alexander Vance &bull; Category: Architecture &amp; Governance</div>
                                        </div>
                                        <div class="list-group-item p-3 border-0">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="fw-bold mb-0 text-primary">Clarification on Annex A.12 controls</h6>
                                                <span class="small text-muted font-mono">2 days ago &bull; 14 replies</span>
                                            </div>
                                            <div class="small text-secondary">Started by Dr. Robert Finch &bull; Category: ISO 27001 Implementation</div>
                                        </div>
                                    </div>
                                </div>
                                @break

                            @case('H5P')
                                @php
                                    $h5p = \App\Models\CourseH5p::find($activeModule->instance_id) ?? \App\Models\CourseH5p::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                                        <span class="badge bg-warning-subtle text-warning fw-bold font-mono">mod_h5p</span>
                                        <span class="badge bg-secondary-subtle text-secondary">HTML5 Interactive Rich Media</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $h5p ? $h5p->title : 'Interactive Threat Simulation (H5P)' }}</h3>
                                    <p class="text-secondary max-w-600 mx-auto mb-4">Engage with real-time video checkpoints, branching checkpoints, and interactive flashcards. Grade telemetry syncs automatically to your course gradebook.</p>
                                    <div class="p-5 border rounded-4 bg-dark text-white mb-4 position-relative overflow-hidden shadow">
                                        <i class="bi bi-play-btn-fill display-3 text-warning mb-3 d-block"></i>
                                        <h5 class="fw-bold">H5P Runtime Engine Initialized</h5>
                                        <div class="small text-white-50">Interactive Video &bull; Pop-Up Checkpoint Quizzes &bull; Grade Sync Active</div>
                                        <button class="btn btn-warning mt-3 px-4 fw-bold" @click="alert('H5P Interactive Container Launched!')">Start Interactive Experience</button>
                                    </div>
                                </div>
                                @break

                            @case('WORKSHOP')
                                @php
                                    $workshop = \App\Models\CourseWorkshop::find($activeModule->instance_id) ?? \App\Models\CourseWorkshop::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-secondary-subtle text-secondary fw-bold font-mono">mod_workshop</span>
                                        <span class="badge bg-primary-subtle text-primary">Multi-Phase Peer Assessment</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $workshop ? $workshop->title : 'Peer Review Workshop' }}</h3>
                                    <p class="text-secondary mb-4">{{ $workshop ? $workshop->intro : 'Dual-grade evaluation: 80% submission quality + 20% peer grading accuracy.' }}</p>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded-3 bg-light text-center">
                                                <div class="small text-muted fw-bold">Phase 1</div>
                                                <div class="fw-semibold">Setup</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded-3 bg-primary text-white text-center shadow-sm">
                                                <div class="small text-white-50 fw-bold">Phase 2</div>
                                                <div class="fw-semibold">Submission (Current)</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded-3 bg-light text-center">
                                                <div class="small text-muted fw-bold">Phase 3</div>
                                                <div class="fw-semibold">Assessment</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded-3 bg-light text-center">
                                                <div class="small text-muted fw-bold">Phase 4</div>
                                                <div class="fw-semibold">Grading &amp; Close</div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-accent"><i class="bi bi-upload me-1"></i> Submit Work for Peer Review</button>
                                </div>
                                @break

                            @case('CHAT')
                                <div class="lms-card p-4 my-3 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <div>
                                            <span class="badge bg-success-subtle text-success fw-bold font-mono">mod_chat</span>
                                            <h5 class="fw-bold mb-0 mt-1">Live Synchronous Course Chat</h5>
                                        </div>
                                        <span class="badge bg-success"><i class="bi bi-circle-fill me-1 small"></i> 4 Active Online</span>
                                    </div>
                                    <div class="p-3 rounded-3 border mb-3" style="min-height: 200px; background: var(--lms-bg-secondary);">
                                        <div class="mb-2"><strong class="text-primary">Instructor Marcus:</strong> Welcome everyone to today's live office hours!</div>
                                        <div class="mb-2"><strong class="text-secondary">Student Alice:</strong> Can you review the Annex A.5 control matrix?</div>
                                        <div class="mb-2"><strong class="text-primary">Instructor Marcus:</strong> Certainly, please open the Book resource in Module 2.</div>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Type your message to the class...">
                                        <button class="btn btn-accent"><i class="bi bi-send-fill"></i></button>
                                    </div>
                                </div>
                                @break

                            @case('GLOSSARY')
                                @php
                                    $glossary = \App\Models\CourseGlossary::find($activeModule->instance_id) ?? \App\Models\CourseGlossary::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-info-subtle text-info fw-bold font-mono">mod_glossary</span>
                                        <span class="badge bg-secondary-subtle text-secondary">Course Lexicon &amp; Terms</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $glossary ? $glossary->title : 'Cybersecurity & Compliance Lexicon' }}</h3>
                                    <p class="text-secondary mb-4">{{ $glossary ? $glossary->intro : 'Search, review, and contribute to standard enterprise definitions and terms.' }}</p>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-4">
                                        @foreach(range('A', 'Z') as $letter)
                                            <button class="btn btn-sm btn-outline-secondary px-2 py-1">{{ $letter }}</button>
                                        @endforeach
                                    </div>
                                    <div class="border rounded-3 p-3 mb-2" style="background: var(--lms-surface-card);">
                                        <h5 class="fw-bold text-primary mb-1">ISMS (Information Security Management System)</h5>
                                        <p class="text-secondary small mb-0">A systematic approach to managing sensitive company information so that it remains secure, encompassing people, processes and IT systems by applying a risk management process.</p>
                                    </div>
                                </div>
                                @break

                            @case('WIKI')
                                @php
                                    $wiki = \App\Models\CourseWiki::find($activeModule->instance_id) ?? \App\Models\CourseWiki::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-warning-subtle text-warning fw-bold font-mono">mod_wiki</span>
                                            <span class="badge bg-secondary-subtle text-secondary">Collaborative Documentation</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i> Edit This Page</button>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $wiki ? $wiki->title : 'Incident Response Playbook Wiki' }}</h3>
                                    <p class="text-secondary mb-4">Co-authored documentation maintained by course participants and certified auditors.</p>
                                    <div class="p-4 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                        <h4>Section 1: Initial Severity Triage</h4>
                                        <p class="text-secondary">When a potential data breach or control deficiency is detected, the lead auditor must classify the incident into Level 1 (Critical), Level 2 (Substantial), or Level 3 (Routine Observation) within 15 minutes.</p>
                                    </div>
                                </div>
                                @break

                            @case('PAGE')
                            @case('BOOK')
                            @case('FILE')
                            @case('FOLDER')
                            @case('URL')
                            @case('LABEL')
                                @php
                                    $res = \App\Models\CourseResource::find($activeModule->instance_id) ?? \App\Models\CourseResource::first();
                                @endphp
                                <div class="lms-card p-4 p-md-5 my-3 shadow-sm">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-primary-subtle text-primary fw-bold font-mono">mod_{{ strtolower($activeModule->module_type) }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary">Content Resource</span>
                                    </div>
                                    <h3 class="fw-bold mb-2">{{ $res ? $res->title : ($course->title . ' - Learning Resource') }}</h3>
                                    <div class="prose text-secondary mb-4">
                                        {!! $res ? $res->content_html : '<p>Comprehensive study materials, guidelines, and reference assets curated for this module.</p>' !!}
                                    </div>
                                    @if($activeModule->module_type === 'FILE' || ($res && $res->file_path))
                                        <a href="#" class="btn btn-outline-primary" @click.prevent="alert('File download initiated: Auditor_Reference_Pack.pdf')">
                                            <i class="bi bi-download me-2"></i> Download Document Reference
                                        </a>
                                    @elseif($activeModule->module_type === 'URL' || ($res && $res->external_url))
                                        <a href="{{ $res ? $res->external_url : 'https://www.iso.org' }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right me-2"></i> Open External Resource
                                        </a>
                                    @endif
                                </div>
                                @break

                            @default
                                <div class="lms-card p-4 p-md-5">
                                    <h3 class="fw-bold mb-3">{{ $course->title }} - Module Overview</h3>
                                    <div class="lead text-secondary mb-4">
                                        Detailed operational lesson curriculum, study guidance, and compliance requirements.
                                    </div>
                                    <hr>
                                    <div class="prose">
                                        <h5>Key Learning Outcomes:</h5>
                                        <ul>
                                            <li>Master organizational standards and governance workflows.</li>
                                            <li>Complete mandatory checkpoints to fulfill audit recertification criteria.</li>
                                            <li>Demonstrate competency in practical scenarios and exam assessments.</li>
                                        </ul>
                                    </div>
                                </div>
                        @endswitch
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-all display-1 text-success mb-3"></i>
                        <h2 class="fw-bold">Course Completed!</h2>
                        <p class="text-secondary mb-4">You have successfully completed all modules for this course.</p>
                        <a href="{{ route('certificates.my') }}" class="btn btn-accent btn-lg">
                            <i class="bi bi-award-fill me-2"></i> Claim Your Certificate
                        </a>
                    </div>
                @endif
            </div>

            <!-- Bottom Navigation Bar -->
            <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-5">
                <a href="{{ route('my-learning') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i> Dashboard
                </a>
                @if($activeModule)
                    <button class="btn btn-accent" @click="markCompleted(currentModuleId)">
                        Next Activity <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
