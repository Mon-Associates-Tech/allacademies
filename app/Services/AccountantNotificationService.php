<?php

namespace App\Services;

use App\Channels\Messages\SmsMessage;
use App\Mail\MessageNotificationMail;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\MessageTemplate;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentPaymentRecord;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class AccountantNotificationService
{
    // ─── Recipient Resolution ────────────────────────────────────────────────

    /**
     * Resolve recipients based on target type + criteria.
     * Returns a collection of User models (students' users + their parents' users).
     */
    public function resolveRecipients(string $targetType, array $criteria): Collection
    {
        $users = match ($targetType) {
            'all_unpaid'       => $this->getUsersWithUnpaidFees($criteria),
            'all_overdue'      => $this->getUsersWithOverdueFees($criteria),
            'all_partial'      => $this->getUsersWithPartialFees($criteria),
            'academic_group'   => $this->getUsersByAcademicGroup($criteria),
            'academic_level'   => $this->getUsersByAcademicLevel($criteria),
            'individual'       => $this->getUsersByIds($criteria),
            'role'             => $this->getUsersByRole($criteria),
            default            => collect(),
        };

        return $users->unique('id')->values();
    }

    /**
     * For a given set of primary recipients (students), also collect their parents.
     * Returns a flat unique collection of all users to notify.
     */
    public function withParents(Collection $studentUsers): Collection
    {
        $studentIds = Student::whereIn('user_id', $studentUsers->pluck('id'))
            ->pluck('id');

        $parentUserIds = StudentParent::whereHas('students', fn ($q) => $q->whereIn('students.id', $studentIds))
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->pluck('id');

        $parentUsers = User::whereIn('id', $parentUserIds)->get();

        return $studentUsers->merge($parentUsers)->unique('id')->values();
    }

    protected function getUsersWithUnpaidFees(array $criteria): Collection
    {
        $query = StudentPaymentRecord::unpaid()
            ->with('student.user');

        if (! empty($criteria['academic_period_id'])) {
            $query->where('academic_period_id', $criteria['academic_period_id']);
        }

        if (! empty($criteria['academic_group_id'])) {
            $query->whereHas('student', fn ($q) => $q->where('academic_group_id', $criteria['academic_group_id']));
        }

        if (! empty($criteria['academic_level_id'])) {
            $query->whereHas('student', fn ($q) => $q->where('academic_level_id', $criteria['academic_level_id']));
        }

        return $query->get()
            ->pluck('student.user')
            ->filter()
            ->values();
    }

    protected function getUsersWithOverdueFees(array $criteria): Collection
    {
        $query = StudentPaymentRecord::overdue()
            ->with('student.user');

        if (! empty($criteria['academic_period_id'])) {
            $query->where('academic_period_id', $criteria['academic_period_id']);
        }

        if (! empty($criteria['academic_group_id'])) {
            $query->whereHas('student', fn ($q) => $q->where('academic_group_id', $criteria['academic_group_id']));
        }

        if (! empty($criteria['academic_level_id'])) {
            $query->whereHas('student', fn ($q) => $q->where('academic_level_id', $criteria['academic_level_id']));
        }

        return $query->get()
            ->pluck('student.user')
            ->filter()
            ->values();
    }

    protected function getUsersWithPartialFees(array $criteria): Collection
    {
        $query = StudentPaymentRecord::partiallyPaid()
            ->with('student.user');

        if (! empty($criteria['academic_period_id'])) {
            $query->where('academic_period_id', $criteria['academic_period_id']);
        }

        return $query->get()
            ->pluck('student.user')
            ->filter()
            ->values();
    }

    protected function getUsersByAcademicGroup(array $criteria): Collection
    {
        $groupIds = $criteria['academic_group_ids'] ?? [];

        return User::whereHas('student', fn ($q) => $q->whereIn('academic_group_id', $groupIds))
            ->where('is_active', true)
            ->get();
    }

    protected function getUsersByAcademicLevel(array $criteria): Collection
    {
        $levelIds = $criteria['academic_level_ids'] ?? [];

        return User::whereHas('student', fn ($q) => $q->whereIn('academic_level_id', $levelIds))
            ->where('is_active', true)
            ->get();
    }

    protected function getUsersByIds(array $criteria): Collection
    {
        return User::whereIn('id', $criteria['user_ids'] ?? [])
            ->where('is_active', true)
            ->get();
    }

    protected function getUsersByRole(array $criteria): Collection
    {
        return User::whereIn('role', $criteria['roles'] ?? [])
            ->where('is_active', true)
            ->get();
    }

    // ─── Template Variable Resolution ────────────────────────────────────────

    /**
     * Build template variables for a given recipient user.
     * For fee templates, looks up the student's payment record for the given period.
     */
    public function buildVariables(User $recipient, array $contextData): array
    {
        $school = app()->bound('current_school') ? app('current_school') : null;

        $vars = [
            'recipient_name' => $recipient->name,
            'school_name'    => $school?->name ?? config('app.name'),
            'currency'       => $school?->currency ?? 'GHS',
        ];

        // Fee-specific variables
        if (! empty($contextData['academic_period_id'])) {
            $period = AcademicPeriod::find($contextData['academic_period_id']);
            $vars['term_name'] = $period?->name ?? '';
        }

        $student = Student::where('user_id', $recipient->id)->first();

        if ($student) {
            $vars['student_name'] = trim("{$student->first_name} {$student->last_name}");

            if (! empty($contextData['academic_period_id'])) {
                $record = StudentPaymentRecord::where('student_id', $student->id)
                    ->where('academic_period_id', $contextData['academic_period_id'])
                    ->first();

                if ($record) {
                    $vars['total_amount'] = number_format((float) $record->total_amount, 2);
                    $vars['amount_paid']  = number_format((float) $record->amount_paid, 2);
                    $vars['balance']      = number_format((float) $record->amount_remaining, 2);
                    $vars['due_date']     = $record->due_date?->format('d M Y') ?? 'N/A';
                }
            }
        } else {
            // Recipient is a parent — try to find their ward
            $parentRecord = StudentParent::where('user_id', $recipient->id)->first();
            if ($parentRecord) {
                $ward = $parentRecord->students()->first();
                if ($ward) {
                    $vars['student_name'] = trim("{$ward->first_name} {$ward->last_name}");

                    if (! empty($contextData['academic_period_id'])) {
                        $record = StudentPaymentRecord::where('student_id', $ward->id)
                            ->where('academic_period_id', $contextData['academic_period_id'])
                            ->first();

                        if ($record) {
                            $vars['total_amount'] = number_format((float) $record->total_amount, 2);
                            $vars['amount_paid']  = number_format((float) $record->amount_paid, 2);
                            $vars['balance']      = number_format((float) $record->amount_remaining, 2);
                            $vars['due_date']     = $record->due_date?->format('d M Y') ?? 'N/A';
                        }
                    }
                }
            }
        }

        // Merge any extra context variables (event details, custom fields, etc.)
        return array_merge($vars, $contextData['extra_vars'] ?? []);
    }

    // ─── Dispatch ────────────────────────────────────────────────────────────

    public function sendMessage(Message $message): bool
    {
        try {
            DB::beginTransaction();

            $recipients = $this->resolveRecipients(
                $message->target_type,
                $message->target_criteria ?? []
            );

            $includeParents = $message->target_criteria['include_parents'] ?? true;
            if ($includeParents) {
                $recipients = $this->withParents($recipients);
            }

            $recipients = $recipients->filter(fn ($u) => filled($u->email) && filter_var($u->email, FILTER_VALIDATE_EMAIL));

            if ($recipients->isEmpty()) {
                throw new Exception('No valid recipients found.');
            }

            $channels    = $message->channels ?? ['email', 'in_app'];
            $template    = $message->template;
            $contextData = $message->context_data ?? [];

            foreach ($recipients as $recipient) {
                $vars = $this->buildVariables($recipient, $contextData);
                $vars['message_body'] = $contextData['message_body'] ?? $message->body ?? '';

                $renderedBody    = $template ? $template->renderBody($vars) : $message->body;
                $renderedSubject = $template ? $template->renderSubject($vars) : $message->subject;
                $renderedSms     = $template ? $template->renderSmsBody($vars) : strip_tags($message->body);

                $recipientRecord = MessageRecipient::create([
                    'message_id' => $message->id,
                    'user_id'    => $recipient->id,
                ]);

                if (in_array('email', $channels) && filled($recipient->email)) {
                    try {
                        Mail::to($recipient->email)->sendNow(
                            new MessageNotificationMail($message, $recipient, $renderedSubject, $renderedBody)
                        );
                        $recipientRecord->update(['email_sent' => true, 'email_sent_at' => now()]);
                    } catch (Exception $e) {
                        $recipientRecord->update(['email_failed_at' => now(), 'failure_reason' => $e->getMessage()]);
                        Log::error('Email failed', ['recipient' => $recipient->id, 'error' => $e->getMessage()]);
                    }
                }

                if (in_array('sms', $channels) && filled($recipient->phone)) {
                    try {
                        $smsProvider = app(\App\Contracts\SmsProvider::class);
                        if ($smsProvider->isAvailable()) {
                            $smsProvider->send($recipient->phone, $renderedSms);
                            $recipientRecord->update(['sms_sent' => true, 'sms_sent_at' => now()]);
                        }
                    } catch (Exception $e) {
                        Log::error('SMS failed', ['recipient' => $recipient->id, 'error' => $e->getMessage()]);
                    }
                }

                if (in_array('in_app', $channels)) {
                    $recipient->notifications()->create([
                        'id'              => \Illuminate\Support\Str::uuid(),
                        'type'            => 'App\Notifications\MessageNotification',
                        'notifiable_type' => User::class,
                        'notifiable_id'   => $recipient->id,
                        'data'            => json_encode([
                            'message_id' => $message->id,
                            'subject'    => $renderedSubject,
                            'body'       => $renderedBody,
                            'sender'     => $message->sender?->name,
                            'is_urgent'  => $message->is_urgent,
                        ]),
                    ]);
                    $recipientRecord->update(['in_app_sent' => true]);
                }
            }

            $message->update(['status' => Message::STATUS_SENT, 'sent_at' => now()]);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $message->update(['status' => Message::STATUS_FAILED]);
            Log::error('AccountantNotificationService::sendMessage failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getTemplatesForSchool(int $schoolId)
    {
        return MessageTemplate::active()
            ->forSchoolOrSystem($schoolId)
            ->orderByRaw('is_system DESC')
            ->orderBy('name')
            ->get();
    }

    public function getAcademicPeriods()
    {
        return AcademicPeriod::orderByDesc('is_current')->orderByDesc('start_date')->get();
    }

    public function getAcademicGroups()
    {
        return AcademicGroup::orderBy('name')->get();
    }

    public function getAcademicLevels()
    {
        return AcademicLevel::orderBy('name')->get();
    }
}
