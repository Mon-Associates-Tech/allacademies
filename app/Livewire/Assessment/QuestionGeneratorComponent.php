<?php

namespace App\Livewire\Assessment;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class QuestionGeneratorComponent extends Component
{
    public $selectedSubject = null;

    public $selectedTopic = null;

    public $selectedSubtopic = null;

    public $questionTypes = [
        'multiple_choice_question' => true,
        'true_or_false_question' => true,
        'essay_question' => false,
    ];

    public $questionCount = 10;

    public $difficulty = 'all';

    public $balancedDistribution = false;

    public $subjects = [];

    public $topics = [];

    public $subtopics = [];

    public $questionCounts = [];

    public $questionDistribution = [];

    public $generatedQuestions = [];

    protected RandomQuestionSelectionService $questionService;

    protected SubjectSelectionService $subjectService;

    public function boot(
        RandomQuestionSelectionService $questionService,
        SubjectSelectionService $subjectService
    ) {
        $this->questionService = $questionService;
        $this->subjectService = $subjectService;
    }

    public function mount()
    {
        $this->loadSubjects();
    }

    public function loadSubjects()
    {
        $this->subjects = $this->subjectService->getAvailableSubjects();
        Log::info('Available subjects:', $this->subjects->toArray());
    }

    public function updatedSelectedSubject($value)
    {
        Log::info("Selected subject changed to: {$value}");

        $this->selectedTopic = null;
        $this->selectedSubtopic = null;
        $this->topics = [];
        $this->subtopics = [];
        $this->resetQuestionData();

        if ($value) {
            $this->topics = $this->subjectService->getTopicsForSubject($value);
            Log::info("Topics for subject {$value}:", $this->topics->toArray());
            $this->updateQuestionData();
        }
    }

    public function updatedSelectedTopic($value)
    {
        Log::info("Selected topic changed to: {$value}");

        $this->selectedSubtopic = null;
        $this->subtopics = [];

        if ($value) {
            $this->subtopics = $this->subjectService->getSubtopicsForTopic($value);
            Log::info("Subtopics for topic {$value}:", $this->subtopics->toArray());
        }

        $this->updateQuestionData();
    }

    public function updatedSelectedSubtopic()
    {
        Log::info("Selected subtopic changed to: {$this->selectedSubtopic}");
        $this->updateQuestionData();
    }

    public function updatedQuestionTypes()
    {
        $this->updateQuestionData();
    }

    public function updatedQuestionCount()
    {
        $this->updateQuestionData();
    }

    public function updatedDifficulty()
    {
        $this->updateQuestionData();
    }

    public function generateQuestions()
    {
        $config = $this->buildConfiguration();
        Log::info('Generating questions with config:', $config);

        if (! $this->questionService->validateConfiguration($config)) {
            session()->flash('error', 'Invalid configuration. Please check your selection.');

            return;
        }

        $questions = $this->balancedDistribution
            ? $this->questionService->generateBalancedQuestions($config)
            : $this->questionService->generateQuestions($config);

        $this->generatedQuestions = $this->questionService->formatQuestionsForAssessment($questions);

        session()->flash('success', 'Generated '.$questions->count().' questions successfully!');
    }

    public function debugData()
    {
        $config = $this->buildConfiguration();
        $debug = $this->questionService->debugQuestionData($config);

        Log::info('Debug data:', $debug);
        session()->flash('success', 'Debug data logged. Check the application logs.');
    }

    protected function updateQuestionData()
    {
        if (! $this->selectedSubject) {
            $this->resetQuestionData();

            return;
        }

        $config = $this->buildConfiguration();
        Log::info('Updating question data with config:', $config);

        $this->questionCounts = $this->questionService->getAvailableQuestionCounts($config);
        $this->questionDistribution = $this->questionService->getQuestionDistribution($config);

        Log::info('Question counts:', $this->questionCounts);
        Log::info('Question distribution:', $this->questionDistribution);
    }

    protected function resetQuestionData()
    {
        $this->questionCounts = [];
        $this->questionDistribution = [];
        $this->generatedQuestions = [];
    }

    protected function buildConfiguration(): array
    {
        return [
            'subject_id' => $this->selectedSubject,
            'topic_id' => $this->selectedTopic,
            'subtopic_id' => $this->selectedSubtopic,
            'question_types' => $this->questionTypes,
            'question_count' => $this->questionCount,
            'difficulty' => $this->difficulty,
        ];
    }

    public function render()
    {
        return view('livewire.assessments.question-generator-component');
    }
}
