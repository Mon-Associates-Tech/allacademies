<?php

namespace App\Observers;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubscription;
use Illuminate\Support\Facades\App;

class MockExamObserver
{

    /**
     * Handle the MockExam "created" event.
     */
    public function created(MockExam $mockExam): void
    {
        // Attempt to link the new mock exam to an active subscription based on subject match
        $this->linkToActiveSubscription($mockExam);
    }

    /**
     * Handle the MockExam "updated" event.
     */
    public function updated(MockExam $mockExam): void
    {
        // Check if academic_subject_id was updated and try to link to an appropriate subscription
        if ($mockExam->isDirty('academic_subject_id')) {
            $this->linkToActiveSubscription($mockExam);
        }
    }

    /**
     * Link a mock exam to an active subscription based on subject matching.
     */
    private function linkToActiveSubscription(MockExam $mockExam): void
    {
        if ($mockExam->mock_exam_subscription_id) {
            // Already linked to a subscription, skip
            return;
        }

        if (!$mockExam->academic_subject_id) {
            // No subject to match against, skip
            return;
        }

        // Find an active subscription that includes the academic subject
        $subscription = MockExamSubscription::where('user_id', $mockExam->user_id)
            ->active()
            ->whereHas('subjects', function ($query) use ($mockExam) {
                $query->where('academic_subjects.id', $mockExam->academic_subject_id);
            })
            ->orderByDesc('activated_at')
            ->first();

        if ($subscription) {
            $mockExam->update(['mock_exam_subscription_id' => $subscription->id]);
        }
    }
}