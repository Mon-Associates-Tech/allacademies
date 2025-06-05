<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EssayGradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assessmentId;

    public function __construct($assessmentId)
    {
        $this->assessmentId = $assessmentId;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Essay Has Been Graded')
            ->greeting('Hello!')
            ->line(new HtmlString('Your essay assessment has been reviewed and scored.'))
            ->action('View Results', route('student.assessment.results', ['id' => $this->assessmentId]))
            ->line('Thank you for your submission!');
    }
}
