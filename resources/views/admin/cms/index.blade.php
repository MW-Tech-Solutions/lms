@extends('layouts.admin')

@section('title', 'Headless CMS & Dynamic Page Engine')

@section('content')
<div class="container-fluid" x-data="{ editingSection: null }">
    
    <!-- Top Action Banner -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="font-family: var(--lms-font-heading);">
                {{ $site_settings['title_cms_manager'] ?? 'Headless CMS & Dynamic Section Engine' }}
            </h2>
            <div class="text-secondary small">
                Manage landing page sections, Explore Categories, Mentors showcase, Live Events, FAQ accordions, and SEO metadata.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.cms.seed-samples') }}" method="POST" onsubmit="return confirm('Sync and restore Homepage sections to match the sample screenshot designs (Categories, Mentors, Events, FAQs)?');">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-1.5 fw-semibold">
                    <i class="bi bi-arrow-repeat text-primary"></i>
                    <span>Sync Sample Screenshots</span>
                </button>
            </form>

            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#newSectionModal">
                <i class="bi bi-plus-lg"></i>
                <span>Add Section</span>
            </button>

            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-1.5">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Preview Live Homepage</span>
            </a>
        </div>
    </div>

    <!-- Alert Notices -->
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- 1. HOMEPAGE DYNAMIC STACK BUILDER -->
    <div class="lms-card p-4 shadow-sm mb-4 border" style="background: var(--lms-surface-card); border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-window-stack text-primary"></i>
                    <span>Homepage Dynamic Section Stack (Live Ordered)</span>
                </h5>
                <span class="text-muted small">Sections render on the public homepage in the exact order shown below.</span>
            </div>
            <span class="badge badge-purple-soft rounded-pill px-3 py-1.5">
                {{ $homePage ? count($homePage->sections) : 0 }} Sections Configured
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 170px;">Section Type</th>
                        <th>Title / Details Preview</th>
                        <th>Dynamic Items</th>
                        <th class="text-end" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($homePage && $homePage->sections->count() > 0)
                        @foreach($homePage->sections as $sec)
                            @php
                                $c = $sec->content_json ?? [];
                                $badgeColor = match($sec->section_type) {
                                    'HERO' => 'bg-primary text-white',
                                    'CATEGORIES' => 'bg-info-subtle text-info-emphasis border border-info',
                                    'CATALOG' => 'bg-primary-subtle text-primary border border-primary',
                                    'MENTORS' => 'bg-warning-subtle text-warning-emphasis border border-warning',
                                    'EVENTS' => 'bg-success-subtle text-success border border-success',
                                    'FAQ' => 'bg-secondary-subtle text-dark border',
                                    'DYNAMIC_FORM' => 'bg-dark text-white',
                                    'PRICING' => 'bg-danger-subtle text-danger border border-danger',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border font-mono fs-6 px-2.5 py-1">
                                        #{{ $sec->order_index }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeColor }} font-mono px-2.5 py-1.5 rounded-3">
                                        {{ $sec->section_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0 text-truncate" style="max-width: 380px;">
                                        {{ $c['title'] ?? ($c['tagline'] ?? 'Default Section Block') }}
                                    </div>
                                    @if(!empty($c['subtitle']))
                                        <div class="text-muted small text-truncate" style="max-width: 420px; font-size: 0.78rem;">
                                            {{ $c['subtitle'] }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($sec->section_type === 'CATEGORIES')
                                        <span class="badge bg-light text-secondary border font-mono small">
                                            {{ count($c['categories'] ?? []) }} Categories &bull; 8 Cards
                                        </span>
                                    @elseif($sec->section_type === 'MENTORS')
                                        <span class="badge bg-light text-secondary border font-mono small">
                                            {{ count($c['mentors'] ?? []) }} Mentors Featured
                                        </span>
                                    @elseif($sec->section_type === 'EVENTS')
                                        <span class="badge bg-light text-secondary border font-mono small">
                                            {{ count($c['events'] ?? []) }} Live Events
                                        </span>
                                    @elseif($sec->section_type === 'FAQ')
                                        <span class="badge bg-light text-secondary border font-mono small">
                                            {{ count($c['faqs'] ?? []) }} Accordion FAQs
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border font-mono small">Built-in Layout</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" 
                                                class="btn btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal{{ $sec->id }}"
                                                title="Edit Section">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                        <!-- Delete Section -->
                                        <form action="{{ route('admin.cms.sections.destroy', $sec) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this section from the homepage?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Section">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- EDIT MODAL FOR THIS SECTION -->
                                    <div class="modal fade text-start" id="editModal{{ $sec->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <form action="{{ route('admin.cms.sections.update', $sec) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="modal-header border-bottom px-4 pt-4 pb-3">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge {{ $badgeColor }} font-mono px-2 py-1">
                                                                {{ $sec->section_type }}
                                                            </span>
                                                            <h5 class="modal-title fw-bold mb-0">Edit Section Content</h5>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body px-4 py-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-semibold text-secondary">Display Order Index</label>
                                                                <input type="number" name="order_index" class="form-control" value="{{ $sec->order_index }}" required min="1" max="99">
                                                                <div class="form-text small">Lower numbers display higher up.</div>
                                                            </div>

                                                            <div class="col-md-8">
                                                                <label class="form-label small fw-semibold text-secondary">Section Title</label>
                                                                <input type="text" name="title" class="form-control" value="{{ $c['title'] ?? '' }}" placeholder="e.g. Explore Categories">
                                                            </div>

                                                            <div class="col-12">
                                                                <label class="form-label small fw-semibold text-secondary">Subtitle / Description</label>
                                                                <textarea name="subtitle" class="form-control" rows="2" placeholder="Descriptive subheading for section...">{{ $c['subtitle'] ?? '' }}</textarea>
                                                            </div>

                                                            @if($sec->section_type === 'CATEGORIES' || $sec->section_type === 'EVENTS')
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-secondary">View All Link Text</label>
                                                                    <input type="text" name="view_all_text" class="form-control" value="{{ $c['view_all_text'] ?? '' }}" placeholder="View All Categories">
                                                                </div>
                                                            @endif

                                                            @if($sec->section_type === 'EVENTS')
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-secondary">Right Featured Speaker / Graphic URL</label>
                                                                    <input type="url" name="featured_image_url" class="form-control font-mono" value="{{ $c['featured_image_url'] ?? '' }}" placeholder="https://images.unsplash.com/...">
                                                                </div>
                                                            @endif

                                                            @if($sec->section_type === 'FAQ')
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-secondary">Support Card Title</label>
                                                                    <input type="text" name="support_title" class="form-control" value="{{ $c['support_title'] ?? 'Still Need Help?' }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-secondary">Support Email Badge</label>
                                                                    <input type="email" name="support_email" class="form-control" value="{{ $c['support_email'] ?? 'hello@lerntix.com' }}">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label small fw-semibold text-secondary">Support Image URL (Desk/Laptop Photo)</label>
                                                                    <input type="url" name="support_image" class="form-control font-mono" value="{{ $c['support_image'] ?? '' }}" placeholder="https://images.unsplash.com/...">
                                                                </div>
                                                            @endif

                                                            @if(in_array($sec->section_type, ['CATEGORIES', 'MENTORS', 'EVENTS', 'FAQ']))
                                                                <div class="col-12">
                                                                    <label class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                                                                        <span>Custom Items (Advanced JSON Array)</span>
                                                                        <span class="text-muted font-mono" style="font-size: 0.72rem;">Valid JSON format</span>
                                                                    </label>
                                                                    <textarea name="content_raw_json" class="form-control font-mono small" rows="8" placeholder="Optional raw JSON override">{{ json_encode(array_filter($c, fn($k) => in_array($k, ['categories', 'mentors', 'events', 'faqs', 'highlights']), ARRAY_FILTER_USE_KEY), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                                                                    <div class="form-text small">Admins can adjust item titles, lesson counts, ratings, and questions in this structured JSON format.</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer border-top px-4 py-3">
                                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                                                            <i class="bi bi-check-lg me-1"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No sections found. Click <strong>Sync Sample Screenshots</strong> above to populate sample modules!
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. ALL PUBLISHED PAGES TABLE -->
    <div class="lms-card p-4 shadow-sm mb-4 border" style="background: var(--lms-surface-card); border-radius: 20px;">
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-layers text-primary"></i>
            <span>Registered CMS Page Index</span>
        </h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Page Title</th>
                        <th>Slug</th>
                        <th>SEO Meta Title</th>
                        <th>Configured Sections</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td class="fw-bold text-dark">{{ $page->title }}</td>
                            <td class="font-mono text-primary">/{{ $page->slug === 'home' ? '' : $page->slug }}</td>
                            <td class="small text-muted">{{ Str::limit($page->meta_title ?? $page->title, 40) }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($page->sections as $s)
                                        <span class="badge bg-secondary-subtle text-secondary font-mono small">
                                            {{ $s->section_type }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($page->is_published)
                                    <span class="badge badge-compliance">PUBLISHED</span>
                                @else
                                    <span class="badge bg-secondary">DRAFT</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('cms.show', ['slug' => $page->slug]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No CMS pages registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- NEW SECTION MODAL -->
    <div class="modal fade" id="newSectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.cms.sections.store') }}" method="POST">
                    @csrf
                    
                    <div class="modal-header border-bottom px-4 pt-4 pb-3">
                        <h5 class="modal-title fw-bold">Add Dynamic Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Target Page</label>
                            <select name="page_id" class="form-select" required>
                                @foreach($pages as $p)
                                    <option value="{{ $p->id }}" {{ $p->slug === 'home' ? 'selected' : '' }}>
                                        {{ $p->title }} (/{{ $p->slug }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Section Type</label>
                            <select name="section_type" class="form-select font-mono" required>
                                <option value="CATEGORIES">CATEGORIES (Explore Categories - 8 Cards &amp; Badges)</option>
                                <option value="MENTORS">MENTORS (Meet Our Mentors - Instructor Cards &amp; Ratings)</option>
                                <option value="EVENTS">EVENTS (Upcoming Live Events - Dates &amp; Spot Reservation)</option>
                                <option value="FAQ">FAQ (Frequently Asked Questions - Accordion &amp; Help Desk)</option>
                                <option value="HERO">HERO (Main Banner &amp; Headline)</option>
                                <option value="CATALOG">CATALOG (Featured Course Programs)</option>
                                <option value="DYNAMIC_FORM">DYNAMIC_FORM (Faster Performance Bento &amp; Inquiry)</option>
                                <option value="PRICING">PRICING (Enterprise Subscription Tiers)</option>
                                <option value="FOOTER">FOOTER (Bottom Links &amp; Brand)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Display Order Index</label>
                            <input type="number" name="order_index" class="form-control" value="10" required min="1" max="99">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Section Title (Optional)</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Explore Top Categories">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Subtitle (Optional)</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="Brief section description">
                        </div>
                    </div>

                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                            Create Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
