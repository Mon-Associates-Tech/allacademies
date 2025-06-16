<?php

namespace App\Livewire\Students;

use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\AcademicSubject as Subject;
use App\Models\AcademicTopic as Topic;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\Question;
use App\Models\Assessment;
use App\Models\AssessmentResponse;

class SelfAssessment extends Component
{
    public $step = 'setup'; // setup, assessment, results

    // Setup phase
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $questionTypes = ['multiple_choice_question' => true, 'true_or_false_question' => true, 'essay_question' => false];
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
    public $subjects = [];

    public function mount()
    {
        $this->subjects = Subject::whereHas('academicLevel', static function ($query) {
            $query->whereHas('students', function ($subQuery) {
                $subQuery->where('id', auth()->user()->student->id);
            });
        })->with('academicLevel')->get();

        // Log student accessing self-assessment
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_self_assessment',
                'page' => 'self-assessment'
            ])
            ->log('Student accessed self-assessment page');
    }

    public function getRecentAssessmentsProperty()
    {
       return Assessment::where('student_id', auth()->user()->student->id)
            ->with(['subject', 'topic'])
            ->latest()
            ->paginate(10);
    }

    public function updatedSelectedSubject()
    {
        $this->topics = $this->selectedSubject
            ? Topic::where('academic_subject_id', $this->selectedSubject)->get()
            : collect();

        // Reset dependent fields
        Log::info('Selected topic: ' . $this->selectedSubject);
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->subtopics = collect();

        // Log subject selection
        if ($this->selectedSubject) {
            $subject = Subject::find($this->selectedSubject);
            activity()->performedOn(auth()->user()->student)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'selected_subject_for_assessment',
                    'subject_id' => $this->selectedSubject,
                    'subject_name' => $subject->name ?? 'Unknown'
                ])
                ->log('Student selected subject for self-assessment');
        }
    }

    public function updatedSelectedTopic()
    {
        $this->subtopics = $this->selectedTopic
            ? Subtopic::where('academic_topic_id', $this->selectedTopic)->get()
            : collect();

        // Reset dependent field
        $this->selectedSubtopic = null;

        // Log topic selection
        if ($this->selectedTopic) {
            $topic = Topic::find($this->selectedTopic);
            activity()->performedOn(auth()->user()->student)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'selected_topic_for_assessment',
                    'topic_id' => $this->selectedTopic,
                    'topic_name' => $topic->name ?? 'Unknown'
                ])
                ->log('Student selected topic for self-assessment');
        }
    }

    public function startAssessment()
    {
        $this->reset(['questions', 'responses', 'assessment']);

        // Validate setup selections
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50',
        ]);

        // Ensure at least one question type is selected
        if (!$this->questionTypes['multiple_choice_question'] &&
            !$this->questionTypes['true_or_false_question'] &&
            !$this->questionTypes['essay_question']) {
            session()->flash('error', 'Please select at least one question type');
            return;
        }

        // Build query per question type
        $allQuestions = collect();

        // Multiple Choice Questions
        if ($this->questionTypes['multiple_choice_question']) {
            $query = MultipleChoiceQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            $allQuestions = $allQuestions->merge($query->get()->map(fn($q) => ['type' => 'multiple_choice_question', 'model' => $q]));
        }

        // True/False Questions
        if ($this->questionTypes['true_or_false_question']) {
            $query = TrueOrFalseQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            $allQuestions = $allQuestions->merge($query->get()->map(fn($q) => ['type' => 'true_or_false_question', 'model' => $q]));
        }

        // Essay Questions
        if ($this->questionTypes['essay_question']) {
            $query = EssayQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            $allQuestions = $allQuestions->merge($query->get()->map(fn($q) => ['type' => 'essay_question', 'model' => $q]));
        }

        // Check if any questions were found
        if ($allQuestions->isEmpty()) {
            session()->flash('error', 'No questions found matching your criteria');
            return;
        }

        // Shuffle and limit
        $randomQuestions = $allQuestions->shuffle()->take($this->questionCount);

        // Persist these questions into the `questions` table
        $this->questions = [];

        foreach ($randomQuestions as $item) {
            $questionModel = $item['model'];
            $question = Question::create([
                'questionable_type' => get_class($questionModel),
                'questionable_id' => $questionModel->id,
                'subtopic_id' => $questionModel->subtopic_id,
                'topic_id' => $questionModel->academic_topic_id,
                'difficulty_level' => $questionModel->difficulty_level,
                'points' => $questionModel->score,
                'user_id' => auth()->user()->id,
            ]);

            $this->questions[] = $question;
        }

        // Initialize responses array using actual question IDs
        foreach ($this->questions as $index => $question) {
            $this->responses[$index] = [
                'question_id' => $question->id,
                'response' => $this->getTypeFromClassName(class_basename($question->questionable_type)) === 'essay_question' ? '' : null,
                'is_answered' => false
            ];
        }

        // Calculate time limit
        $this->timeLimitSeconds = 0;
        foreach ($this->questions as $question) {
            if ($this->getTypeFromClassName(class_basename($question->questionable_type)) === 'essay_question') {
                $this->timeLimitSeconds += 5 * 60; // 5 minutes
            } else {
                $this->timeLimitSeconds += 1 * 60; // 1 minute
            }
        }

        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'title' => "Self-Assessment for {$this->selectedSubject} - {$this->selectedTopic}",
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Set timer
        $this->timeRemaining = $this->timeLimitSeconds;
        $this->dispatch('start-timer', ['seconds' => $this->timeRemaining]);

        // Log assessment start
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'started_self_assessment',
                'question_count' => $this->questionCount,
                'difficulty' => $this->difficulty,
                'question_types' => array_keys(array_filter($this->questionTypes)),
                'subject_id' => $this->selectedSubject,
                'topic_id' => $this->selectedTopic,
                'subtopic_id' => $this->selectedSubtopic
            ])
            ->log('Student started self-assessment');

        $this->step = 'assessment';
    }



    public function saveResponse($index, $response)
    {
        $question = $this->questions[$index];


        $questionType = $this->getTypeFromClassName(class_basename($question->questionable_type));
        if (!$questionType) {
            session()->flash('error', "Unknown question type.");
            return;
        }

        $this->responses[$index]['response'] = $response;
        $this->responses[$index]['is_answered'] = true;

        // Only calculate correctness for non-essay questions
        if ($questionType !== 'essay_question') {
            $isCorrect = false;

            // Handle correct answer logic based on question model
            if (isset($question->questionable?->answer)) {
                $isCorrect = $response === $question->questionable->answer;
            } elseif (isset($question->questionable?->answer)) {
                $isCorrect = $response === $question->questionable->answer;
            }

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

    /**
     * @throws \JsonException
     */
    public function completeAssessment(): void
    {
        $totalScore = 0;
        $maxScore = 0;
        $needsGrading = false;

        // Score breakdown by question type
        $scoreBreakdown = [
            'multiple_choice' => ['score' => 0, 'max_score' => 10],
            'true_false'      => ['score' => 0, 'max_score' => 10],
            'essay'           => ['score' => 0, 'max_score' => 10],
        ];

        // Build full assessment data
        $responseData = [
            'assessment_id' => $this->assessment->id,
            'started_at' => $this->assessment->start_time,
            'ended_at' => now(),
            'questions' => [],
        ];

        foreach ($this->responses as $index => $res) {
            $question = $this->questions[$index];
            $questionType = $this->getTypeFromClassName(class_basename($question->questionable_type));

            $item = $this->buildQuestionResponseData($question, $res);
            $responseData['questions'][] = $item;

            // Map to breakdown key
            $typeKey = match($questionType) {
                'multiple_choice_question' => 'multiple_choice',
                'true_or_false_question'   => 'true_false',
                'essay_question'           => 'essay',
                default => null,
            };

            if ($typeKey && isset($scoreBreakdown[$typeKey])) {
                if ($questionType === 'essay_question') {
                    $scoreBreakdown[$typeKey]['max_score'] += $question->points;
                    $needsGrading = true;
                } else {
                    $scoreBreakdown[$typeKey]['score'] += $item['score'];
                    $scoreBreakdown[$typeKey]['max_score'] += $item['max_score'];
                }
            }

            // Aggregate total score and max score
            if ($questionType !== 'essay_question') {
                $totalScore += $item['score'];
                $maxScore += $item['max_score'];
            } else {
                $maxScore += $question->points;
            }
        }

        // Calculate percentages
        foreach ($scoreBreakdown as $key => $data) {
            $scoreBreakdown[$key]['percentage'] = $data['max_score'] > 0
                ? round(($data['score'] / $data['max_score']) * 100)
                : 0;
        }

        // Add breakdown to response
        $responseData['byType'] = $scoreBreakdown;

        // Calculate time spent in minutes
        $startTime = \Carbon\Carbon::parse($this->assessment->start_time);
        $endTime = \Carbon\Carbon::now();
        $timeSpentSeconds = $endTime->diffInSeconds($startTime);

        $responseData['time_spent'] = round($timeSpentSeconds / 60, 2); // in minutes


        // Add totals
        $responseData['total_score'] = $totalScore;
        $responseData['max_score'] = $maxScore;
        $responseData['percentage_score'] = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0;
        $responseData['needs_grading'] = $needsGrading;

        // Save to AssessmentResponse model
        AssessmentResponse::create([
            'assessment_id' => $this->assessment->id,
            'data' => json_encode($responseData, JSON_THROW_ON_ERROR),
        ]);

        // Update assessment status
        $this->assessment->end_time = now();
        $this->assessment->total_score = $totalScore;
//        $this->assessment->max_score = $maxScore;
        $this->assessment->percentage_score = $responseData['percentage_score'];
        $this->assessment->status = $needsGrading ? 'needs_grading' : 'completed';
        $this->assessment->save();

        // Set result for UI
        $this->result = $responseData;

        // Log assessment completion
        activity()->performedOn($this->assessment)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'completed_self_assessment',
                'assessment_id' => $this->assessment->id,
                'score' => $this->assessment->total_score,
                'max_score' => $maxScore,
                'percentage' => $this->assessment->percentage_score,
                'question_count' => count($this->questions)
            ])
            ->log('Student completed self-assessment');

        $this->step = 'results';
    }



    private function calculateScoreByType($type)
    {
        // Map short type to full class name
        $classMap = [
            'multiple_choice_question' => 'MultipleChoiceQuestion',
            'true_or_false_question' => 'TrueOrFalseQuestion',
            'essay_question' => 'EssayQuestion',
        ];

        $className = $classMap[$type] ?? null;

        if (!$className) {
            return null;
        }

        // Filter questions by questionable_type class name
        $questions = $this->questions->filter(fn($q) => $this->getQuestionType($q) === $className);


        if ($questions->isEmpty()) {
            return null;
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($questions as $index => $question) {
            $response = $this->responses[$index];
            $maxScore += $question->points;

            if ($type === 'essay_question') continue;

            if ($response['is_correct']) {
                $totalScore += $question->points;
            }
        }

        return [
            'score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0
        ];
    }
    private function getQuestionType($question)
    {
        return strtolower(class_basename($question->questionable_type));
    }

    private function getTypeFromClassName($className)
    {
        $typeMap = [
            'MultipleChoiceQuestion' => 'multiple_choice_question',
            'TrueOrFalseQuestion' => 'true_false_question',
            'EssayQuestion' => 'essay_question',
        ];

        return $typeMap[$className] ?? null;
    }



    public function startNewAssessment()
    {
        $this->reset([
            'step', 'questions', 'responses', 'currentQuestionIndex',
            'assessment', 'result', 'timeRemaining'
        ]);
        $this->step = 'setup';
    }

    private function applyContentFilters($query)
    {
        if ($this->selectedSubtopic) {
            // Only include questions with this specific subtopic
            $query->where('academic_subtopic_id', $this->selectedSubtopic);
        } elseif ($this->selectedTopic) {
            // Include:
            // 1. Questions linked via subtopic.topic_id = selectedTopic
            // 2. Questions directly linked to topic_id = selectedTopic
            $query->where(function ($q) {
                $q->whereHas('subtopic', function ($s) {
                    $s->where('academic_topic_id', $this->selectedTopic);
                })->orWhere('academic_topic_id', $this->selectedTopic);
            });
        } elseif ($this->selectedSubject) {
            // Include:
            // 1. Questions linked via subtopic → topic → subject
            // 2. Questions directly linked to topic → subject
            $query->where(function ($q) {
                $q->whereHas('subtopic.topic', function ($t) {
                    $t->where('academic_subject_id', $this->selectedSubject);
                })->orWhereHas('topic', function ($t) {
                    $t->where('academic_subject_id', $this->selectedSubject);
                });
            });
        }
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

private function buildQuestionResponseData($question, $response)
    {
        $questionType = $this->getTypeFromClassName(class_basename($question->questionable_type));
        $model = $question->questionable;
        $correctAnswer = null;
        $userAnswer = $response['response'];
        $isCorrect = false;
        $score = 0;
        $maxScore = $question->points;

        // Get correct answer and compare based on question type
        if ($questionType === 'multiple_choice_question') {
            // For multiple choice, compare the letter (A, B, C, D, E)
            $correctAnswer = strtoupper($model->answer);
            $userAnswer = strtoupper($userAnswer);
            $isCorrect = $userAnswer === $correctAnswer;
            $score = $isCorrect ? $maxScore : 0;
        }
        elseif ($questionType === 'true_or_false_question') {
            // For true/false, normalize to boolean string comparison
            $correctAnswer = strtolower($model->answer);
            $userAnswer = strtolower($userAnswer);
            $isCorrect = $userAnswer === $correctAnswer;
            $score = $isCorrect ? $maxScore : 0;
        }
        elseif ($questionType === 'essay_question') {
            // For essays, store response but don't grade
            $correctAnswer = $model->answer?->down ?? null;
            $score = null; // Pending manual grading
            $isCorrect = null;
        }

        // Build question text and options
        $questionText = $model->question->down ?? '';
        $options = [];

        if ($questionType === 'multiple_choice_question') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                $optionKey = 'option_' . $letter;
                if ($model->{$optionKey}?->down) {
                    $options[] = [
                        'label' => strtoupper($letter),
                        'value' => $model->{$optionKey}->down,
                        'is_correct' => strtoupper($letter) === $correctAnswer
                    ];
                }
            }
        }

        return [
            'question_id' => $question->id,
            'question_type' => $questionType,
            'difficulty_level' => $question->difficulty_level,
            'question' => $questionText,
            'options' => $options,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'score' => $score,
            'max_score' => $maxScore,
            'is_correct' => $isCorrect,
            'needs_grading' => $questionType === 'essay_question'
        ];
    }

public function render()
{
    return view('livewire.students.self-assessment', [
        'subjects' => $this->subjects,
        'topics' => $this->selectedSubject
            ? Topic::where('academic_subject_id', $this->selectedSubject)->get()
            : collect(),
        'subtopics' => $this->selectedTopic
            ? Subtopic::where('academic_topic_id', $this->selectedTopic)->get()
            : collect(),
    ]);
}
}
