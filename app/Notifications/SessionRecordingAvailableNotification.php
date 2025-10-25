<?php

namespace App\Notifications;

use App\Models\Classroom\SessionRecording;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionRecordingAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SessionRecording $recording
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $session = $this->recording->virtualSession;

        return (new MailMessage)
            ->subject('Recording Available: ' . $session->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('The recording for your virtual classroom session is now available.')
            ->line('**Session:** ' . $session->title)
            ->line('**Teacher:** ' . $session->teacher->user->name)
            ->line('**Recorded:** ' . $this->recording->recorded_at->format('F j, Y'))
            ->line('**Duration:** ' . $this->recording->getFormattedDuration())
            ->action('Watch Recording', route('students.classroom.recordings'))
            ->line('You can watch the recording at any time.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'session_recording_available',
            'recording_id' => $this->recording->id,
            'session_id' => $this->recording->virtual_session_id,
            'session_title' => $this->recording->virtualSession->title,
            'recorded_at' => $this->recording->recorded_at,
        ];
    }
}
