@extends('layouts.admin')

@section('title', 'Course Curriculum & Program Manager')

@section('content')
<div class="container-fluid" x-data="{ showCreateModal: false }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_courses_manager'] ?? 'Courses & Curriculum Architecture' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_courses_manager'] ?? 'Design modular learning paths, set annual recertification cycles, and configure dual audiences.' }}</div>
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
                        <th>Title &amp; Slug</th>
                        <th>Target Audience</th>
                        <th>Format</th>
                        <th>Sections &amp; Modules</th>
                        <th>Passing Threshold</th>
                        <th>Recertification</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $course->title }}</div>
                                <div class="font-mono text-muted small">/courses/{{ $course->slug }}</div>
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
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('learning.player', ['course' => $course->slug]) }}" class="btn btn-sm btn-outline-primary" title="Preview in Player">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                    <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="View Landing">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No courses created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Course Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" x-show="showCreateModal" x-cloak style="background: rgba(0,0,0,0.6);" @click.self="showCreateModal = false" @keydown.escape.window="showCreateModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content lms-card p-2">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Create New Learning Program</h5>
                    <button type="button" class="btn-close" @click="showCreateModal = false"></button>
                </div>
                <form action="{{ route('admin.courses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Program Title</label>
                                <input type="text" name="title" class="form-control" required placeholder="Advanced Cloud Security Compliance">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">URL Slug</label>
                                <input type="text" name="slug" class="form-control" required placeholder="advanced-cloud-security">
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
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishSwitch" checked>
                                    <label class="form-check-label fw-semibold" for="publishSwitch">Publish Immediately</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
                        <button type="submit" class="btn btn-accent px-4">Create Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
