<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
        public string $resetUrl,
        public int $expiresMinutes = 60
    ) {
    }

    public function envelope(): Envelope
    {
        $brand = config('app.name', 'AllThingsCambridge');
        return new Envelope(
            subject: "Your Password Reset Code: {$this->code} - {$brand}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset_code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
