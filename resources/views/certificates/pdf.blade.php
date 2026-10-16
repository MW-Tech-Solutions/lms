<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement - {{ $certificate->uuid }}</title>
    @php
        $settings = $site_settings ?? (app()->bound(\App\Services\SiteConfigService::class) ? app(\App\Services\SiteConfigService::class)->all() : []);
        $brandName = $settings['brand_name'] ?? 'Enterprise International Academy';
        $logoBase64 = null;
        $logoPath = null;
        if (!empty($settings['brand_logo_url']) && str_starts_with($settings['brand_logo_url'], '/')) {
            $logoPath = public_path(ltrim($settings['brand_logo_url'], '/'));
        } elseif (!empty($settings['brand_logo_url']) && str_contains($settings['brand_logo_url'], 'uploads/branding/')) {
            $parts = explode('uploads/branding/', $settings['brand_logo_url']);
            $logoPath = public_path('uploads/branding/' . end($parts));
        } elseif (file_exists(public_path('images/site_logo.png'))) {
            $logoPath = public_path('images/site_logo.png');
        }
        if ($logoPath && file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . ($type === 'svg' ? 'svg+xml' : $type) . ';base64,' . base64_encode($data);
        }
    @endphp
    <style>
        @page {
            margin: 0;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #FDFBF7;
            color: #0F172A;
            margin: 0;
            padding: 24px;
            width: 100%;
            height: 100%;
        }
        .outer-border {
            border: 4px solid #1E293B;
            padding: 8px;
            background: #FFFFFF;
            height: 100%;
            position: relative;
        }
        .inner-border {
            border: 2px solid #D97706;
            outline: 1px dashed #B45309;
            outline-offset: -6px;
            padding: 32px 48px;
            text-align: center;
            background: radial-gradient(circle at center, #FFFFFF 40%, #FAFAF8 100%);
            position: relative;
            min-height: 520px;
        }
        .corner-ornament {
            position: absolute;
            width: 28px;
            height: 28px;
            color: #D97706;
            font-size: 20px;
            line-height: 28px;
        }
        .corner-tl { top: 6px; left: 8px; }
        .corner-tr { top: 6px; right: 8px; }
        .corner-bl { bottom: 6px; left: 8px; }
        .corner-br { bottom: 6px; right: 8px; }

        .logo-wrap {
            margin-bottom: 8px;
            text-align: center;
        }
        .header-brand {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #1E293B;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        .cert-ribbon {
            display: inline-block;
            background: #D97706;
            color: #FFFFFF;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 20px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
        .cert-title {
            font-size: 36px;
            font-weight: 900;
            color: #0F172A;
            letter-spacing: 2px;
            margin: 4px 0 10px 0;
            text-transform: uppercase;
            font-family: 'Georgia', 'Times New Roman', serif;
        }
        .cert-subtitle {
            font-size: 13px;
            font-style: italic;
            letter-spacing: 1.5px;
            color: #64748B;
            margin-bottom: 16px;
        }
        .recipient-name {
            font-size: 32px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 2px solid #D97706;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 16px;
            min-width: 460px;
            letter-spacing: 1px;
        }
        .cert-body {
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            max-width: 680px;
            margin: 0 auto 20px auto;
        }
        .course-title {
            font-size: 20px;
            font-weight: 700;
            color: #1E3A8A;
            display: block;
            margin-top: 6px;
            font-family: 'Georgia', serif;
        }

        .footer-table {
            width: 100%;
            margin-top: 24px;
            border-collapse: collapse;
        }
        .signature-line {
            width: 180px;
            border-bottom: 1.5px solid #64748B;
            margin-bottom: 6px;
        }
        .signature-title {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #1E293B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .signature-sub {
            font-size: 10px;
            color: #64748B;
            margin-top: 2px;
        }
        .seal-medal {
            display: inline-block;
            width: 82px;
            height: 82px;
            border-radius: 50%;
            border: 3px double #D97706;
            background: radial-gradient(circle, #FDE68A 0%, #D97706 100%);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
            text-align: center;
            vertical-align: middle;
            color: #78350F;
            padding-top: 14px;
        }
        .seal-star {
            font-size: 14px;
            line-height: 14px;
            margin-bottom: 2px;
        }
        .seal-text {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 11px;
        }
        .qr-wrap {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            color: #64748B;
        }
        .qr-uuid {
            font-size: 10px;
            color: #1E293B;
            font-weight: bold;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="inner-border">
            <!-- Corner Ornaments -->
            <div class="corner-ornament corner-tl">&#10022;</div>
            <div class="corner-ornament corner-tr">&#10022;</div>
            <div class="corner-ornament corner-bl">&#10022;</div>
            <div class="corner-ornament corner-br">&#10022;</div>

            <!-- Dynamic System Logo & Header Brand -->
            <div class="logo-wrap">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="System Logo" style="max-height: 48px; max-width: 180px;">
                @elseif(!empty($settings['brand_logo_url']))
                    <img src="{{ $settings['brand_logo_url'] }}" alt="System Logo" style="max-height: 48px; max-width: 180px;">
                @else
                    <span style="font-size: 26px; color: #D97706;">&#9813;</span>
                @endif
                <div class="header-brand">{{ $brandName }}</div>
                <div class="cert-ribbon">Accredited Academic Qualification &bull; Verified Credential</div>
            </div>

            <!-- Certificate Title -->
            <div class="cert-title">Certificate of Achievement</div>
            <div class="cert-subtitle">This official diploma is honorably conferred upon</div>

            <!-- Recipient Name -->
            <div class="recipient-name">{{ $user->name }}</div>

            <!-- Body Statement -->
            <div class="cert-body">
                For successfully fulfilling all rigorous academic standards, practical examinations, and syllabus criteria in
                <span class="course-title">"{{ $course->title }}"</span>
            </div>

            <!-- Signatures, Official Seal & Verification QR -->
            <table class="footer-table">
                <tr>
                    <!-- Left: Signatures & Dates -->
                    <td style="width: 33%; text-align: left; vertical-align: bottom;">
                        <div class="signature-line"></div>
                        <div class="signature-title">Board of Academic Examiners</div>
                        <div class="signature-sub">Conferred: {{ $issuedDate }}</div>
                        @if(!empty($expiresDate))
                            <div class="signature-sub" style="color: #D97706;">Valid Until: {{ $expiresDate }}</div>
                        @endif
                    </td>

                    <!-- Center: 3D Gold Seal -->
                    <td style="width: 34%; text-align: center; vertical-align: middle;">
                        <div class="seal-medal">
                            <div class="seal-star">&#9733; &#9733; &#9733;</div>
                            <div class="seal-text">OFFICIAL<br>SEAL<br>VERIFIED</div>
                        </div>
                    </td>

                    <!-- Right: Verification QR & UUID -->
                    <td style="width: 33%; text-align: right; vertical-align: bottom;">
                        <div class="qr-wrap">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($verificationUrl) }}" alt="QR" width="75" height="75">
                            <div class="qr-uuid">ID: {{ substr($certificate->uuid, 0, 13) }}...</div>
                            <div style="font-size: 8px; margin-top: 2px;">Scan QR to verify validity</div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
