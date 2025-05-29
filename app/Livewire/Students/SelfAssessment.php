<?php

namespace App\Livewire\Students;

use Livewire\Component;
use App\Models\AcademicSubject as Subject;
use App\Models\AcademicTopic as Topic;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\Question;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SelfAssessment extends Component
{
    public $step = 'setup'; // setup, assessment, results

    // Setup phase
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $questionTypes = ['multiple_choice' => true, 'true_false' => true, 'essay' => false];
    public $questionCount = 10;
    public $difficulty = 'all'; // all, easy, medium, hard

    // Assessment phase
    public $currentQuestionIndex = 0;
    public $questions = [];
    public $responses = [];
    public $assessment = null;
    public $timeRemaining = null;
    public $timeLimitSeconds = 0;

    // Results phase
    public $result = null;

    public function mount()
    {
        $this->subjects = Subject::all();
    }

    public function updatedSelectedSubject()
    {
        if ($this->selectedSubject) {
            $this->topics = Topic::where('subject_id', $this->selectedSubject)->get();
        } else {
            $this->topics = collect();
        }
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
    }

    public function updatedSelectedTopic()
    {
        if ($this->selectedTopic) {
            $this->subtopics = Subtopic::where('topic_id', $this->selectedTopic)->get();
        } else {
            $this->subtopics = collect();
        }
        $this->selectedSubtopic = null;
    }

    public function startAssessment()
    {
        // Validate setup selections
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50',
        ]);

        // Ensure at least one question type is selected
        if (!$this->questionTypes['multiple_choice'] &&
            !$this->questionTypes['true_false'] &&
            !$this->questionTypes['essay']) {
            session()->flash('error', 'Please select at least one question type');
            return;
        }

        // Get applicable question types
        $questionTypes = [];
        if ($this->questionTypes['multiple_choice']) $questionTypes[] = 'multiple_choice';
        if ($this->questionTypes['true_false']) $questionTypes[] = 'true_false';
        if ($this->questionTypes['essay']) $questionTypes[] = 'essay';

        // Build question query
        $query = Question::query()->whereIn('question_type', $questionTypes);

        // Apply difficulty filter if specified
        if ($this->difficulty !== 'all') {
            $query->where('difficulty_level', $this->difficulty);
        }

        // Apply content filters (subject, topic, subtopic)
        if ($this->selectedSubtopic) {
            $query->where('subtopic_id', $this->selectedSubtopic);
        } elseif ($this->selectedTopic) {
            $query->whereHas('subtopic', function($q) {
                $q->where('topic_id', $this->selectedTopic);
            });
        } elseif ($this->selectedSubject) {
            $query->whereHas('subtopic.topic', function($q) {
                $q->where('subject_id', $this->selectedSubject);
            });
        }

        // Get questions
        $this->questions = $query->inRandomOrder()->take($this->questionCount)->get();

        if (count($this->questions) === 0) {
            session()->flash('error', 'No questions found matching your criteria');
            return;
        }

        // Initialize responses array
        foreach ($this->questions as $index => $question) {
            $this->responses[$index] = [
                'question_id' => $question->id,
                'response' => $question->question_type === 'essay' ? '' : null,
                'is_answered' => false
            ];
        }

        // Calculate time limit: 1 min for MC/TF, 5 min for essay
        $this->timeLimitSeconds = 0;
        foreach ($this->questions as $question) {
            if ($question->question_type === 'essay') {
                $this->timeLimitSeconds += 5 * 60; // 5 minutes per essay
            } else {
                $this->timeLimitSeconds += 1 * 60; // 1 minute per MC/TF
            }
        }

        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->id(),
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'title' => 'Self Assessment',
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Set timer
        $this->timeRemaining = $this->timeLimitSeconds;
        $this->dispatchBrowserEvent('start-timer', ['seconds' => $this->timeRemaining]);

        $this->step = 'assessment';
    }

    public function saveResponse($index, $response)
    {
        $question = $this->questions[$index];

        $this->responses[$index]['response'] = $response;
        $this->responses[$index]['is_answered'] = true;

        // For immediate grading of multiple choice and true/false
        if ($question->question_type !== 'essay') {
            $isCorrect = $response === $question->correct_answer;
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $question->points : 0;
            $this->responses[$index]['max_score'] = $question->points;
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        } elseif ($this->allQuestionsAnswered()) {
            $this->completeAssessment();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function jumpToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function allQuestionsAnswered()
    {
        foreach ($this->responses as $response) {
            if (!$response['is_answered']) {
                return false;
            }
        }
        return true;
    }

    public function completeAssessment()
    {
        $totalScore = 0;
        $maxScore = 0;
        $needsGrading = false;

        // Save responses and calculate score
        foreach ($this->responses as $index => $responseData) {
            $question = $this->questions[$index];
            $response = null;
            $score = 0;
            $isCorrect = false;

            if ($question->question_type === 'essay') {
                $response = $responseData['response'];
                $needsGrading = true;
                // Essay max score added to total, but actual score pending teacher grading
                $maxScore += $question->points;
            } else {
                $response = $responseData['response'];
                $isCorrect = $response === $question->correct_answer;
                $score = $isCorrect ? $question->points : 0;
                $totalScore += $score;
                $maxScore += $question->points;
            }

            // Save response
            AssessmentResponse::create([
                'assessment_id' => $this->assessment->id,
                'question_id' => $question->id,
                'response' => $response,
                'score' => $score,
                'max_score' => $question->points,
                'is_correct' => $question->question_type !== 'essay' ? $isCorrect : null,
            ]);
        }

        // Update assessment
        $this->assessment->end_time = now();
        $this->assessment->total_score = $totalScore;
        $this->assessment->max_score = $maxScore;
        $this->assessment->percentage_score = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $this->assessment->status = $needsGrading ? 'needs_grading' : 'completed';
        $this->assessment->save();

        // Prepare result data
        $this->result = [
            'totalScore' => $totalScore,
            'maxScore' => $maxScore,
            'percentageScore' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0,
            'needsGrading' => $needsGrading,
            'timeSpent' => Carbon::parse($this->assessment->start_time)->diffInMinutes($this->assessment->end_time),
            'byType' => [
                'multiple_choice' => $this->calculateScoreByType('multiple_choice'),
                'true_false' => $this->calculateScoreByType('true_false'),
                'essay' => $this->calculateScoreByType('essay'),
            ]
        ];

        $this->step = 'results';
    }

    private function calculateScoreByType($type)
    {
        $questions = $this->questions->where('question_type', $type);
        if ($questions->isEmpty()) {
            return null;
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($questions as $index => $question) {
            $response = $this->responses[$index];
            $maxScore += $question->points;

            if ($type === 'essay') {
                // Essays are pending grading
                continue;
            }

            if ($response['is_correct']) {
                $totalScore += $question->points;
            }
        }

        return [
            'score' => $totalScore,
            'maxScore' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0
        ];
    }

    public function startNewAssessment()
    {
        $this->reset([
            'step', 'questions', 'responses', 'currentQuestionIndex',
            'assessment', 'result', 'timeRemaining'
        ]);
        $this->step = 'setup';
    }

    public function render()
    {
        return view('livewire.students.self-assessment');
    }
}
