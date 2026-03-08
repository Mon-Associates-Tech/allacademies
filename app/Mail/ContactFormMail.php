<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(
                    $this->contactData['email'],
                    $this->contactData['first_name'].' '.$this->contactData['last_name']
                ),
            ],
            subject: 'New Contact Form Submission - '.$this->contactData['subject']
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_form',
            with: ['data' => $this->contactData]
        );
    }
}
