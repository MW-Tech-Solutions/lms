<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - {{ $certificate->certificate_number ?? $certificate->uuid }}</title>
    @php
        $settings = $site_settings ?? (app()->bound(\App\Services\SiteConfigService::class) ? app(\App\Services\SiteConfigService::class)->all() : []);
        $vUrl = $verificationUrl ?? url('/verify/' . $certificate->uuid);
        $imgBase64 = $certificateImageBase64 ?? null;
        if (empty($imgBase64)) {
            $service = app(\App\Services\CertificateGeneratorService::class);
            $raw = $service->generateCertificateCompositeImage($certificate, $user, $course, $vUrl, $settings);
            if ($raw) {
                $imgBase64 = 'data:image/jpeg;base64,' . base64_encode($raw);
            }
        }
    @endphp
    <style>
        @page {
            margin: 0;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background: #0F172A;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .cert-viewport {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .certificate-img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            aspect-ratio: 942 / 670;
            object-fit: contain;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-radius: 4px;
            display: block;
            background: #FFFFFF;
        }
        @media print {
            html, body {
                background: #FFFFFF !important;
                display: block !important;
                width: 100% !important;
                height: 100% !important;
                overflow: visible !important;
            }
            .cert-viewport {
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                display: block !important;
            }
            .certificate-img {
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                max-height: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                object-fit: fill !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="cert-viewport">
        @if(!empty($imgBase64))
            <img src="{{ $imgBase64 }}" class="certificate-img" alt="Certificate of Completion - {{ $certificate->certificate_number }} awarded to {{ $user->name }} for {{ $course->title }}">
        @endif
    </div>

    <!-- Accessible screen-reader metadata -->
    <div style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">
        <h1>Certificate of Completion</h1>
        <p>Recipient: {{ $user->name }}</p>
        <p>Course: {{ $course->title }}</p>
        <p>Certificate ID: {{ $certificate->certificate_number }}</p>
        <p>Date of Completion: {{ $issuedDate }}</p>
        <p>Verification URL: {{ $vUrl }}</p>
    </div>

    @if($isHtmlView ?? false)
        <div class="no-print" style="position: fixed; top: 16px; right: 16px; z-index: 9999; display: flex; gap: 8px;">
            <button onclick="window.print()" style="background: #10B981; color: white; border: none; padding: 10px 18px; border-radius: 9999px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.25); font-family: sans-serif; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print / Save as PDF
            </button>
            <a href="{{ route('certificates.verify', ['uuid' => $certificate->uuid]) }}" style="background: #334155; color: white; text-decoration: none; padding: 10px 18px; border-radius: 9999px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.25); font-family: sans-serif; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to Verification
            </a>
        </div>
    @endif
</body>
</html>
