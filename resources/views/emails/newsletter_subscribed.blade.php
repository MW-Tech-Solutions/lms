@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #ECFDF5; color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #A7F3D0;">
            Subscription Active
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        Cambridge Examination Alerts Confirmed
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Hello,
    </p>

    <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6; color: #475569;">
        Thank you for subscribing to <strong>AllThingsCambridge Exam Notifications</strong>. You'll now receive timely, verified updates directly from our academic board.
    </p>

    <!-- Benefits Box -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border: 1px solid #E2E8F0; border-radius: 14px; padding: 22px 24px; margin: 24px 0;">
        <div style="font-size: 13px; font-weight: 700; color: #0F172A; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
            What you will receive:
        </div>
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; color: #334155; line-height: 1.6;">
            <tr>
                <td style="vertical-align: top; width: 24px; padding-bottom: 10px; color: #0284C7; font-weight: bold;">&#10003;</td>
                <td style="padding-bottom: 10px; color: #475569;">Official May/June &amp; Oct/Nov timetable notifications &amp; key deadline alerts</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 24px; padding-bottom: 10px; color: #0284C7; font-weight: bold;">&#10003;</td>
                <td style="padding-bottom: 10px; color: #475569;">Past paper publishing alerts with worked mark scheme solutions</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 24px; padding-bottom: 10px; color: #0284C7; font-weight: bold;">&#10003;</td>
                <td style="padding-bottom: 10px; color: #475569;">Grade threshold updates and exam tips from certified Cambridge educators</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 24px; color: #0284C7; font-weight: bold;">&#10003;</td>
                <td style="color: #475569;">New topical question pack drops and interactive flashcard releases</td>
            </tr>
        </table>
    </div>

    <!-- Subscriber Details -->
    <div style="background-color: #F8FAFC; border-left: 4px solid #723EC3; border-radius: 8px; padding: 14px 18px; margin: 24px 0;">
        <div style="font-size: 13px; color: #475569; line-height: 1.6;">
            Registered Email: <strong style="color: #0F172A;">{{ $subscriber->email }}</strong><br>
            Syllabus Focus: <strong style="color: #723EC3;">{{ $subscriber->level_interest ?? 'All Cambridge Assessment Levels' }}</strong>
        </div>
    </div>

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Best regards,<br>
        <strong style="color: #0F172A;">The Examination Notification Bureau</strong><br>
        AllThingsCambridge Academic Portal
    </p>
@endsection
