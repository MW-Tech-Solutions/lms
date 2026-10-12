<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement - {{ $certificate->uuid }}</title>
    <style>
        @page {
            margin: 0;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #ffffff;
            color: #0F172A;
            margin: 0;
            padding: 40px;
            box-sizing: border-box;
        }
        .cert-container {
            border: 8px double #1E3A8A;
            padding: 40px 50px;
            text-align: center;
            background: radial-gradient(circle at center, #FFFFFF 60%, #F8FAFC 100%);
            position: relative;
            min-height: 500px;
        }
        .header-brand {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #2563EB;
            margin-bottom: 8px;
        }
        .cert-title {
            font-size: 38px;
            font-weight: 900;
            color: #0F172A;
            letter-spacing: 2px;
            margin: 15px 0 20px 0;
            text-transform: uppercase;
        }
        .cert-subtitle {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 2px;
            color: #64748B;
            text-transform: uppercase;
            margin-bottom: 25px;
        }
        .recipient-name {
            font-size: 32px;
            font-weight: 800;
            color: #1E3A8A;
            border-bottom: 2px solid #CBD5E1;
            display: inline-block;
            padding-bottom: 6px;
            margin-bottom: 20px;
            min-width: 450px;
        }
        .cert-body {
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
            max-width: 650px;
            margin: 0 auto 30px auto;
        }
        .course-title {
            font-size: 22px;
            font-weight: 700;
            color: #0F172A;
            display: block;
            margin-top: 6px;
        }
        .footer-grid {
            margin-top: 40px;
            width: 100%;
        }
        .footer-col {
            width: 33.33%;
            display: inline-block;
            vertical-align: bottom;
            text-align: center;
            box-sizing: border-box;
        }
        .signature-line {
            width: 160px;
            border-bottom: 1px solid #94A3B8;
            margin: 0 auto 8px auto;
        }
        .signature-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
        }
        .qr-box {
            font-family: monospace;
            font-size: 10px;
            color: #475569;
        }
        .cert-uuid {
            font-family: monospace;
            font-size: 11px;
            color: #64748B;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .seal-badge {
            display: inline-block;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #2563EB;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 800;
            line-height: 70px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="header-brand">ENTERPRISE ACADEMY &amp; COMPLIANCE</div>
        <div class="cert-title">Certificate of Completion</div>
        <div class="cert-subtitle">This is officially presented to</div>
        
        <div class="recipient-name">{{ $user->name }}</div>
        
        <div class="cert-body">
            For successfully demonstrating competency and fulfilling all operational requirements for
            <span class="course-title">"{{ $course->title }}"</span>
        </div>

        <table style="width: 100%; margin-top: 25px;">
            <tr>
                <td style="width: 33%; text-align: left; vertical-align: bottom;">
                    <div class="signature-line" style="margin: 0 0 8px 0;"></div>
                    <div class="signature-title">Authorized Signatory</div>
                    <div style="font-size: 11px; color: #475569; margin-top: 3px;">Date: {{ $issuedDate }}</div>
                    @if($expiresDate)
                        <div style="font-size: 11px; color: #D97706; margin-top: 2px;">Recertification Due: {{ $expiresDate }}</div>
                    @endif
                </td>
                <td style="width: 34%; text-align: center; vertical-align: middle;">
                    <div class="seal-badge">VERIFIED</div>
                </td>
                <td style="width: 33%; text-align: right; vertical-align: bottom;">
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($verificationUrl) }}" alt="QR" width="80" height="80">
                        <div class="cert-uuid">UUID: {{ substr($certificate->uuid, 0, 8) }}...</div>
                        <div style="font-size: 9px; color: #64748B;">Scan to verify status</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
