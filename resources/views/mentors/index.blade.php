@extends('layouts.app')

@section('title', 'Meet Our Mentors & Faculty - Cambridge International')
@section('meta_description', 'Learn from official Cambridge examiners, subject matter leads, and certified educators across all Cambridge levels.')

@section('content')
<!-- Ambient Hero Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%); color: #FFF;">
    <div class="position-absolute top-0 end-0 rounded-circle" 
         style="width: 400px; height: 400px; background: radial-gradient(circle, rgba(114, 62, 195, 0.25) 0%, transparent 70%); filter: blur(50px); pointer-events: none;"></div>

    <div class="container position-relative py-4" style="z-index: 2;">
        <div class="max-w-700">
            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase mb-3" 
                  style="background: rgba(114, 62, 195, 0.35); color: #C4B5FD; border: 1px solid rgba(196, 181, 253, 0.3); font-size: 0.78rem;">
                <i class="bi bi-patch-check-fill me-1 text-warning"></i> Cambridge Accredited Instructors
            </span>
            <h1 class="display-5 fw-bold mb-3 text-white" style="font-family: var(--lms-font-heading); letter-spacing: -0.5px; color: #FFFFFF !important;">
                Meet Our Mentors &amp; Examiners
            </h1>
            <p class="lead text-white-50 fs-6 mb-0" style="max-width: 600px; line-height: 1.8;">
                Learn from official Cambridge examiners and certified educators who bring authentic exam mastery, worked step-by-step past papers, and proven syllabus success straight to your screen.
            </p>
        </div>
    </div>
</section>

<!-- Filter & Search Bar Section -->
<section class="py-4 border-bottom" style="background: var(--lms-surface-card);">
    <div class="container">
        <form method="GET" action="{{ route('mentors.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-1" 
                           placeholder="Search mentors by name, subject, or expertise..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="specialization" class="form-select">
                    <option value="">All Subject Fields</option>
                    <option value="Mathematics" {{ request('specialization') == 'Mathematics' ? 'selected' : '' }}>Mathematics (9709 / 0580)</option>
                    <option value="Physics" {{ request('specialization') == 'Physics' ? 'selected' : '' }}>Physics (9702 / 0625)</option>
                    <option value="Chemistry" {{ request('specialization') == 'Chemistry' ? 'selected' : '' }}>Chemistry (9701 / 0620)</option>
                    <option value="Biology" {{ request('specialization') == 'Biology' ? 'selected' : '' }}>Biology (9700 / 0610)</option>
                    <option value="Computer Science" {{ request('specialization') == 'Computer Science' ? 'selected' : '' }}>Computer Science (9618 / 0478)</option>
                    <option value="English" {{ request('specialization') == 'English' ? 'selected' : '' }}>English &amp; Humanities</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="tj-btn-primary py-2 px-3 flex-grow-1 text-center justify-content-center">
                    <span>Filter Mentors</span>
                </button>
                @if(request()->anyFilled(['search', 'specialization']))
                    <a href="{{ route('mentors.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</section>

<!-- Mentors Grid -->
<section class="py-5" style="background: var(--lms-bg-body); min-height: 60vh;">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Active Faculty Directory
                </h4>
                <span class="text-muted small">Showing {{ $mentors->total() }} verified mentors</span>
            </div>
        </div>

        <div class="row g-4">
            @forelse($mentors as $mentor)
                @php
                    $avatar = (!empty($mentor->profile?->avatar_url) && !str_contains($mentor->profile->avatar_url, 'unsplash.com')) ? $mentor->profile->avatar_url : asset('images/profile-img.png');
                    $jobTitle = $mentor->profile?->job_title ?? 'Cambridge Examiner & Faculty Lead';
                    $courseCount = $mentor->courses->count();
                @endphp
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="lms-mentor-card h-100 p-3.5 rounded-4 shadow-sm border transition-all d-flex flex-column justify-content-between"
                         style="background: var(--lms-surface-card); border-color: var(--lms-border) !important; border-radius: 20px;">
                        
                        <div>
                            <!-- Top Avatar -->
                            <div class="mentor-avatar-wrapper rounded-4 overflow-hidden mb-3 position-relative mx-auto w-100 shadow-xs" 
                                 style="height: 220px; background: linear-gradient(180deg, #F8FAFC 0%, #EDE9FE 100%); border: 1px solid rgba(114, 62, 195, 0.12);">
                                
                                <img src="{{ $avatar }}" 
                                     alt="{{ $mentor->name }}" 
                                     class="w-100 h-100"
                                     style="object-fit: cover; object-position: center 15%;">

                                <span class="position-absolute bottom-2 end-2 badge bg-dark bg-opacity-75 text-white font-mono rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-patch-check-fill text-warning me-1"></i>Verified
                                </span>
                            </div>

                            <!-- Mentor Info -->
                            <div class="text-center mb-3">
                                <h4 class="h5 fw-bold mb-1" style="color: var(--lms-text-primary); font-family: var(--lms-font-heading);">
                                    {{ $mentor->name }}
                                </h4>
                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-truncate d-inline-block w-100" 
                                      style="background: rgba(114, 62, 195, 0.08); color: var(--lms-accent, #723EC3); font-size: 0.72rem;">
                                    {{ $jobTitle }}
                                </span>

                                @if($mentor->profile?->bio)
                                    <p class="text-secondary small mt-2 mb-0 line-clamp-2" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $mentor->profile->bio }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Footer & Action -->
                        <div>
                            <div class="pt-2 border-top d-flex justify-content-around align-items-center mb-3" style="border-color: var(--lms-border) !important;">
                                <div class="text-center">
                                    <div class="fw-bold fs-6 font-mono text-primary">{{ $courseCount > 0 ? $courseCount : '3+' }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">Courses</div>
                                </div>
                                <div class="border-end h-75" style="border-color: var(--lms-border) !important;"></div>
                                <div class="text-center">
                                    <div class="fw-bold fs-6 font-mono text-primary">5.0 ★</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">Rating</div>
                                </div>
                            </div>

                            <a href="{{ route('mentors.show', $mentor->id) }}" class="btn btn-primary w-100 btn-sm rounded-3 py-2 fw-semibold">
                                View Profile &amp; Courses <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <div class="text-muted mb-3"><i class="bi bi-people fs-1"></i></div>
                    <h5 class="fw-bold text-secondary">No mentors matched your search criteria.</h5>
                    <p class="text-muted small mb-3">Try adjusting your filters or search keywords.</p>
                    <a href="{{ route('mentors.index') }}" class="btn btn-outline-primary btn-sm">Clear All Filters</a>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $mentors->links() }}
        </div>
    </div>
</section>
@endsection
