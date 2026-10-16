@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #FEF2F2; color: #DC2626; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #FECACA;">
            Review Status Notice
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        {{ $heading ?? 'Compliance Review Notice' }}
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Dear <strong>{{ $recipientName ?? 'Educator' }}</strong>,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #475569;">
        {{ $introText }}
    </p>

    <!-- Reason Box -->
    <div style="background: #FFFBEB; border-left: 4px solid #D97706; border-radius: 10px; padding: 18px 22px; margin: 24px 0;">
        <div style="font-size: 11px; font-weight: 700; color: #B45309; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
            Feedback &amp; Non-Compliance Notes
        </div>
        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #78350F; white-space: pre-line;">
            {{ $reasonText }}
        </p>
    </div>

    @if(!empty($actionUrl))
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 26px 0;">
            <tr>
                <td align="center">
                    <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #723EC3 0%, #0284C7 100%); color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 12px 32px; border-radius: 25px; box-shadow: 0 4px 12px rgba(114, 62, 195, 0.3);">
                        {{ $actionText ?? 'Review & Resubmit' }} &rarr;
                    </a>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Kind regards,<br>
        <strong style="color: #0F172A;">System Academic Administration</strong><br>
        AllThingsCambridge Academic Quality Assurance
    </p>
@endsection
