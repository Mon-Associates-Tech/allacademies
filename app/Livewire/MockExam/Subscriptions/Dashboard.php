<?php

namespace App\Livewire\MockExam\Subscriptions;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public string $activeTab = 'subscriptions';

    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';


    public function mount(): void
    {
        $this->activeTab = 'subscriptions';
    }

    public function getMockSubscriptionsProperty(): Collection
    {
        $user = auth()->user();

        $query = MockExamSubscription::where('user_id', $user->id)
            ->where('status', 'active');  // Only get active subscriptions

        // Apply search filtering
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('plan', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('subjects', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('mockExams', function ($subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'plan.name':
                $query->orderBy('name');
                break;
            case 'expires_at':
                $query->orderBy('expires_at', $this->sortDirection);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $this->sortDirection);
                break;
        }

        $subscriptions = $query->with([
            'plan',
            'subjects',
            'grantedBy',
            'mockExams:id,mock_exam_subscription_id,title,status,delivery_type',
        ])->get();

        // Load mock exams with their subject exams separately to access counts properly
        foreach ($subscriptions as $subscription) {
            $subscription->load('mockExams.subjectExams:id,mock_exam_id,academic_subject_id,title,duration_in_minutes');
            $subscription->load('mockExams.subjectExams.academicSubject:id,name');
            $subscription->load('mockExams.subjectExams.sections.questions');
        }

        return $subscriptions;
    }

    public function render()
    {
        return view('livewire.mock-exam.subscriptions.dashboard', [
            'mockSubscriptions' => $this->getMockSubscriptionsProperty(),
        ]);
    }
}