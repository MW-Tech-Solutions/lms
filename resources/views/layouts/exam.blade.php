<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $site_settings['brand_name'] ?? config('app.name', 'AllThingsCambridge') }} &bull; @yield('title', 'Examination Environment')</title>

    <!-- Favicon from Dynamic Brand Settings -->
    <link rel="icon" type="image/x-icon" href="{{ !empty($site_settings['brand_favicon_url']) ? $site_settings['brand_favicon_url'] : asset('favicon.ico') }}">

    <!-- Google Fonts: Manrope (Body) & Urbanist (Headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Urbanist:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/lms.css') }}">

    <style>
        :root {
            --lms-primary: {{ $site_settings['brand_primary_color'] ?? '#0F1321' }};
            --lms-accent: {{ $site_settings['brand_accent_color'] ?? '#723EC3' }};
            --lms-accent-hover: {{ $site_settings['brand_accent_hover_color'] ?? '#5E32A6' }};
            --tj-color-theme-primary: {{ $site_settings['brand_accent_color'] ?? '#723EC3' }};
            --tj-color-theme-primary-hover: {{ $site_settings['brand_accent_hover_color'] ?? '#5E32A6' }};
            --tj-color-theme-primary-rgb: {{ $site_settings['brand_accent_rgb'] ?? '114, 62, 195' }};
        }
        body {
            user-select: none;
            -webkit-user-select: none;
            overflow-x: hidden;
            background-color: #090D16;
            color: #F8FAFC;
        }
        .proctoring-hud {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
            padding: 10px 14px;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body oncontextmenu="return false;">
    <!-- Exam Header -->
    <header class="exam-header d-flex align-items-center justify-content-between px-4">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-danger text-uppercase px-2 py-1 fw-bold tracking-wider">{{ $site_settings['label_secure_exam'] ?? 'SECURE EXAM RUNTIME' }}</span>
            <span class="fw-bold fs-5 text-truncate">@yield('exam_title', 'Assessment')</span>
        </div>

        <div class="d-flex align-items-center gap-4">
            <div class="anti-cheat-indicator">
                <span class="pulse-dot"></span>
                <span class="small font-mono text-light">{{ $site_settings['label_anti_cheat_active'] ?? 'Anti-Cheat Proctoring Active' }}</span>
            </div>

            @yield('header_timer')
        </div>
    </header>

    <!-- Proctoring Camera PiP HUD -->
    <div class="proctoring-hud d-flex align-items-center gap-3 shadow">
        <video id="proctoring-webcam" width="90" height="60" autoplay playsinline muted style="border-radius: 6px; background: #000; object-fit: cover;"></video>
        <div class="small">
            <div class="fw-bold text-success d-flex align-items-center gap-1">
                <i class="bi bi-camera-video-fill"></i>
                <span>{{ $site_settings['label_stream_connected'] ?? 'AI Proctor Live' }}</span>
            </div>
            <div class="text-muted font-mono" style="font-size: 10px;">ID: {{ Auth::user()->employee_id ?? Auth::user()->id }}</div>
        </div>
    </div>

    <!-- Main Exam Container -->
    <main class="container my-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Request webcam access for AI Proctoring
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(stream => {
                const videoEl = document.getElementById('proctoring-webcam');
                if (videoEl) videoEl.srcObject = stream;
            })
            .catch(err => {
                console.warn('Webcam permission not granted or device not present:', err);
            });
    </script>
    @stack('scripts')
</body>
</html>
