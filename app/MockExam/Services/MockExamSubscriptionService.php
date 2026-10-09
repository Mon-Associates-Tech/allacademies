<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExamPricingTier;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Models\MockExamSubscriptionPayment;
use App\MockExam\Models\MockExamSubscriptionPlan;
use App\MockExam\Models\MockExamSubscriptionStatus;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockExamSubscriptionService
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
        $plan = MockExamSubscriptionPlan::findOrFail($config['plan_id']);

        return $plan->calculatePrice(
            $config['subject_count'],
            $config['participant_count'] ?? 0,
            $config['user_group'] ?? 'individual',
            max(1, (int) ($config['exam_cycles'] ?? $config['max_exams'] ?? 1))
        );
    }

    /**
     * Calculate the top-up price for adding more participant slots.
     */
    public function calculateTopUpPrice(MockExamSubscription $subscription, int $additionalParticipants): float
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

        $tier = MockExamPricingTier::forSubjectCount($subjectCount);

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
        $plan = MockExamSubscriptionPlan::findOrFail($config['plan_id']);
        $subjectCount = count($config['subject_ids']);
        $userGroup = $config['user_group'] ?? 'individual';
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));
        $amount = $plan->calculatePrice($subjectCount, $config['participant_count'] ?? 0, $userGroup, $examCyclesPerSubject);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize payment: calculated amount is invalid. Please review pricing tiers for the selected subject count and user group.');
        }

        return DB::transaction(function () use ($user, $plan, $config, $amount, $examCyclesPerSubject, $userGroup) {
            $subscription = MockExamSubscription::create([
                'user_id' => $user->id,
                'mock_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'user_group' => $userGroup,
                'status' => MockExamSubscriptionStatus::Pending,
                'participant_slots' => $config['participant_count'] ?? 0,
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => $amount,
            ]);

            $subscription->subjects()->sync($config['subject_ids']);
            $this->syncUserMockSubjectsToActiveSubscriptions($user->id);
            $this->linkExistingMockExamsToSubscription($subscription);

            $reference = 'MES-'.strtoupper(Str::random(12));

            $payment = MockExamSubscriptionPayment::create([
                'mock_exam_subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'paystack_reference' => $reference,
                'amount' => $amount,
                'currency' => 'GHS',
                'status' => 'pending',
                'payment_type' => 'new',
            ]);

            $paystackData = $this->paystack->initializeTransaction([
                'email' => $user->email,
                'amount' => (int) ($amount * 100),
                'reference' => $reference,
                'currency' => 'GHS',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                    'type' => 'mock_exam_subscription',
                ],
                'callback_url' => route('mock-exams.subscription.payment.callback'),
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

    public function syncUserMockSubjectsToActiveSubscriptions(int $userId): void
    {
        $mockSubjectIds = \App\MockExam\Models\MockExam::where('user_id', $userId)
            ->with('subjectExams')
            ->get()
            ->flatMap(fn (\App\MockExam\Models\MockExam $exam) => array_merge(
                $exam->subjectExams->pluck('academic_subject_id')->filter()->all(),
                $exam->academic_subject_id ? [$exam->academic_subject_id] : []
            ))
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach (MockExamSubscription::where('user_id', $userId)->active()->get() as $subscription) {
            $currentIds = $subscription->subjects()->pluck('academic_subjects.id')->all();
            $merged = array_values(array_unique(array_merge($currentIds, $mockSubjectIds)));
            $subscription->subjects()->sync($merged);
        }
    }

    /**
     * Link existing mock exams to a subscription based on subject matching
     */
    public function linkExistingMockExamsToSubscription(MockExamSubscription $subscription): void
    {
        // Get all subjects in this subscription
        $subscriptionSubjectIds = $subscription->subjects()->pluck('academic_subjects.id')->toArray();
        
        if (empty($subscriptionSubjectIds)) {
            return;
        }

        // Find all mock exams that belong to the user and match the subscription subjects
        $mockExams = MockExam::where('user_id', $subscription->user_id)
            ->where(function ($query) use ($subscriptionSubjectIds) {
                $query->whereIn('academic_subject_id', $subscriptionSubjectIds)
                      ->orWhereHas('subjectExams', function ($subQuery) use ($subscriptionSubjectIds) {
                          $subQuery->whereIn('academic_subject_id', $subscriptionSubjectIds);
                      });
            })
            ->get();

        // Update each matching mock exam to reference this subscription
        foreach ($mockExams as $mockExam) {
            // Only update if not already linked to another subscription
            if (!$mockExam->mock_exam_subscription_id) {
                $mockExam->update(['mock_exam_subscription_id' => $subscription->id]);
            }
        }
    }

    /**
     * Verify and activate a subscription after Paystack callback.
     */
    public function verifyAndActivate(string $reference): array
    {
        $payment = MockExamSubscriptionPayment::where('paystack_reference', $reference)->firstOrFail();       if ($payment->isSuccessful()) {
            return ['success' => true, 'subscription' => $payment->subscription];
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
        } catch (\Exception $e) {
            Log::error('Paystack verification failed for mock exam subscription', [
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
            
            // Link existing mock exams to the activated subscription
            $this->linkExistingMockExamsToSubscription($payment->subscription);

            return ['success' => true, 'subscription' => $payment->subscription->fresh()];
        }

        return ['success' => false, 'error' => 'Payment was not successful.'];
    }

    /**
     * Initiate a top-up payment for additional participant slots.
     */
    public function initiateTopUp(User $user, MockExamSubscription $subscription, int $additionalParticipants): array  {
        $amount = $this->calculateTopUpPrice($subscription, $additionalParticipants);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize top-up: calculated amount is invalid.');
        }

        $reference = 'MES-TOP-'.strtoupper(Str::random(10));

        $payment = MockExamSubscriptionPayment::create([
            'mock_exam_subscription_id' => $subscription->id,
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
                'type' => 'mock_exam_subscription_topup',
                'additional_participants' => $additionalParticipants,
            ],
            'callback_url' => route('mock-exams.subscription.payment.callback'),
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
        $payment = MockExamSubscriptionPayment::where('paystack_reference', $reference)->firstOrFail();

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
    public function grantSubscription(User $owner, User $targetUser, array $config): MockExamSubscription
    {
        $plan = MockExamSubscriptionPlan::findOrFail($config['plan_id']);
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));
        $userGroup = $config['user_group'] ?? 'individual';

        return DB::transaction(function () use ($owner, $targetUser, $plan, $config, $examCyclesPerSubject, $userGroup) {
            $subscription = MockExamSubscription::create([
                'user_id' => $targetUser->id,
                'mock_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'user_group' => $userGroup,
                'status' => MockExamSubscriptionStatus::Active,
                'participant_slots' => $config['participant_count'] ?? 0,
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => 0,
                'granted_by_owner' => true,
                'granted_by' => $owner->id,
                'activated_at' => now(),
                'expires_at' => $this->calculateExpiry($plan),
            ]);

            $subscription->subjects()->sync($config['subject_ids']);
            $this->syncUserMockSubjectsToActiveSubscriptions($targetUser->id);
            $this->linkExistingMockExamsToSubscription($subscription);

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
        $plan = MockExamSubscriptionPlan::findOrFail($config['plan_id']);
        $subjectCount = count($config['subject_ids']);
        $userGroup = $config['user_group'] ?? 'individual';
        $examCyclesPerSubject = max(1, (int) ($config['max_exams'] ?? 1));
        $amount = $plan->calculatePrice($subjectCount, $config['participant_count'] ?? 0, $userGroup, $examCyclesPerSubject);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unable to initialize payment: calculated amount is invalid. Please review pricing tiers for the selected subject count and user group.');
        }

        return DB::transaction(function () use ($owner, $targetUser, $plan, $config, $amount, $examCyclesPerSubject, $userGroup) {
            $subscription = MockExamSubscription::create([
                'user_id' => $targetUser->id,
                'mock_exam_subscription_plan_id' => $plan->id,
                'type' => $config['type'],
                'user_group' => $userGroup,
                'status' => MockExamSubscriptionStatus::Pending,
                'participant_slots' => $config['participant_count'] ?? 0,
                'max_exams' => $examCyclesPerSubject,
                'amount_paid' => $amount,
                'granted_by_owner' => true,
                'granted_by' => $owner->id,
            ]);

            $subscription->subjects()->sync($config['subject_ids']);
            $this->syncUserMockSubjectsToActiveSubscriptions($targetUser->id);
            $this->linkExistingMockExamsToSubscription($subscription);

            $reference = 'MES-OWN-'.strtoupper(Str::random(10));

            $payment = MockExamSubscriptionPayment::create([
                'mock_exam_subscription_id' => $subscription->id,
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
                    'type' => 'mock_exam_subscription',
                    'on_behalf_of' => $targetUser->id,
                ],
                'callback_url' => route('mock-exams.subscription.payment.callback'),
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
        MockExamSubmission $submission,
        array $questionScores,
        User $editor,
        ?string $reason = null
    ): MockExamSubmission {
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
                'status' => MockExamSubmission::STATUS_MANUALLY_REVIEWED,
                'graded_by' => $editor->id,
                'graded_at' => now(),
            ]);

            MockExamScoreAuditLog::create([
                'mock_exam_submission_id' => $submission->id,
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
        $examIds = \App\ExaminationHub\Models\GeneralExam::where('user_id', $instructor->id)->pluck('id');

        $submissions = MockExamSubmission::whereIn('mock_exam_id', $examIds)
            ->whereNotNull('submitted_at')
            ->with(['assignment', 'participant'])
            ->get()
            ->filter(function ($submission) use ($identifier) {
                $name = strtolower($submission->getParticipantName());
                $email = strtolower($subscription->getParticipantEmail());
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

    protected function calculateExpiry(MockExamSubscriptionPlan $plan): ?\Carbon\Carbon
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