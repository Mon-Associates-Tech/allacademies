<?php

namespace App\Livewire\Assessment;


use Livewire\Component;

class SubjectSelectionComponent extends Component
{
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;

    public $subjects = [];
    public $topics = [];
    public $subtopics = [];
    public $questionCounts = [];

    protected SubjectSelectionService $selectionService;

    public function boot(SubjectSelectionService $selectionService)
    {
        $this->selectionService = $selectionService;
    }

    public function mount()
    {
        $this->loadSubjects();
    }

    public function loadSubjects()
    {
        $this->subjects = $this->selectionService->getAvailableSubjects();
    }

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];
        $this->questionCounts = [];

        if ($value) {
            $this->topics = $this->selectionService->getTopicsForSubject($value);
            $this->updateQuestionCounts();
        }
    }

    public function updatedSelectedTopic($value)
    {
        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = $this->selectionService->getSubtopicsForTopic($value);
        }

        $this->updateQuestionCounts();
    }

    public function updatedSelectedSubtopic()
    {
        $this->updateQuestionCounts();
    }

    protected function updateQuestionCounts()
    {
        if ($this->selectedSubject) {
            $this->questionCounts = $this->selectionService->getAvailableQuestionCounts(
                $this->selectedSubject,
                $this->selectedTopic,
                $this->selectedSubtopic
            );
        }
    }

    public function getSelectionHierarchy()
    {
        if (!$this->selectedSubject) {
            return [];
        }

        return $this->selectionService->getSelectionHierarchy(
            $this->selectedSubject,
            $this->selectedTopic,
            $this->selectedSubtopic
        );
    }

    public function render()
    {
        return view('livewire.assessments.subject-selection-component');
    }
}
