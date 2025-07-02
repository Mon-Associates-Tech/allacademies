<?php

namespace App\Mail;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assignment $assignment;
    public Student $student;

    public function __construct(Assignment $assignment, Student $student)
    {
        $this->assignment = $assignment;
        $this->student = $student;
    }

    public function build()
    {
        return $this->subject('New Assignment: ' . $this->assignment->title)
                    ->view('emails.assignment-assigned')
                    ->with([
                        'assignment' => $this->assignment,
                        'student' => $this->student,
                        'teacherName' => $this->assignment->teacher->user->name ?? 'Your Teacher',
                        'subjectName' => $this->assignment->academicSubject->name ?? 'Unknown Subject',
                    ]);
    }
}
