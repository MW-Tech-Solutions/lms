<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EventReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReservationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventReservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Spot Confirmed: ' . ($this->reservation->event?->title ?? 'Cambridge Live Session'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event_reservation_approved',
        );
    }
}
