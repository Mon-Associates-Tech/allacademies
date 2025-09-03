<?php

namespace App\Mail;

use App\Models\Message as ModelMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class MessageNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ModelMessage $userMessage;
    public User $recipient;

    public function __construct(ModelMessage $user_message, User $recipient)
    {
        $this->userMessage = $user_message;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: ($this->userMessage->is_urgent ? '[URGENT] ' : '') . $this->userMessage->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.message-notification',
            with: [
                'userMessage' => $this->userMessage,
                'recipient' => $this->recipient,
                'messageUrl' => route('admin.messages.show', $this->userMessage),
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->userMessage->attachments as $attachment) {
            $attachments[] = Attachment::fromPath(storage_path('app/public/' . $attachment->path))
                ->as($attachment->original_filename)
                ->withMime($attachment->mime_type);
        }

        return $attachments;
    }
}
