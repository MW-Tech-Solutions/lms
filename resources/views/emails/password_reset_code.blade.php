@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 24px;">
        <span style="display: inline-block; background: #EEF2FF; color: #4F46E5; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #C7D2FE;">
            Security Notification
        </span>
    </div>

    <p style="margin: 0 0 16px; font-size: 17px; color: #0F172A; font-weight: 700;">
        Hello {{ $user->name }},
    </p>
    <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
        We received a request to reset the password for your account associated with <strong>{{ $user->email }}</strong>. Please use the 6-digit verification code below to authorize your password update:
    </p>

    <!-- Big 6-Digit Code Card -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #EEF2F6 100%); border: 2px dashed #CBD5E1; border-radius: 16px; padding: 26px 20px; text-align: center; margin: 28px 0;">
        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #64748B; margin-bottom: 10px;">
            Your 6-Digit Reset Code
        </div>
        <div style="font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace; font-size: 40px; font-weight: 800; letter-spacing: 8px; color: #723EC3; text-shadow: 0 1px 2px rgba(114, 62, 195, 0.2);">
            {{ $code }}
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #64748B;">
            Expires in <strong>{{ $expiresMinutes }} minutes</strong> &bull; Do not disclose this code to anyone
        </div>
    </div>

    <!-- One-Click Direct Link -->
    <p style="margin: 0 0 18px; font-size: 14px; line-height: 1.6; color: #475569; text-align: center;">
        Alternatively, you can tap the button below to automatically load your verification code and proceed:
    </p>
    
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0 28px;">
        <tr>
            <td align="center">
                <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #723EC3 0%, #0284C7 100%); color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 38px; border-radius: 30px; box-shadow: 0 4px 14px rgba(114, 62, 195, 0.35); letter-spacing: 0.3px;">
                    Reset Password Now &rarr;
                </a>
            </td>
        </tr>
    </table>

    <!-- Security Warning Banner -->
    <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px 18px; border-radius: 8px; margin-top: 28px;">
        <p style="margin: 0; font-size: 13px; color: #92400E; line-height: 1.5;">
            <strong>Didn't make this request?</strong> If you did not initiate this request, you can safely disregard this email. Your login credentials and account remain secure.
        </p>
    </div>
@endsection
