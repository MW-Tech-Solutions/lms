<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCORM Runtime - {{ $package->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #0F172A; font-family: sans-serif; }
        #scorm-frame { width: 100%; height: calc(100% - 48px); border: none; background: #FFF; }
        .runtime-toolbar { height: 48px; background: #1E293B; color: #FFF; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; border-bottom: 1px solid #334155; }
    </style>
</head>
<body>
    <div class="runtime-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">SCORM {{ $package->version }}</span>
            <span class="fw-semibold text-truncate" style="max-width: 400px;">{{ $package->title }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted font-monospace" id="cmi-status-indicator">Status: not attempted</span>
            <span class="small text-muted font-monospace" id="cmi-score-indicator">Score: 0</span>
        </div>
    </div>

    <!-- Active Content Frame or Interactive Demonstration Sandbox -->
    @if(!empty($package->entry_url))
        <iframe id="scorm-frame" src="{{ $package->entry_url }}"></iframe>
    @else
        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
            <div class="card p-5 text-dark text-center shadow-lg" style="max-width: 600px;">
                <i class="bi bi-box-seam display-3 text-warning mb-3"></i>
                <h4 class="fw-bold">SCORM 1.2 / 2004 Runtime Simulation Sandbox</h4>
                <p class="text-secondary small mb-4">
                    The LMS CMI Runtime Bridge is active and listening for <code>window.API</code> or <code>window.API_1484_11</code> calls.
                </p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="API.LMSSetValue('cmi.core.lesson_status', 'incomplete'); API.LMSCommit('');">
                        Simulate Lesson Started (Incomplete)
                    </button>
                    <button class="btn btn-success" onclick="API.LMSSetValue('cmi.core.score.raw', '95'); API.LMSSetValue('cmi.core.lesson_status', 'passed'); API.LMSCommit('');">
                        Simulate Lesson Passed (Score 95%) &amp; Commit Telemetry
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Standard SCORM 1.2 & 2004 CMI JavaScript Bridge -->
    <script>
        const packageId = {{ $package->id }};
        const commitUrl = '{{ route('api.scorm.commit', ['package_id' => $package->id]) }}';
        const csrfToken = '{{ csrf_token() }}';

        let cmiData = {
            'cmi.core.lesson_status': 'not attempted',
            'cmi.core.score.raw': '0',
            'cmi.core.lesson_location': '',
            'cmi.suspend_data': ''
        };

        function updateUI() {
            const statusEl = document.getElementById('cmi-status-indicator');
            const scoreEl = document.getElementById('cmi-score-indicator');
            if (statusEl) statusEl.textContent = 'Status: ' + (cmiData['cmi.core.lesson_status'] || 'unknown');
            if (scoreEl) scoreEl.textContent = 'Score: ' + (cmiData['cmi.core.score.raw'] || '0');
        }

        // SCORM 1.2 API
        window.API = {
            LMSInitialize: function() { console.log('[SCORM 1.2] LMSInitialize'); return "true"; },
            LMSFinish: function() { console.log('[SCORM 1.2] LMSFinish'); return "true"; },
            LMSGetValue: function(element) { return cmiData[element] !== undefined ? String(cmiData[element]) : ""; },
            LMSSetValue: function(element, value) {
                cmiData[element] = String(value);
                updateUI();
                return "true";
            },
            LMSCommit: function() {
                fetch(commitUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(cmiData)
                }).then(r => r.json()).then(res => console.log('[SCORM CMI Commited]', res));
                return "true";
            },
            LMSGetLastError: function() { return "0"; },
            LMSGetErrorString: function() { return "No error"; },
            LMSGetDiagnostic: function() { return "OK"; }
        };

        // SCORM 2004 API
        window.API_1484_11 = {
            Initialize: window.API.LMSInitialize,
            Terminate: window.API.LMSFinish,
            GetValue: window.API.LMSGetValue,
            SetValue: window.API.LMSSetValue,
            Commit: window.API.LMSCommit,
            GetLastError: window.API.LMSGetLastError,
            GetErrorString: window.API.LMSGetErrorString,
            GetDiagnostic: window.API.LMSGetDiagnostic
        };
    </script>
</body>
</html>
