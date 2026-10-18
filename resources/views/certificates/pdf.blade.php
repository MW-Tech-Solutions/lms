<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - {{ $certificate->certificate_number ?? $certificate->uuid }}</title>
    @php
        $settings = $site_settings ?? (app()->bound(\App\Services\SiteConfigService::class) ? app(\App\Services\SiteConfigService::class)->all() : []);
        $brandName = $settings['brand_name'] ?? 'AllThingsCambridge';
        $websiteUrl = $settings['certificate_website_url'] ?? 'www.allthingscambridge.com';
        $tagline1 = $settings['certificate_tagline_1'] ?? 'Better Learning';
        $tagline2 = $settings['certificate_tagline_2'] ?? 'Brighter Future';
        $signatoryTitle = $settings['certificate_signatory_title'] ?? 'Head of E-Learning';
        $signatorySub = $settings['certificate_signatory_sub'] ?? $brandName;
        $certNum = $certificate->certificate_number ?? ('ATC-' . ($certificate->issued_at ?? now())->format('Y-md') . '-' . str_pad((string)$certificate->id, 3, '0', STR_PAD_LEFT));
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
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #FFFFFF;
            color: #0F172A;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        /* Outer & Inner Decorative Borders */
        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 24px 28px;
            background: #FFFFFF;
        }
        .outer-frame {
            position: absolute;
            top: 18px;
            left: 20px;
            right: 20px;
            bottom: 18px;
            border: 2px solid #C5A059;
            pointer-events: none;
            z-index: 10;
        }
        .inner-frame {
            position: absolute;
            top: 23px;
            left: 25px;
            right: 25px;
            bottom: 23px;
            border: 1px solid #E6CA65;
            pointer-events: none;
            z-index: 10;
        }

        /* Top-Left Geometric Accents */
        .corner-tl {
            position: absolute;
            top: 0;
            left: 0;
            width: 190px;
            height: 190px;
            overflow: hidden;
            z-index: 5;
        }
        .corner-br {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 190px;
            height: 190px;
            overflow: hidden;
            z-index: 5;
        }

        /* Watermark */
        .watermark-wrap {
            position: absolute;
            top: 110px;
            left: 40px;
            width: 240px;
            height: 240px;
            opacity: 0.05;
            z-index: 1;
            pointer-events: none;
        }

        /* Certificate Content Container */
        .content-wrap {
            position: relative;
            z-index: 20;
            height: 100%;
            padding: 10px 24px 14px 24px;
            text-align: center;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .header-logo-cell {
            text-align: left;
            vertical-align: top;
            width: 60%;
            padding-left: 10px;
        }
        .header-tagline-cell {
            text-align: right;
            vertical-align: top;
            width: 40%;
            padding-right: 10px;
        }
        .tagline-top {
            font-size: 13px;
            font-weight: 700;
            color: #064E3B;
            line-height: 1.2;
        }
        .tagline-bot {
            font-size: 13px;
            font-weight: 700;
            color: #059669;
            line-height: 1.2;
        }
        .tagline-line {
            display: inline-block;
            width: 65px;
            height: 2.5px;
            background: #059669;
            margin-top: 4px;
        }

        /* Main Titles */
        .cert-main-title {
            font-size: 40px;
            font-weight: 900;
            color: #064E3B;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 2px;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.1;
        }
        .cert-sub-badge {
            font-size: 16px;
            font-weight: 800;
            color: #059669;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .cert-sub-badge .rule-line {
            display: inline-block;
            width: 50px;
            height: 2px;
            background: #059669;
            vertical-align: middle;
            margin: 0 8px;
        }

        .certify-statement {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        /* Recipient Name in Cursive Script */
        .recipient-name {
            font-family: 'Brush Script MT', 'Dancing Script', 'Great Vibes', 'Times New Roman', cursive, serif;
            font-size: 40px;
            font-weight: normal;
            font-style: italic;
            color: #064E3B;
            display: inline-block;
            min-width: 480px;
            border-bottom: 2px solid #059669;
            padding-bottom: 2px;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .completed-course-text {
            font-size: 12px;
            color: #334155;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .course-title {
            font-size: 21px;
            font-weight: 800;
            color: #065F46;
            margin-bottom: 8px;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        .recognition-paragraph {
            font-size: 11px;
            line-height: 1.6;
            color: #64748B;
            max-width: 620px;
            margin: 0 auto 20px auto;
        }

        /* Footer 5-Column Table */
        .footer-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .footer-grid-table td {
            vertical-align: middle;
            padding: 4px 6px;
        }

        .meta-col {
            text-align: left;
        }
        .meta-label {
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 2px;
        }
        .meta-val {
            font-size: 12px;
            font-weight: 800;
            color: #0F172A;
        }

        /* Golden Seal Medal */
        .seal-cell {
            text-align: center;
            width: 110px;
        }
        .seal-medal-wrap {
            display: inline-block;
            position: relative;
            text-align: center;
        }

        /* Signature Column */
        .sig-col {
            text-align: center;
            width: 180px;
        }
        .sig-img-wrap {
            height: 44px;
            margin-bottom: 4px;
        }
        .sig-line {
            width: 160px;
            border-bottom: 1.5px solid #064E3B;
            margin: 0 auto 4px auto;
        }
        .sig-title {
            font-size: 10.5px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.2;
        }
        .sig-sub {
            font-size: 10.5px;
            font-weight: 700;
            color: #059669;
            line-height: 1.2;
        }

        /* QR Code Column */
        .qr-col {
            text-align: right;
            width: 95px;
            padding-right: 10px;
        }
        .qr-img {
            border: 1px solid #E2E8F0;
            padding: 2px;
            background: #FFFFFF;
            border-radius: 4px;
        }
        .qr-label {
            font-size: 9px;
            font-weight: 700;
            color: #334155;
            margin-top: 3px;
        }

        /* Bottom Left Website */
        .bottom-url {
            position: absolute;
            bottom: 28px;
            left: 38px;
            font-size: 10px;
            font-weight: 700;
            color: #059669;
            z-index: 30;
        }
    </style>
</head>
<body>
    <div class="page-container">

        <!-- Double Gold Border Frames -->
        <div class="outer-frame"></div>
        <div class="inner-frame"></div>

        <!-- Top-Left Corner Geometric Layers -->
        <div class="corner-tl">
            <svg width="190" height="190" viewBox="0 0 190 190" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Deep Pine Green Triangle -->
                <polygon points="0,0 150,0 0,150" fill="#064E3B" />
                <!-- Gold Accent Ribbon -->
                <polygon points="0,150 150,0 162,0 0,162" fill="#D4AF37" />
                <!-- Teal Inner Ribbon -->
                <polygon points="0,162 162,0 180,0 0,180" fill="#047857" />
                <!-- Small Gold Tip Accent -->
                <polygon points="0,0 50,0 0,50" fill="#C5A059" opacity="0.3" />
            </svg>
        </div>

        <!-- Bottom-Right Corner Geometric Layers -->
        <div class="corner-br">
            <svg width="190" height="190" viewBox="0 0 190 190" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Deep Pine Green Triangle -->
                <polygon points="190,190 40,190 190,40" fill="#064E3B" />
                <!-- Gold Accent Ribbon -->
                <polygon points="40,190 190,40 190,28 28,190" fill="#D4AF37" />
                <!-- Emerald Inner Ribbon -->
                <polygon points="28,190 190,28 190,10 10,190" fill="#059669" />
                <!-- Small Gold Tip Accent -->
                <polygon points="190,190 140,190 190,140" fill="#C5A059" opacity="0.3" />
            </svg>
        </div>

        <!-- Watermark (Graduation Cap & Open Book silhouette) -->
        <div class="watermark-wrap">
            <svg viewBox="0 0 200 200" fill="#059669" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 20 L20 60 L100 100 L180 60 Z"/>
                <path d="M40 75 V130 Q100 160 160 130 V75 Q100 105 40 75 Z"/>
                <path d="M180 60 V120 H185 V60 Z"/>
            </svg>
        </div>

        <!-- Main Content Wrap -->
        <div class="content-wrap">

            <!-- Top Header -->
            <table class="header-table">
                <tr>
                    <td class="header-logo-cell">
                        @if(!empty($logoBase64))
                            <img src="{{ $logoBase64 }}" alt="{{ $brandName }}" style="max-height: 52px; max-width: 220px; object-fit: contain;">
                        @else
                            <div style="display: inline-block; vertical-align: middle;">
                                <div style="font-size: 20px; font-weight: 900; color: #064E3B; letter-spacing: 0.5px;">{{ $brandName }}</div>
                                <div style="font-size: 9.5px; font-weight: 700; color: #059669; letter-spacing: 2px;">LEARN &bull; GROW &bull; ACHIEVE</div>
                            </div>
                        @endif
                    </td>
                    <td class="header-tagline-cell">
                        <div class="tagline-top">{{ $tagline1 }}</div>
                        <div class="tagline-bot">{{ $tagline2 }}</div>
                        <div class="tagline-line"></div>
                    </td>
                </tr>
            </table>

            <!-- Heading & Subtitle -->
            <div class="cert-main-title">CERTIFICATE</div>
            <div class="cert-sub-badge">
                <span class="rule-line"></span>OF COMPLETION<span class="rule-line"></span>
            </div>

            <div class="certify-statement">THIS IS TO CERTIFY THAT</div>

            <!-- Student Name in Calligraphic Cursive Font -->
            <div>
                <span class="recipient-name">{{ $user->name }}</span>
            </div>

            <!-- Course Title -->
            <div class="completed-course-text">has successfully completed the online course</div>
            <div class="course-title">{{ $course->title }}</div>

            <!-- Recognition Text -->
            <div class="recognition-paragraph">
                This certificate is awarded in recognition of your dedication, commitment and successful completion of all course requirements on the {{ $brandName }} E-Learning Platform.
            </div>

            <!-- Bottom 5-Column Grid -->
            <table class="footer-grid-table">
                <tr>
                    <!-- 1. Date of Completion -->
                    <td style="width: 21%; text-align: left; padding-left: 12px;">
                        <table style="border-collapse: collapse;">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 6px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="17" rx="3" stroke="#059669" stroke-width="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9" stroke="#059669" stroke-width="2"/>
                                        <line x1="8" y1="2" x2="8" y2="5" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="16" y1="2" x2="16" y2="5" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 13 L11 16 L16 11" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="meta-label">Date of Completion</div>
                                    <div class="meta-val">{{ $issuedDate }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- 2. Golden Circular Seal Badge with Ribbons -->
                    <td class="seal-cell" style="width: 14%;">
                        <div class="seal-medal-wrap">
                            <svg width="68" height="68" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Hanging Green Ribbons -->
                                <polygon points="35,65 30,95 45,86 50,95 45,65" fill="#047857"/>
                                <polygon points="65,65 70,95 55,86 50,95 55,65" fill="#065F46"/>
                                <!-- Outer Gold Scalloped Ring -->
                                <circle cx="50" cy="48" r="38" fill="#F6C343" stroke="#D4AF37" stroke-width="2.5"/>
                                <circle cx="50" cy="48" r="33" fill="#EAB308" stroke="#B45309" stroke-width="1.5" stroke-dasharray="2 2"/>
                                <circle cx="50" cy="48" r="28" fill="#FEF08A"/>
                                <circle cx="50" cy="48" r="24" fill="#FFFFFF" stroke="#D4AF37" stroke-width="1"/>
                                <!-- Center Emblem: Graduation Cap & Book in Green -->
                                <path d="M50 38 L36 44 L50 50 L64 44 Z" fill="#064E3B"/>
                                <path d="M40 46 V53 Q50 57 60 53 V46 Q50 50 40 46 Z" fill="#059669"/>
                            </svg>
                        </div>
                    </td>

                    <!-- 3. Certificate ID -->
                    <td style="width: 23%; text-align: left; padding-left: 6px;">
                        <table style="border-collapse: collapse;">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 6px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="4" y="3" width="16" height="18" rx="2" stroke="#059669" stroke-width="2"/>
                                        <line x1="8" y1="8" x2="16" y2="8" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="12" x2="16" y2="12" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="16" x2="13" y2="16" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="meta-label">Certificate ID</div>
                                    <div class="meta-val" style="font-family: monospace; font-size: 11.5px;">{{ $certNum }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- 4. Authorized Signature -->
                    <td class="sig-col" style="width: 24%;">
                        <div class="sig-img-wrap">
                            @if(!empty($signatureBase64))
                                <img src="{{ $signatureBase64 }}" alt="Authorized Signature" style="max-height: 42px; max-width: 140px; object-fit: contain;">
                            @else
                                <!-- Default Handwritten Vector Signature -->
                                <svg width="130" height="40" viewBox="0 0 260 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 60 C15 20, 35 10, 50 35 C60 55, 70 65, 80 30 C90 20, 110 50, 125 35 C135 25, 155 60, 170 30 C180 15, 205 55, 220 25 C230 15, 245 40, 250 45" stroke="#064E3B" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M35 70 C100 62, 170 68, 235 58 C245 56, 255 65, 240 70" stroke="#064E3B" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            @endif
                        </div>
                        <div class="sig-line"></div>
                        <div class="sig-title">{{ $signatoryTitle }}</div>
                        <div class="sig-sub">{{ $signatorySub }}</div>
                    </td>

                    <!-- 5. Verification QR Code -->
                    <td class="qr-col" style="width: 18%;">
                        @if(!empty($qrBase64))
                            <img src="{{ $qrBase64 }}" alt="QR Code" width="66" height="66" class="qr-img">
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verificationUrl) }}" alt="QR Code" width="66" height="66" class="qr-img">
                        @endif
                        <div class="qr-label">Verify Certificate</div>
                    </td>
                </tr>
            </table>

        </div>

        <!-- Official Website Bottom-Left Footer -->
        <div class="bottom-url">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" style="vertical-align: middle; margin-right: 3px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
            {{ $websiteUrl }}
        </div>

    </div>
</body>
</html>
