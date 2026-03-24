<?php

namespace App\Livewire\Courses;

use App\Models\Lms\CourseEnrollment;
use App\Models\Lms\CourseSection;
use App\Services\Lms\CourseProgressService;
use Livewire\Component;

class SectionProgress extends Component
{
    public CourseSection $section;

    public CourseEnrollment $enrollment;

    public int $totalContents = 0;

    public int $completedContents = 0;

    public float $progressPercentage = 0;

    public bool $isComplete = false;

    protected $listeners = ['content-completed' => 'refreshProgress'];

    public function mount(CourseSection $section, CourseEnrollment $enrollment): void
    {
        $this->section = $section;
        $this->enrollment = $enrollment;
        $this->calculateProgress();
    }

    public function refreshProgress(): void
    {
        $this->calculateProgress();
    }

    protected function calculateProgress(): void
    {
        $progressService = app(CourseProgressService::class);
        $progress = $progressService->getSectionProgress($this->enrollment, $this->section);

        $this->totalContents = $progress['total'];
        $this->completedContents = $progress['completed'];
        $this->progressPercentage = $progress['percentage'];
        $this->isComplete = $progress['is_complete'];
    }

    public function getProgressColor(): string
    {
        if ($this->isComplete) {
            return 'bg-green-500';
        }

        if ($this->progressPercentage >= 50) {
            return 'bg-yellow-500';
        }

        if ($this->progressPercentage > 0) {
            return 'bg-blue-500';
        }

        return 'bg-gray-300';
    }

    public function getStatusIcon(): string
    {
        if ($this->isComplete) {
            return 'check-circle';
        }

        if ($this->progressPercentage > 0) {
            return 'clock';
        }

        return 'circle';
    }

    public function getStatusText(): string
    {
        if ($this->isComplete) {
            return 'Completed';
        }

        if ($this->progressPercentage > 0) {
            return "{$this->completedContents}/{$this->totalContents} completed";
        }

        return 'Not started';
    }

    public function render()
    {
        return view('livewire.courses.section-progress', [
            'progressColor' => $this->getProgressColor(),
            'statusIcon' => $this->getStatusIcon(),
            'statusText' => $this->getStatusText(),
        ]);
    }
}
