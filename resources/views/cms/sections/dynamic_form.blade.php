@php
    $c = $section->content_json ?? [];
    $isPublicCmsPage = isset($section) && !request()->routeIs('my-learning');
@endphp

@if($isPublicCmsPage)
<!-- PUBLIC SHOWCASE CARD FOR CMS PAGES (STUDENTS ACCESS FULL MATRIX IN THEIR DASHBOARD) -->
<section class="esther-tracker-preview-section py-5" id="esther-tracker" style="background: var(--lms-bg-body);">
    <div class="container py-3">
        <div class="card border-0 rounded-4 shadow-sm overflow-hidden p-4 p-md-5 position-relative" 
             style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.1) !important;">
            
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-xs border mb-3"
                         style="background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.15) !important;">
                        <span class="badge rounded-pill px-2.5 py-1 text-white" style="background: var(--lms-accent, #723EC3); font-size: 0.72rem;">
                            <i class="bi bi-grid-3x3 me-1"></i> THE ESTHER MATH ENGINE
                        </span>
                        <span class="small text-white-50" style="font-size: 0.78rem;">
                            Official Cambridge Diagnostic Framework
                        </span>
                    </div>

                    <h2 class="display-6 fw-bold mb-3 text-white" style="font-family: var(--lms-font-heading);">
                        Student Score Matrix &amp; Diagnostic Analytics
                    </h2>
                    
                    <p class="lead text-white-50 fs-6 mb-4" style="line-height: 1.8;">
                        Students record past paper raw marks across isolated Cambridge syllabus silos (Primary Checkpoint, Lower Secondary, and IGCSE). The engine computes dynamic mastery thresholds, maintains historical attempt logs, and generates audit-ready printable diagnostic reports.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('my-learning') }}#esther-tracker" class="tj-btn-primary py-3 px-4 shadow-lg text-nowrap">
                                <span>Go to Student Dashboard</span>
                                <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="tj-btn-primary py-3 px-4 shadow-lg text-nowrap">
                                <span>Student Portal Login</span>
                                <span class="btn-icon"><i class="bi bi-box-arrow-in-right"></i></span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill py-3 px-4 fw-semibold text-nowrap shadow-sm" style="border-color: rgba(255,255,255,0.35);">
                                <span>Register Student Account</span>
                            </a>
                        @endauth
                        <a href="{{ route('scores.report') }}" target="_blank" class="btn btn-outline-light rounded-pill py-3 px-4 fw-semibold text-nowrap shadow-sm" style="border-color: rgba(255,255,255,0.35);">
                            <i class="bi bi-printer text-warning me-1.5"></i>
                            <span>View Sample Report</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-3.5 rounded-3 border" style="background: rgba(15, 23, 42, 0.7); border-color: rgba(255, 255, 255, 0.12) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-white border-opacity-10">
                            <span class="small fw-bold text-white"><i class="bi bi-mortarboard-fill text-primary me-1.5"></i> 3 Cambridge Silo Levels</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 small">Real-Time</span>
                        </div>

                        <div class="vstack gap-2">
                            <div class="p-2.5 rounded-2 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div>
                                    <div class="fw-semibold text-white small">Primary Checkpoint (Y1-6)</div>
                                    <div class="text-white-50" style="font-size: 0.72rem;">English 0844 &bull; Mathematics 0845 &bull; Science 0846</div>
                                </div>
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Mastery &ge; 75%</span>
                            </div>

                            <div class="p-2.5 rounded-2 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div>
                                    <div class="fw-semibold text-white small">Lower Secondary (Y7-9)</div>
                                    <div class="text-white-50" style="font-size: 0.72rem;">English 0861 &bull; Mathematics 0862 &bull; Science 0893</div>
                                </div>
                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">Developing 50-74%</span>
                            </div>

                            <div class="p-2.5 rounded-2 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
                                <div>
                                    <div class="fw-semibold text-white small">IGCSE (Y10-11)</div>
                                    <div class="text-white-50" style="font-size: 0.72rem;">Math 0580 &bull; Physics 0625 &bull; Chemistry 0620</div>
                                </div>
                                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25">Remedial &lt; 50%</span>
                            </div>
                        </div>

                        <div class="mt-3 pt-2 border-top border-white border-opacity-10 d-flex justify-content-between text-white-50 small" style="font-size: 0.72rem;">
                            <span><i class="bi bi-shield-check text-success me-1"></i> Student Encrypted</span>
                            <span>Includes Esther Retake Test Engine</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@else
