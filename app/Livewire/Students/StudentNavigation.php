<?php

namespace App\Livewire\Students;

use App\Models\MessageRecipient;
use App\Models\Student;
use App\Services\Students\StudentProgressQueryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class StudentNavigation extends Component
{
    public $activeTab = 'overview';

    public int $upcomingAssignmentsCount = 0;

    public int $unreadMessagesCount = 0;

    public int $unreadNotificationsCount = 0;

    public int $totalQuizzesCount = 0;

    protected StudentProgressQueryService $studentProgressQueryService;

    protected $listeners = ['studentTabChanged' => 'updateActiveTab'];

    public function boot(StudentProgressQueryService $studentProgressQueryService): void
    {
        $this->studentProgressQueryService = $studentProgressQueryService;
    }

    public function mount($activeTab = 'overview')
    {
        $this->activeTab = Request::input('activeTab', $activeTab);
        $this->refreshBadges();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('studentTabChanged', $tab);
    }

    public function studentTabChanged($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function refreshBadges(): void
    {
        $user = Auth::user();
        if (! $user) {
            $this->upcomingAssignmentsCount = 0;
            $this->unreadMessagesCount = 0;
            $this->unreadNotificationsCount = 0;
            $this->totalQuizzesCount = 0;

            return;
        }

        $student = $user->student;
        if (! $student) {
            $student = Student::withoutGlobalScopes()->where('user_id', $user->id)->first();
        }

        if (! $student) {
            $this->upcomingAssignmentsCount = 0;
            $this->totalQuizzesCount = 0;
        } else {
            $snapshot = $this->studentProgressQueryService->buildSnapshot($student);
            $this->upcomingAssignmentsCount = (int) ($snapshot['assignments']['upcoming'] ?? 0);
            $this->totalQuizzesCount = (int) ($snapshot['quizzes']['total'] ?? 0);
        }

        $this->unreadMessagesCount = MessageRecipient::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $this->unreadNotificationsCount = $user->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.navigations.student-navigation');
    }
}
