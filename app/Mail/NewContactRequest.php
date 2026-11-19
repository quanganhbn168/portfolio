<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewContactRequest extends Mailable
{
    public function __construct(public readonly ContactRequest $contactRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Liên hệ mới từ '.$this->contactRequest->name,
            replyTo: filled($this->contactRequest->email) ? [new Address($this->contactRequest->email, $this->contactRequest->name)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.new-contact-request');
    }
}
