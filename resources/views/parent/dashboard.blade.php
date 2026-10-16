@extends('layouts.app')

@section('title', 'Guardian & Parent Oversight Portal')

@section('content')
<div class="py-4 border-bottom position-relative" style="background: radial-gradient(circle at 85% 10%, rgba(2, 132, 199, 0.08) 0%, rgba(255, 242, 229, 0.4) 60%, var(--lms-bg-body) 100%);">
    <div class="container py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-sm border mb-2 bg-white">
                    <i class="bi bi-person-heart text-primary"></i>
                    <span class="small fw-bold text-dark">PARENT &amp; GUARDIAN PORTAL</span>
                </div>
                <h1 class="display-6 fw-bold mb-1 text-dark" style="font-family: var(--lms-font-heading);">
                    Welcome, {{ $parent->name }}
                </h1>
                <p class="text-secondary small mb-0">
                    Monitor syllabus progress, view Esther Math past-paper scores, and follow your child's Cambridge exam preparation.
                </p>
            </div>

            <!-- Connect Child Action -->
            <button class="tj-btn-primary px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#linkChildModal">
                <i class="bi bi-plus-circle me-1"></i> Connect Student
            </button>
        </div>
    </div>
</div>

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($linkedStudents as $link)
            <div class="col-md-6 col-lg-4">
                <div class="lms-card h-100 p-4 border shadow-sm rounded-4 bg-white d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-5" style="width: 52px; height: 52px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                            {{ strtoupper(substr($link->student->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $link->student->name }}</h5>
                            <div class="text-muted small">{{ $link->student->email }}</div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.7rem;">
                                {{ $link->relationship }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3 border small">
                        <div class="d-flex justify-content-between mb-1.5">
                            <span class="text-muted">Enrolled Courses:</span>
                            <span class="fw-bold font-mono">{{ $link->student->enrollments->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Past Paper Attempts:</span>
                            <span class="fw-bold font-mono">{{ $link->attempts_count ?? 0 }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-1 pt-2 border-top">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small" title="Mastered (>=75%)">
                                🟢 {{ $link->mastery_count ?? 0 }} Mastered
                            </span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 small" title="Developing (50-74%)">
                                🟡 {{ $link->developing_count ?? 0 }} Review
                            </span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 small" title="Remedial (<50%)">
                                🔴 {{ $link->remedial_count ?? 0 }} Help
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto pt-2">
                        <a href="{{ route('parent.child.progress', $link->student_id) }}" class="tj-btn-outline w-100 justify-content-center py-2 text-decoration-none">
                            <span>View Academic Tracker</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 border rounded-4 bg-white shadow-sm" style="max-width: 540px; margin: 0 auto;">
                    <i class="bi bi-people display-4 text-primary opacity-50 d-block mb-3"></i>
                    <h4 class="fw-bold text-dark">No Students Connected Yet</h4>
                    <p class="text-secondary small mb-4">
                        To link your account, ask your child to generate an invitation code from their student dashboard settings, then enter the 6-character code below.
                    </p>
                    <button class="tj-btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#linkChildModal">
                        <i class="bi bi-link-45deg me-1"></i> Enter Student Code
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: Link Child via Invite Code -->
<div class="modal fade" id="linkChildModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Connect Student Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('parent.link.student') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p class="small text-secondary mb-3">
                        Enter the unique 6-character invitation code provided by your child (e.g. <code>CMP-742K</code>). Codes expire after 48 hours.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Invitation Code</label>
                        <input type="text" name="invite_code" class="form-control form-control-lg font-mono text-center text-uppercase" placeholder="CMP-XXXXXX" required maxlength="12">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tj-btn-primary px-4 py-2">Connect Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
