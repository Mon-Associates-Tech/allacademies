<?php

namespace App\Services;

use App\Mail\AssignmentAssignedMail;
use App\Models\Assignment;
use App\Models\AssignmentNotification;
use App\Models\Student;
use App\Models\User;
use App\Notifications\NewAssignmentNotification;
use App\Mail\AssignmentAssigned;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;

class AssignmentNotificationService
{
    public function sendAssignmentNotifications(Assignment $assignment): void
    {
        $eligibleStudents = $this->getEligibleStudents($assignment);

        foreach ($eligibleStudents as $student) {
            $this->createNotificationRecord($assignment, $student);
            $this->sendEmailNotification($assignment, $student);
            $this->sendInAppNotification($assignment, $student);
        }
    }

    protected function getEligibleStudents(Assignment $assignment): Collection
    {
        $students = collect();

        // From academic groups
        foreach ($assignment->academicGroups as $group) {
            $groupStudents = Student::whereHas('academicLevel.academicGroup', function ($query) use ($group) {
                $query->where('id', $group->id);
            })->with('user')->get();
            $students = $students->merge($groupStudents);
        }

        // From academic levels
        foreach ($assignment->academicLevels as $level) {
            $levelStudents = Student::where('academic_level_id', $level->id)->with('user')->get();
            $students = $students->merge($levelStudents);
        }

        // From student groups
        foreach ($assignment->studentGroups as $group) {
            $students = $students->merge($group->students()->with('user')->get());
        }

        // Directly assigned students
        $students = $students->merge($assignment->students()->with('user')->get());

        return $students->unique('id');
    }

    protected function createNotificationRecord(Assignment $assignment, Student $student): void
    {
        AssignmentNotification::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'notification_type' => 'assignment_created',
            'notified_at' => now(),
            'status' => 'sent',
            'message' => "New assignment '{$assignment->title}' has been assigned to you.",
        ]);
    }

    protected function sendEmailNotification(Assignment $assignment, Student $student): void
    {
        if ($student->user && $student->user->email) {
            try {
                Mail::to($student->user->email)->send(new AssignmentAssignedMail($assignment, $student));
            } catch (\Exception $e) {
                \Log::error('Failed to send assignment email notification', [
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function sendInAppNotification(Assignment $assignment, Student $student): void
    {
        if ($student->user) {
            try {
                $student->user->notify(new NewAssignmentNotification($assignment));
            } catch (\Exception $e) {
                \Log::error('Failed to send in-app assignment notification', [
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
