@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #EEF2FF; color: #4338CA; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #C7D2FE;">
            Official Response
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        Response to Your Inquiry
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Dear <strong>{{ $contactMessage->name }}</strong>,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #475569;">
        Thank you for contacting <strong>AllThingsCambridge</strong>. In response to your inquiry regarding <em>"{{ $contactMessage->subject }}"</em>:
    </p>

    <!-- Support Team Reply Box -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #EEF2F6 100%); border-left: 4px solid #723EC3; border-radius: 12px; padding: 20px 24px; margin: 24px 0;">
        <div style="font-size: 12px; font-weight: 700; color: #723EC3; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
            Response from {{ $adminName ?? 'Support Specialist' }}
        </div>
        <div style="font-size: 15px; line-height: 1.7; color: #1E293B; white-space: pre-line;">
            {{ $replyText }}
        </div>
    </div>

    <!-- Original Message Quote -->
    <div style="background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 10px; padding: 14px 18px; margin: 24px 0;">
        <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
            Your Original Inquiry
        </div>
        <p style="margin: 0; font-size: 13px; color: #64748B; line-height: 1.5; font-style: italic;">
            "{{ $contactMessage->message }}"
        </p>
    </div>

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Kind regards,<br>
        <strong style="color: #0F172A;">{{ $adminName ?? 'Academic Support Team' }}</strong><br>
        AllThingsCambridge Academic Support Desk<br>
        <a href="mailto:support@allthingscambridge.com" style="color: #723EC3; text-decoration: none;">support@allthingscambridge.com</a>
    </p>
@endsection
