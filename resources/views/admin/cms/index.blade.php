@extends('layouts.admin')

@section('title', 'Headless CMS & Dynamic Page Engine')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_cms_manager'] ?? 'Headless CMS & Dynamic Layout Engine' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_cms_manager'] ?? 'Manage landing pages, dynamic Bootstrap section stacks (Hero, Metrics, Catalog, Pricing, Forms), and SEO metadata.' }}</div>
        </div>
    </div>

    <!-- CMS Pages Table -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-layers text-primary me-2"></i>Published CMS Pages</h5>
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
                            <td class="fw-bold">{{ $page->title }}</td>
                            <td class="font-mono text-primary">/{{ $page->slug === 'home' ? '' : $page->slug }}</td>
                            <td class="small text-muted">{{ Str::limit($page->meta_title ?? $page->title, 40) }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($page->sections as $sec)
                                        <span class="badge bg-secondary-subtle text-secondary font-mono small">
                                            {{ $sec->section_type }}
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
                                <a href="{{ route('cms.show', ['slug' => $page->slug]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
</div>
@endsection
