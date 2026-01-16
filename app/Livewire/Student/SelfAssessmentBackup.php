<?php

namespace App\Livewire\Student;

use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SelfAssessmentBackup extends Component
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

    public $topics = [];

    public $subtopics = [];

    public function mount()
    {
        $student = auth()->user()->student;

        if (! $student) {
            $this->subjects = collect();

            return;
        }

        // Get subjects that the student has access to
        $this->subjects = collect();

        // Get subjects from student's academic level
        if ($student->academicLevel) {
            $levelSubjects = $student->academicLevel->academicSubjects()
                ->with('academicLevel')
                ->get();
            $this->subjects = $this->subjects->merge($levelSubjects);
        }

        // Get individual subjects assigned to the student
        $individualSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->with('academicLevel')
            ->get();

        // Merge individual subjects, removing duplicates
        foreach ($individualSubjects as $subject) {
            if (! $this->subjects->contains('id', $subject->id)) {
                $this->subjects->push($subject);
            }
        }

        // Remove subjects that are individually marked as inactive
        $removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', false)
            ->pluck('academic_subjects.id');

        $this->subjects = $this->subjects->reject(function ($subject) use ($removedSubjects) {
            return $removedSubjects->contains($subject->id);
        });

        if (count($this->subjects) === 0) {
            $this->subjects = Subject::get();
        }

        // Log student accessing self-assessment
        activity()->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_self_assessment',
                'page' => 'self-assessment',
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
        Log::info('Selected topic: '.$this->selectedSubject);
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
                    'subject_name' => $subject->name ?? 'Unknown',
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
                    'topic_name' => $topic->name ?? 'Unknown',
                ])
                ->log('Student selected topic for self-assessment');
        }
    }

    public function startAssessment(): void
    {
        Log::info('Starting self-assessment with parameters:', [
            'subject' => $this->selectedSubject,
            'topic' => $this->selectedTopic,
            'subtopic' => $this->selectedSubtopic,
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
            'question_types' => $this->questionTypes,
        ]);
        $this->reset(['questions', 'responses', 'assessment']);

        // Validate setup selections
        $this->validate([
            'selectedSubject' => 'required',
            'questionCount' => 'required|integer|min:1|max:50',
        ]);

        Log::info('Debug: After validation');

        // Ensure at least one question type is selected
        if (! $this->questionTypes['multiple_choice_question'] &&
            ! $this->questionTypes['true_or_false_question'] &&
            ! $this->questionTypes['essay_question']) {
            session()->flash('error', 'Please select at least one question type');

            return;
        }

        Log::info('Debug: Question types validated');

        // Build query per question type
        $allQuestions = collect();

        // Multiple Choice Questions
        if ($this->questionTypes['multiple_choice_question']) {
            $query = MultipleChoiceQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            Log::info('Debug: Multiple choice questions found: '.$query->get()->count());

            $allQuestions = $allQuestions->merge($query->get()->map(fn ($q) => ['type' => 'multiple_choice_question', 'model' => $q]));
        }

        // True/False Questions
        if ($this->questionTypes['true_or_false_question']) {
            $query = TrueOrFalseQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            Log::info('Debug: True/False questions found: '.$query->get()->count());

            $allQuestions = $allQuestions->merge($query->get()->map(fn ($q) => ['type' => 'true_or_false_question', 'model' => $q]));
        }

        // Essay Questions
        if ($this->questionTypes['essay_question']) {
            $query = EssayQuestion::query();
            if ($this->difficulty !== 'all') {
                $query->where('difficulty_level', $this->difficulty);
            }
            $this->applyContentFilters($query);
            Log::info('Debug: Essay questions found: '.$query->get()->count());
            $allQuestions = $allQuestions->merge($query->get()->map(fn ($q) => ['type' => 'essay_question', 'model' => $q]));
        }
        Log::info('Debug: Total questions found: '.$allQuestions->count());

        // Check if any questions were found
        if ($allQuestions->isEmpty()) {
            Log::info('No questions found matching the criteria');
            session()->flash('error', 'No questions found matching your criteria');

            return;
        }
        Log::info('Debug: Questions collected, proceeding to shuffle and limit');

        // Shuffle and limit
        $randomQuestions = $allQuestions->shuffle()->take($this->questionCount);

        // Store questions with their type and model information
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

            // Store the question with type and model information for the Blade template
            $this->questions[] = [
                'id' => $question->id,
                'type' => $item['type'],
                'model' => $questionModel,
                'question_record' => $question, // Store the Question record for later use
                'points' => $question->points,
                'difficulty_level' => $question->difficulty_level,
            ];
        }

        Log::info('Debug: Questions persisted, initializing responses');

        // Initialize responses array using actual question IDs
        foreach ($this->questions as $index => $questionData) {
            $this->responses[$index] = [
                'question_id' => $questionData['id'],
                'response' => $questionData['type'] === 'essay_question' ? '' : null,
                'is_answered' => false,
            ];
        }

        // Calculate time limit
        $this->timeLimitSeconds = 0;
        foreach ($this->questions as $questionData) {
            if ($questionData['type'] === 'essay_question') {
                $this->timeLimitSeconds += 5 * 60; // 5 minutes
            } else {
                $this->timeLimitSeconds += 1 * 60; // 1 minute
            }
        }

        Log::info('Debug: Time limit set to '.$this->timeLimitSeconds.' seconds');

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

        Log::info('Debug: Assessment created with ID '.$this->assessment->id);

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
                'subtopic_id' => $this->selectedSubtopic,
            ])
            ->log('Student started self-assessment');

        $this->step = 'assessment';
        Log::info('Step after: '.$this->step);
    }

    private function applyContentFilters($query): void
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

    private function getTypeFromClassName($className)
    {
        $typeMap = [
            'MultipleChoiceQuestion' => 'multiple_choice_question',
            'TrueOrFalseQuestion' => 'true_or_false_question', // Fixed: was 'true_false_question'
            'EssayQuestion' => 'essay_question',
        ];

        return $typeMap[$className] ?? null;
    }

    public function saveResponse($index, $response)
    {
        Log::info('saveResponse called', [
            'index' => $index,
            'response' => $response,
            'current_response_data' => $this->responses[$index] ?? 'NOT_SET',
        ]);

        if (! isset($this->responses[$index])) {
            Log::error('Response index not found', ['index' => $index]);

            return;
        }

        $questionData = $this->questions[$index];
        $questionType = $questionData['type'];

        if (! $questionType) {
            session()->flash('error', 'Unknown question type.');

            return;
        }

        $this->responses[$index]['response'] = $response;
        $this->responses[$index]['is_answered'] = true;

        // Calculate correctness and score for auto-gradable questions
        if ($questionType === 'multiple_choice_question') {
            $questionModel = $questionData['model'];
            $isCorrect = strtoupper($response) === strtoupper($questionModel->answer);
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $questionData['points'] : 0;

            Log::info('Multiple choice response processed', [
                'user_answer' => $response,
                'correct_answer' => $questionModel->answer,
                'is_correct' => $isCorrect,
                'score' => $this->responses[$index]['score'],
            ]);
        } elseif ($questionType === 'true_or_false_question') {
            $questionModel = $questionData['model'];
            // Convert response to boolean for comparison
            $responseBoolean = $response === 'true' || $response === '1' || $response === 1 || $response === true;
            $isCorrect = $responseBoolean === (bool) $questionModel->answer;
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $questionData['points'] : 0;

            Log::info('True/false response processed', [
                'user_answer' => $response,
                'correct_answer' => $questionModel->answer,
                'is_correct' => $isCorrect,
                'score' => $this->responses[$index]['score'],
            ]);
        } else {
            // Essay questions don't get auto-graded
            $this->responses[$index]['is_correct'] = null;
            $this->responses[$index]['score'] = 0; // Will be manually graded later

            Log::info('Essay response processed', [
                'user_answer' => $response,
            ]);
        }

        Log::info('Response after processing', [
            'index' => $index,
            'response_data' => $this->responses[$index],
        ]);
    }

    public function completeAssessment(): void
    {
        // Add debugging
        Log::info('Starting completeAssessment', [
            'responses_count' => count($this->responses),
            'questions_count' => count($this->questions),
            'responses' => $this->responses,
        ]);

        // Calculate results
        $totalScore = 0;
        $maxScore = 0;
        $correctCount = 0;
        $totalAnswered = 0;

        $byType = [
            'multiple_choice' => ['correct_count' => 0, 'total_count' => 0, 'score' => 0, 'max_score' => 0],
            'true_false' => ['correct_count' => 0, 'total_count' => 0, 'score' => 0, 'max_score' => 0],
            'essay' => ['correct_count' => 0, 'total_count' => 0, 'score' => 0, 'max_score' => 0],
        ];

        // Prepare all responses data for single storage
        $allResponsesData = [
            'questions' => [],
            'assessment_metadata' => [
                'assessment_id' => $this->assessment->id,
                'student_id' => auth()->user()->student->id,
                'completed_at' => now()->toISOString(),
                'time_taken_seconds' => $this->timeLimitSeconds - ($this->timeRemaining ?? 0),
            ],
        ];

        foreach ($this->responses as $index => $response) {
            $questionData = $this->questions[$index];
            $questionType = $questionData['type'];

            $maxScore += $questionData['points'];

            Log::info("Processing response {$index}", [
                'response' => $response,
                'question_type' => $questionType,
                'is_answered' => $response['is_answered'] ?? false,
            ]);

            // Check if question was answered - handle both 'response' and 'answer' fields
            $isAnswered = $response['is_answered'] ?? false;
            $userAnswer = $response['response'] ?? $response['answer'] ?? null;
            $hasResponse = ! empty($userAnswer) || $userAnswer === '0' || $userAnswer === 0;

            if ($isAnswered || $hasResponse) {
                $totalAnswered++;

                // If we have an answer but no score calculated, calculate it now
                if ($hasResponse && ! isset($response['score']) && $userAnswer) {
                    $this->calculateScore($index, $userAnswer, $questionData, $questionType);
                    $response = $this->responses[$index]; // Get updated response
                }

                // Add to total score if correct
                if (isset($response['score'])) {
                    $totalScore += $response['score'];
                }

                // Count correct answers
                if (isset($response['is_correct']) && $response['is_correct']) {
                    $correctCount++;
                }

                // Add this question's response to the consolidated data
                $allResponsesData['questions'][] = [
                    'question_id' => $response['question_id'],
                    'question_type' => $questionType,
                    'question_text' => $questionData['model']->question ?? '',
                    'user_answer' => $userAnswer,
                    'correct_answer' => $questionData['model']->answer ?? null,
                    'is_correct' => $response['is_correct'] ?? null,
                    'score' => $response['score'] ?? 0,
                    'max_score' => $questionData['points'],
                    'difficulty_level' => $questionData['difficulty_level'],
                    'answered_at' => now()->toISOString(),
                ];
            }

            // Track by question type - count ALL questions, not just answered ones
            $typeKey = $questionType === 'multiple_choice_question' ? 'multiple_choice' :
                ($questionType === 'true_or_false_question' ? 'true_false' : 'essay');

            $byType[$typeKey]['total_count']++;
            $byType[$typeKey]['max_score'] += $questionData['points'];

            // Only add to answered stats if actually answered
            if ($isAnswered || $hasResponse) {
                if (isset($response['score'])) {
                    $byType[$typeKey]['score'] += $response['score'];
                }

                if (isset($response['is_correct']) && $response['is_correct']) {
                    $byType[$typeKey]['correct_count']++;
                }
            }
        }

        Log::info('Assessment calculation results', [
            'total_answered' => $totalAnswered,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'by_type' => $byType,
            'all_responses_data' => $allResponsesData,
        ]);

        // Save all assessment responses in a single record
        if (! empty($allResponsesData['questions'])) {
            try {
                $assessmentResponse = AssessmentResponse::create([
                    'assessment_id' => $this->assessment->id,
                    'data' => $allResponsesData,
                ]);
                Log::info('AssessmentResponse created successfully', ['id' => $assessmentResponse->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create AssessmentResponse', [
                    'error' => $e->getMessage(),
                    'data' => $allResponsesData,
                ]);
            }
        } else {
            Log::warning('No questions data to save in AssessmentResponse');
        }

        // Update assessment
        $this->assessment->update([
            'end_time' => now(),
            'status' => 'completed',
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage_score' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
        ]);

        // Prepare result data
        $this->result = [
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage_score' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'correct_count' => $correctCount,
            'total_questions' => count($this->questions),
            'answered_count' => $totalAnswered,
            'byType' => $byType,
        ];

        // Log assessment completion
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'completed_self_assessment',
                'assessment_id' => $this->assessment->id,
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'percentage_score' => $this->result['percentage_score'],
                'questions_answered' => $totalAnswered,
                'total_questions' => count($this->questions),
            ])
            ->log('Student completed self-assessment');

        $this->step = 'results';
    }

    private function calculateScore($index, $userAnswer, $questionData, $questionType)
    {
        if ($questionType === 'multiple_choice_question') {
            $questionModel = $questionData['model'];
            $isCorrect = strtoupper($userAnswer) === strtoupper($questionModel->answer);
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $questionData['points'] : 0;
            $this->responses[$index]['response'] = $userAnswer;
            $this->responses[$index]['is_answered'] = true;
        } elseif ($questionType === 'true_or_false_question') {
            $questionModel = $questionData['model'];
            $responseBoolean = $userAnswer === 'true' || $userAnswer === '1' || $userAnswer === 1 || $userAnswer === true;
            $isCorrect = $responseBoolean === (bool) $questionModel->answer;
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $questionData['points'] : 0;
            $this->responses[$index]['response'] = $userAnswer;
            $this->responses[$index]['is_answered'] = true;
        } else {
            // Essay questions don't get auto-graded
            $this->responses[$index]['is_correct'] = null;
            $this->responses[$index]['score'] = 0;
            $this->responses[$index]['response'] = $userAnswer;
            $this->responses[$index]['is_answered'] = true;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function retakeAssessment(): void
    {
        // Log the retake action
        activity()->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'retake_assessment_requested',
                'previous_assessment_id' => $this->assessment?->id,
                'previous_score' => $this->result['percentage'] ?? null,
            ])
            ->log('Student requested to retake assessment');

        // Reset all assessment-related data
        $this->reset([
            'step', 'questions', 'responses', 'currentQuestionIndex',
            'assessment', 'result', 'timeRemaining',
        ]);

        // Set step back to setup to allow configuration changes
        $this->step = 'setup';

        // Flash a message to inform the user
        session()->flash('info', 'Assessment reset. You can now configure and start a new assessment.');
    }

    public function jumpToQuestion($index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function startNewAssessment(): void
    {
        $this->reset([
            'step', 'questions', 'responses', 'currentQuestionIndex',
            'assessment', 'result', 'timeRemaining',
        ]);
        $this->step = 'setup';
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
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

    private function buildQuestionResponseData($question, $response): array
    {
        $questionType = $this->getTypeFromClassName(class_basename($question->questionable_type));
        $model = $question->questionable;
        $correctAnswer = $model->answer ?? null;
        $userAnswer = $response['response'];
        $isCorrect = false;
        $score = 0;
        $maxScore = $question->points;

        // Replace the correctness logic in buildQuestionResponseData with:
        $isCorrect = $this->isCorrectAnswer($questionType, $userAnswer, $correctAnswer);
        $score = $isCorrect ? $maxScore : 0;

        // Build question text and options
        $questionText = $model->question->down ?? '';
        $options = [];

        if ($questionType === 'multiple_choice_question') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                $optionKey = 'option_'.$letter;
                if ($model->{$optionKey}?->down) {
                    $options[] = [
                        'label' => strtoupper($letter),
                        'value' => $model->{$optionKey}->down,
                        'is_correct' => strtoupper($letter) === $correctAnswer,
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
            'needs_grading' => $questionType === 'essay_question',
        ];
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

        if (! $className) {
            return null;
        }

        // Filter questions by questionable_type class name
        $questions = $this->questions->filter(fn ($q) => $this->getQuestionType($q) === $className);

        if ($questions->isEmpty()) {
            return null;
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($questions as $index => $question) {
            $response = $this->responses[$index];
            $maxScore += $question->points;

            if ($type === 'essay_question') {
                continue;
            }

            if ($response['is_correct']) {
                $totalScore += $question->points;
            }
        }

        return [
            'score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0,
        ];
    }

    private function getQuestionType($question): string
    {
        return strtolower(class_basename($question->questionable_type));
    }

    public function allQuestionsAnswered(): bool
    {
        foreach ($this->responses as $response) {
            if (! $response['is_answered']) {
                return false;
            }
        }

        return true;
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        } elseif ($this->allQuestionsAnswered()) {
            $this->completeAssessment();
        }
    }
}
