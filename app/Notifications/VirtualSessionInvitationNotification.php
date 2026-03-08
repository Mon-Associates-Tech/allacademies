<?php

namespace App\Notifications;

use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VirtualSessionInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VirtualSession $session,
        public SessionParticipant $participant
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation: '.$this->session->title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('You have been invited to join a virtual classroom session.')
            ->line('**Session:** '.$this->session->title)
            ->line('**Teacher:** '.$this->session->teacher->user->name)
            ->line('**Date:** '.$this->session->scheduled_start->format('l, F j, Y'))
            ->line('**Time:** '.$this->session->scheduled_start->format('g:i A').' - '.$this->session->scheduled_end->format('g:i A'))
            ->when($this->session->description, function ($mail) {
                return $mail->line('**Description:** '.$this->session->description);
            })
            ->action('View Session', route('students.classroom.sessions'))
            ->line('Please join on time. The session link will be available 15 minutes before the scheduled start time.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'virtual_session_invitation',
            'session_id' => $this->session->id,
            'participant_id' => $this->participant->id,
            'session_title' => $this->session->title,
            'teacher_name' => $this->session->teacher->user->name,
            'scheduled_start' => $this->session->scheduled_start,
            'scheduled_end' => $this->session->scheduled_end,
        ];
    }
}
