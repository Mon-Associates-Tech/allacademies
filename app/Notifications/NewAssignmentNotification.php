<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAssignmentNotification extends Notification
{
    use Queueable;

    public Assignment $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'title' => $this->assignment->title,
            'subject' => $this->assignment->academicSubject->name ?? 'Unknown Subject',
            'teacher' => $this->assignment->user->name ?? 'Unknown Teacher',
            'type' => $this->assignment->type,
            'starts_at' => $this->assignment->starts_at?->format('Y-m-d H:i:s'),
            'ends_at' => $this->assignment->ends_at?->format('Y-m-d H:i:s'),
            'duration_in_minutes' => $this->assignment->duration_in_minutes,
            'message' => "New {$this->assignment->type} '{$this->assignment->title}' has been assigned to you.",
        ];
    }
}
