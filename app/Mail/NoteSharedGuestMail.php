<?php

namespace App\Mail;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoteSharedGuestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Note $note;
    public string $guestEmail;
    public bool $canEdit;

    public function __construct(Note $note, string $guestEmail, bool $canEdit = false)
    {
        $this->note = $note;
        $this->guestEmail = $guestEmail;
        $this->canEdit = $canEdit;

        \Log::info('NoteSharedGuest mailable constructed', [
            'note_id' => $note->id,
            'guest_email' => $guestEmail,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Note Shared: ' . $this->note->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.note-shared-guest',
            with: [
                'note' => $this->note,
                'guestEmail' => $this->guestEmail,
                'sharer' => $this->note->user,
                'canEdit' => $this->canEdit,
                'noteUrl' => route('notes.show', $this->note),
            ]
        );
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('NoteSharedGuest mailable failed', [
            'note_id' => $this->note->id,
            'guest_email' => $this->guestEmail,
            'error' => $exception->getMessage(),
        ]);
    }
}
