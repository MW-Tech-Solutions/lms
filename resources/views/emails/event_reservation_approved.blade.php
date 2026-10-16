@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #E0F2FE; color: #0369A1; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #BAE6FD;">
            Reservation Confirmed
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        Your Masterclass Spot is Approved!
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Dear <strong>{{ $reservation->name }}</strong>,
    </p>

    <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
        Great news! Your reservation for our upcoming Cambridge Live Masterclass session has been officially approved and confirmed by the academic team.
    </p>

    <!-- Event Summary Card -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border: 1px solid #E2E8F0; border-left: 4px solid #0284C7; border-radius: 14px; padding: 22px 24px; margin: 24px 0;">
        <h3 style="margin: 0 0 12px; color: #0369A1; font-size: 17px; font-weight: 700;">
            {{ $reservation->event?->title }}
        </h3>
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; color: #334155;">
            <tr>
                <td style="padding: 6px 0; width: 110px; color: #64748B; font-weight: 600;">Date &amp; Time:</td>
                <td style="padding: 6px 0; font-weight: 700; color: #0F172A;">{{ $reservation->event?->formatted_date }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748B; font-weight: 600;">Duration:</td>
                <td style="padding: 6px 0;">{{ $reservation->event?->duration_minutes }} minutes</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748B; font-weight: 600;">Instructor:</td>
                <td style="padding: 6px 0; font-weight: 600; color: #0F172A;">
                    {{ $reservation->event?->instructor_name }} 
                    <span style="font-weight: 400; color: #64748B;">({{ $reservation->event?->instructor_role }})</span>
                </td>
            </tr>
        </table>

        @if($reservation->event?->meeting_link)
            <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid #E2E8F0;">
                <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                    Live Meeting Room:
                </div>
                <a href="{{ $reservation->event->meeting_link }}" target="_blank" style="display: inline-block; background: #0284C7; color: #FFFFFF; font-size: 13px; font-weight: 700; text-decoration: none; padding: 10px 22px; border-radius: 8px; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);">
                    Join Live Session &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- Preparation Checklist -->
    <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px 20px; margin: 24px 0;">
        <div style="font-size: 13px; font-weight: 700; color: #0F172A; margin-bottom: 6px;">
            &#128221; Preparation Checklist:
        </div>
        <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #64748B;">
            Please ensure you have your Cambridge past paper booklets, working notebooks, and calculators ready at least 5 minutes prior to session kick-off.
        </p>
    </div>

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Warm regards,<br>
        <strong style="color: #0F172A;">The Cambridge Academic Faculty</strong><br>
        AllThingsCambridge Academic Team
    </p>
@endsection
