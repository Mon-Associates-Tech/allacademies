<?php

namespace App\Mail;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscription $subscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' Newsletter!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-welcome',
        );
    }
}
