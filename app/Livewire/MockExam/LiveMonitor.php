<?php

namespace App\Livewire\MockExam;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubmission;
use App\MockExam\Services\MockExamMonitoringService;
use Livewire\Attributes\On;
use Livewire\Component;

class LiveMonitor extends Component
{
    public MockExam $mockExam;
    public $participants = [];
    public $stats = [];
    public int $pollInterval = 15000;

    public function mount(MockExam $mockExam): void
    {
        $this->mockExam = $mockExam;
        $this->refreshData();
    }

    #[On('refresh-monitor')]
    public function refreshData(): void
    {
        $service = app(MockExamMonitoringService::class);
        $this->participants = $service->getActiveParticipants($this->mockExam)->toArray();
        $this->stats = $service->getExamStats($this->mockExam);
    }

    public function kickParticipant(int $submissionId): void
    {
        $submission = MockExamSubmission::find($submissionId);

        if ($submission && $submission->mock_exam_id === $this->mockExam->id) {
            $submission->submit(auto: true, reason: 'instructor_removed');
            session()->forget("mock_exam_{$this->mockExam->id}_submission_id");

            $this->dispatch('participant-kicked', submissionId: $submissionId);
            $this->refreshData();
        }
    }

    public function adjustPollInterval(int $ms): void
    {
        $this->pollInterval = $ms;
    }

    public function render()
    {
        return view('livewire.mock-exam.live-monitor');
    }

    public function getListeners(): array
    {
        return [
            "echo-private:mock-exam.{$this->mockExam->id},ParticipantActivity" => 'refreshData',
        ];
    }
}
