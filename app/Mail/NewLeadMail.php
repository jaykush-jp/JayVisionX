<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Enquiry from ' . $this->lead->name . ' — JayVisionX',
            replyTo: $this->lead->email ? [$this->lead->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-lead',
            with: ['lead' => $this->lead],
        );
    }
}
