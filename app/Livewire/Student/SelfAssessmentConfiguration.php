<?php

namespace App\Livewire\Student;

use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use App\Models\Assessment;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\Question;
use App\Models\TrueOrFalseQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SelfAssessmentConfiguration extends Component
{
    use StartsAssessment;

    public $step = 'setup'; // setup, assessment, results

    // Setup phase
    public $selectedSubject = null;
    public $selectedTopic = null;
    public $selectedSubtopic = null;
    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false
    ];
    public $questionCount = 10;
    public $difficulty = 'all';
    public $timeLimitMinutes = null;

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
    public $subjects = [];
    public $topics = [];
    public $subtopics = [];

    protected $rules = [
        'selectedSubject' => 'required',
        'questionCount' => 'required|integer|min:1|max:50',
    ];

    public function mount()
    {
        $student = auth()->user()->student;

        if (!$student) {
            $this->subjects = collect();
            return;
        }

        $this->loadStudentSubjects($student);

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
//            ->wherePivot('is_active', true)
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
            ->wherePivot('is_active', true) //this is supposed to be false, just testing now
            ->pluck('academic_subjects.id');

        $this->subjects = $this->subjects->reject(function ($subject) use ($removedSubjects) {
            return $removedSubjects->contains($subject->id);
        });

        if(count($this->subjects) === 0) {
           $this->subjects = Subject::get();
        }
    }

    public function updatedSelectedSubject($value)
    {
        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];

        if ($value) {
            $this->topics = Topic::where('academic_subject_id', $value)->get();
        }
    }

    public function updatedSelectedTopic($value)
    {
        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = Subtopic::where('academic_topic_id', $value)->get();
        }
    }

    public function startAssessment(): void
    {
        $this->validate();

        // Validate question type combinations
        if (!$this->validateQuestionTypeCombinations()) {
            return;
        }

        $this->initializeAssessmentFromConfiguration();
    }

    private function validateQuestionTypeCombinations(): bool
    {
        $selectedTypes = array_filter($this->questionTypes);

        if (empty($selectedTypes)) {
            session()->flash('error', 'Please select at least one question type.');
            return false;
        }

        // If essay is selected, it must be the only type
        if ($this->questionTypes['essay_question'] && count($selectedTypes) > 1) {
            session()->flash('error', 'Essay questions cannot be combined with other question types.');
            return false;
        }

        return true;
    }

    private function initializeAssessmentFromConfiguration(): void
    {
        $config = $this->getConfigurationArray();
        Log::info('Starting self-assessment with configuration', [
            'config' => $config,
            'student_id' => auth()->user()->student->id
        ]);

        // Create assessment record
        $this->assessment = Assessment::create([
            'student_id' => auth()->user()->student->id,
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'title' => $this->generateAssessmentTitle(),
            'type' => Assessment::TYPE_SELF,
            'start_time' => now(),
            'status' => Assessment::STATUS_IN_PROGRESS,
            'time_limit_minutes' => $this->timeLimitMinutes,
            'question_types' => array_keys(array_filter($this->questionTypes)),
        ]);

        // Set time limit
        $this->setupTimeLimit($this->timeLimitMinutes);

        // Generate questions based on configuration
        $this->generateQuestionsFromConfiguration();

        $this->finalizeAssessmentStart();
    }

    private function generateAssessmentTitle(): string
    {
        $subject = Subject::find($this->selectedSubject);
        $topic = $this->selectedTopic ? Topic::find($this->selectedTopic) : null;
        $subtopic = $this->selectedSubtopic ? Subtopic::find($this->selectedSubtopic) : null;

        $title = "Self Assessment: {$subject->title}";
        if ($topic) {
            $title .= " - {$topic->title}";
        }
        if ($subtopic) {
            $title .= " - {$subtopic->title}";
        }

        return $title;
    }

    private function getConfigurationArray(): array
    {
        return [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => array_keys(array_filter($this->questionTypes)),
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
            'time_limit_minutes' => $this->timeLimitMinutes,
        ];
    }

    private function generateQuestionsFromConfiguration(): void
    {
        // Log configuration for debugging
        Log::info('Self-assessment configuration', [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => array_keys(array_filter($this->questionTypes)),
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
        ]);

        $questions = collect();
        $selectedTypes = array_filter($this->questionTypes);

        // For essay questions, we need to handle them specially
        if ($this->questionTypes['essay_question']) {
            $questions = $this->generateEssayQuestions();
        } else {
            // For other question types, distribute evenly
            $typesCount = count($selectedTypes);
            $questionsPerType = intval($this->questionCount / $typesCount);
            $remainder = $this->questionCount % $typesCount;

            foreach ($selectedTypes as $type => $enabled) {
                if ($enabled) {
                    $count = $questionsPerType;
                    if ($remainder > 0) {
                        $count++;
                        $remainder--;
                    }

                    $typeQuestions = $this->generateQuestionsByType($type, $count);
                    $questions = $questions->merge($typeQuestions);
                }
            }
        }

        $this->questions = $questions->shuffle()->toArray();
        $this->responses = array_fill(0, count($this->questions), []);

        // Store questions data in assessment
        $this->assessment->setQuestionsData($this->questions);

        Log::info('Generated questions for self-assessment', [
            'assessment_id' => $this->assessment->id,
            'question_count' => count($this->questions),
            'question_types' => array_keys($selectedTypes)
        ]);
    }

    private function generateEssayQuestions(): Collection
    {
        $query = EssayQuestion::query();
        $this->applyFilters($query);

        $questions = $query->take($this->questionCount)->get();

        return $questions->map(function ($question) {
            return [
                'type' => 'essay_question',
                'model' => $question,
                'points' => $question->score ?? 5,
                'difficulty_level' => $question->difficulty_level ?? 'medium'
            ];
        });
    }

    private function generateQuestionsByType($type, $count): Collection
    {
        $modelClass = $this->getQuestionModelClass($type);
        $query = $modelClass::query();
        $this->applyFilters($query);

        $questions = $query->take($count)->get();

        return $questions->map(function ($question) use ($type) {
            return [
                'type' => $type,
                'model' => $question,
                'points' => $question->score ?? 1,
                'difficulty_level' => $question->difficulty_level ?? 'medium'
            ];
        });
    }

    private function getQuestionModelClass($type): string
    {
        return match($type) {
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
            default => Question::class,
        };
    }

    private function applyFilters($query): void
    {
        if ($this->selectedSubject) {
//            $query->where('academic_subject_id', $this->selectedSubject);
        }

        if ($this->selectedTopic) {
            $query->where('academic_topic_id', $this->selectedTopic);
        }

        if ($this->selectedSubtopic) {
            $query->where('academic_subtopic_id', $this->selectedSubtopic);
        }

        if ($this->difficulty !== 'all') {
            $query->where('difficulty_level', $this->difficulty);
        }
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
                'action' => 'started_self_assessment',
                'assessment_id' => $this->assessment->id,
                'question_count' => count($this->questions),
                'time_limit_minutes' => $this->timeLimitMinutes,
            ])
            ->log('Student started self-assessment');
    }


    public function render()
    {
        return view('livewire.students.assessments');
    }
}
