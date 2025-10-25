<?php

namespace App\Notifications;

use App\Models\Classroom\VirtualSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VirtualSessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VirtualSession $session,
        public int $minutesBefore
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: ' . $this->session->title . ' starts in ' . $this->minutesBefore . ' minutes')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that your virtual classroom session is starting soon.')
            ->line('**Session:** ' . $this->session->title)
            ->line('**Teacher:** ' . $this->session->teacher->user->name)
            ->line('**Starts at:** ' . $this->session->scheduled_start->format('g:i A'))
            ->action('Join Session', route('students.classroom.sessions'))
            ->line('See you there!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'virtual_session_reminder',
            'session_id' => $this->session->id,
            'session_title' => $this->session->title,
            'teacher_name' => $this->session->teacher->user->name,
            'scheduled_start' => $this->session->scheduled_start,
            'minutes_before' => $this->minutesBefore,
        ];
    }
}
