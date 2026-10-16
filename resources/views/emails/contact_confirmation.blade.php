@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #ECFDF5; color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #A7F3D0;">
            Inquiry Received
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        We Have Received Your Message
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Hello <strong>{{ $messageRecord->name }}</strong>,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #475569;">
        Thank you for reaching out to <strong>AllThingsCambridge</strong>. We have logged your inquiry regarding <em>"{{ $messageRecord->subject }}"</em>.
    </p>

    <!-- Message Summary Card -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border: 1px solid #E2E8F0; border-left: 4px solid #723EC3; border-radius: 12px; padding: 18px 22px; margin: 24px 0;">
        <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
            Submitted Inquiry Summary
        </div>
        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #334155; white-space: pre-line;">
            {{ $messageRecord->message }}
        </p>
    </div>

    <p style="margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #475569;">
        Our academic support team will review your message and reply directly to your email (<strong>{{ $messageRecord->email }}</strong>) as soon as possible.
    </p>

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Kind regards,<br>
        <strong style="color: #0F172A;">AllThingsCambridge Support Team</strong><br>
        <a href="mailto:support@allthingscambridge.com" style="color: #723EC3; text-decoration: none;">support@allthingscambridge.com</a>
    </p>
@endsection
