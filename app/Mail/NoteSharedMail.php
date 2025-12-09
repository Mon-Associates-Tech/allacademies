<?php

namespace App\Mail;

use App\Models\Note;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class NoteSharedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Note $note;
    public User $recipient;
    public bool $canEdit;

    public function __construct(Note $note, User $recipient, bool $canEdit = false)
    {
        $this->note = $note;
        $this->recipient = $recipient;
        $this->canEdit = $canEdit;

        \Log::info('NoteShared mailable constructed', [
            'note_id' => $note->id,
            'recipient_id' => $recipient->id,
            'recipient_email' => $recipient->email,
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
            view: 'emails.note-shared',
            with: [
                'note' => $this->note,
                'recipient' => $this->recipient,
                'sharer' => $this->note->user,
                'canEdit' => $this->canEdit,
                'noteUrl' => route('notes.show', $this->note),
            ]
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->recipient->email, $this->recipient->name)
            ->subject('Note Shared: ' . $this->note->title)
            ->view('emails.note-shared')
            ->with([
                'note' => $this->note,
                'recipient' => $this->recipient,
                'sharer' => $this->note->user,
                'canEdit' => $this->canEdit,
                'noteUrl' => route('notes.show', $this->note),
            ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('NoteShared mailable failed', [
            'note_id' => $this->note->id,
            'recipient_id' => $this->recipient->id,
            'recipient_email' => $this->recipient->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