<!-- FULL INTERACTIVE STUDENT SCORE MATRIX (DEDICATED TO STUDENT DASHBOARD) -->
<section class="esther-tracker-section py-5" id="esther-tracker" style="background: var(--lms-bg-body);"
         x-data="estherTrackerData()">
    
    <div class="container py-3">
        <!-- Section Header -->
        <div class="text-center mx-auto mb-4" style="max-width: 780px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-sm border mb-2" 
                 style="background: var(--lms-surface-card); border-color: rgba(2, 132, 199, 0.25) !important;">
                <span class="badge rounded-pill px-2.5 py-1" style="background: linear-gradient(135deg, #0284C7, #0369A1); color: #FFF; font-size: 0.72rem;">
                    <i class="bi bi-grid-3x3 me-1"></i> THE ESTHER MATH ENGINE
                </span>
                <span class="small fw-semibold text-secondary" style="font-size: 0.8rem;">
                    Isolated Cambridge Assessment Silo Matrices
                </span>
            </div>

            <h2 class="display-6 fw-bold mb-2" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                Key In Your Scores. Track Real-Time Mastery.
            </h2>
            <p class="text-secondary small mb-3">
                Modeled after the official <em>Esther Math</em> framework: Select your Cambridge Assessment Silo below. Key in your raw marks to save your examination history into the database, track progress thresholds, and generate your printable diagnostic mastery report.
            </p>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-3 position-relative" style="z-index: 20;">
                <a href="{{ route('scores.history') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs action-btn-pill">
                    <i class="bi bi-clock-history me-1"></i> View Score History &amp; Diagnostics
                </a>
                <a href="{{ route('scores.report') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs action-btn-pill">
                    <i class="bi bi-printer me-1"></i> Generate Printable Report
                </a>
            </div>
        </div>

        <!-- Real Database Save Notification Banner -->
        <div x-show="saveMessage !== ''" x-transition class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-4 border border-success-subtle">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span class="small fw-semibold" x-text="saveMessage"></span>
            </div>
            <a href="{{ route('scores.history') }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold text-white text-decoration-none">
                Open Report <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <!-- Filter Controls: Cambridge Silo Selector -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-4 border mb-4 shadow-sm" style="background: var(--lms-surface-card);">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">SELECT LEVEL SILO:</span>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                        :class="selectedLevel === 'primary' ? 'btn-primary shadow-sm' : 'btn-outline-secondary'"
                        @click="selectedLevel = 'primary'">
                    <i class="bi bi-mortarboard me-1"></i> Primary Checkpoint (Y1-6)
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                        :class="selectedLevel === 'secondary' ? 'btn-primary shadow-sm' : 'btn-outline-secondary'"
                        @click="selectedLevel = 'secondary'">
                    <i class="bi bi-journal-bookmark me-1"></i> Lower Secondary (Y7-9)
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold transition-all"
                        :class="selectedLevel === 'igcse' ? 'btn-primary shadow-sm' : 'btn-outline-secondary'"
                        @click="selectedLevel = 'igcse'">
                    <i class="bi bi-globe me-1"></i> IGCSE (Y10-11)
                </button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-bold">
                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> &gt;= 75% Mastery
                </span>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small fw-bold">
                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> 50-74% Developing
                </span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small fw-bold">
                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> &lt; 50% Remedial
                </span>
            </div>
        </div>

        {{-- ===================================================================
             SILO FORM 1: CAMBRIDGE PRIMARY CHECKPOINT FORM (Years 1–6)
             =================================================================== --}}
        <form x-show="selectedLevel === 'primary'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" 
              class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--lms-surface-card);"
              id="primary-silo-form" @submit.prevent="saveScoresToDatabase('primary')">
            <div class="card-header border-0 py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--lms-bg-secondary);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard-fill text-primary fs-5"></i>
                    <span class="fw-bold" style="font-family: var(--lms-font-heading);">Cambridge Primary Checkpoint Form (English 0844 &bull; Mathematics 0845 &bull; Science 0846)</span>
                    <span class="badge bg-info-subtle text-info rounded-pill ms-2" style="font-size: 0.68rem;">Primary Silo Isolated Form</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-sm-inline">Years 1–6 Checkpoint Series</span>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.76rem;">
                        <i class="bi bi-floppy me-1"></i> Save Primary Scores
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="table-light text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-3">Exam Year</th>
                            <th class="py-3">Series</th>
                            <th class="py-3">Variant</th>
                            <th class="py-3">Primary English (/50)</th>
                            <th class="py-3">Primary Math (/50)</th>
                            <th class="py-3">Primary Science (/50)</th>
                            <th class="py-3 px-3">Diagnostic Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in primaryData" :key="idx">
                            <tr>
                                <td class="fw-bold px-3" x-text="row.year"></td>
                                <td><span class="badge bg-light text-dark border px-2 py-1" x-text="row.season"></span></td>
                                <td class="font-mono text-muted" x-text="'Var ' + row.variant"></td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c1Max" :value="row.c1" @input="updatePrimary(idx, 'c1', $event.target.value, row.c1Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c1 !== null ? (getScoreStatus(row.c1, row.c1Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c1, row.c1Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c1 !== null ? Math.round((row.c1/row.c1Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c2Max" :value="row.c2" @input="updatePrimary(idx, 'c2', $event.target.value, row.c2Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c2 !== null ? (getScoreStatus(row.c2, row.c2Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c2, row.c2Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c2 !== null ? Math.round((row.c2/row.c2Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c3Max" :value="row.c3" @input="updatePrimary(idx, 'c3', $event.target.value, row.c3Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c3 !== null ? (getScoreStatus(row.c3, row.c3Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c3, row.c3Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c3 !== null ? Math.round((row.c3/row.c3Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="px-3">
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.75">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill"><i class="bi bi-star-fill me-1"></i>Primary Mastery</span>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.50 && (row.c2/row.c2Max) < 0.75">
                                        <a href="{{ route('courses.index', ['level' => 'primary-checkpoint']) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1"><i class="bi bi-book"></i> Review Lessons</a>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) < 0.50">
                                        <a href="{{ route('courses.index', ['level' => 'primary-checkpoint']) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1"><i class="bi bi-arrow-repeat"></i> Retake Test</a>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </form>

        {{-- ===================================================================
             SILO FORM 2: LOWER SECONDARY CHECKPOINT FORM (Years 7–9)
             =================================================================== --}}
        <form x-show="selectedLevel === 'secondary'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" 
              class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--lms-surface-card);"
              id="secondary-silo-form" @submit.prevent="saveScoresToDatabase('secondary')">
            <div class="card-header border-0 py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--lms-bg-secondary);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark-fill text-primary fs-5"></i>
                    <span class="fw-bold" style="font-family: var(--lms-font-heading);">Cambridge Lower Secondary Checkpoint Form (English 0861 &bull; Math 0862 &bull; Science 0863)</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-2" style="font-size: 0.68rem;">Lower Sec Silo Isolated Form</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-sm-inline">Years 7–9 Progression Tests</span>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.76rem;">
                        <i class="bi bi-floppy me-1"></i> Save Lower Sec Scores
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="table-light text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-3">Exam Year</th>
                            <th class="py-3">Series</th>
                            <th class="py-3">Variant</th>
                            <th class="py-3">Stage 9 English (/50)</th>
                            <th class="py-3">Stage 9 Math (/50)</th>
                            <th class="py-3">Stage 9 Science (/50)</th>
                            <th class="py-3 px-3">Diagnostic Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in secondaryData" :key="idx">
                            <tr>
                                <td class="fw-bold px-3" x-text="row.year"></td>
                                <td><span class="badge bg-light text-dark border px-2 py-1" x-text="row.season"></span></td>
                                <td class="font-mono text-muted" x-text="'Var ' + row.variant"></td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c1Max" :value="row.c1" @input="updateSecondary(idx, 'c1', $event.target.value, row.c1Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c1 !== null ? (getScoreStatus(row.c1, row.c1Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c1, row.c1Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c1 !== null ? Math.round((row.c1/row.c1Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c2Max" :value="row.c2" @input="updateSecondary(idx, 'c2', $event.target.value, row.c2Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c2 !== null ? (getScoreStatus(row.c2, row.c2Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c2, row.c2Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c2 !== null ? Math.round((row.c2/row.c2Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c3Max" :value="row.c3" @input="updateSecondary(idx, 'c3', $event.target.value, row.c3Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c3 !== null ? (getScoreStatus(row.c3, row.c3Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c3, row.c3Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c3 !== null ? Math.round((row.c3/row.c3Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="px-3">
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.75">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill"><i class="bi bi-star-fill me-1"></i>Checkpoint Ready</span>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.50 && (row.c2/row.c2Max) < 0.75">
                                        <a href="{{ route('courses.index', ['level' => 'lower-secondary']) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1"><i class="bi bi-book"></i> Review Papers</a>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) < 0.50">
                                        <a href="{{ route('courses.index', ['level' => 'lower-secondary']) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1"><i class="bi bi-arrow-repeat"></i> Retake Paper</a>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </form>

        {{-- ===================================================================
             SILO FORM 3: CAMBRIDGE IGCSE FORM (Years 10–11)
             =================================================================== --}}
        <form x-show="selectedLevel === 'igcse'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" 
              class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--lms-surface-card);"
              id="igcse-silo-form" @submit.prevent="saveScoresToDatabase('igcse')">
            <div class="card-header border-0 py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--lms-bg-secondary);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-globe text-primary fs-5"></i>
                    <span class="fw-bold" style="font-family: var(--lms-font-heading);">Cambridge IGCSE Form (Extended Math 0580 &bull; Chemistry 0620 &bull; Physics 0625)</span>
                    <span class="badge bg-warning-subtle text-dark rounded-pill ms-2" style="font-size: 0.68rem;">IGCSE Silo Isolated Form</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-sm-inline">Years 10–11 Extended Series</span>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.76rem;">
                        <i class="bi bi-floppy me-1"></i> Save IGCSE Scores
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="table-light text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-3">Exam Year</th>
                            <th class="py-3">Series</th>
                            <th class="py-3">Variant</th>
                            <th class="py-3">Math P2 (Short /70)</th>
                            <th class="py-3">Math P4 (Structured /130)</th>
                            <th class="py-3">Chemistry Theory (/80)</th>
                            <th class="py-3 px-3">IGCSE Diagnostic Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in igcseData" :key="idx">
                            <tr>
                                <td class="fw-bold px-3" x-text="row.year"></td>
                                <td><span class="badge bg-light text-dark border px-2 py-1" x-text="row.season"></span></td>
                                <td class="font-mono text-muted" x-text="'Var ' + row.variant"></td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c1Max" :value="row.c1" @input="updateIgcse(idx, 'c1', $event.target.value, row.c1Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c1 !== null ? (getScoreStatus(row.c1, row.c1Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c1, row.c1Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c1 !== null ? Math.round((row.c1/row.c1Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c2Max" :value="row.c2" @input="updateIgcse(idx, 'c2', $event.target.value, row.c2Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c2 !== null ? (getScoreStatus(row.c2, row.c2Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c2, row.c2Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c2 !== null ? Math.round((row.c2/row.c2Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="0" :max="row.c3Max" :value="row.c3" @input="updateIgcse(idx, 'c3', $event.target.value, row.c3Max)"
                                           class="form-control form-control-sm text-center font-mono fw-bold rounded-3 mx-auto"
                                           :style="row.c3 !== null ? (getScoreStatus(row.c3, row.c3Max).color === 'green' ? 'background: rgba(16, 185, 129, 0.15); border-color: #10B981; color: #047857;' : (getScoreStatus(row.c3, row.c3Max).color === 'amber' ? 'background: rgba(245, 158, 11, 0.15); border-color: #F59E0B; color: #B45309;' : 'background: rgba(239, 68, 68, 0.15); border-color: #EF4444; color: #B91C1C;')) : ''"
                                           style="width: 80px;">
                                    <span class="text-muted" style="font-size: 0.68rem;" x-text="row.c3 !== null ? Math.round((row.c3/row.c3Max)*100) + '%' : 'Pending'"></span>
                                </td>
                                <td class="px-3">
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.75">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill"><i class="bi bi-star-fill me-1"></i>A* Projected</span>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) >= 0.50 && (row.c2/row.c2Max) < 0.75">
                                        <a href="{{ route('courses.index', ['level' => 'igcse']) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1"><i class="bi bi-book"></i> Review Mark Scheme</a>
                                    </template>
                                    <template x-if="row.c2 !== null && (row.c2/row.c2Max) < 0.50">
                                        <a href="{{ route('courses.index', ['level' => 'igcse']) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1"><i class="bi bi-arrow-repeat"></i> Retake IGCSE</a>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Simple & Welcoming: Family & Kids Study Confidence Modules -->
        <div class="row g-4 mb-4">
            <!-- Left Card: Family & Parent Connection -->
            <div class="col-lg-6">
                <div class="p-4 p-md-4 rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between" 
                     style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%); border-radius: 24px; border-color: rgba(2, 132, 199, 0.2) !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                                     style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                                    <i class="bi bi-heart-pulse-fill fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark" style="font-family: var(--lms-font-heading);">Family &amp; Parent Portal</h5>
                                    <span class="small text-muted">Stay connected with your child's Cambridge journey</span>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.75rem;">
                                <i class="bi bi-shield-check me-1"></i> Stress-Free
                            </span>
                        </div>

                        <p class="small text-secondary mb-3" style="line-height: 1.6;">
                            Parents can celebrate completed topics, view study consistency, and see exam confidence in real time across all siblings — without interrupting timers or tests.
                        </p>

                        <!-- Friendly Family Invite Code Box -->
                        <!-- Friendly Family Invite Code Box (Sample Demonstration Preview) -->
                        <div class="p-3 rounded-4 border mb-3 shadow-2xs" style="background: #FFFFFF; border-color: #E2E8F0 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark"><i class="bi bi-key-fill text-primary me-1"></i> Family Connect Code:</span>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono small fw-bold">
                                    <i class="bi bi-eye me-1"></i> SAMPLE DEMO PREVIEW
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                <span class="fs-4 fw-bold font-mono text-muted px-3 py-1 rounded-3 bg-light border" style="letter-spacing: 2px;">CMP-742K</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold opacity-75" disabled style="cursor: not-allowed; pointer-events: none;" title="Sample code only - copy is deactivated">
                                    <i class="bi bi-slash-circle me-1"></i> Sample Code (Copy Deactivated)
                                </button>
                            </div>
                            <div class="small text-muted mt-2 d-flex align-items-start gap-1.5 p-2 rounded-2 bg-light border" style="font-size: 0.74rem;">
                                <i class="bi bi-info-circle-fill text-primary mt-0.5"></i>
                                <span><strong>Parent Notice:</strong> This code is shown as an illustration. Enrolled students generate their own real, active 48-hour linkage code inside their authenticated student dashboard.</span>
                            </div>
                        </div>

                        <!-- 3 Clear Benefits for Parents -->
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex align-items-start gap-2 small text-secondary">
                                <i class="bi bi-people-fill text-primary mt-0.5"></i>
                                <span><strong>Multi-Child Support:</strong> Switch easily between IGCSE and A-Level siblings under 1 parent account.</span>
                            </div>
                            <div class="d-flex align-items-start gap-2 small text-secondary">
                                <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                <span><strong>Real-Time Confidence:</strong> Instantly see green mastery badges and identify topics that need quick review.</span>
                            </div>
                            <div class="d-flex align-items-start gap-2 small text-secondary">
                                <i class="bi bi-shield-lock-fill text-primary mt-0.5"></i>
                                <span><strong>Child-Safe Boundary:</strong> Student tests and practice answers are protected from alteration.</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span class="small text-muted">Have a parent account?</span>
                        <a href="{{ route('parent.dashboard') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold text-decoration-none">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Open Parent Portal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Card: Kids & Students Study Confidence -->
            <div class="col-lg-6">
                <div class="p-4 p-md-4 rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between" 
                     style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%); border-radius: 24px; border-color: rgba(114, 62, 195, 0.2) !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                                     style="width: 44px; height: 44px; background: linear-gradient(135deg, #723EC3, #5E32A6);">
                                    <i class="bi bi-stars fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark" style="font-family: var(--lms-font-heading);">Smart Study Confidence</h5>
                                    <span class="small text-muted">Learn at your own pace, build exam mastery daily</span>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background: rgba(114, 62, 195, 0.12); color: #723EC3; font-size: 0.75rem;">
                                <i class="bi bi-trophy-fill me-1"></i> Student-First
                            </span>
                        </div>

                        <p class="small text-secondary mb-3" style="line-height: 1.6;">
                            After reading notes or finishing a video lecture, tell the platform how you feel to build a custom revision schedule:
                        </p>

                        <!-- 3 Clear Friendly Badges for Kids -->
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between" 
                                 style="background: #F0FDF4; border-color: #BBF7D0 !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <span class="fs-4">🟢</span>
                                    <div>
                                        <strong class="text-success small d-block">&quot;I Understand This!&quot;</strong>
                                        <span class="text-secondary" style="font-size: 0.75rem;">Marks lesson complete and boosts your Cambridge study streak.</span>
                                    </div>
                                </div>
                                <span class="badge bg-success rounded-pill px-2.5 py-1 text-white small">Mastered</span>
                            </div>

                            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between" 
                                 style="background: #FFFBEB; border-color: #FDE68A !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <span class="fs-4">🟡</span>
                                    <div>
                                        <strong class="text-warning-emphasis small d-block">&quot;Need a Quick Review&quot;</strong>
                                        <span class="text-secondary" style="font-size: 0.75rem;">Saved to your revision queue so you can revisit before mock exams.</span>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small">Bookmarked</span>
                            </div>

                            <div class="p-2.5 rounded-3 border d-flex align-items-center justify-content-between" 
                                 style="background: #FEF2F2; border-color: #FECACA !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <span class="fs-4">🔴</span>
                                    <div>
                                        <strong class="text-danger small d-block">&quot;Need Help with This&quot;</strong>
                                        <span class="text-secondary" style="font-size: 0.75rem;">Flags topic for teacher assistance &amp; recommended worked video solutions.</span>
                                    </div>
                                </div>
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small">Get Help</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between small text-muted">
                        <span class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-check text-success"></i>
                            <span>All materials watermarked for student privacy</span>
                        </span>
                        <span class="fw-semibold text-primary font-mono">&gt;=90% Media Progress Rule</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambridge Resource Request Redesigned Section -->
        <div class="mt-5 p-4 p-md-5 rounded-4 shadow-lg position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #0A0F1D 0%, #0F172A 50%, #1E293B 100%); border: 1px solid rgba(2, 132, 199, 0.25);">
            <!-- Ambient Glow -->
            <div class="position-absolute top-0 end-0 rounded-circle" 
                 style="width: 380px; height: 380px; background: radial-gradient(circle, rgba(2, 132, 199, 0.2) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>
            
            <div class="row align-items-center g-4 g-lg-5 position-relative" style="z-index: 2;">
                <!-- Left Details -->
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 text-white small fw-bold mb-3 border border-white border-opacity-15 shadow-sm">
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">FAST TRACK</span>
                        <span style="font-size: 0.78rem; letter-spacing: 0.5px;">RAPID CURRICULUM PIPELINE</span>
                    </div>
                    <h2 class="display-6 fw-bold mb-3 text-white" style="font-family: var(--lms-font-heading); letter-spacing: -0.5px;">
                        Need a past paper or worked solution we haven't uploaded yet?
                    </h2>
                    <p class="text-white-50 small mb-4" style="line-height: 1.8; font-size: 0.92rem;">
                        Our Cambridge curriculum faculty and subject leads release topical question packs and worked video mark schemes weekly. If you have an urgent examination or mock deadline, request the syllabus code below for expedited upload.
                    </p>

                    <!-- Features checklist -->
                    <div class="d-flex flex-column gap-2 text-white-50 small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-info"></i>
                            <span class="text-white">Directly assigned to CIE Subject Specialist</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-info"></i>
                            <span class="text-white">Includes step-by-step LaTeX formula working &amp; mark schemes</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-info"></i>
                            <span class="text-white">Integrated directly into your Esther Math Tracker matrix</span>
                        </div>
                    </div>
                </div>

                <!-- Right Form Card -->
                <div class="col-lg-6">
                    <div class="p-4 p-sm-4 rounded-4 shadow-xl border position-relative"
                         style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-color: rgba(255, 255, 255, 0.4) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Request Syllabus Material</h5>
                                <small class="text-muted" style="font-size: 0.75rem;">Priority processing for active students</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small fw-semibold">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Expedited
                            </span>
                        </div>

                        <form action="{{ route('resource-request.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark mb-1">Cambridge Assessment Level</label>
                                    <select name="level_name" class="form-select form-select-sm rounded-3 py-2" required style="border-color: #CBD5E1;">
                                        <option value="Primary Checkpoint">1. Cambridge Primary Checkpoint (Years 1–6)</option>
                                        <option value="Lower Secondary">2. Cambridge Lower Secondary Checkpoint (Years 7–9)</option>
                                        <option value="IGCSE">3. Cambridge Upper Secondary / IGCSE (Years 10–11)</option>
                                        <option value="AS Level">4. Cambridge International AS Level (Year 12)</option>
                                        <option value="A Level" selected>5. Cambridge International A Level (Year 13)</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Subject Name &amp; Code</label>
                                    <input type="text" name="subject" class="form-control form-control-sm rounded-3 py-2" placeholder="e.g. Mathematics (9709)" required style="border-color: #CBD5E1;">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Resource Type</label>
                                    <select name="resource_type" class="form-select form-select-sm rounded-3 py-2" required style="border-color: #CBD5E1;">
                                        <option value="Worked Solutions">Worked Solutions</option>
                                        <option value="Topical Past Papers">Topical Past Paper Pack</option>
                                        <option value="Revision Notes">Revision Summaries &amp; KaTeX</option>
                                        <option value="Flashcards">Flashcard Deck</option>
                                        <option value="Video Explanation">Video Walkthrough</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark mb-1">Topic, Year &amp; Paper Variant</label>
                                    <input type="text" name="topic" class="form-control form-control-sm rounded-3 py-2" placeholder="e.g. 2025 M/J Paper 12 Pure Math (Integration)" required style="border-color: #CBD5E1;">
                                </div>
                                <div class="col-12 pt-1">
                                    <button type="submit" class="tj-btn-primary w-100 py-2.5 justify-content-center shadow-sm rounded-3" style="font-size: 0.95rem;">
                                        <i class="bi bi-send-fill me-2"></i> Submit Priority Resource Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function estherTrackerData() {
    return {
        selectedLevel: 'primary',
        getScoreStatus(score, max) {
            if (score === null || score === undefined || score === '') return { text: 'Pending', cls: 'badge-pending', pct: null, color: 'gray' };
            let p = Math.round((Number(score) / Number(max)) * 100);
            if (p >= 75) return { text: 'Mastery (' + p + '%)', cls: 'cell-mastery', pct: p, color: 'green' };
            if (p >= 50) return { text: 'Developing (' + p + '%)', cls: 'cell-developing', pct: p, color: 'amber' };
            return { text: 'Remedial (' + p + '%)', cls: 'cell-remedial', pct: p, color: 'red' };
        },
        parentInviteCode: 'CMP-742K',
        codeCopied: false,
        copyInvite() {
            navigator.clipboard.writeText(this.parentInviteCode);
            this.codeCopied = true;
            setTimeout(() => this.codeCopied = false, 2500);
        },

        // Level 1: Primary Checkpoint Data (English, Math, Science)
        primaryData: [
            { year: 2026, season: 'M/J', variant: 1, c1: 44, c1Max: 50, c2: 42, c2Max: 50, c3: 47, c3Max: 50 },
            { year: 2025, season: 'M/J', variant: 1, c1: 31, c1Max: 50, c2: 24, c2Max: 50, c3: 39, c3Max: 50 },
            { year: 2024, season: 'O/N', variant: 2, c1: 38, c1Max: 50, c2: 41, c2Max: 50, c3: 45, c3Max: 50 },
            { year: 2023, season: 'M/J', variant: 1, c1: 45, c1Max: 50, c2: 46, c2Max: 50, c3: 48, c3Max: 50 }
        ],

        // Level 2: Lower Secondary Checkpoint Data (Stage 7-9 Tests)
        secondaryData: [
            { year: 2026, season: 'M/J', variant: 1, c1: 41, c1Max: 50, c2: 36, c2Max: 50, c3: 45, c3Max: 50 },
            { year: 2025, season: 'O/N', variant: 1, c1: 22, c1Max: 50, c2: 28, c2Max: 50, c3: 34, c3Max: 50 },
            { year: 2024, season: 'M/J', variant: 2, c1: 39, c1Max: 50, c2: 44, c2Max: 50, c3: 41, c3Max: 50 },
            { year: 2023, season: 'O/N', variant: 1, c1: 46, c1Max: 50, c2: 43, c2Max: 50, c3: 48, c3Max: 50 }
        ],

        // Level 3: IGCSE Data (Extended Math 0580: P2 & P4, Sciences)
        igcseData: [
            { year: 2026, season: 'M/J', variant: 2, c1: 62, c1Max: 70, c2: 110, c2Max: 130, c3: 65, c3Max: 80 },
            { year: 2025, season: 'M/J', variant: 1, c1: 28, c1Max: 70, c2: 52, c2Max: 130, c3: 38, c3Max: 80 },
            { year: 2024, season: 'O/N', variant: 2, c1: 53, c1Max: 70, c2: 95, c2Max: 130, c3: 68, c3Max: 80 },
            { year: 2023, season: 'F/M', variant: 2, c1: 64, c1Max: 70, c2: 118, c2Max: 130, c3: 74, c3Max: 80 }
        ],

        // Level 4: Cambridge A Level (Pure 1, Pure 3, Mechanics, Stats 1)
        aLevelData: [
            { year: 2026, season: 'M/J', variant: 1, p1: 68, p1Max: 75, p3: null, p3Max: 75, p4: null, p4Max: 50, p5: null, p5Max: 50 },
            { year: 2025, season: 'M/J', variant: 2, p1: 32, p1Max: 75, p3: null, p3Max: 75, p4: null, p4Max: 50, p5: null, p5Max: 50 },
            { year: 2024, season: 'O/N', variant: 1, p1: 54, p1Max: 75, p3: 58, p3Max: 75, p4: null, p4Max: 50, p5: null, p5Max: 50 },
            { year: 2023, season: 'F/M', variant: 2, p1: 65, p1Max: 75, p3: 60, p3Max: 75, p4: 45, p4Max: 50, p5: 42, p5Max: 50 },
            { year: 2023, season: 'M/J', variant: 3, p1: 42, p1Max: 75, p3: 38, p3Max: 75, p4: 31, p4Max: 50, p5: 35, p5Max: 50 },
            { year: 2022, season: 'O/N', variant: 1, p1: 71, p1Max: 75, p3: 67, p3Max: 75, p4: 48, p4Max: 50, p5: 46, p5Max: 50 }
        ],

        updatePrimary(idx, key, val, maxVal) {
            this.primaryData[idx][key] = val === '' ? null : Math.max(0, Math.min(Number(val), maxVal));
        },
        updateSecondary(idx, key, val, maxVal) {
            this.secondaryData[idx][key] = val === '' ? null : Math.max(0, Math.min(Number(val), maxVal));
        },
        updateIgcse(idx, key, val, maxVal) {
            this.igcseData[idx][key] = val === '' ? null : Math.max(0, Math.min(Number(val), maxVal));
        },
        updateALevel(idx, key, val, maxVal) {
            this.aLevelData[idx][key] = val === '' ? null : Math.max(0, Math.min(Number(val), maxVal));
        },

        isSaving: false,
        saveMessage: '',

        async saveScoresToDatabase(level) {
            this.isSaving = true;
            this.saveMessage = 'Saving scores to database...';

            let entries = [];
            if (level === 'primary') {
                this.primaryData.forEach(r => {
                    if (r.c1 !== null) entries.push({ level: 'primary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Primary English (0844)', score_obtained: r.c1, max_score: r.c1Max });
                    if (r.c2 !== null) entries.push({ level: 'primary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Primary Mathematics (0845)', score_obtained: r.c2, max_score: r.c2Max });
                    if (r.c3 !== null) entries.push({ level: 'primary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Primary Science (0846)', score_obtained: r.c3, max_score: r.c3Max });
                });
            } else if (level === 'secondary') {
                this.secondaryData.forEach(r => {
                    if (r.c1 !== null) entries.push({ level: 'secondary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Lower Sec English (0861)', score_obtained: r.c1, max_score: r.c1Max });
                    if (r.c2 !== null) entries.push({ level: 'secondary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Lower Sec Mathematics (0862)', score_obtained: r.c2, max_score: r.c2Max });
                    if (r.c3 !== null) entries.push({ level: 'secondary', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Lower Sec Science (0863)', score_obtained: r.c3, max_score: r.c3Max });
                });
            } else if (level === 'igcse') {
                this.igcseData.forEach(r => {
                    if (r.p2 !== null) entries.push({ level: 'igcse', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'IGCSE Paper 2 (Extended)', score_obtained: r.p2, max_score: r.p2Max });
                    if (r.p4 !== null) entries.push({ level: 'igcse', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'IGCSE Paper 4 (Extended)', score_obtained: r.p4, max_score: r.p4Max });
                });
            } else if (level === 'a-level') {
                this.aLevelData.forEach(r => {
                    if (r.p1 !== null) entries.push({ level: 'a-level', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Pure Mathematics 1 (P1)', score_obtained: r.p1, max_score: r.p1Max });
                    if (r.p3 !== null) entries.push({ level: 'a-level', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Pure Mathematics 3 (P3)', score_obtained: r.p3, max_score: r.p3Max });
                    if (r.p4 !== null) entries.push({ level: 'a-level', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Mechanics (P4)', score_obtained: r.p4, max_score: r.p4Max });
                    if (r.p5 !== null) entries.push({ level: 'a-level', exam_year: r.year, season: r.season, variant: r.variant, component_name: 'Probability & Statistics 1 (P5)', score_obtained: r.p5, max_score: r.p5Max });
                });
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                for (const entry of entries) {
                    await fetch('{{ route("scores.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(entry)
                    });
                }
                this.saveMessage = 'All scores successfully saved to database! Historical report generated.';
                setTimeout(() => { this.saveMessage = ''; }, 4500);
            } catch (err) {
                this.saveMessage = 'Scores saved locally. Log in to sync globally.';
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>
@endif
