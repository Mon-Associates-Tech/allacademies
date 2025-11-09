<?php

namespace App\Livewire\Students;

use App\Enums\Grade;
use App\Livewire\Assessment\RandomQuestionSelectionService;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssessmentResponse;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AssignmentTakingComponent extends Component
{
    // Assignment data
    public $assignment = null;
    public $assignmentId = null;
    public $assignmentSubmission = null;

    // Assessment state
    public $step = 'loading'; // loading, taking, results
    public $assessment = null;
    public $questions = [];
    public $responses = [];
    public $currentQuestionIndex = 0;
    public $timeRemaining = null;
    public $startTime = null;
    public $isTimerActive = false;
    public $isSubmitted = false;
    public $results = null;

    // UI state
    public $showResults = false;
    public $showReview = false;
    public $darkMode = false;

    public $tabSwitchCount = 0;
    public $maxTabSwitches = null;
    public $showViolationWarning = false;
    public $violationMessage = '';
    public $restrictNavigation = false;

    // Services
    protected RandomQuestionSelectionService $questionService;

    protected $rules = [
        'responses.*' => 'nullable',
    ];

    public function boot(RandomQuestionSelectionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function mount($assignment = null)
    {
        $this->darkMode = request()->cookie('theme') === 'dark';

        if ($assignment) {
            $this->assignmentId = $assignment;
            $this->loadAssignment();
        } else {
            session()->flash('error', 'No assignment specified.');
            return redirect()->route('students.assignments');
        }
    }

    public function loadAssignment()
    {
        $student = Auth::user()->student;
        if(!$student){
            $student = Student::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        }
        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            return redirect()->route('students.assignments');
        }

        // Load the assignment
        $this->assignment = Assignment::with(['academicSubject', 'teacher.user'])
            ->find($this->assignmentId);

        if (!$this->assignment) {
            session()->flash('error', 'Assignment not found.');
            return redirect()->route('students.assignments');
        }

        $this->restrictNavigation = $this->assignment->restrict_navigation ?? false;
        $this->maxTabSwitches = $this->assignment->max_tab_switches;


        // Check if student is eligible
        if (!$this->canStartAssignment()) {
            session()->flash('error', 'You are not eligible to take this assignment.');
            return redirect()->route('students.assignments');
        }

        // Check if assignment is within time bounds
        if (!$this->isAssignmentActive()) {
            session()->flash('error', 'Assignment is not currently active.');
            return redirect()->route('students.assignments');
        }

        // Check if already submitted
        $existingSubmission = $this->getExistingSubmission();
        if ($existingSubmission && in_array($existingSubmission->status, ['submitted', 'graded'])) {
            session()->flash('error', 'You have already completed this assignment.');
            return redirect()->route('students.assignments');
        }

        // If there's an in-progress submission, resume it
        if ($existingSubmission && $existingSubmission->status === 'in_progress') {
            $this->resumeAssignment($existingSubmission);
        } else {
            $this->startAssessment();
        }
    }

    public function recordTabSwitch()
    {
        if (!$this->restrictNavigation || !$this->assignmentSubmission) {
            return;
        }

        $this->tabSwitchCount++;

        // Log the violation
        $violations = $this->assignmentSubmission->violation_logs ?? [];
        $violations[] = [
            'type' => 'tab_switch',
            'timestamp' => now()->toISOString(),
            'count' => $this->tabSwitchCount,
        ];

        $this->assignmentSubmission->update([
            'tab_switch_count' => $this->tabSwitchCount,
            'violation_logs' => $violations,
        ]);

        // Check if exceeded limit
        if ($this->maxTabSwitches && $this->tabSwitchCount >= $this->maxTabSwitches) {
            $this->handleViolation();
        } else {
            $remaining = $this->maxTabSwitches ? ($this->maxTabSwitches - $this->tabSwitchCount) : 'unlimited';
            $this->showViolationWarning = true;
            $this->violationMessage = "Warning: You switched tabs/windows. Remaining switches: {$remaining}";

            $this->dispatch('show-violation-warning', message: $this->violationMessage);
        }
    }

    private function handleViolation()
    {
        if ($this->assignment->auto_submit_on_violation) {
            // Auto-submit and mark as violated
            $this->assignmentSubmission->update([
                'cancelled_due_to_violation' => true,
                'status' => 'cancelled',
                'submitted_at' => now(),
            ]);

            $this->isSubmitted = true;
            $this->isTimerActive = false;
            $this->step = 'violation';

            Log::warning('Assignment cancelled due to violation', [
                'submission_id' => $this->assignmentSubmission->id,
                'student_id' => $this->assignmentSubmission->student_id,
                'tab_switches' => $this->tabSwitchCount,
            ]);

            session()->flash('error', 'Your assignment has been automatically cancelled due to excessive tab switching.');
        }
    }

    private function getExistingSubmission()
    {
        $student = Auth::user()->student;

        if(!$student){
            $student = Student::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        }

        return AssignmentSubmission::where('assignment_id', $this->assignment->id)
            ->where('student_id', $student->id)
            ->first();
    }

    private function resumeAssignment($submission)
    {
        $this->assignmentSubmission = $submission;

        // Load saved state
        $savedAnswers = $submission->answers ?? [];

        // Generate questions (same as starting new)
        $this->generateQuestionsFromAssignment();

        // Restore responses from saved answers
        $this->responses = [];
        foreach ($this->questions as $index => $question) {
            $questionId = $question['id'];

            // Handle both old format (direct value) and new format (structured data)
            if (isset($savedAnswers[$questionId])) {
                $savedAnswer = $savedAnswers[$questionId];

                // New format: answer is stored as array with 'response' key
                if (is_array($savedAnswer) && isset($savedAnswer['response'])) {
                    $this->responses[$index] = $savedAnswer['response'];
                } else {
                    // Old format: answer is stored directly
                    $this->responses[$index] = $savedAnswer;
                }
            } else {
                $this->responses[$index] = null;
            }
        }

        // Calculate time remaining
        $timeSpent = $submission->time_spent_minutes * 60; // Convert to seconds
        $totalTime = $this->assignment->duration_in_minutes * 60;
        $this->timeRemaining = max(0, $totalTime - $timeSpent);

        if ($this->timeRemaining > 0) {
            $this->isTimerActive = true;
        }

        $this->startTime = now();
        $this->step = 'taking';

        session()->flash('info', 'Resuming your assignment. You have ' . gmdate('H:i:s', $this->timeRemaining) . ' remaining.');
    }
    private function canStartAssignment()
    {
        $student = Auth::user()->student;

        if(!$student){
            $student = Student::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        }

        if ($this->assignment->status !== 'published') {
            return false;
        }

        // Check if student is in assigned groups/levels
        $isEligible = false;

        // Check academic groups
        if ($this->assignment->academicGroups->isNotEmpty()) {
            $studentAcademicGroupIds = $student->academicGroups?->pluck('id')->toArray();
            $assignmentAcademicGroupIds = $this->assignment->academicGroups?->pluck('id')->toArray();

            if(is_array($studentAcademicGroupIds) && is_array($assignmentAcademicGroupIds) && array_intersect($studentAcademicGroupIds, $assignmentAcademicGroupIds)) {
                $isEligible = true;
            }

        }

        // Check academic levels
        if (!$isEligible && $this->assignment->academicLevels?->isNotEmpty()) {
            $assignmentAcademicLevelIds = $this->assignment->academicLevels?->pluck('id')->toArray();

            if (in_array($student->academic_level_id, $assignmentAcademicLevelIds)) {
                $isEligible = true;
            }
        }

        // Check student groups
        if (!$isEligible && $this->assignment->studentGroups->isNotEmpty()) {
            $studentGroupIds = $student->studentGroups?->pluck('id')->toArray();
            $assignmentStudentGroupIds = $this->assignment->studentGroups?->pluck('id')->toArray();

            if (array_intersect($studentGroupIds, $assignmentStudentGroupIds)) {
                $isEligible = true;
            }
        }

        // Check direct assignment to student
        if (!$isEligible && $this->assignment->students->isNotEmpty()) {
            $assignmentStudentIds = $this->assignment->students->pluck('id')->toArray();

            if (in_array($student->id, $assignmentStudentIds)) {
                $isEligible = true;
            }
        }

        return $isEligible;
    }

    private function isAssignmentActive()
    {
        $now = now();
        return $this->assignment->starts_at <= $now && $this->assignment->ends_at > $now;
    }

    public function startAssessment()
    {
        try {
            // Generate questions from assignment configuration
            $this->generateQuestionsFromAssignment();

            if (empty($this->questions)) {
                session()->flash('error', 'No questions available for this assignment.');
                return redirect()->route('students.assignments');
            }

            // Create assignment submission record
            $this->createAssignmentSubmission();

            // Create assessment record (for compatibility with existing system)
            $this->createAssessment();

            // Initialize responses
            $this->initializeResponses();

            // Set timer from assignment duration
            if ($this->assignment->duration_in_minutes) {
                $this->timeRemaining = $this->assignment->duration_in_minutes * 60;
                $this->isTimerActive = true;
            }

            $this->startTime = now();
            $this->step = 'taking';

            session()->flash('success', 'Assignment started with ' . count($this->questions) . ' questions!');

        } catch (\Exception $e) {
            Log::error('Failed to start assignment', [
                'error' => $e->getMessage(),
                'assignment_id' => $this->assignment->id,
                'user_id' => Auth::id()
            ]);

            session()->flash('error', 'Failed to start assignment. Please try again.');
            return redirect()->route('students.assignments');
        }
    }

    private function createAssignmentSubmission()
    {
        $student = Auth::user()->student;

        if(!$student){
            $student = Student::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        }

        $this->assignmentSubmission = AssignmentSubmission::create([
            'assignment_id' => $this->assignment->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'answers' => [],
            'total_marks' => $this->assignment->total_marks,
            'status' => 'in_progress',
            'time_spent_minutes' => 0,
        ]);

        Log::info('Assignment submission created:', [
            'submission_id' => $this->assignmentSubmission->id,
            'assignment_id' => $this->assignment->id,
            'student_id' => $student->id
        ]);
    }

    private function generateQuestionsFromAssignment()
    {
        // Check if assignment has embedded questions (book-based)
        if ($this->assignment->hasEmbeddedQuestions()) {
            $this->questions = $this->assignment->getEmbeddedQuestions()->toArray();

            Log::info("Loaded embedded questions from book-based assignment", [
                'assignment_id' => $this->assignment->id,
                'question_count' => count($this->questions)
            ]);

            return;
        }

        // Otherwise, generate from database questions (existing logic)
        $questions = [];
        $questionsConfig = $this->assignment->questions ?? [];

        foreach ($questionsConfig as $questionConfig) {
            $requestedCount = $questionConfig['count'];
            $generatedForThisType = [];

            // If specific question IDs are provided, use them
            if (!empty($questionConfig['specific_ids'])) {
                $generatedForThisType = $this->getQuestionsByIds($questionConfig['type'], $questionConfig['specific_ids']);
            } else {
                // ... existing generation logic ...
                $config = [
                    'subject_id' => $this->assignment->academic_subject_id,
                    'question_types' => [$questionConfig['type'] => true],
                    'question_count' => $requestedCount,
                    'difficulty' => $questionConfig['difficulty'] ?? 'all',
                ];

                if (!empty($questionConfig['subtopic_ids'])) {
                    $subtopicIds = $questionConfig['subtopic_ids'];
                    $questionsPerSubtopic = max(1, ceil($requestedCount / count($subtopicIds)));

                    foreach ($subtopicIds as $subtopicId) {
                        $subtopicConfig = array_merge($config, [
                            'subtopic_id' => $subtopicId,
                            'question_count' => $questionsPerSubtopic
                        ]);

                        $generatedQuestions = $this->questionService->generateQuestions($subtopicConfig);
                        $formattedQuestions = $this->questionService->formatQuestionsForAssessment($generatedQuestions);
                        $generatedForThisType = array_merge($generatedForThisType, $formattedQuestions->toArray());

                        if (count($generatedForThisType) >= $requestedCount) {
                            break;
                        }
                    }
                } elseif (!empty($questionConfig['topic_ids'])) {
                    $topicIds = $questionConfig['topic_ids'];
                    $questionsPerTopic = max(1, ceil($requestedCount / count($topicIds)));

                    foreach ($topicIds as $topicId) {
                        $topicConfig = array_merge($config, [
                            'topic_id' => $topicId,
                            'question_count' => $questionsPerTopic
                        ]);

                        $generatedQuestions = $this->questionService->generateQuestions($topicConfig);
                        $formattedQuestions = $this->questionService->formatQuestionsForAssessment($generatedQuestions);
                        $generatedForThisType = array_merge($generatedForThisType, $formattedQuestions->toArray());

                        if (count($generatedForThisType) >= $requestedCount) {
                            break;
                        }
                    }
                } else {
                    $generatedQuestions = $this->questionService->generateQuestions($config);
                    $formattedQuestions = $this->questionService->formatQuestionsForAssessment($generatedQuestions);
                    $generatedForThisType = $formattedQuestions->toArray();
                }
            }

            if (count($generatedForThisType) > $requestedCount) {
                shuffle($generatedForThisType);
                $generatedForThisType = array_slice($generatedForThisType, 0, $requestedCount);
            }

            $questions = array_merge($questions, $generatedForThisType);
        }

        // Shuffle questions if assignment is randomized
        if ($this->assignment->is_randomized) {
            shuffle($questions);
        }

        $this->questions = $questions;

        Log::info("Generated questions from database for assignment", [
            'assignment_id' => $this->assignment->id,
            'question_count' => count($this->questions)
        ]);
    }
    private function getQuestionsByIds($type, $ids)
    {
        $model = match($type) {
            'multiple_choice_question' => \App\Models\MultipleChoiceQuestion::class,
            'true_or_false_question' => \App\Models\TrueOrFalseQuestion::class,
            'essay_question' => \App\Models\EssayQuestion::class,
            default => throw new \InvalidArgumentException("Unknown question type: {$type}")
        };

        $questions = $model::whereIn('id', $ids)->get();
        return $this->questionService->formatQuestionsForAssessment($questions)->toArray();
    }

    private function createAssessment()
    {
        $student = Auth::user()->student;

        if(!$student){
            $student = Student::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        }

        if (!$student) {
            throw new \Exception('Student profile not found.');
        }

        $this->assessment = Assessment::create([
            'student_id' => $student->id,
            'assignment_id' => $this->assignment->id,
            'subject_id' => $this->assignment->academic_subject_id,
            'title' => $this->assignment->title,
            'type' => 'assignment',
            'status' => 'in_progress',
            'start_time' => now(),
            'questions_data' => $this->questions,
            'time_limit_minutes' => $this->assignment->duration_in_minutes,
            'max_score' => array_sum(array_column($this->questions, 'points')),
        ]);

        Log::info('Assessment created for assignment:', [
            'assessment_id' => $this->assessment->id,
            'assignment_id' => $this->assignment->id
        ]);
    }

    private function initializeResponses()
    {
        $this->responses = array_fill(0, count($this->questions), null);
    }

    // Save answer automatically when student responds
    public function updatedResponses($value, $key)
    {
        // Auto-save the response
        $this->saveCurrentAnswers();
    }

    private function saveCurrentAnswers()
    {
        if (!$this->assignmentSubmission) {
            return;
        }

        // Format answers for storage - using question ID as key with question type
        $answers = [];
        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;
            if ($response !== null) {
                $answers[$question['id']] = [
                    'response' => $response,
                    'question_type' => $question['type'],
                    'answered_at' => now()->toDateTimeString(),
                ];
            }
        }

        // Calculate time spent
        $timeSpent = $this->startTime ? now()->diffInMinutes($this->startTime) : 0;

        // Update submission
        $this->assignmentSubmission->update([
            'answers' => $answers,
            'time_spent_minutes' => $timeSpent,
        ]);

        Log::debug('Answers auto-saved for assignment submission', [
            'submission_id' => $this->assignmentSubmission->id,
            'answers_count' => count($answers),
            'time_spent' => $timeSpent
        ]);
    }

    // Assessment Navigation
    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function isQuestionAnswered($index)
    {
        $response = $this->responses[$index] ?? null;
        return $response !== null && $response !== '';
    }

    public function getAnsweredCount()
    {
        return count(array_filter($this->responses, function($response) {
            return $response !== null && $response !== '';
        }));
    }

    public function getProgress()
    {
        $total = count($this->questions);
        $answered = $this->getAnsweredCount();
        return $total > 0 ? round(($answered / $total) * 100) : 0;
    }

    // Assessment Submission and Grading
    public function submitAssessment()
    {
        if ($this->isSubmitted) {
            return;
        }

        DB::beginTransaction();

        try {
            // Save final answers
            $this->saveCurrentAnswers();

            // Grade the responses
            $results = $this->gradeResponses();

            // Update assignment submission with final data
            $timeSpent = $this->startTime ? now()->diffInMinutes($this->startTime) : 0;

            $this->assignmentSubmission->update([
                'submitted_at' => now(),
                'score' => $results['total_score'],
                'status' => $results['needs_manual_grading'] ? 'submitted' : 'graded',
                'time_spent_minutes' => $timeSpent,
            ]);

            // Create or update assessment response for compatibility (only if assessment exists)
            if ($this->assessment) {
                $assessmentResponse = $this->createOrUpdateAssessmentResponse();

                // Update the data with grading results
                if ($assessmentResponse) {
                    $data = $assessmentResponse->data;
                    $data['grading_results'] = $results;
                    $data['is_graded'] = !$results['needs_manual_grading'];
                    $data['graded_at'] = !$results['needs_manual_grading'] ? now()->toDateTimeString() : null;

                    $assessmentResponse->update(['data' => $data]);
                }

                // Update assessment status
                $this->assessment->update([
                    'status' => 'completed',
                    'end_time' => now(),
                    'score' => $results['total_score'],
                    'max_score' => $results['max_score'],
                    'percentage_score' => $results['percentage'],
                ]);
            }

            $this->results = $results;
            $this->isSubmitted = true;
            $this->isTimerActive = false;
            $this->step = 'results';

            DB::commit();

            session()->flash('success', 'Assignment submitted successfully!');

            Log::info('Assignment completed', [
                'submission_id' => $this->assignmentSubmission->id,
                'assignment_id' => $this->assignment->id,
                'score' => $results['total_score'],
                'percentage' => $results['percentage']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit assignment', [
                'error' => $e->getMessage(),
                'submission_id' => $this->assignmentSubmission->id ?? null,
                'assignment_id' => $this->assignment->id,
                'user_id' => Auth::id()
            ]);

            session()->flash('error', 'Failed to submit assignment. Please try again.');
        }
    }
    private function createOrUpdateAssessmentResponse()
    {
        if (!$this->assessment) {
            return null;
        }

        // Build the data structure for the AssessmentResponse model
        $data = [
            'questions' => $this->formatQuestionsForAssessmentResponse(),
            'submission_info' => [
                'submitted_at' => now()->toDateTimeString(),
                'time_taken' => $this->startTime ? now()->diffInMinutes($this->startTime) : null,
                'answered_questions' => $this->getAnsweredCount(),
                'total_questions' => count($this->questions),
            ],
            'subject_id' => $this->assignment->academic_subject_id,
            'topic_id' => null, // Assignment might not have specific topic
            'subtopic_id' => null, // Assignment might not have specific subtopic
        ];

        return AssessmentResponse::updateOrCreate(
            ['assessment_id' => $this->assessment->id],
            [
                'responses_data' => $this->formatResponsesForStorage(),
                'submission_data' => $data['submission_info'],
            ]
        );
    }


    private function formatQuestionsForAssessmentResponse()
    {
        $formattedQuestions = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;

            $formattedQuestions[] = AssessmentResponse::createQuestionData([
                'type' => $question['type'],
                'question' => $question['question'],
                'options' => $question['options'] ?? null,
                'student_answer' => $response,
                'correct_answer' => $question['answer'] ?? null,
                'is_correct' => $this->isResponseCorrect($question, $response),
                'points_possible' => $question['points'] ?? 1,
                'points_earned' => $this->calculateQuestionPoints($question, $response),
                'response_time' => null, // You can track this if needed
                'is_graded' => $question['type'] !== 'essay_question',
            ]);
        }

        return $formattedQuestions;
    }

    private function isResponseCorrect($question, $response)
    {
        if ($question['type'] === 'essay_question' || $response === null) {
            return null;
        }

        return match ($question['type']) {
            'multiple_choice_question' => strtoupper($response) === strtoupper($question['answer']),
            'true_or_false_question' => ($response === 'true' || $response === 'True') === filter_var($question['answer'], FILTER_VALIDATE_BOOLEAN),
            default => false,
        };
    }

    private function calculateQuestionPoints($question, $response)
    {
        if ($question['type'] === 'essay_question') {
            return 0; // Will be graded manually
        }

        $isCorrect = $this->isResponseCorrect($question, $response);
        return $isCorrect ? ($question['points'] ?? 1) : 0;
    }

    private function formatResponsesForStorage()
    {
        $formattedResponses = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;

            if ($response !== null) {
                $formattedResponses[$question['id']] = [
                    'question_id' => $question['id'],
                    'question_type' => $question['type'],
                    'response' => $this->formatSingleResponse($question, $response),
                    'answered_at' => now()->toDateTimeString(),
                ];
            }
        }

        return $formattedResponses;
    }

    private function formatSingleResponse($question, $response)
    {
        return match ($question['type']) {
            'multiple_choice_question' => [
                'selected_option' => $response,
                'options' => $question['options'] ?? [],
                'answer' => $question['answer'] ?? null
            ],
            'true_or_false_question' => [
                'selected_answer' => filter_var($response, FILTER_VALIDATE_BOOLEAN),
                'answer_boolean' => $response === ('True' || 'true')
            ],
            'essay_question' => [
                'essay_text' => $response,
                'word_count' => str_word_count($response ?? ''),
                'character_count' => strlen($response ?? '')
            ],
            default => $response,
        };
    }

    private function gradeResponses()
    {
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $needsManualGrading = false;
        $gradedResponses = [];

        foreach ($this->questions as $index => $question) {
            $response = $this->responses[$index] ?? null;
            $questionMaxScore = $question['points'] ?? 1;
            $maxScore += $questionMaxScore;

            if (!empty($response)) {
                $gradeResult = $this->gradeQuestionResponse($question, $response);

                if ($gradeResult['needs_manual_grading'] ?? false) {
                    $needsManualGrading = true;
                } else {
                    $totalScore += $gradeResult['score_earned'];
                    if ($gradeResult['is_correct']) {
                        $correctAnswers++;
                    }
                }

                $gradedResponses[$index] = $gradeResult;
            } else {
                $gradedResponses[$index] = [
                    'is_correct' => false,
                    'score_earned' => 0,
                    'feedback' => 'Question not answered',
                    'question_id' => $question['id'],
                    'question_type' => $question['type']
                ];
            }
        }

        return [
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'correct_answers' => $correctAnswers,
            'answered_questions' => $this->getAnsweredCount(),
            'total_questions' => count($this->questions),
            'completion_rate' => count($this->questions) > 0 ?
                round(($this->getAnsweredCount() / count($this->questions)) * 100, 2) : 0,
            'graded_responses' => $gradedResponses,
            'needs_manual_grading' => $needsManualGrading,
            'graded_at' => now()->toDateTimeString()
        ];
    }

    private function gradeQuestionResponse($question, $response)
    {
        return match ($question['type']) {
            'multiple_choice_question' => $this->gradeMultipleChoice($question, $response),
            'true_or_false_question' => $this->gradeTrueFalse($question, $response),
            'essay_question' => $this->prepareEssayForGrading($question, $response),
            default => [
                'is_correct' => false,
                'score_earned' => 0,
                'feedback' => 'Unknown question type',
                'question_id' => $question['id'],
                'question_type' => $question['type']
            ],
        };
    }

    /**
     * Normalize answer to letter format (A-E) or match against option text
     */
    private function normalizeAnswerToLetter($answer, $options = null): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E'];

        // If it's already a letter, return it uppercase
        if (is_string($answer) && strlen($answer) === 1 && in_array(strtoupper($answer), $letters)) {
            return strtoupper($answer);
        }

        // If it's numeric, convert to letter
        if (is_numeric($answer)) {
            return $letters[$answer] ?? strtoupper((string)$answer);
        }

        // If it's text and we have options, try to match it to an option
        if (is_string($answer) && $options) {
            foreach ($options as $letter => $optionText) {
                if (strcasecmp(trim($optionText), trim($answer)) === 0) {
                    return strtoupper($letter);
                }
            }
        }

        // Default: return uppercase version
        return strtoupper((string)$answer);
    }

    private function gradeMultipleChoice($question, $response)
    {
        Log::info('Grading multiple choice question', [
            'question' => $question,
            'response' => $response
        ]);

        $correctAnswer = $question['answer'] ?? $question['correct_answer'] ?? null;
        $options = $question['options'] ?? null;

        if (!$correctAnswer) {
            return [
                'is_correct' => null,
                'score_earned' => 0,
                'feedback' => 'No correct answer defined for this question',
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'needs_manual_grading' => true
            ];
        }

        // Normalize both the response and correct answer to letter format
        $normalizedResponse = $this->normalizeAnswerToLetter($response, $options);
        $normalizedCorrectAnswer = $this->normalizeAnswerToLetter($correctAnswer, $options);

        $isCorrect = $normalizedResponse === $normalizedCorrectAnswer;

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : "Incorrect. The correct answer was {$normalizedCorrectAnswer}",
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_option' => $normalizedResponse,
            'correct_answer' => $normalizedCorrectAnswer,
            'explanation' => $isCorrect ? null : ($question['explanation'] ?? null)
        ];
    }

    private function gradeTrueFalse($question, $response)
    {
        $correctAnswer = $question['answer'] ?? $question['correct_answer'] ?? null;

        if ($correctAnswer === null) {
            return [
                'is_correct' => null,
                'score_earned' => 0,
                'feedback' => 'No correct answer defined for this question',
                'question_id' => $question['id'],
                'question_type' => $question['type'],
                'needs_manual_grading' => true
            ];
        }

        $correctAnswerBoolean = filter_var($correctAnswer, FILTER_VALIDATE_BOOLEAN);
        $responseBoolean = $response === 'true' || $response === 'True';
        $isCorrect = $responseBoolean === $correctAnswerBoolean;

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $isCorrect ? ($question['points'] ?? 1) : 0,
            'feedback' => $isCorrect ? 'Correct!' : 'Incorrect. The correct answer was ' . ($correctAnswerBoolean ? 'True' : 'False'),
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'selected_answer' => $response,
            'correct_answer' => $correctAnswerBoolean,
            'explanation' => $isCorrect ? null : ($question['explanation'] ?? null)
        ];
    }

    private function prepareEssayForGrading($question, $response)
    {
        return [
            'is_correct' => null,
            'score_earned' => 0,
            'needs_manual_grading' => true,
            'feedback' => 'Essay submitted for manual grading',
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'essay_text' => $response,
            'word_count' => str_word_count($response ?? ''),
            'character_count' => strlen($response ?? '')
        ];
    }

    // Timer Management
    public function updateTimer()
    {
        if ($this->isTimerActive && $this->timeRemaining > 0) {
            $this->timeRemaining--;

            // Auto-save every 30 seconds
            if ($this->timeRemaining % 30 === 0) {
                $this->saveCurrentAnswers();
            }

            if ($this->timeRemaining <= 0) {
                $this->submitAssessment();
            }
        }
    }

    // Results Management
    public function toggleReview()
    {
        $this->showReview = !$this->showReview;
    }

    public function backToAssignments()
    {
        return redirect()->route('students.assignments');
    }

    public function getGrade($percentage)
    {
        return Grade::fromPercentage($percentage);
    }

    public function render()
    {
        return view('livewire.students.assignment-taking');
    }
}
