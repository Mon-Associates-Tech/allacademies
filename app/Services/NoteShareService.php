<?php

namespace App\Services;

use App\Models\Note;
use App\Models\NoteShare;
use App\Models\User;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\StudentGroup;
use App\Notifications\NoteSharedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NoteShareService
{
    public const SHARE_INDIVIDUAL = 'individual';
    public const SHARE_ACADEMIC_GROUP = 'academic_group';
    public const SHARE_ACADEMIC_LEVEL = 'academic_level';
    public const SHARE_STUDENT_GROUP = 'student_group';
    public const SHARE_SCHOOL_WIDE = 'school_wide';
    public const SHARE_EMAIL = 'email';

    /**
     * Share a note with recipients based on share type
     * @throws \Throwable
     */
    public function shareNote(Note $note, string $shareType, array $recipientIds, bool $canEdit = false): array
    {
        if ($shareType === self::SHARE_EMAIL) {
            return $this->shareNoteByEmail($note, $recipientIds[0], $canEdit);
        }

        $recipients = $this->resolveRecipients($shareType, $recipientIds, $note->user->school_id);
        $sharesCreated = 0;
        $usersNotified = [];

        DB::transaction(function () use ($note, $shareType, $recipientIds, $recipients, $canEdit, &$sharesCreated, &$usersNotified) {
            // Create shares based on type
            if ($shareType === self::SHARE_INDIVIDUAL) {
                foreach ($recipients as $user) {
                    $share = $this->createIndividualShare($note, $user, $canEdit);
                    if ($share->wasRecentlyCreated) {
                        $sharesCreated++;
                        $usersNotified[] = $user;
                    }
                }
            } else {
                // For group-based sharing, create one share record per group
                foreach ($recipientIds as $recipientId) {
                    $modelClass = $this->getModelClass($shareType);

                    $share = NoteShare::updateOrCreate(
                        [
                            'note_id' => $note->id,
                            'share_type' => $shareType,
                            'shareable_type' => $modelClass,
                            'shareable_id' => $recipientId,
                        ],
                        [
                            'can_edit' => $canEdit,
                        ]
                    );

                    if ($share->wasRecentlyCreated) {
                        $sharesCreated++;
                    }
                }

                // Add recipients to notification list
                foreach ($recipients as $user) {
                    $usersNotified[] = $user;
                }
            }
        });

        // Send notifications
        $this->sendNotifications($note, $usersNotified, $canEdit);

        return [
            'shares_created' => $sharesCreated,
            'users_notified' => count($usersNotified),
            'recipients' => $recipients,
        ];
    }

    /**
     * Share note with an email address (may or may not be in database)
     */
    private function shareNoteByEmail(Note $note, string $email, bool $canEdit = false): array
    {
        $user = User::where('email', $email)
            ->where('school_id', $note->user->school_id)
            ->first();

        $sharesCreated = 0;
        $usersNotified = 0;

        DB::transaction(function () use ($note, $email, $user, $canEdit, &$sharesCreated) {
            if ($user) {
                // User exists - create regular share
                $share = $this->createIndividualShare($note, $user, $canEdit);
                if ($share->wasRecentlyCreated) {
                    $sharesCreated++;
                }
            } else {
                // Guest email - create guest share
                $share = NoteShare::updateOrCreate(
                    [
                        'note_id' => $note->id,
                        'guest_email' => $email,
                        'share_type' => self::SHARE_EMAIL,
                    ],
                    [
                        'can_edit' => $canEdit,
                    ]
                );

                if ($share->wasRecentlyCreated) {
                    $sharesCreated++;
                }
            }
        });

        // Send notification
        if ($user) {
            $this->sendNotifications($note, [$user], $canEdit);
            $usersNotified = 1;
        } else {
            $this->sendGuestNotification($note, $email, $canEdit);
            $usersNotified = 1;
        }

        return [
            'shares_created' => $sharesCreated,
            'users_notified' => $usersNotified,
            'recipients' => $user ? collect([$user]) : collect([]),
        ];
    }

    /**
     * Send notification to guest email
     */
    private function sendGuestNotification(Note $note, string $email, bool $canEdit): void
    {
        try {
            \Mail::to($email)->send(new \App\Mail\NoteSharedGuestMail($note, $email, $canEdit));

            NoteShare::where('note_id', $note->id)
                ->where('guest_email', $email)
                ->update([
                    'notification_sent' => true,
                    'notified_at' => now(),
                ]);

            \Log::info('Guest notification sent', [
                'note_id' => $note->id,
                'guest_email' => $email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to notify guest about note share', [
                'note_id' => $note->id,
                'guest_email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve recipients based on share type
     */
    public function resolveRecipients(string $shareType, array $ids, int $schoolId): Collection
    {
        $recipients = collect();

        switch ($shareType) {
            case self::SHARE_INDIVIDUAL:
                $recipients = User::whereIn('id', $ids)
                    ->where('school_id', $schoolId)
                    ->get();
                break;

            case self::SHARE_ACADEMIC_GROUP:
                // Get all students in the selected academic groups for this school
                $recipients = User::whereHas('student', function ($query) use ($ids, $schoolId) {
                    $query->whereIn('academic_group_id', $ids)
                        ->where('school_id', $schoolId);
                })->where('school_id', $schoolId)->get();
                break;

            case self::SHARE_ACADEMIC_LEVEL:
                // Get all students in the selected academic levels for this school
                $recipients = User::whereHas('student', function ($query) use ($ids, $schoolId) {
                    $query->whereIn('academic_level_id', $ids)
                        ->where('school_id', $schoolId);
                })->where('school_id', $schoolId)->get();
                break;

            case self::SHARE_STUDENT_GROUP:
                // Get all students in the selected student groups
                $recipients = User::whereHas('student', function ($query) use ($ids, $schoolId) {
                    $query->whereIn('student_group_id', $ids)
                        ->where('school_id', $schoolId);
                })->where('school_id', $schoolId)->get();
                break;

            case self::SHARE_SCHOOL_WIDE:
                $recipients = User::where('school_id', $schoolId)
                    ->where('id', '!=', auth()->id())
                    ->get();
                break;
        }

        // Log recipients without valid emails
        $invalidEmailUsers = $recipients->filter(function ($user) {
            return empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL);
        });

        if ($invalidEmailUsers->isNotEmpty()) {
            \Log::warning("Some recipients don't have valid email addresses", [
                'count' => $invalidEmailUsers->count(),
                'user_ids' => $invalidEmailUsers->pluck('id')->toArray(),
            ]);
        }

        return $recipients;
    }
    /**
     * Create an individual share
     */
    private function createIndividualShare(Note $note, User $user, bool $canEdit): NoteShare
    {
        return NoteShare::updateOrCreate(
            [
                'note_id' => $note->id,
                'shared_with_user_id' => $user->id,
                'share_type' => self::SHARE_INDIVIDUAL,
            ],
            [
                'can_edit' => $canEdit,
            ]
        );
    }

    /**
     * Send notifications to recipients
     */
    private function sendNotifications(Note $note, array $users, bool $canEdit): void
    {
        \Log::info('Starting to send notifications', [
            'note_id' => $note->id,
            'note_title' => $note->title,
            'total_users' => count($users),
            'can_edit' => $canEdit,
        ]);

        foreach ($users as $user) {
            try {
                // Validate email
                if (empty($user->email)) {
                    \Log::warning("User has no email address", [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                    ]);
                    continue;
                }

                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    \Log::warning("User has invalid email address", [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                    ]);
                    continue;
                }

                \Log::info('Preparing to notify user', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'note_id' => $note->id,
                ]);

                // Send notification
                $user->notify(new NoteSharedNotification($note, $canEdit));

                \Log::info('Notification dispatched successfully', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                // Update notification tracking
                NoteShare::where('note_id', $note->id)
                    ->where('shared_with_user_id', $user->id)
                    ->update([
                        'notification_sent' => true,
                        'notified_at' => now(),
                    ]);

            } catch (\Exception $e) {
                \Log::error("Failed to notify user about note share", [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email ?? 'null',
                    'note_id' => $note->id,
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'stack_trace' => $e->getTraceAsString(),
                ]);
            }
        }

        \Log::info('Finished sending notifications', [
            'note_id' => $note->id,
            'total_users' => count($users),
        ]);
    }

    /**
     * Get model class for share type
     */
    private function getModelClass(string $shareType): ?string
    {
        return match ($shareType) {
            self::SHARE_ACADEMIC_GROUP => AcademicGroup::class,
            self::SHARE_ACADEMIC_LEVEL => AcademicLevel::class,
            self::SHARE_STUDENT_GROUP => StudentGroup::class,
            default => null,
        };
    }

    /**
     * Remove share
     */
    public function unshare(Note $note, string $shareType, $identifier): bool
    {
        if ($shareType === self::SHARE_INDIVIDUAL) {
            return NoteShare::where('note_id', $note->id)
                    ->where('shared_with_user_id', $identifier)
                    ->delete() > 0;
        }

        $modelClass = $this->getModelClass($shareType);
        return NoteShare::where('note_id', $note->id)
                ->where('shareable_type', $modelClass)
                ->where('shareable_id', $identifier)
                ->delete() > 0;
    }
}
