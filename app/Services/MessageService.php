<?php

namespace App\Services;

use App\Mail\MessageNotificationMail;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageService
{
    public function sendMessage(Message $message): bool
    {
        try {
            DB::beginTransaction();

            $recipients = $this->getRecipientsForMessage($message);

            Log::info('Sending message to recipients', [
                'message_id' => $message->id,
                'recipient_count' => $recipients->count(),
                'target_type' => $message->target_type,
                'target_criteria' => $message->target_criteria,
            ]);

            if ($recipients->isEmpty()) {
                Log::warning('No recipients found for message', ['message_id' => $message->id]);
                throw new Exception('No recipients found for this message.');
            }

            // Create recipient records and send emails
            foreach ($recipients as $recipient) {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'user_id' => $recipient->id,
                ]);

                Mail::to($recipient->email)->send(new MessageNotificationMail($message, $recipient));

                Log::info('Email sent to recipient', [
                    'message_id' => $message->id,
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                ]);
            }

            // Update message status
            $message->update([
                'status' => Message::STATUS_SENT,
                'sent_at' => now(),
            ]);

            DB::commit();

            Log::info('Message sent successfully', [
                'message_id' => $message->id,
                'recipients_sent' => $recipients->count(),
            ]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to send message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message->update([
                'status' => Message::STATUS_FAILED,
            ]);

            throw $e;
        }
    }

    public function getRecipientsForMessage(Message $message): Collection
    {
        return $this->resolveRecipients($message->target_type, $message->target_criteria);
    }

    public function resolveRecipients(string $targetType, array $criteria): Collection
    {
        switch ($targetType) {
            case Message::TARGET_ROLE:
                return $this->getRecipientsByRole($criteria);

            case Message::TARGET_ACADEMIC_GROUP:
                return $this->getRecipientsByAcademicGroup($criteria);

            case Message::TARGET_ACADEMIC_LEVEL:
                return $this->getRecipientsByAcademicLevel($criteria);

            case Message::TARGET_SUBJECT:
                return $this->getRecipientsBySubject($criteria);

            case Message::TARGET_INDIVIDUAL:
                return $this->getRecipientsByIndividual($criteria);

            case Message::TARGET_CUSTOM:
                return $this->getRecipientsByCustomCriteria($criteria);

            default:
                return collect();
        }
    }

    protected function getRecipientsByRole(array $criteria): Collection
    {
        $roles = $criteria['roles'] ?? [];

        return User::whereIn('role', $roles)
            ->where('is_active', true)
            ->where('status', 'active')
            ->get();
    }

    protected function getRecipientsByAcademicGroup(array $criteria): Collection
    {
        $groupIds = $criteria['academic_group_ids'] ?? [];
        $includeStudents = $criteria['include_students'] ?? true;
        $includeTeachers = $criteria['include_teachers'] ?? true;

        $userIds = collect();

        if ($includeStudents) {
            $students = Student::whereIn('academic_group_id', $groupIds)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($students->pluck('id'));
        }

        if ($includeTeachers) {
            $teachers = Teacher::whereHas('academicGroups', function ($query) use ($groupIds) {
                $query->whereIn('academic_groups.id', $groupIds);
            })
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($teachers->pluck('id'));
        }

        // Return an Eloquent Collection directly
        return User::whereIn('id', $userIds->unique())->get();
    }

    protected function getRecipientsByAcademicLevel(array $criteria): Collection
    {
        $levelIds = $criteria['academic_level_ids'] ?? [];
        $includeStudents = $criteria['include_students'] ?? true;
        $includeTeachers = $criteria['include_teachers'] ?? true;

        $userIds = collect();

        if ($includeStudents) {
            $students = Student::whereIn('academic_level_id', $levelIds)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($students->pluck('id'));
        }

        if ($includeTeachers) {
            $teachers = Teacher::whereHas('academicLevels', function ($query) use ($levelIds) {
                $query->whereIn('academic_levels.id', $levelIds);
            })
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($teachers->pluck('id'));
        }

        // Return an Eloquent Collection directly
        return User::whereIn('id', $userIds->unique())->get();
    }

    protected function getRecipientsBySubject(array $criteria): Collection
    {
        $subjectIds = $criteria['subject_ids'] ?? [];
        $includeStudents = $criteria['include_students'] ?? true;
        $includeTeachers = $criteria['include_teachers'] ?? true;

        $userIds = collect();

        if ($includeTeachers) {
            $teachers = Teacher::whereHas('academicSubjects', function ($query) use ($subjectIds) {
                $query->whereIn('academic_subjects.id', $subjectIds);
            })
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($teachers->pluck('id'));
        }

        if ($includeStudents) {
            $students = Student::whereHas('academicSubjects', function ($query) use ($subjectIds) {
                $query->whereIn('academic_subjects.id', $subjectIds);
            })
                ->orWhereHas('academicLevel.academicSubjects', function ($query) use ($subjectIds) {
                    $query->whereIn('academic_subjects.id', $subjectIds);
                })
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            $userIds = $userIds->merge($students->pluck('id'));
        }

        // Return an Eloquent Collection directly
        return User::whereIn('id', $userIds->unique())->get();
    }

    protected function getRecipientsByIndividual(array $criteria): Collection
    {
        $userIds = $criteria['user_ids'] ?? [];

        return User::whereIn('id', $userIds)
            ->where('is_active', true)
            ->where('status', 'active')
            ->get();
    }

    protected function getRecipientsByCustomCriteria(array $criteria): Collection
    {
        $recipients = collect();

        // Combine multiple criteria types
        if (! empty($criteria['roles'])) {
            $recipients = $recipients->merge(
                $this->getRecipientsByRole(['roles' => $criteria['roles']])
            );
        }

        if (! empty($criteria['academic_group_ids'])) {
            $recipients = $recipients->merge(
                $this->getRecipientsByAcademicGroup($criteria)
            );
        }

        if (! empty($criteria['academic_level_ids'])) {
            $recipients = $recipients->merge(
                $this->getRecipientsByAcademicLevel($criteria)
            );
        }

        if (! empty($criteria['subject_ids'])) {
            $recipients = $recipients->merge(
                $this->getRecipientsBySubject($criteria)
            );
        }

        if (! empty($criteria['user_ids'])) {
            $recipients = $recipients->merge(
                $this->getRecipientsByIndividual($criteria)
            );
        }

        return $recipients->unique('id');
    }

    public function getAvailableRoles(): array
    {
        return [
            'student' => 'Students',
            'teacher' => 'Teachers',
            'librarian' => 'Librarians',
            'parent' => 'Parents',
            'author' => 'Authors',
            'moderator' => 'Moderators',
            'finance' => 'Finance Staff',
            'guest' => 'Guests',
        ];
    }

    public function getAcademicGroups(): Collection
    {
        return AcademicGroup::select('id', 'name')->orderBy('name')->get();
    }

    public function getAcademicLevels(): Collection
    {
        return AcademicLevel::select('id', 'name', 'label')->orderBy('name')->get();
    }

    public function getAcademicSubjects(): Collection
    {
        return AcademicSubject::select('id', 'name')->orderBy('name')->get();
    }
}
