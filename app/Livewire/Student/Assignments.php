<?php

namespace App\Livewire\Student;

use App\Models\Assessment;
use App\Models\Assignment;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Assignments extends Component
{
    use StartsAssessment;

    public $step = 'setup'; // setup, assessment, results

    // Setup phase
    public $selectedAssignment = null;

    // Assessment phase
    public $currentQuestionIndex = 0;

    public $questions = [];

    public $responses = [];

    public $assessment = null;

    public $timeRemaining = null;

    public $timeLimitSeconds = 0;

    public $startTime = null;

    // Results phase
    public $assessmentResult = null;

    public $availableAssignments = [];

    protected $rules = [
        'selectedAssignment' => 'required',
    ];

    public function mount()
    {
        $student = auth()->user()->student;

        if (! $student) {
            $this->availableAssignments = collect();

            return;
        }

        $this->loadAvailableAssignments();

        // Log student accessing assignment practice
        activity()->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_assignment_practice',
                'page' => 'assignment-practice',
            ])
            ->log('Student accessed assignment practice page');
    }

    private function loadAvailableAssignments(): void
    {
        $this->availableAssignments = $this->getAvailableAssignments();
    }

    public function startAssessment(): void
    {
        $this->validate();

        $assignment = Assignment::find($this->selectedAssignment);
        if (! $assignment) {
            session()->flash('error', 'Assignment not found.');

            return;
        }

        // Check if student can start this assignment
        if (! $this->canStartAssignment($assignment)) {
            session()->flash('error', 'You are not eligible to start this assignment or it is not available.');

            return;
        }

        $this->initializeAssessmentFromAssignment($assignment);
    }

    private function initializeAssessmentFromAssignment(Assignment $assignment): void
    {
        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $assignment->academic_subject_id,
            'title' => "Assignment Practice: {$assignment->title}",
            'type' => Assessment::TYPE_ASSIGNMENT,
            'start_time' => now(),
            'status' => Assessment::STATUS_IN_PROGRESS,
            'assignment_id' => $assignment->id,
            'time_limit_minutes' => $assignment->duration_in_minutes,
        ]);

        // Set time limit
        $this->setupTimeLimit($assignment->duration_in_minutes);

        // Generate questions from assignment
        $this->generateQuestionsFromAssignment($assignment);

        $this->finalizeAssessmentStart();
    }

    private function generateQuestionsFromAssignment(Assignment $assignment): void
    {
        // Generate questions based on assignment configuration
        $generatedQuestions = $assignment->generateQuestionsForStudent(auth()->user()->student->id);

        $this->questions = $generatedQuestions->toArray();
        $this->responses = array_fill(0, count($this->questions), []);

        // Store questions data in assessment
        $this->assessment->setQuestionsData($this->questions);

        Log::info('Generated questions from assignment', [
            'assignment_id' => $assignment->id,
            'assessment_id' => $this->assessment->id,
            'question_count' => count($this->questions),
        ]);
    }

    private function setupTimeLimit($minutes): void
    {
        if ($minutes) {
            $this->timeLimitSeconds = $minutes * 60;
            $this->timeRemaining = $this->timeLimitSeconds;
        } else {
            $this->timeLimitSeconds = 0;
            $this->timeRemaining = null;
        }
    }

    private function finalizeAssessmentStart(): void
    {
        $this->startTime = now();
        $this->step = 'assessment';
        $this->currentQuestionIndex = 0;

        // Log assessment start
        activity()->performedOn($this->assessment)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'started_assignment_practice',
                'assessment_id' => $this->assessment->id,
                'assignment_id' => $this->selectedAssignment,
                'question_count' => count($this->questions),
                'time_limit_minutes' => $this->assessment->time_limit_minutes,
            ])
            ->log('Student started assignment practice');
    }

    public function render()
    {
        return view('livewire.students.assignments');
    }
}
