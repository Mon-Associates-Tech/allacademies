<?php

namespace App\Livewire\Teachers;

use App\Models\Assessment;
use App\Livewire\Assessment\AssessmentService;
use Livewire\Component;
use Livewire\WithPagination;

class EssayGrader extends Component
{
    use WithPagination;

    public $selectedAssessment = null;
    public $essayQuestions = [];
    public $currentQuestionIndex = 0;
    public $points = null;
    public $feedback = '';

    protected $rules = [
        'points' => 'required|numeric|min:0',
        'feedback' => 'nullable|string|max:1000',
    ];

    public function selectAssessment($assessmentId)
    {
        $this->selectedAssessment = Assessment::with(['student.user', 'subject', 'assessmentResponse'])
            ->findOrFail($assessmentId);

        if ($this->selectedAssessment->assessmentResponse) {
            $this->essayQuestions = $this->selectedAssessment->assessmentResponse->getEssayQuestionsForGrading();
            $this->currentQuestionIndex = 0;

            if (!empty($this->essayQuestions)) {
                $this->points = $this->essayQuestions[0]['points_possible'];
            }
        }
    }

    public function gradeCurrentQuestion()
    {
        $this->validate();

        if (!empty($this->essayQuestions) && isset($this->essayQuestions[$this->currentQuestionIndex])) {
            $questionData = $this->essayQuestions[$this->currentQuestionIndex];

            $assessmentService = app(AssessmentService::class);
            $assessmentService->gradeEssayQuestion(
                $this->selectedAssessment->assessmentResponse,
                $questionData['index'],
                $this->points,
                $this->feedback,
                auth()->user()->teacher
            );

            session()->flash('success', 'Question graded successfully!');

            // Remove graded question from array
            unset($this->essayQuestions[$this->currentQuestionIndex]);
            $this->essayQuestions = array_values($this->essayQuestions);

            // Reset form
            $this->feedback = '';

            // Set up next question or finish
            if (!empty($this->essayQuestions)) {
                $this->points = $this->essayQuestions[0]['points_possible'];
            } else {
                $this->selectedAssessment = null;
                $this->currentQuestionIndex = 0;
                $this->points = null;
            }
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->essayQuestions) - 1) {
            $this->currentQuestionIndex++;
            $this->points = $this->essayQuestions[$this->currentQuestionIndex]['points_possible'];
            $this->feedback = '';
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->points = $this->essayQuestions[$this->currentQuestionIndex]['points_possible'];
            $this->feedback = '';
        }
    }

    public function render()
    {
        $pendingAssessments = Assessment::with(['student.user', 'subject', 'assessmentResponse'])
            ->where('status', Assessment::STATUS_PENDING_REVIEW)
            ->whereHas('student.teachers', function ($query) {
                $query->where('teacher_id', auth()->user()->teacher->id);
            })
            ->paginate(10);

        return view('livewire.teachers.essay-grader', [
            'pendingAssessments' => $pendingAssessments,
        ]);
    }
}
