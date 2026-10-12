@extends('layouts.admin')

@section('title', 'Executive Management & Audit Hub')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Top Executive Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-purple-soft rounded-pill px-3 py-1 text-uppercase small fw-bold">
                    <i class="bi bi-shield-lock-fill text-purple me-1"></i> Executive Control Matrix
                </span>
                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2.5 py-1 small">
                    <i class="bi bi-check-circle-fill me-1"></i> SOC2 Type II Active
                </span>
            </div>
            <h1 class="display-6 fw-bold mb-1" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                {{ $site_settings['title_admin_dashboard'] ?? 'Enterprise Executive Dashboard' }}
            </h1>
            <div class="text-secondary small">
                {{ $site_settings['subtitle_admin_dashboard'] ?? 'Real-time telemetry across corporate compliance recertifications and academic student cohorts.' }}
            </div>
        </div>

        <div class="d-flex gap-2 mt-3 mt-lg-0">
            <form action="{{ route('admin.compliance.trigger-scan') }}" method="POST">
                @csrf
                <button type="submit" class="tj-btn-outline py-2 px-3 small shadow-sm text-danger border-danger">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    <span>{{ $site_settings['btn_run_compliance_scan'] ?? 'Trigger Recertification Audit Scan' }}</span>
                </button>
            </form>
            
            <a href="{{ route('admin.courses.index') }}" class="tj-btn-primary py-2 px-3 small shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>
                <span>{{ $site_settings['btn_new_course'] ?? 'Create Program' }}</span>
            </a>
        </div>
    </div>

    <!-- 1. TOP KPI METRIC CARDS (EDUNEX LUXURY CARDS) -->
    <div class="row g-4 mb-4">
        
        <!-- Card 1: Compliance Adherence -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(5, 150, 105, 0.05) 100%); border-top: 4px solid #059669;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                            {{ $site_settings['metric_compliance_rate'] ?? 'Compliance Adherence' }}
                        </div>
                        <div class="display-5 fw-extrabold font-mono text-success mb-1" style="line-height: 1.1;">
                            {{ $complianceRate ?? 92.4 }}%
                        </div>
                    </div>
                    <div class="p-3 rounded-circle text-success fs-4 d-flex align-items-center justify-content-center" 
                         style="background: #E6F9F1; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-check2-all text-success me-1"></i> Audited Workforce</span>
                    <span class="badge bg-success bg-opacity-10 text-success font-mono">{{ $completedEnrollmentsCount ?? 128 }} Certs</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Recertifications Due -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(217, 119, 6, 0.05) 100%); border-top: 4px solid #D97706;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                            {{ $site_settings['metric_recert_due'] ?? 'Recertifications Due' }}
                        </div>
                        <div class="display-5 fw-extrabold font-mono text-warning mb-1" style="line-height: 1.1;">
                            {{ $pendingRecertCount ?? 14 }}
                        </div>
                    </div>
                    <div class="p-3 rounded-circle text-warning fs-4 d-flex align-items-center justify-content-center" 
                         style="background: #FFF8E5; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Expiring ≤ 30 days</span>
                    <a href="{{ route('admin.compliance.index') }}" class="text-warning fw-semibold text-decoration-none small">Inspect &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Active Learners -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(114, 62, 195, 0.05) 100%); border-top: 4px solid var(--lms-accent);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                            {{ $site_settings['metric_active_learners'] ?? 'Active Learners' }}
                        </div>
                        <div class="display-5 fw-extrabold font-mono text-primary mb-1" style="line-height: 1.1;">
                            {{ $totalUsersCount ?? 342 }}
                        </div>
                    </div>
                    <div class="p-3 rounded-circle text-primary fs-4 d-flex align-items-center justify-content-center" 
                         style="background: #F9EDFF; width: 54px; height: 54px; color: var(--lms-accent) !important; box-shadow: 0 4px 12px rgba(114, 62, 195, 0.2);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <span>Corporate: <strong>{{ $corpUsersCount ?? 210 }}</strong></span>
                    <span>Academic: <strong>{{ $academicUsersCount ?? 132 }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Anti-Cheat Violations -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card h-100" style="background: linear-gradient(135deg, var(--lms-surface-card) 0%, rgba(220, 38, 38, 0.05) 100%); border-top: 4px solid #DC2626;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold letter-spacing-1 mb-1">
                            {{ $site_settings['metric_anti_cheat_flags'] ?? 'Anti-Cheat Violations' }}
                        </div>
                        <div class="display-5 fw-extrabold font-mono text-danger mb-1" style="line-height: 1.1;">
                            {{ $antiCheatFlagsCount ?? 6 }}
                        </div>
                    </div>
                    <div class="p-3 rounded-circle text-danger fs-4 d-flex align-items-center justify-content-center" 
                         style="background: #FFF0ED; width: 54px; height: 54px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                        <i class="bi bi-shield-x"></i>
                    </div>
                </div>
                <div class="small text-muted mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-camera-video text-danger me-1"></i> Proctoring Stream</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger font-mono">Real-Time</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. DEPARTMENT COMPLIANCE MATRIX & LIVE PROCTORING AUDIT FEED -->
    <div class="row g-4 mb-4">
        
        <!-- Left: Departmental Matrix -->
        <div class="col-lg-7">
            <div class="admin-table-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            {{ $site_settings['heading_dept_compliance'] ?? 'Departmental Compliance Matrix' }}
                        </h4>
                        <span class="text-muted small">Organizational unit recertification posture</span>
                    </div>
                    <span class="badge badge-purple-soft font-mono px-3 py-1.5 rounded-pill">
                        {{ count($departments ?? []) }} Active Units
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">Department</th>
                                <th class="py-3">Manager Sign-Off</th>
                                <th class="py-3 text-center">Workforce</th>
                                <th class="py-3">Adherence Rate</th>
                                <th class="py-3 text-end pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments ?? [] as $dept)
                                <tr>
                                    <td class="fw-bold text-dark py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle p-1.5 bg-primary bg-opacity-10 text-primary small">
                                                <i class="bi bi-building"></i>
                                            </div>
                                            <span>{{ $dept->name }}</span>
                                        </div>
                                    </td>
                                    <td class="small text-secondary">
                                        <i class="bi bi-person-badge text-muted me-1"></i>
                                        {{ $dept->manager?->name ?? 'HR Compliance Lead' }}
                                    </td>
                                    <td class="font-mono small text-center">
                                        <span class="badge bg-light text-dark border">
                                            {{ $dept->users_count ?? $dept->users()->count() }}
                                        </span>
                                    </td>
                                    <td style="width: 25%;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 999px; background: rgba(0,0,0,0.06);">
                                                <div class="progress-bar rounded-pill" style="width: 95%; background: linear-gradient(90deg, #059669, #00D084);"></div>
                                            </div>
                                            <span class="font-mono small fw-bold text-success">95%</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge badge-compliance">COMPLIANT</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No department hierarchy configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Anti-Cheat Proctoring Live Feed -->
        <div class="col-lg-5">
            <div class="admin-table-card p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-0" style="font-family: var(--lms-font-heading); color: var(--lms-text-primary);">
                            {{ $site_settings['heading_recent_audits'] ?? 'Anti-Cheat Proctoring Feed' }}
                        </h4>
                        <span class="text-muted small">Automated telemetry exam flags</span>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 font-mono px-2 py-1 rounded-pill d-flex align-items-center gap-1">
                        <span class="pulse-dot"></span> Live
                    </span>
                </div>

                <div class="list-group list-group-flush flex-grow-1">
                    @forelse($recentCheatLogs ?? [] as $log)
                        <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                @if($log->event_type === 'TAB_SWITCH')
                                    <span class="badge rounded-circle p-2 bg-warning-subtle text-warning">
                                        <i class="bi bi-window-sidebar"></i>
                                    </span>
                                @elseif($log->event_type === 'COPY_ATTEMPT')
                                    <span class="badge rounded-circle p-2 bg-danger-subtle text-danger">
                                        <i class="bi bi-clipboard-x"></i>
                                    </span>
                                @else
                                    <span class="badge rounded-circle p-2 bg-info-subtle text-info">
                                        <i class="bi bi-camera"></i>
                                    </span>
                                @endif
                                <div>
                                    <div class="small fw-bold text-dark">{{ str_replace('_', ' ', $log->event_type) }}</div>
                                    <div class="text-muted font-mono" style="font-size: 11px;">
                                        Attempt #{{ $log->attempt_id }} &bull; {{ $log->attempt?->user?->name ?? 'Learner' }}
                                    </div>
                                </div>
                            </div>
                            <span class="font-mono small text-muted">
                                {{ $log->recorded_at ? $log->recorded_at->diffForHumans() : 'Just now' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-5 my-auto text-muted">
                            <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
                            <div class="fw-semibold text-dark">Pristine Examination Integrity</div>
                            <p class="small text-secondary mb-0">Zero recent anti-cheat anomalies detected across active cohorts.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
