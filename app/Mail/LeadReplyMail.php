<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A reply an admin composes to a lead from inside the CRM.
 */
class LeadReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $bodyText,
        public string $leadName = '',
    ) {
    }

    public function envelope(): Envelope
    {
        $replyTo = Setting::get('site_email', config('mail.from.address'));

        return new Envelope(
            subject: $this->subjectLine,
            replyTo: $replyTo ? [$replyTo] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-reply',
            with: [
                'bodyText' => $this->bodyText,
                'leadName' => $this->leadName,
            ],
        );
    }
}
