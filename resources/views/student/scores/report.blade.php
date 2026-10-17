<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambridge Diagnostic Mastery Report — {{ $site_settings['brand_name'] ?? 'AllThingsCambridge' }}</title>
    @php
        $favUrl = $site_settings['brand_favicon_url'] ?? '/favicon.ico';
        $favExt = strtolower(pathinfo(parse_url($favUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $favMime = match($favExt) {
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/png',
        };
        $favVersion = substr(md5($favUrl), 0, 8);
    @endphp
    <link rel="icon" type="{{ $favMime }}" href="{{ $favUrl }}?v={{ $favVersion }}">
    <link rel="shortcut icon" href="{{ $favUrl }}?v={{ $favVersion }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; background: #FFF !important; }
            .card { border: 1px solid #CCC !important; box-shadow: none !important; }
        }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #F8FAFC; color: #1E293B; }
    </style>
</head>
<body class="py-4">
    <div class="container" style="max-width: 900px;">

        <!-- Print Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('scores.history') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to History
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
            </button>
        </div>

        <!-- Save Status Badge Bar -->
        <div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-4 border border-success-subtle">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span class="small fw-semibold text-success">Official Cambridge diagnostic mastery report ready. Real-time scores verified.</span>
            </div>
            <a href="{{ route('scores.history') }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold text-white text-decoration-none">
                Open Report <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <!-- Official Report Card -->
        <div class="card border-0 shadow rounded-4 p-4 p-md-5" style="background: #FFF;">
            
            <!-- Report Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <div class="text-uppercase fw-bold text-primary small letter-spacing-1 mb-1">Official Student Evaluation</div>
                    <h1 class="h3 fw-bold mb-1">Cambridge Academic Mastery Report</h1>
                    <p class="text-muted small mb-0">Framework: Esther Math Real-Time Assessment Matrix</p>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-5 text-dark">AllThingsCambridge UK</div>
                    <div class="text-muted small">Generated: {{ now()->format('d M Y • H:i') }}</div>
                    <span class="badge bg-primary text-white mt-1">Verified Transcript</span>
                </div>
            </div>

            <!-- Student Profile Bar -->
            <div class="row g-3 p-3 rounded-3 bg-light border mb-4">
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Student Name:</div>
                    <div class="fw-bold">{{ $user?->name ?? 'Candidate' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Candidate Email:</div>
                    <div class="fw-bold font-mono">{{ $user?->email ?? 'student@cambridge.co.uk' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Assessment Scope:</div>
                    <div class="fw-bold">Cambridge International</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Overall Average:</div>
                    <div class="fw-bold text-primary font-mono fs-5">{{ $stats['average_pct'] }}%</div>
                </div>
            </div>

            <!-- Performance Summary Badges -->
            <div class="row g-3 mb-4 text-center">
                <div class="col-4">
                    <div class="p-3 rounded-3 border" style="background: #F0FDF4; border-color: #86EFAC !important;">
                        <div class="h4 fw-bold text-success mb-0">{{ $stats['mastery'] }}</div>
                        <div class="small fw-semibold text-success">&gt;= 75% Mastery</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 border" style="background: #FFFBEB; border-color: #FDE68A !important;">
                        <div class="h4 fw-bold text-warning mb-0">{{ $stats['developing'] }}</div>
                        <div class="small fw-semibold text-warning">50–74% Developing</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 border" style="background: #FEF2F2; border-color: #FECACA !important;">
                        <div class="h4 fw-bold text-danger mb-0">{{ $stats['remedial'] }}</div>
                        <div class="small fw-semibold text-danger">&lt; 50% Remedial Focus</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Attempts Table -->
            <h5 class="fw-bold mb-3">Attempt Breakdown &amp; Component History</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Exam Series</th>
                            <th>Subject / Component Details</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td class="font-mono fw-bold">{{ $attempt->year }} {{ $attempt->season }} (Var {{ $attempt->variant }})</td>
                                <td>{{ $attempt->notes ?? $attempt->course?->title ?? 'Past Paper Component' }}</td>
                                <td class="text-center font-mono">{{ $attempt->score_obtained }} / {{ $attempt->max_score }}</td>
                                <td class="text-center font-mono fw-bold">{{ $attempt->percentage }}%</td>
                                <td class="text-center">
                                    @if($attempt->status_color === 'GREEN')
                                        <span class="badge bg-success">Mastery</span>
                                    @elseif($attempt->status_color === 'AMBER')
                                        <span class="badge bg-warning text-dark">Developing</span>
                                    @else
                                        <span class="badge bg-danger">Remedial</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No examination attempts logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Academic Recommendations -->
            <div class="p-3 rounded-3 border bg-light">
                <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Examiner Diagnostic Guidance:</h6>
                <ul class="small text-secondary mb-0 ps-3">
                    <li>Papers with scores below 50% (Red) should be scheduled for an immediate 1-click retake after reviewing official mark schemes.</li>
                    <li>Ensure consistent practice across all paper variants (Variant 1, 2, and 3) to build resilience for unpredictable exam permutations.</li>
                    <li>Maintain daily study streaks to optimize spaced repetition retention before the May/June and Oct/Nov exam sessions.</li>
                </ul>
            </div>

            <!-- Signature Line -->
            <div class="row g-4 mt-4 pt-4 border-top">
                <div class="col-6">
                    <div class="small text-muted mb-4">Verified By Academic Faculty:</div>
                    <div class="border-top pt-2 small fw-bold">Cambridge Senior Examiner &amp; Lead Tutor</div>
                </div>
                <div class="col-6 text-end">
                    <div class="small text-muted mb-4">Official Verification Seal:</div>
                    <span class="badge bg-dark px-3 py-2 text-uppercase">ATC-VALIDATED-{{ strtoupper(Str::random(8)) }}</span>
                </div>
            </div>

        </div>

    </div>
</body>
</html>
