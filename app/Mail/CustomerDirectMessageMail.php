<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerDirectMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $customSubject,
        public string $customMessage,
        public ?User $staff = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🧺 [Trowa Laundry] {$this->customSubject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_direct_message',
        );
    }
}
