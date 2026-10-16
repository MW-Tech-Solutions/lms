@extends('emails.layout')

@section('content')
    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: #EEF2FF; color: #4F46E5; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid #C7D2FE;">
            Authentication Security
        </span>
    </div>

    <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -0.3px;">
        Two-Factor Security Code
    </h2>

    <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
        Hello <strong>{{ $user->name }}</strong>,
    </p>

    <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
        Please use the one-time authentication code below to complete your login verification for <strong>{{ config('app.name', 'AllThingsCambridge') }}</strong>:
    </p>

    <!-- Big 6-Digit Code Card -->
    <div style="background: linear-gradient(135deg, #F8FAFC 0%, #EEF2F6 100%); border: 2px dashed #CBD5E1; border-radius: 16px; padding: 26px 20px; text-align: center; margin: 28px 0;">
        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #64748B; margin-bottom: 10px;">
            One-Time Security Code
        </div>
        <div style="font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace; font-size: 40px; font-weight: 800; letter-spacing: 8px; color: #723EC3; text-shadow: 0 1px 2px rgba(114, 62, 195, 0.2);">
            {{ $code }}
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #64748B;">
            Valid for <strong>10 minutes</strong> &bull; Single-use only
        </div>
    </div>

    <!-- Security Warning -->
    <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px 18px; border-radius: 8px; margin-top: 24px;">
        <p style="margin: 0; font-size: 13px; color: #92400E; line-height: 1.5;">
            <strong>Did not attempt to log in?</strong> If you did not initiate this authentication request, someone may be attempting to access your account. Please log in immediately and update your password.
        </p>
    </div>

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Warm regards,<br>
        <strong style="color: #0F172A;">AllThingsCambridge Security Team</strong>
    </p>
@endsection
