<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AssessmentResponse;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use App\Models\TrueOrFalseQuestion;
use App\Traits\StartsAssessment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SelfAssessment extends Component
{
    use StartsAssessment;

    public $step = 'setup'; // setup, assessment, results
    public $assessmentMode = 'self'; // 'self' or 'assignment'

    // Setup phase
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $selectedAssignment = null;
    public $questionTypes = ['multiple_choice_question' => true, 'true_or_false_question' => true, 'essay_question' => false];
    public $questionCount = 10;
    public $difficulty = 'all'; // all, easy, medium, hard
    public $timeLimitMinutes = null;

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
    public $availableAssignments = [];

    public function mount()
    {
        $student = auth()->user()->student;

        if (!$student) {
            $this->subjects = collect();
            return;
        }

        $this->loadStudentSubjects($student);
        $this->loadAvailableAssignments();

        // Log student accessing self-assessment
        activity()->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_self_assessment',
                'page' => 'self-assessment'
            ])
            ->log('Student accessed self-assessment page');
    }

    private function loadStudentSubjects($student): void
    {
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
            if (!$this->subjects->contains('id', $subject->id)) {
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

        if(count($this->subjects) === 0) {
           $this->subjects = Subject::get();
        }
    }

    private function loadAvailableAssignments(): void
    {
        $this->availableAssignments = $this->getAvailableAssignments();
    }

    public function switchAssessmentMode($mode): void
    {
        $this->assessmentMode = $mode;
        $this->reset(['selectedAssignment', 'selectedSubject', 'selectedTopic', 'selectedSubtopic']);
    }

    public function startAssessment(): void
    {
        if ($this->assessmentMode === 'assignment') {
            $this->startFromAssignment();
        } else {
            $this->startFromConfiguration();
        }
    }

    private function startFromAssignment(): void
    {
        if (!$this->selectedAssignment) {
            session()->flash('error', 'Please select an assignment.');
            return;
        }

        $assignment = Assignment::find($this->selectedAssignment);
        if (!$assignment) {
            session()->flash('error', 'Assignment not found.');
            return;
        }

        // Check if student can start this assignment
        if (!$this->canStartAssignment($assignment)) {
            session()->flash('error', 'You are not eligible to start this assignment or it is not available.');
            return;
        }

        try {
            // Reset assessment state
            $this->reset(['questions', 'responses', 'assessment']);

            // Create assessment record
            $this->assessment = Assessment::create([
                'student_id' => auth()->user()->student->id,
                'subject_id' => $assignment->academic_subject_id,
                'title' => "Assignment Practice: {$assignment->title}",
                'start_time' => now(),
                'status' => 'in_progress',
                'assignment_id' => $assignment->id,
            ]);

            // Set time limit from assignment
            if ($assignment->duration_in_minutes) {
                $this->timeLimitSeconds = $assignment->duration_in_minutes * 60;
                $this->timeRemaining = $this->timeLimitSeconds;
            } else {
                $this->timeLimitSeconds = 0;
                $this->timeRemaining = null;
            }

            // Generate questions from assignment sections
            $this->generateQuestionsFromAssignment($assignment);

            // Initialize responses array
            $this->responses = [];
            foreach ($this->questions as $index => $questionData) {
                $this->responses[$index] = [
                    'question_id' => $questionData['id'],
                    'response' => null,
                    'is_answered' => false
                ];
            }

            $this->currentQuestionIndex = 0;
            $this->step = 'assessment';

            // Log assignment start
            activity()->performedOn(auth()->user()->student)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'started_assignment_practice',
                    'assignment_id' => $assignment->id,
                    'assignment_title' => $assignment->title,
                    'question_count' => count($this->questions)
                ])
                ->log('Student started assignment practice');

            session()->flash('success', 'Assignment practice started successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to start assignment practice', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id
            ]);

            session()->flash('error', 'Failed to start assignment practice. Please try again.');
        }
    }

    private function startFromConfiguration(): void
    {
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

        $config = [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => $this->questionTypes,
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'marks_per_question' => 1,
            'title' => 'Self Assessment - ' . Subject::find($this->selectedSubject)?->name,
        ];

        $this->startSelfAssessment($config);
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
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->subtopics = collect();

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

    public function updatedSelectedTopic(): void
    {
        $this->subtopics = $this->selectedTopic
            ? Subtopic::where('academic_topic_id', $this->selectedTopic)->get()
            : collect();

        $this->selectedSubtopic = null;

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
        Log::info("saveResponse called", [
            'index' => $index,
            'response' => $response,
            'current_response_data' => $this->responses[$index] ?? 'NOT_SET'
        ]);

        if (!isset($this->responses[$index])) {
            Log::error("Response index not found", ['index' => $index]);
            return;
        }

        $questionData = $this->questions[$index];
        $questionType = $questionData['type'];

        if (!$questionType) {
            session()->flash('error', "Unknown question type.");
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

            Log::info("Multiple choice response processed", [
                'user_answer' => $response,
                'correct_answer' => $questionModel->answer,
                'is_correct' => $isCorrect,
                'score' => $this->responses[$index]['score']
            ]);
        } elseif ($questionType === 'true_or_false_question') {
            $questionModel = $questionData['model'];
            // Convert response to boolean for comparison
            $responseBoolean = $response === 'true' || $response === '1' || $response === 1 || $response === true;
            $isCorrect = $responseBoolean === (bool)$questionModel->answer;
            $this->responses[$index]['is_correct'] = $isCorrect;
            $this->responses[$index]['score'] = $isCorrect ? $questionData['points'] : 0;

            Log::info("True/false response processed", [
                'user_answer' => $response,
                'correct_answer' => $questionModel->answer,
                'is_correct' => $isCorrect,
                'score' => $this->responses[$index]['score']
            ]);
        } else {
            // Essay questions don't get auto-graded
            $this->responses[$index]['is_correct'] = null;
            $this->responses[$index]['score'] = 0; // Will be manually graded later

            Log::info("Essay response processed", [
                'user_answer' => $response
            ]);
        }

        Log::info("Response after processing", [
            'index' => $index,
            'response_data' => $this->responses[$index]
        ]);
    }

    public function completeAssessment(): void
    {
        // Add debugging
        Log::info('Starting completeAssessment', [
            'responses_count' => count($this->responses),
            'questions_count' => count($this->questions),
            'responses' => $this->responses
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
            ]
        ];

        foreach ($this->responses as $index => $response) {
            $questionData = $this->questions[$index];
            $questionType = $questionData['type'];

            $maxScore += $questionData['points'];

            Log::info("Processing response {$index}", [
                'response' => $response,
                'question_type' => $questionType,
                'is_answered' => $response['is_answered'] ?? false
            ]);

            // Check if question was answered - handle both 'response' and 'answer' fields
            $isAnswered = $response['is_answered'] ?? false;
            $userAnswer = $response['response'] ?? $response['answer'] ?? null;
            $hasResponse = !empty($userAnswer) || $userAnswer === '0' || $userAnswer === 0;

            if ($isAnswered || $hasResponse) {
                $totalAnswered++;

                // If we have an answer but no score calculated, calculate it now
                if ($hasResponse && !isset($response['score']) && $userAnswer) {
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
            'all_responses_data' => $allResponsesData
        ]);

        // Save all assessment responses in a single record
        if (!empty($allResponsesData['questions'])) {
            try {
                $assessmentResponse = AssessmentResponse::create([
                    'assessment_id' => $this->assessment->id,
                    'data' => $allResponsesData,
                ]);
                Log::info('AssessmentResponse created successfully', ['id' => $assessmentResponse->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create AssessmentResponse', [
                    'error' => $e->getMessage(),
                    'data' => $allResponsesData
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
                'total_questions' => count($this->questions)
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
            $isCorrect = $responseBoolean === (bool)$questionModel->answer;
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
                'previous_score' => $this->result['percentage_score'] ?? null
            ])
            ->log('Student requested to retake assessment');

        // Reset all assessment-related data
        $this->reset([
            'step', 'questions', 'responses', 'currentQuestionIndex',
            'assessment', 'result', 'timeRemaining'
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
            'assessment', 'result', 'timeRemaining'
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
            'percentage_score' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0
        ];
    }

    private function getQuestionType($question): string
    {
        return strtolower(class_basename($question->questionable_type));
    }

    public function allQuestionsAnswered(): bool
    {
        foreach ($this->responses as $response) {
            if (!$response['is_answered']) {
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

    /**
     * Generate questions from assignment sections
     */
    private function generateQuestionsFromAssignment(Assignment $assignment): void
    {
        $this->questions = [];

        foreach ($assignment->assignmentSections as $section) {
            // Get questions based on section configuration
            $sectionQuestions = $this->getQuestionsForSection($section);

            foreach ($sectionQuestions as $questionModel) {
                // Create Question record
                $question = Question::create([
                    'questionable_type' => get_class($questionModel),
                    'questionable_id' => $questionModel->id,
                    'subtopic_id' => $questionModel->academic_subtopic_id ?? null,
                    'topic_id' => $questionModel->academic_topic_id ?? null,
                    'difficulty_level' => $questionModel->difficulty_level,
                    'points' => $section->marks_per_question ?? 1,
                    'user_id' => auth()->user()->id,
                ]);

                // Store question data
                $this->questions[] = [
                    'id' => $question->id,
                    'type' => $this->getTypeFromClassName(class_basename(get_class($questionModel))),
                    'model' => $questionModel,
                    'question_record' => $question,
                    'points' => $question->points,
                    'difficulty_level' => $question->difficulty_level,
                    'section_id' => $section->id
                ];
            }
        }
    }

    /**
     * Get questions for a specific assignment section
     */
    private function getQuestionsForSection($section)
    {
        $questions = collect();

        // Determine question types for this section
        $questionTypes = [];
        if ($section->include_multiple_choice) {
            $questionTypes[] = MultipleChoiceQuestion::class;
        }
        if ($section->include_true_false) {
            $questionTypes[] = TrueOrFalseQuestion::class;
        }
        if ($section->include_essay) {
            $questionTypes[] = EssayQuestion::class;
        }

        foreach ($questionTypes as $questionType) {
            $query = $questionType::query();

            // Apply section filters
            if ($section->academic_subtopic_id) {
                $query->where('academic_subtopic_id', $section->academic_subtopic_id);
            } elseif ($section->academic_topic_id) {
                $query->where(function ($q) use ($section) {
                    $q->whereHas('subtopic', function ($s) use ($section) {
                        $s->where('academic_topic_id', $section->academic_topic_id);
                    })->orWhere('academic_topic_id', $section->academic_topic_id);
                });
            }

            // Apply difficulty filter
            if ($section->difficulty_level && $section->difficulty_level !== 'all') {
                $query->where('difficulty_level', $section->difficulty_level);
            }

            // Get questions for this type
            $typeQuestions = $query->inRandomOrder()
                ->limit($section->question_count_per_type ?? 5)
                ->get();

            $questions = $questions->merge($typeQuestions);
        }

        // Shuffle and limit to section's total question count
        return $questions->shuffle()->take($section->total_questions ?? 10);
    }

    /**
     * Check if student can start assignment
     */
    private function canStartAssignment(Assignment $assignment): bool
    {
        $student = auth()->user()->student;

        // Check if assignment is published and active
        if ($assignment->status !== 'published') {
            return false;
        }

        // Check time constraints
        if ($assignment->starts_at > now() || $assignment->ends_at < now()) {
            return false;
        }

        // Check if student is in eligible groups
        $studentGroupIds = $student->academicGroups->pluck('id');
        $assignmentGroupIds = $assignment->academicGroups->pluck('id');

        if ($assignmentGroupIds->isNotEmpty() && $studentGroupIds->intersect($assignmentGroupIds)->isEmpty()) {
            // Check if student is in eligible academic level
            if ($assignment->academicLevels->pluck('id')->doesntContain($student->academic_level_id)) {
                // Check if student is directly assigned
                if (!$assignment->students->contains('id', $student->id)) {
                    return false;
                }
            }
        }
        return true;
    }


}
