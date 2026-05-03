<?php

namespace App\Services\GeneralExam;

use App\Enums\GeneralExamSubscriptionStatus;
use App\Models\GeneralExamScoreAuditLog;
use App\Models\GeneralExamSubmission;
use App\Models\GeneralExamSubscription;
use App\Models\GeneralExamSubscriptionPayment;
use App\Models\GeneralExamSubscriptionPlan;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeneralExamSubscriptionService
{
    public function __construct(protected PaystackService $paystack) {}

    // ==================== PRICING ====================

    /**
     * Calculate the price for a subscription configuration.
     *
     * @param  array{type: string, subject_count: int, participant_count: int, plan_id: int}  $config
     */
    public function calculatePrice(array $config): float
    {
        $plan = GeneralExamSubscriptionPlan::findOrFail($config['plan_id']);

        return $plan->calculatePrice(
            $config['subject_count'],
            $config['participant_count'] ?? 0
        );
    }

    /**
     * Calculate the top-up price for adding more participant slots.
     */
    public function calculateTopUpPrice(GeneralExamSubscription $subscription, int $additionalParticipants): float
    {
        $subjectCount = $subscription->subjects()->count();
        $plan = $subscription->plan;

        if ($plan->base_price > 0) {
            // Pro-rate based on original per-slot cost
            $originalSlots = $subscription->participant_slots;
            if ($originalSlots === 0) {
                return 0.0;
            }
            $perSlot = (float) $subscription->amount_paid / $originalSlots;

            return round($perSlot * $additionalParticipants, 2);
        }

        $tier = \App\Models\GeneralExamPricingTier::forSubjectCount($subjectCount);

        if (! $tier) {
            return 0.0;
        }

        return (float) $tier->price_per_student * $additionalParticipants;
    }

    // ==================== PAYMENT FLOW ====================

    /**
     * Initialise a Paystack payment for a new subscription.
     *
     * @param  array{plan_id: int, type: string, subject_ids: int[], participant_count: int, max_exams: ?int}  $config
     */
    public function initiatePayment(User $user, array $config): array
    {
        $plan = GeneralExamSubscriptionPlan::findOrFail($config['plan_id']);
        $subjectCount = count($config['subject_ids']);
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));
        $amount = $plan->calculatePrice($subjectCount, $config['participant_count'] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize payment: calculated amount is invalid. Please review pricing tiers for the selected subject count.');
        }

        return DB::transaction(function () use ($user, $plan, $config, $amount, $examCyclesPerSubject) {
            $subscription = GeneralExamSubscription::create([
                'user_id' => $user->id,
                'general_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'status' => GeneralExamSubscriptionStatus::Pending,
                'participant_slots' => $config['participant_count'] ?? 0,
                // Stored as "exam cycles per subject".
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => $amount,
            ]);

            $subscription->subjects()->sync($config['subject_ids']);

            $reference = 'GES-'.strtoupper(Str::random(12));

            $payment = GeneralExamSubscriptionPayment::create([
                'general_exam_subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'paystack_reference' => $reference,
                'amount' => $amount,
                'currency' => 'GHS',
                'status' => 'pending',
                'payment_type' => 'new',
            ]);

            $paystackData = $this->paystack->initializeTransaction([
                'email' => $user->email,
                'amount' => (int) ($amount * 100), // Paystack uses kobo/pesewas
                'reference' => $reference,
                'currency' => 'GHS',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                    'type' => 'general_exam_subscription',
                ],
                'callback_url' => route('general-exams.subscription.payment.callback'),
            ]);

            $payment->update([
                'paystack_access_code' => $paystackData['data']['access_code'] ?? null,
                'paystack_response' => $paystackData,
            ]);

            return [
                'subscription' => $subscription,
                'payment' => $payment,
                'authorization_url' => $paystackData['data']['authorization_url'] ?? null,
                'reference' => $reference,
            ];
        });
    }

    /**
     * Verify and activate a subscription after Paystack callback.
     */
    public function verifyAndActivate(string $reference): array
    {
        $payment = GeneralExamSubscriptionPayment::where('paystack_reference', $reference)->firstOrFail();

        if ($payment->isSuccessful()) {
            return ['success' => true, 'subscription' => $payment->subscription];
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
        } catch (\Exception $e) {
            Log::error('Paystack verification failed for general exam subscription', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => 'Payment verification failed.'];
        }

        $status = $response['data']['status'] ?? 'failed';

        $payment->update([
            'status' => $status === 'success' ? 'success' : 'failed',
            'paystack_response' => $response,
            'paid_at' => $status === 'success' ? now() : null,
        ]);

        if ($status === 'success') {
            $payment->subscription->activate();

            return ['success' => true, 'subscription' => $payment->subscription->fresh()];
        }

        return ['success' => false, 'error' => 'Payment was not successful.'];
    }

    /**
     * Initiate a top-up payment for additional participant slots.
     */
    public function initiateTopUp(User $user, GeneralExamSubscription $subscription, int $additionalParticipants): array
    {
        $amount = $this->calculateTopUpPrice($subscription, $additionalParticipants);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize top-up: calculated amount is invalid.');
        }

        $reference = 'GES-TOP-'.strtoupper(Str::random(10));

        $payment = GeneralExamSubscriptionPayment::create([
            'general_exam_subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'paystack_reference' => $reference,
            'amount' => $amount,
            'currency' => 'GHS',
            'status' => 'pending',
            'payment_type' => 'topup',
            'additional_participants' => $additionalParticipants,
        ]);

        $paystackData = $this->paystack->initializeTransaction([
            'email' => $user->email,
            'amount' => (int) ($amount * 100),
            'reference' => $reference,
            'currency' => 'GHS',
            'metadata' => [
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'type' => 'general_exam_subscription_topup',
                'additional_participants' => $additionalParticipants,
            ],
            'callback_url' => route('general-exams.subscription.payment.callback'),
        ]);

        $payment->update([
            'paystack_access_code' => $paystackData['data']['access_code'] ?? null,
            'paystack_response' => $paystackData,
        ]);

        return [
            'payment' => $payment,
            'authorization_url' => $paystackData['data']['authorization_url'] ?? null,
            'reference' => $reference,
        ];
    }

    /**
     * Verify and apply a top-up payment.
     */
    public function verifyTopUp(string $reference): array
    {
        $payment = GeneralExamSubscriptionPayment::where('paystack_reference', $reference)->firstOrFail();

        if ($payment->isSuccessful()) {
            return ['success' => true, 'subscription' => $payment->subscription];
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Payment verification failed.'];
        }

        $status = $response['data']['status'] ?? 'failed';

        $payment->update([
            'status' => $status === 'success' ? 'success' : 'failed',
            'paystack_response' => $response,
            'paid_at' => $status === 'success' ? now() : null,
        ]);

        if ($status === 'success') {
            $payment->subscription->increment('participant_slots', $payment->additional_participants);

            return ['success' => true, 'subscription' => $payment->subscription->fresh()];
        }

        return ['success' => false, 'error' => 'Payment was not successful.'];
    }

    // ==================== OWNER ALLOCATION ====================

    /**
     * Grant a subscription to a user without payment (owner allocation).
     *
     * @param  array{plan_id: int, type: string, subject_ids: int[], participant_count: int, max_exams: ?int}  $config
     */
    public function grantSubscription(User $owner, User $targetUser, array $config): GeneralExamSubscription
    {
        $plan = GeneralExamSubscriptionPlan::findOrFail($config['plan_id']);
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));

        return DB::transaction(function () use ($owner, $targetUser, $plan, $config, $examCyclesPerSubject) {
            $subscription = GeneralExamSubscription::create([
                'user_id' => $targetUser->id,
                'general_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'status' => GeneralExamSubscriptionStatus::Active,
                'participant_slots' => $config['participant_count'] ?? 0,
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => 0,
                'granted_by_owner' => true,
                'granted_by' => $owner->id,
                'activated_at' => now(),
                'expires_at' => $this->calculateExpiry($plan),
            ]);

            $subscription->subjects()->sync($config['subject_ids']);

            return $subscription;
        });
    }

    /**
     * Owner initiates payment on behalf of a user (goes through Paystack).
     *
     * @param  array{plan_id: int, type: string, subject_ids: int[], participant_count: int, max_exams: ?int}  $config
     */
    public function initiateOwnerPayment(User $owner, User $targetUser, array $config): array
    {
        $plan = GeneralExamSubscriptionPlan::findOrFail($config['plan_id']);
        $subjectCount = count($config['subject_ids']);
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));
        $amount = $plan->calculatePrice($subjectCount, $config['participant_count'] ?? 0);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize payment: calculated amount is invalid. Please review pricing tiers for the selected subject count.');
        }

        return DB::transaction(function () use ($owner, $targetUser, $plan, $config, $amount, $examCyclesPerSubject) {
            $subscription = GeneralExamSubscription::create([
                'user_id' => $targetUser->id,
                'general_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'status' => GeneralExamSubscriptionStatus::Pending,
                'participant_slots' => $config['participant_count'] ?? 0,
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => $amount,
                'granted_by_owner' => true,
                'granted_by' => $owner->id,
            ]);

            $subscription->subjects()->sync($config['subject_ids']);

            $reference = 'GES-OWN-'.strtoupper(Str::random(10));

            $payment = GeneralExamSubscriptionPayment::create([
                'general_exam_subscription_id' => $subscription->id,
                'user_id' => $owner->id,
                'paystack_reference' => $reference,
                'amount' => $amount,
                'currency' => 'GHS',
                'status' => 'pending',
                'payment_type' => 'new',
            ]);

            $paystackData = $this->paystack->initializeTransaction([
                'email' => $owner->email,
                'amount' => (int) ($amount * 100),
                'reference' => $reference,
                'currency' => 'GHS',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                    'type' => 'general_exam_subscription',
                    'on_behalf_of' => $targetUser->id,
                ],
                'callback_url' => route('general-exams.subscription.payment.callback'),
            ]);

            $payment->update([
                'paystack_access_code' => $paystackData['data']['access_code'] ?? null,
                'paystack_response' => $paystackData,
            ]);

            return [
                'subscription' => $subscription,
                'payment' => $payment,
                'authorization_url' => $paystackData['data']['authorization_url'] ?? null,
                'reference' => $reference,
            ];
        });
    }

    // ==================== SCORE AUDITING ====================

    /**
     * Update a submission's score with full audit trail and grade recalculation.
     *
     * @param  array<int, float>  $questionScores  Map of question_id => new_points
     */
    public function updateScoreWithAudit(
        GeneralExamSubmission $submission,
        array $questionScores,
        User $editor,
        ?string $reason = null
    ): GeneralExamSubmission {
        return DB::transaction(function () use ($submission, $questionScores, $editor, $reason) {
            $oldScore = $submission->score;
            $oldGrade = $submission->grade;
            $oldPercentage = $submission->percentage;

            $responses = $submission->responses ?? [];
            $questionChanges = [];

            foreach ($questionScores as $questionId => $newPoints) {
                $oldPoints = $responses[$questionId]['points_earned'] ?? 0;

                if ((float) $oldPoints !== (float) $newPoints) {
                    $questionChanges[$questionId] = [
                        'old' => $oldPoints,
                        'new' => $newPoints,
                    ];
                    $responses[$questionId]['points_earned'] = $newPoints;
                    $responses[$questionId]['manually_graded'] = true;
                }
            }

            // Recalculate totals
            $newScore = collect($responses)->sum(fn ($r) => $r['points_earned'] ?? 0);
            $totalMarks = $submission->total_marks > 0 ? $submission->total_marks : 1;
            $newPercentage = round(($newScore / $totalMarks) * 100, 2);
            $newGrade = $this->calculateGrade($newPercentage);

            $submission->update([
                'responses' => $responses,
                'score' => $newScore,
                'percentage' => $newPercentage,
                'grade' => $newGrade,
                'status' => GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
                'graded_by' => $editor->id,
                'graded_at' => now(),
            ]);

            GeneralExamScoreAuditLog::create([
                'general_exam_submission_id' => $submission->id,
                'edited_by' => $editor->id,
                'old_score' => $oldScore,
                'new_score' => $newScore,
                'old_grade' => $oldGrade,
                'new_grade' => $newGrade,
                'old_percentage' => $oldPercentage,
                'new_percentage' => $newPercentage,
                'reason' => $reason,
                'question_changes' => $questionChanges,
            ]);

            return $submission->fresh();
        });
    }

    // ==================== PARTICIPANT PERFORMANCE ====================

    /**
     * Get performance history for a participant across all exams by a given instructor.
     *
     * @param  string  $identifier  Email, name, or student ID
     */
    public function getParticipantPerformance(User $instructor, string $identifier): array
    {
        $examIds = \App\Models\GeneralExam::where('user_id', $instructor->id)->pluck('id');

        $submissions = GeneralExamSubmission::whereIn('general_exam_id', $examIds)
            ->whereNotNull('submitted_at')
            ->with(['assignment', 'participant'])
            ->get()
            ->filter(function ($submission) use ($identifier) {
                $name = strtolower($submission->getParticipantName());
                $email = strtolower($submission->getParticipantEmail());
                $search = strtolower($identifier);

                return str_contains($name, $search) || str_contains($email, $search);
            })
            ->values();

        return [
            'submissions' => $submissions,
            'total_exams' => $submissions->count(),
            'average_score' => $submissions->avg('percentage') ?? 0,
            'highest_score' => $submissions->max('percentage') ?? 0,
            'lowest_score' => $submissions->min('percentage') ?? 0,
            'grade_distribution' => $submissions->groupBy('grade')->map->count(),
        ];
    }

    // ==================== HELPERS ====================

    protected function calculateExpiry(GeneralExamSubscriptionPlan $plan): ?\Carbon\Carbon
    {
        if ($plan->duration_type === 'period' && $plan->duration_value) {
            return now()->addDays($plan->duration_value);
        }

        return null;
    }

    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}
