<?php

namespace App\Notifications;

use App\Mail\NoteSharedMail;
use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NoteSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Note $note;
    public bool $canEdit;

    public function __construct(Note $note, bool $canEdit = false)
    {
        $this->note = $note;
        $this->canEdit = $canEdit;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        // Only add mail channel if user has a valid email
        if (!empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): NoteSharedMail
    {
        return new NoteSharedMail($this->note, $notifiable, $this->canEdit);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'note_id' => $this->note->id,
            'note_title' => $this->note->title,
            'shared_by' => $this->note->user->name,
            'shared_by_id' => $this->note->user_id,
            'can_edit' => $this->canEdit,
            'message' => $this->note->user->name . ' shared a note with you: ' . $this->note->title,
            'action_url' => route('notes.show', $this->note),
        ];
    }
}
