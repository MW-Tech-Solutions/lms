@extends('layouts.admin')

@section('title', 'Course Curriculum & Program Manager')

@section('content')
<div class="container-fluid" 
     x-data="{ 
         showCreateModal: false,
         showEditModal: false,
         editCourse: {
             id: '',
             title: '',
             slug: '',
             description: '',
             thumbnail: '',
             thumbnail_url: '',
             target_audience: 'BOTH',
             format: 'SELF_PACED',
             passing_grade: 70,
             recertification_days: '',
             price_cents: 0,
             currency: 'USD',
             is_published: true,
             action_url: ''
         },
         openEdit(c) {
             this.editCourse = {
                 id: c.id,
                 title: c.title,
                 slug: c.slug,
                 description: c.description || '',
                 thumbnail: c.thumbnail || '',
                 thumbnail_url: (c.thumbnail && (c.thumbnail.startsWith('http') || c.thumbnail.startsWith('/'))) ? c.thumbnail : '',
                 target_audience: c.target_audience,
                 format: c.format,
                 passing_grade: c.passing_grade,
                 recertification_days: c.recertification_days || '',
                 price_cents: c.price_cents,
                 currency: c.currency,
                 is_published: !!c.is_published,
                 action_url: '{{ url('admin/courses') }}/' + c.id
             };
             this.showEditModal = true;
         }
     }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_courses_manager'] ?? 'Courses & Curriculum Architecture' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_courses_manager'] ?? 'Design modular learning paths, upload course thumbnails, set recertification cycles, and configure audiences.' }}</div>
        </div>
        <button class="tj-btn-primary py-2 px-3 small shadow-sm" @click="showCreateModal = true">
            <i class="bi bi-plus-lg me-1"></i> {{ $site_settings['btn_create_course'] ?? 'Create New Program' }}
        </button>
    </div>

    <!-- Courses Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Thumbnail</th>
                        <th>Title &amp; Slug</th>
                        <th>Target Audience</th>
                        <th>Format</th>
                        <th>Sections &amp; Modules</th>
                        <th>Passing Threshold</th>
                        <th>Recertification</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>
                                @if($course->thumbnailUrl())
                                    <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 58px; height: 42px; border: 1px solid var(--lms-border);">
                                @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white small" 
                                         style="width: 58px; height: 42px; background: {{ $course->target_audience === 'CORPORATE' ? 'linear-gradient(135deg, #0F172A, #1E293B)' : 'linear-gradient(135deg, #723EC3, #2563EB)' }}; font-size: 1rem;">
                                        <i class="bi {{ $course->target_audience === 'CORPORATE' ? 'bi-shield-check text-warning' : 'bi-mortarboard-fill text-light' }}"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $course->title }}</div>
                                <div class="font-mono text-muted small">/courses/{{ $course->slug }}</div>
                                @if($course->description)
                                    <div class="text-secondary small text-truncate" style="max-width: 280px; font-size: 0.75rem;">
                                        {{ $course->description }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary small">{{ $course->target_audience }}</span>
                            </td>
                            <td class="small">{{ ucfirst(strtolower($course->format)) }}</td>
                            <td class="font-mono small">
                                {{ $course->sections()->count() }} Sections &bull; {{ $course->sections->flatMap->modules->count() }} Modules
                            </td>
                            <td class="font-mono">{{ $course->passing_grade }}%</td>
                            <td>
                                @if($course->recertification_days)
                                    <span class="badge badge-amber font-mono">{{ $course->recertification_days }} Days</span>
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>
                            <td class="font-mono">{{ $course->formattedPrice() }}</td>
                            <td>
                                @if($course->is_published)
                                    <span class="badge badge-compliance">PUBLISHED</span>
                                @else
                                    <span class="badge bg-secondary">DRAFT</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Edit Course Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary" 
                                            title="Edit Course & Thumbnail"
                                            @click="openEdit({{ json_encode($course) }})">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="{{ route('learning.player', ['course' => $course->slug]) }}" class="btn btn-sm btn-outline-success" title="Preview in Player">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </a>
                                    <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="View Public Landing Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Course">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No courses created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. CREATE COURSE MODAL -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showCreateModal }" 
         :style="showCreateModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showCreateModal = false" 
         @keydown.escape.window="showCreateModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Create New Learning Program</h5>
                        <div class="text-muted small">Configure curriculum parameters and upload an engaging course thumbnail.</div>
                    </div>
                    <button type="button" class="btn-close" @click="showCreateModal = false"></button>
                </div>
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Program Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g., ISO 27001 Information Security Recertification">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">URL Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" required placeholder="iso-27001-recertification">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Course Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of the course syllabus and competencies..."></textarea>
                            </div>

                            <!-- Thumbnail Section -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                    <label class="form-label small fw-bold mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-image text-primary"></i> Course Thumbnail Image
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload File (JPG, PNG, WebP, SVG)</label>
                                            <input type="file" name="thumbnail_file" class="form-control form-control-sm" accept="image/*">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Image URL</label>
                                            <input type="url" name="thumbnail_url" class="form-control form-control-sm" placeholder="https://example.com/image.jpg">
                                        </div>
                                    </div>
                                    <div class="form-text small mt-1">Recommended size: 1280x720 (16:9 ratio). High resolution thumbnails boost learner engagement.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Target Audience</label>
                                <select name="target_audience" class="form-select">
                                    <option value="BOTH">Dual-Audience (Both)</option>
                                    <option value="CORPORATE">Corporate Workforce</option>
                                    <option value="PUBLIC">Public &amp; Academic</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Course Format</label>
                                <select name="format" class="form-select">
                                    <option value="SELF_PACED">Self-Paced Modular</option>
                                    <option value="WEEKLY">Weekly Cohorts</option>
                                    <option value="TOPICS">Thematic Topics</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Passing Threshold (%)</label>
                                <input type="number" name="passing_grade" class="form-control" value="70" min="1" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Recertification Cycle (Days)</label>
                                <input type="number" name="recertification_days" class="form-control" placeholder="e.g. 365 for annual">
                                <div class="form-text small">Leave blank for lifetime credential.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Price (Cents)</label>
                                <input type="number" name="price_cents" class="form-control" value="0">
                                <div class="form-text small">0 for Free / Corporate Sponsored.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Currency</label>
                                <input type="text" name="currency" class="form-control" value="USD" maxlength="3">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="createPublishSwitch" checked>
                                    <label class="form-check-label fw-semibold" for="createPublishSwitch">Publish Immediately to Course Catalog</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" @click="showCreateModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">
                            <i class="bi bi-check-lg me-1"></i> Create Program
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. EDIT COURSE & THUMBNAIL MODAL -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" 
         :class="{ 'show': showEditModal }" 
         :style="showEditModal ? 'display: block !important; background: rgba(0,0,0,0.7);' : ''" 
         x-cloak 
         @click.self="showEditModal = false" 
         @keydown.escape.window="showEditModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content lms-card p-3 shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Edit Program &amp; Thumbnail</h5>
                        <div class="text-muted small">Update course metadata, change pricing, and replace the course thumbnail.</div>
                    </div>
                    <button type="button" class="btn-close" @click="showEditModal = false"></button>
                </div>
                <form :action="editCourse.action_url" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Program Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required x-model="editCourse.title">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">URL Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" required x-model="editCourse.slug">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Course Description</label>
                                <textarea name="description" class="form-control" rows="2" x-model="editCourse.description" placeholder="Brief summary of the course syllabus..."></textarea>
                            </div>

                            <!-- Thumbnail Section in Edit Modal -->
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: var(--lms-bg-secondary);">
                                    <label class="form-label small fw-bold mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-image text-primary"></i> Course Thumbnail Image
                                    </label>
                                    
                                    <!-- Current Thumbnail Preview -->
                                    <template x-if="editCourse.thumbnail">
                                        <div class="mb-3 d-flex align-items-center gap-3 p-2 bg-white rounded-3 border">
                                            <img :src="editCourse.thumbnail.startsWith('http') || editCourse.thumbnail.startsWith('/') ? editCourse.thumbnail : ('/storage/' + editCourse.thumbnail)" 
                                                 alt="Current Thumbnail" 
                                                 class="rounded-2 object-fit-cover shadow-sm" 
                                                 style="width: 80px; height: 50px;">
                                            <div>
                                                <div class="small fw-semibold text-dark">Current Thumbnail Attached</div>
                                                <div class="text-muted small" style="font-size: 0.72rem;" x-text="editCourse.thumbnail"></div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Upload New Image File</label>
                                            <input type="file" name="thumbnail_file" class="form-control form-control-sm" accept="image/*">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Or Set Image URL</label>
                                            <input type="url" name="thumbnail_url" class="form-control form-control-sm" x-model="editCourse.thumbnail_url" placeholder="https://example.com/new-image.jpg">
                                        </div>
                                    </div>
                                    <div class="form-text small mt-1">Leave empty to keep existing thumbnail.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Target Audience</label>
                                <select name="target_audience" class="form-select" x-model="editCourse.target_audience">
                                    <option value="BOTH">Dual-Audience (Both)</option>
                                    <option value="CORPORATE">Corporate Workforce</option>
                                    <option value="PUBLIC">Public &amp; Academic</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Course Format</label>
                                <select name="format" class="form-select" x-model="editCourse.format">
                                    <option value="SELF_PACED">Self-Paced Modular</option>
                                    <option value="WEEKLY">Weekly Cohorts</option>
                                    <option value="TOPICS">Thematic Topics</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Passing Threshold (%)</label>
                                <input type="number" name="passing_grade" class="form-control" min="1" max="100" x-model="editCourse.passing_grade">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Recertification Cycle (Days)</label>
                                <input type="number" name="recertification_days" class="form-control" placeholder="e.g. 365" x-model="editCourse.recertification_days">
                                <div class="form-text small">Leave blank for lifetime credential.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Price (Cents)</label>
                                <input type="number" name="price_cents" class="form-control" x-model="editCourse.price_cents">
                                <div class="form-text small">0 for Free / Corporate Sponsored.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Currency</label>
                                <input type="text" name="currency" class="form-control" maxlength="3" x-model="editCourse.currency">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="editPublishSwitch" x-model="editCourse.is_published">
                                    <label class="form-check-label fw-semibold" for="editPublishSwitch">Published in Public &amp; Enterprise Catalogs</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" @click="showEditModal = false">Cancel</button>
                        <button type="submit" class="tj-btn-primary px-4 py-2">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
