<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? 'AllThingsCambridge' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; color: #1E293B;">
    <!-- Container Table with clean, light-grey background -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F1F5F9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <!-- Inner Wrapper (Max 600px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Clean, Simple, Professional Header -->
                    <tr>
                        <td style="background-color: #FFFFFF; padding: 28px 32px 24px; text-align: left; border-bottom: 2px solid #723EC3;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px; line-height: 1.2;">
                                            <span style="color: #723EC3;">AllThings</span><span style="color: #0284C7;">Cambridge</span>
                                        </div>
                                        <div style="margin-top: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #64748B;">
                                            Cambridge International Study Portal
                                        </div>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <!-- Standalone High-Fidelity Vector Badge of System Logo (Renders 100% reliably in Gmail, Outlook, mobile) -->
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 6px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td style="vertical-align: middle; padding-right: 8px;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 100 100" style="vertical-align: middle; display: block;">
                                                                    <!-- Golden Sun Rays -->
                                                                    <g stroke="#D4AF37" stroke-width="3" stroke-linecap="round">
                                                                        <line x1="50" y1="18" x2="50" y2="8" />
                                                                        <line x1="61" y1="20" x2="67" y2="10" />
                                                                        <line x1="71" y1="26" x2="80" y2="18" />
                                                                        <line x1="79" y1="35" x2="90" y2="30" />
                                                                        <line x1="82" y1="46" x2="94" y2="45" />
                                                                        <line x1="39" y1="20" x2="33" y2="10" />
                                                                        <line x1="29" y1="26" x2="20" y2="18" />
                                                                        <line x1="21" y1="35" x2="10" y2="30" />
                                                                        <line x1="18" y1="46" x2="6" y2="45" />
                                                                    </g>
                                                                    <!-- Golden Arch -->
                                                                    <path d="M 22 50 A 28 28 0 0 1 78 50" fill="none" stroke="#D4AF37" stroke-width="4.5" stroke-linecap="round" />
                                                                    <!-- A*TC Brand Monogram -->
                                                                    <text x="50" y="47" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif" font-size="16" font-weight="900" fill="#0A0D18" text-anchor="middle" letter-spacing="1">A*TC</text>
                                                                    <!-- Green Open Book Pages -->
                                                                    <path d="M 14 58 Q 50 64 50 72 Q 50 64 86 58 L 88 68 Q 50 74 50 82 Q 50 74 12 68 Z" fill="#0B6B38" />
                                                                    <path d="M 12 68 Q 50 74 50 82 Q 50 74 88 68 L 86 74 Q 50 80 50 88 Q 50 80 14 74 Z" fill="#064E26" />
                                                                </svg>
                                                            </td>
                                                            <td style="vertical-align: middle; text-align: left; line-height: 1.1;">
                                                                <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 900; color: #0B6B38; letter-spacing: 0.5px;">A*TC</div>
                                                                <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; font-size: 8px; font-weight: 700; color: #D4AF37; text-transform: uppercase; letter-spacing: 1px;">Cambridge</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Clean Simple Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 20px 32px; border-top: 1px solid #E2E8F0; text-align: center;">
                            <p style="margin: 0 0 4px; font-size: 12px; font-weight: 600; color: #475569;">
                                AllThingsCambridge UK
                            </p>
                            <p style="margin: 0 0 10px; font-size: 11px; color: #64748B; line-height: 1.5;">
                                Supporting Primary Checkpoint, Lower Secondary, IGCSE &amp; A-Level Students.<br>
                                Need help? Email <a href="mailto:support@allthingscambridge.com" style="color: #723EC3; text-decoration: none; font-weight: 600;">support@allthingscambridge.com</a>
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #94A3B8;">
                                &copy; {{ date('Y') }} AllThingsCambridge. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
