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
                                                @switch($mod->module_type)
                                                    @case('VIDEO')
                                                        <i class="bi bi-play-circle text-danger"></i>
                                                        @break
                                                    @case('SCORM')
                                                        <i class="bi bi-box-seam text-warning"></i>
                                                        @break
                                                    @case('QUIZ')
                                                        <i class="bi bi-patch-question text-info"></i>
                                                        @break
                                                    @case('ASSIGNMENT')
                                                        <i class="bi bi-file-earmark-arrow-up text-primary"></i>
                                                        @break
                                                    @case('ATTENDANCE')
                                                        <i class="bi bi-qr-code text-success"></i>
                                                        @break
                                                    @default
                                                        <i class="bi bi-file-text text-secondary"></i>
                                                @endswitch
                                                <span class="small text-truncate">Module {{ $mod->sort_order + 1 }} ({{ $mod->module_type }})</span>
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
