<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🧺 [Trowa Laundry] Verification Code: {$this->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification_code',
        );
    }
}
