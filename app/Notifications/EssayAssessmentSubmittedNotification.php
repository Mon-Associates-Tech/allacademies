<?php
// Location: app/Notifications/EssayAssessmentSubmitted.php

namespace App\Notifications;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EssayAssessmentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Essay Assessment Submitted - Action Required')
            ->greeting('Hello ' . $notifiable->user->name)
            ->line('A student has submitted an essay assessment that requires your review.')
            ->line('Student: ' . $this->assessment->student->user->name)
            ->line('Assessment: ' . $this->assessment->title)
            ->line('Subject: ' . $this->assessment->subject->name)
            ->action('Grade Assessment', url('/teacher/assessments/' . $this->assessment->id . '/grade'))
            ->line('Please review and grade the assessment at your earliest convenience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'essay_assessment_submitted',
            'assessment_id' => $this->assessment->id,
            'student_name' => $this->assessment->student->user->name,
            'assessment_title' => $this->assessment->title,
            'subject_name' => $this->assessment->subject->name,
            'submitted_at' => $this->assessment->end_time,
            'message' => 'New essay assessment submitted for grading',
        ];
    }
}
