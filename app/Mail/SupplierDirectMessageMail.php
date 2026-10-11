<?php

namespace App\Mail;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierDirectMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Supplier $supplier,
        public string $customSubject,
        public string $customMessage,
        public ?User $sender = null,
        public ?string $poReference = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🧺 [Trowa Laundry Supply PO] {$this->customSubject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier_direct_message',
        );
    }
}
