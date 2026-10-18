@extends('emails.layout')

@section('content')
    @php
        $status = strtoupper($statusType ?? 'NOTICE');
        $badgeConfig = match($status) {
            'APPROVED' => [
                'bg' => '#ECFDF5',
                'color' => '#059669',
                'border' => '#A7F3D0',
                'label' => 'Account Approved & Active',
                'boxBg' => '#F0FDF4',
                'boxBorder' => '#10B981',
                'boxTitle' => 'Approval & Accreditation Confirmation',
                'boxTitleColor' => '#047857',
                'boxTextColor' => '#065F46',
                'btnBg' => 'linear-gradient(135deg, #059669 0%, #10B981 100%)',
            ],
            'PENDING' => [
                'bg' => '#FFFBEB',
                'color' => '#D97706',
                'border' => '#FDE68A',
                'label' => 'Application Under Review',
                'boxBg' => '#FEFCE8',
                'boxBorder' => '#EAB308',
                'boxTitle' => 'Review in Progress',
                'boxTitleColor' => '#A16207',
                'boxTextColor' => '#854D0E',
                'btnBg' => 'linear-gradient(135deg, #D97706 0%, #F59E0B 100%)',
            ],
            'UNAPPROVED' => [
                'bg' => '#F8FAFC',
                'color' => '#64748B',
                'border' => '#CBD5E1',
                'label' => 'Account Status: Unapproved',
                'boxBg' => '#F1F5F9',
                'boxBorder' => '#94A3B8',
                'boxTitle' => 'Status Notification',
                'boxTitleColor' => '#475569',
                'boxTextColor' => '#334155',
                'btnBg' => 'linear-gradient(135deg, #475569 0%, #64748B 100%)',
            ],
            default => [
                'bg' => '#FEF2F2',
                'color' => '#DC2626',
                'border' => '#FECACA',
                'label' => 'Review Status Notice',
                'boxBg' => '#FFFBEB',
                'boxBorder' => '#D97706',
                'boxTitle' => 'Feedback & Compliance Notes',
                'boxTitleColor' => '#B45309',
                'boxTextColor' => '#78350F',
                'btnBg' => 'linear-gradient(135deg, #723EC3 0%, #0284C7 100%)',
            ],
        };
    @endphp

    <div style="margin-bottom: 20px;">
        <span style="display: inline-block; background: {{ $badgeConfig['bg'] }}; color: {{ $badgeConfig['color'] }}; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 20px; border: 1px solid {{ $badgeConfig['border'] }};">
            {{ $badgeConfig['label'] }}
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

    @if(!empty($reasonText))
        <!-- Status / Feedback Box -->
        <div style="background: {{ $badgeConfig['boxBg'] }}; border-left: 4px solid {{ $badgeConfig['boxBorder'] }}; border-radius: 10px; padding: 18px 22px; margin: 24px 0;">
            <div style="font-size: 11px; font-weight: 700; color: {{ $badgeConfig['boxTitleColor'] }}; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                {{ $badgeConfig['boxTitle'] }}
            </div>
            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: {{ $badgeConfig['boxTextColor'] }}; white-space: pre-line;">
                {{ $reasonText }}
            </p>
        </div>
    @endif

    @if(!empty($actionUrl))
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 26px 0;">
            <tr>
                <td align="center">
                    <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; background: {{ $badgeConfig['btnBg'] }}; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 12px 32px; border-radius: 25px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                        {{ $actionText ?? 'Access Account & Console' }} &rarr;
                    </a>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin: 28px 0 0; font-size: 14px; line-height: 1.6; color: #475569;">
        Kind regards,<br>
        <strong style="color: #0F172A;">System Academic Administration</strong><br>
        AllThingsCambridge Academic Quality Assurance &amp; Faculty Operations
    </p>
@endsection

