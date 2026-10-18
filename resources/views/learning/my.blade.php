@extends('layouts.app')

@section('title', 'My Learning & Compliance Dashboard')

@section('content')
<div class="py-5" style="background: var(--lms-bg-body);">
    <div class="container py-2">
        
        <!-- 1. WELCOME PROFILE BANNER -->
        <div class="p-4 p-md-5 rounded-4 mb-5 position-relative overflow-hidden shadow-sm" 
             style="background: linear-gradient(135deg, #151928 0%, #272F54 50%, #723EC3 100%); color: #FFFFFF;">
            
            <div class="position-relative" style="z-index: 2;">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-extrabold display-6 shadow" 
                                 style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--lms-accent), #FF4D00); border: 3px solid rgba(255,255,255,0.3);">
                                {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                            </div>
                            <div>
                                <span class="badge rounded-pill px-3 py-1 mb-1 text-white" style="background: rgba(255,255,255,0.18); font-size: 0.72rem; letter-spacing: 0.5px;">
                                    {{ Auth::user()->user_type === 'EMPLOYEE' ? 'CORPORATE WORKFORCE' : 'ACADEMIC SCHOLAR' }}
                                </span>
                                <h1 class="display-6 fw-bold mb-0 text-white" style="font-family: var(--lms-font-heading);">
                                    Welcome back, {{ Auth::user()->name ?? 'Learner' }}!
                                </h1>
                                <div class="text-white-50 small font-mono mt-1">
                                    ID: {{ Auth::user()->employee_id ?? 'STD-9901' }} &bull; Department: {{ Auth::user()->department?->name ?? 'Enterprise Hub' }}
                                </div>
                            </div>
                        </div>
                        <p class="text-white-50 small mb-0 pe-lg-4" style="line-height: 1.7;">
                            Track enrolled certifications, review passing telemetry, and ensure mandatory compliance recertifications remain audit-ready.
                        </p>
                    </div>

                    <!-- Quick Summary Pills -->
                    <div class="col-lg-5">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-3 rounded-3 text-center" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                                    <div class="display-6 fw-extrabold font-mono text-warning mb-0">{{ count($enrollments) }}</div>
                                    <div class="text-white-50 small" style="font-size: 0.75rem;">Active Courses</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 text-center" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                                    <div class="display-6 fw-extrabold font-mono text-success mb-0">{{ count($certificates) }}</div>
                                    <div class="text-white-50 small" style="font-size: 0.75rem;">Certificates Earned</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. ACTIVE ENROLLED PROGRAMS (FIRST AFTER PROFILE BANNER) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                    Active Enrolled Programs
                </h3>
                <p class="text-secondary small mb-0">Continue your curriculum or access self-paced modules.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="tj-btn-outline py-2 px-3 small">
                <i class="bi bi-search me-1"></i>
                <span>Explore Catalog</span>
            </a>
        </div>

        <!-- ENROLLED COURSES CARDS GRID -->
        <div class="row g-4 mb-5">
            @forelse($enrollments as $enrollment)
                <div class="col-md-6 col-lg-4">
                    <div class="lms-card p-4 h-100 d-flex flex-column shadow-sm border position-relative" 
                         style="background: var(--lms-surface-card); border-radius: 20px;">
                        
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                                {{ $enrollment->course->category?->name ?? 'Course' }}
                            </span>
                            @if($enrollment->status === 'COMPLETED')
                                <span class="badge badge-compliance">COMPLETED</span>
                            @elseif($enrollment->status === 'EXPIRED')
                                <span class="badge badge-overdue">RECERTIFICATION EXPIRED</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-bold">ACTIVE</span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-2" style="font-family: var(--lms-font-heading);">
                            <a href="{{ route('learning.player', ['course' => $enrollment->course->slug]) }}" class="text-decoration-none text-reset">
                                {{ $enrollment->course->title }}
                            </a>
                        </h4>

                        <!-- Recertification Due Badge -->
                        @if($enrollment->expires_at)
                            <div class="mb-3 p-2.5 rounded-3 small font-mono d-flex align-items-center gap-2 {{ $enrollment->isExpired() ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                                <i class="bi bi-clock-history fs-6"></i>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.72rem;">RECERTIFICATION DEADLINE</div>
                                    <div style="font-size: 0.8rem;">{{ $enrollment->expires_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Progress Bar Simulation -->
                        <div class="my-3">
                            <div class="d-flex justify-content-between small text-muted mb-1 font-mono">
                                <span>Curriculum Progress</span>
                                <span class="fw-bold text-body">{{ $enrollment->status === 'COMPLETED' ? '100%' : '65%' }}</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar rounded-pill" 
                                     style="width: {{ $enrollment->status === 'COMPLETED' ? '100%' : '65%' }}; background: linear-gradient(90deg, #723EC3, #2563EB);"></div>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted font-mono" style="font-size: 0.75rem;">
                                Enrolled: {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'Active' }}
                            </span>
                            <a href="{{ route('learning.player', ['course' => $enrollment->course->slug]) }}" class="tj-btn-primary py-2 px-3 small">
                                <span>Resume</span>
                                <span class="btn-icon"><i class="bi bi-play-fill ms-1"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="lms-card p-5 mx-auto text-center" style="max-width: 500px; border-radius: 24px;">
                        <i class="bi bi-journal-plus display-3 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold">No active enrollments found</h4>
                        <p class="text-secondary small">Explore our catalog of corporate compliance tracks and public accredited certifications.</p>
                        <a href="{{ route('courses.index') }}" class="tj-btn-primary py-2.5 px-4 mx-auto mt-2">
                            <span>Browse Course Catalog</span>
                            <span class="btn-icon"><i class="bi bi-arrow-right ms-2"></i></span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- 3. FAMILY & PARENT CONNECTION MODULE -->
        <div class="lms-card p-4 p-md-4 rounded-4 mb-5 border shadow-sm" 
             style="background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%); border-radius: 24px; border-color: rgba(2, 132, 199, 0.2) !important;"
             x-data="{
                 currentCode: '{{ $activeInvite->invite_code ?? '' }}',
                 codeCopied: false,
                 isGenerating: false,
                 async generateCode() {
                     this.isGenerating = true;
                     try {
                         const csrf = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '';
                         const res = await fetch('{{ route('parent.invite.generate') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'Accept': 'application/json',
                                 'X-CSRF-TOKEN': csrf,
                             },
                             body: JSON.stringify({})
                         });
                         const data = await res.json();
                         if (data.success) {
                             this.currentCode = data.invite_code;
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Family Code Generated!',
                                 text: 'Share code ' + data.invite_code + ' with your parent or guardian. It is active for 48 hours.',
                                 confirmButtonColor: '#723EC3'
                             });
                         }
                     } catch (e) {
                         Swal.fire({ icon: 'error', title: 'Error', text: 'Could not generate code right now.' });
                     } finally {
                         this.isGenerating = false;
                     }
                 },
                 copyCode() {
                     if (!this.currentCode) return;
                     navigator.clipboard.writeText(this.currentCode);
                     this.codeCopied = true;
                     setTimeout(() => this.codeCopied = false, 2500);
                 }
             }">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" 
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" style="font-family: var(--lms-font-heading);">
                            Family &amp; Guardian Connection
                        </h4>
                        <div class="text-secondary small">
                            Connect your student account with your parent or guardian to celebrate your exam milestones.
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold" 
                            @click="generateCode()" :disabled="isGenerating">
                        <i class="bi bi-arrow-repeat me-1" :class="isGenerating ? 'spin' : ''"></i>
                        <span x-text="currentCode ? 'Generate Fresh Code' : 'Generate Parent Invite Code'"></span>
                    </button>
                </div>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Left: Connected Parents Status -->
                <div class="col-lg-6">
                    <div class="p-3 rounded-3 border bg-white h-100">
                        <div class="small fw-bold text-dark text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            Connected Parents / Guardians
                        </div>
                        @if(isset($linkedParents) && $linkedParents && $linkedParents->isNotEmpty())
                            <div class="d-flex flex-column gap-2">
                                @foreach($linkedParents as $link)
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold small" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($link->parent->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small">{{ $link->parent->name ?? 'Parent' }}</strong>
                                                <span class="text-muted" style="font-size: 0.72rem;">{{ $link->relationship ?? 'Parent/Guardian' }} &bull; Connected</span>
                                            </div>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active Link
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-2 text-muted small">
                                <i class="bi bi-person-x fs-4 d-block text-secondary mb-1"></i>
                                <span>No parent linked yet. Share your invite code with mom or dad to connect!</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Active Invite Code Display -->
                <div class="col-lg-6">
                    <div class="p-3 rounded-3 border bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark"><i class="bi bi-key-fill text-primary me-1"></i> Your 6-Digit Parent Link Code:</span>
                                <span class="badge bg-light text-muted border font-mono small">Valid for 48 Hours</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                <span class="fs-4 fw-bold font-mono text-primary px-3 py-1 rounded-3 bg-light border flex-grow-1 text-center" 
                                      style="letter-spacing: 2px;" x-text="currentCode ? currentCode : 'NO CODE ACTIVE'">
                                    {{ $activeInvite->invite_code ?? 'NO CODE ACTIVE' }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold flex-shrink-0" 
                                        @click="copyCode()" :disabled="!currentCode">
                                    <i class="bi" :class="codeCopied ? 'bi-check2' : 'bi-clipboard'"></i>
                                    <span x-text="codeCopied ? 'Copied!' : 'Copy Code'"></span>
                                </button>
                            </div>
                        </div>
                        <div class="small text-muted mt-2" style="font-size: 0.75rem; line-height: 1.4;">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Parents can view your completed past papers and study streaks, but cannot change your answers or exam timers.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. STUDENT SCORE ENTRY & ESTHER MATH MATRIX TRACKER -->
        <div class="mb-5">
            @include('cms.sections.dynamic_form')
        </div>

        <!-- 4. ACHIEVED CRYPTOGRAPHIC CERTIFICATES -->
        @if(isset($certificates) && $certificates && $certificates->isNotEmpty())
            <div class="lms-card p-4 p-md-5 shadow-sm border" style="background: var(--lms-surface-card); border-radius: 24px;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h3 class="fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            <i class="bi bi-award-fill text-warning me-2"></i>Verified Cryptographic Credentials
                        </h3>
                        <p class="text-secondary small mb-0">Publicly auditable certificates signed with cryptographic SHA-256 tokens.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill font-mono small">
                        <i class="bi bi-patch-check-fill me-1"></i> {{ count($certificates) }} Verified Credentials
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">Program Title</th>
                                <th class="py-3">Certificate UUID</th>
                                <th class="py-3">Issued Date</th>
                                <th class="py-3">Recertification Status</th>
                                <th class="py-3">Verification</th>
                                <th class="py-3 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $cert)
                                <tr>
                                    <td class="fw-bold text-dark py-3">{{ $cert->course->title }}</td>
                                    <td class="font-mono text-muted small">{{ $cert->uuid }}</td>
                                    <td class="font-mono small">{{ $cert->issued_at->format('M d, Y') }}</td>
                                    <td class="font-mono small">
                                        {{ $cert->expires_at ? $cert->expires_at->format('M d, Y') : 'Permanent Validity' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $cert->isValid() ? 'badge-compliance' : 'badge-overdue' }}">
                                            {{ $cert->isValid() ? 'VALID & VERIFIED' : 'EXPIRED' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('certificates.verify', ['uuid' => $cert->uuid]) }}" target="_blank" class="tj-btn-outline py-1 px-2.5 small" title="View Public Verification">
                                                <i class="bi bi-qr-code-scan me-1"></i>
                                                <span>Verify</span>
                                            </a>
                                            <a href="{{ route('certificates.pdf', ['uuid' => $cert->uuid]) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2.5" title="Open in PDF Viewer">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
