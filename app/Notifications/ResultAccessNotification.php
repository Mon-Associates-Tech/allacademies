<?php

namespace App\Notifications;

use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResultAccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly GeneralExamSubmission $submission,
        private readonly string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $exam = $this->submission->assignment;
        $accessUrl = route('examination-hub.results.show', [
            'submission' => $this->submission,
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject("Your Exam Results Are Ready - {$exam->title}")
            ->greeting("Hello {$this->submission->participant_name},")
            ->line("Great news! Your results for **{$exam->title}** are now available.")
            ->line("**Your Score:** {$this->submission->score} / {$this->submission->total_marks} ({$this->submission->percentage}%)")
            ->line("**Grade:** {$this->submission->grade}")
            ->action('View Your Results', $accessUrl)
            ->line('This secure link will expire in 7 days.')
            ->line('If you did not take this exam, please ignore this email.')
            ->salutation('Best regards, Examination Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'exam_title' => $this->submission->assignment->title,
            'score' => $this->submission->score,
            'total_marks' => $this->submission->total_marks,
            'percentage' => $this->submission->percentage,
            'grade' => $this->submission->grade,
        ];
    }
}
