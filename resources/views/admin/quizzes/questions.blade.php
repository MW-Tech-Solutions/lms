@extends('layouts.admin')

@section('title', 'Question Bank & Bulk Upload')

@section('content')
<div class="container-fluid" 
     x-data="{
         showAddModal: false,
         showUploadModal: false,
         questionType: 'SINGLE_CHOICE',
         correctOption: 0
     }">

    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.quizzes.index') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Assessments &amp; Exams
                </a>
                <span class="text-muted small">/</span>
                <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                    <i class="bi bi-collection-fill me-1"></i> Question Bank Repository
                </span>
            </div>
            <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                Course Question Bank &amp; Material Chapters
            </h2>
            <div class="text-secondary small">
                Manage questions categorized by subject, course, and textbook chapter. Create questions directly or bulk import using our formatted spreadsheet template.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.questions.template') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Download Excel/CSV Template
            </a>
            @if($isTeacher && !auth()->user()->isApprovedTeacher())
                <button type="button" class="btn btn-secondary py-2 px-3 small shadow-sm rounded-pill opacity-75" disabled title="Administrator Approval Required">
                    <i class="bi bi-lock-fill me-1"></i> Awaiting Admin Approval
                </button>
            @else
                <button class="btn btn-outline-primary btn-sm shadow-sm" type="button" @click="showUploadModal = true">
                    <i class="bi bi-upload me-1"></i> Bulk Upload (CSV)
                </button>
                <button class="tj-btn-primary py-2 px-3 small shadow-sm" type="button" @click="showAddModal = true">
                    <i class="bi bi-plus-lg me-1"></i> Add Question
                </button>
            @endif
        </div>
    </div>

    @if($isTeacher && !auth()->user()->isApprovedTeacher())
        <!-- Teacher Pending Verification Notification Banner -->
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" style="background: rgba(245, 158, 11, 0.12); border-left: 5px solid #F59E0B !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Faculty Account Pending Administrator Approval</h6>
                    <span class="small text-secondary">Your instructor account is currently undergoing verification. You cannot add questions or upload question banks until an administrator approves your profile.</span>
                </div>
            </div>
            <span class="badge bg-warning text-dark font-mono px-3 py-2 rounded-pill">PENDING APPROVAL</span>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="lms-card p-3 shadow-xs border h-100 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 text-white" style="background: linear-gradient(135deg, #723EC3, #8B5CF6);">
                    <i class="bi bi-patch-question-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Available Questions</div>
                    <div class="fs-4 fw-bold font-mono">{{ $questions->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="lms-card p-3 shadow-xs border h-100 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 text-white" style="background: linear-gradient(135deg, #2563EB, #3B82F6);">
                    <i class="bi bi-journal-bookmark-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Accessible Courses</div>
                    <div class="fs-4 fw-bold font-mono">{{ $courses->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="lms-card p-3 shadow-xs border h-100 d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 text-white" style="background: linear-gradient(135deg, #10B981, #059669);">
                    <i class="bi bi-layers-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Course Chapters Covered</div>
                    <div class="fs-4 fw-bold font-mono">{{ $chapters->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="lms-card p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('admin.quizzes.questions.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Search question keyword..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select form-select-sm">
                    <option value="">All Filtered Courses ({{ $courses->count() }})</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->course_code }} &bull; {{ Str::limit($c->title, 28) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="chapter" class="form-control form-control-sm" 
                       placeholder="Filter by Chapter..." 
                       value="{{ request('chapter') }}" list="chaptersList">
                <datalist id="chaptersList">
                    @foreach($chapters as $ch)
                        <option value="{{ $ch }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'course_id', 'chapter']))
                    <a href="{{ route('admin.quizzes.questions.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Questions Table Card -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3" style="width: 45%;">Question Details &amp; Options</th>
                        <th class="py-3" style="width: 25%;">Course &amp; Chapter</th>
                        <th class="py-3 text-center" style="width: 15%;">Points / Type</th>
                        <th class="py-3 text-end pe-3" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td class="py-3 align-top">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">
                                    {{ $q->question_text }}
                                </div>
                                @if($q->options->isNotEmpty())
                                    <div class="d-flex flex-column gap-1 mt-2 ps-2 border-start border-2 border-primary border-opacity-50">
                                        @foreach($q->options as $opt)
                                            <div class="small d-flex align-items-center gap-2 {{ $opt->is_correct ? 'text-success fw-bold' : 'text-secondary' }}" style="font-size: 0.8rem;">
                                                @if($opt->is_correct)
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @else
                                                    <i class="bi bi-circle text-muted"></i>
                                                @endif
                                                <span>{{ $opt->option_text }}</span>
                                                @if($opt->is_correct)
                                                    <span class="badge bg-success-subtle text-success py-0 px-1 font-mono" style="font-size: 0.65rem;">Correct Answer</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 align-top">
                                @if($q->course_title)
                                    <div class="fw-bold small text-primary mb-1">
                                        <i class="bi bi-journal-text me-1"></i>{{ $q->course_title }}
                                    </div>
                                @else
                                    <div class="text-muted small mb-1"><i class="bi bi-globe me-1"></i>General Question Bank</div>
                                @endif
                                <div class="badge bg-light text-secondary border font-mono">
                                    <i class="bi bi-bookmark-fill text-warning me-1"></i>{{ $q->chapter ?? 'General Chapter' }}
                                </div>
                            </td>
                            <td class="py-3 text-center align-top font-mono">
                                <span class="badge bg-purple-subtle text-purple border fw-bold px-2 py-1 mb-1 d-inline-block">
                                    {{ $q->points }} {{ Str::plural('Point', $q->points) }}
                                </span>
                                <div class="small text-muted font-sans" style="font-size: 0.72rem;">
                                    {{ str_replace('_', ' ', $q->type) }}
                                </div>
                            </td>
                            <td class="py-3 text-end pe-3 align-top">
                                <form action="{{ route('admin.quizzes.questions.destroy', $q->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this question permanently from the Question Bank?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2 rounded-2" title="Delete Question">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
                                <div class="fw-bold text-secondary">No questions found in this category.</div>
                                <div class="small text-muted mb-3">Add questions manually or import an Excel/CSV spreadsheet.</div>
                                <button type="button" class="btn btn-primary btn-sm" @click="showAddModal = true">
                                    <i class="bi bi-plus-lg me-1"></i> Add First Question
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $questions->links() }}
        </div>
    </div>

    <!-- 1. ADD SINGLE QUESTION MODAL -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog"
         :class="{ 'show': showAddModal }"
         :style="showAddModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''"
         x-cloak
         @click.self="showAddModal = false"
         @keydown.escape.window="showAddModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(114, 62, 195, 0.12); color: var(--lms-accent);">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Add Question to Bank</h5>
                    </div>
                    <button type="button" class="btn-close" @click="showAddModal = false"></button>
                </div>
                <form action="{{ route('admin.quizzes.questions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Belongs to Course / Subject <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-select" required>
                                    <option value="">Select Course...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->course_code }} &bull; {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Chapter / Module Section <span class="text-danger">*</span></label>
                                <input type="text" name="chapter" class="form-control" required placeholder="e.g. Chapter 1: Kinematics &amp; Dynamics">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Question Type</label>
                                <select name="type" class="form-select" x-model="questionType">
                                    <option value="SINGLE_CHOICE">Multiple Choice (Single Answer)</option>
                                    <option value="TRUE_FALSE">True / False</option>
                                    <option value="SHORT_ANSWER">Short Answer</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Point Weight</label>
                                <input type="number" name="points" class="form-control font-mono" value="1" min="1" max="100" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Question Statement / Prompt <span class="text-danger">*</span></label>
                                <textarea name="question_text" class="form-control" rows="3" required placeholder="State the question clearly (e.g. Which of the following equations represents Newton's second law of motion?)"></textarea>
                            </div>

                            <!-- Multiple Choice Options -->
                            <div class="col-12" x-show="questionType === 'SINGLE_CHOICE'">
                                <label class="form-label small fw-semibold mb-2">Options &amp; Correct Answer Selection</label>
                                <div class="vstack gap-2">
                                    <div class="input-group">
                                        <div class="input-group-text bg-light">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="0" checked title="Mark as correct answer">
                                        </div>
                                        <span class="input-group-text fw-bold">A</span>
                                        <input type="text" name="options[]" class="form-control" placeholder="Option A text" required>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-text bg-light">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="1" title="Mark as correct answer">
                                        </div>
                                        <span class="input-group-text fw-bold">B</span>
                                        <input type="text" name="options[]" class="form-control" placeholder="Option B text" required>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-text bg-light">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="2" title="Mark as correct answer">
                                        </div>
                                        <span class="input-group-text fw-bold">C</span>
                                        <input type="text" name="options[]" class="form-control" placeholder="Option C text (Optional)">
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-text bg-light">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="3" title="Mark as correct answer">
                                        </div>
                                        <span class="input-group-text fw-bold">D</span>
                                        <input type="text" name="options[]" class="form-control" placeholder="Option D text (Optional)">
                                    </div>
                                </div>
                                <div class="form-text small mt-1"><i class="bi bi-info-circle me-1"></i>Select the radio button on the left that corresponds to the correct answer.</div>
                            </div>

                            <!-- True/False Options -->
                            <div class="col-12" x-show="questionType === 'TRUE_FALSE'">
                                <label class="form-label small fw-semibold mb-2">Select Correct Answer</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tf_correct" id="tfTrue" value="true" checked>
                                        <label class="form-check-label fw-bold text-success" for="tfTrue">True</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tf_correct" id="tfFalse" value="false">
                                        <label class="form-check-label fw-bold text-danger" for="tfFalse">False</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showAddModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">Save Question to Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. BULK UPLOAD CSV MODAL -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog"
         :class="{ 'show': showUploadModal }"
         :style="showUploadModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''"
         x-cloak
         @click.self="showUploadModal = false"
         @keydown.escape.window="showUploadModal = false">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="sidebar-icon-box rounded-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10B981;">
                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: var(--lms-font-heading);">Upload Questions (Excel/CSV)</h5>
                    </div>
                    <button type="button" class="btn-close" @click="showUploadModal = false"></button>
                </div>
                <form action="{{ route('admin.quizzes.questions.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="alert alert-info border-0 rounded-3 small p-3 mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Download our spreadsheet template to ensure columns match:
                            <strong>Course_ID, Chapter, Question_Type, Question_Text, Option_A, Option_B, Option_C, Option_D, Correct_Option, Points</strong>.
                            <div class="mt-2">
                                <a href="{{ route('admin.quizzes.questions.template') }}" class="btn btn-sm btn-light border fw-bold">
                                    <i class="bi bi-download me-1 text-success"></i> Download Template (.CSV)
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Select Excel/CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv, .txt" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Default Course (Optional Fallback)</label>
                            <select name="target_course_id" class="form-select">
                                <option value="">Use Course_ID from file rows</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->course_code }} &bull; {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Default Chapter (Optional Fallback)</label>
                            <input type="text" name="target_chapter" class="form-control" placeholder="e.g. Unit 1 Comprehensive Review">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" @click="showUploadModal = false">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 py-2">
                            <i class="bi bi-upload me-1"></i> Import Questions Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
