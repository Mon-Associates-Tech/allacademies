<?php

namespace App\Livewire\Shared;

use App\Services\Quiz\QuizGradingService;
use Livewire\Attributes\On;
use Livewire\Component;

class QuizEngine extends Component
{
    public array $quizData = [];

    public array $responses = [];

    public int $currentSectionIndex = 0;

    public int $currentQuestionIndex = 0;

    public bool $isFinished = false;

    public array $results = [];

    public ?int $timeLeft = null;

    public ?int $totalTimeLeft = null;

    public bool $showReview = false;

    public function mount(array $quizData): void
    {
        $this->quizData = $this->normalizeQuizData($quizData);
        $this->initializeResponses();
        $this->initializeOverallTimer();
        $this->startTimeForSection();
    }

    protected function initializeOverallTimer(): void
    {
        if (isset($this->quizData['duration_in_minutes']) && $this->quizData['duration_in_minutes'] > 0) {
            $this->totalTimeLeft = $this->quizData['duration_in_minutes'] * 60;
        }
    }

    protected function normalizeQuizData(array $data): array
    {
        // Ensure data follows the expected structure
        if (! isset($data['sections']) || empty($data['sections'])) {
            $data['sections'] = [
                [
                    'title' => $data['title'] ?? 'Default Section',
                    'instructions' => $data['instructions'] ?? '',
                    'questions' => $data['questions'] ?? [],
                    'duration_minutes' => $data['duration_minutes'] ?? null,
                    'grading_mode' => $data['grading_mode'] ?? 'automatic',
                    'marking_scheme' => $data['marking_scheme'] ?? null,
                ],
            ];
        }

        foreach ($data['sections'] as &$section) {
            foreach ($section['questions'] as &$question) {
                if (isset($question['type']) && $question['type'] === 'multiple_choice' && isset($question['options'])) {
                    $question['options'] = $this->ensureFiveOptions($question['options']);
                }
            }
        }

        return $data;
    }

    protected function ensureFiveOptions(array $options): array
    {
        $keys = ['A', 'B', 'C', 'D', 'E'];
        $normalized = [];
        foreach ($keys as $index => $key) {
            $normalized[$key] = $options[$key] ?? ($options[$index] ?? null);
        }

        return array_filter($normalized);
    }

    protected function initializeResponses(): void
    {
        foreach ($this->quizData['sections'] as $sIndex => $section) {
            foreach ($section['questions'] as $qIndex => $question) {
                $this->responses[$sIndex][$qIndex] = null;
            }
        }
    }

    protected function startTimeForSection(): void
    {
        $section = $this->quizData['sections'][$this->currentSectionIndex];
        if (isset($section['duration_minutes']) && $section['duration_minutes'] > 0) {
            $this->timeLeft = $section['duration_minutes'] * 60;
        } else {
            $this->timeLeft = null;
        }
    }

    public function nextQuestion(): void
    {
        $currentSection = $this->quizData['sections'][$this->currentSectionIndex];
        if ($this->currentQuestionIndex < count($currentSection['questions']) - 1) {
            $this->currentQuestionIndex++;
        } else {
            $this->nextSection();
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        } elseif ($this->currentSectionIndex > 0) {
            $this->currentSectionIndex--;
            $this->currentQuestionIndex = count($this->quizData['sections'][$this->currentSectionIndex]['questions']) - 1;
        }
    }

    public function nextSection(): void
    {
        if ($this->currentSectionIndex < count($this->quizData['sections']) - 1) {
            $this->currentSectionIndex++;
            $this->currentQuestionIndex = 0;
            $this->startTimeForSection();
        } else {
            $this->finish();
        }
    }

    #[On('timer-tick')]
    public function tick(): void
    {
        if ($this->timeLeft !== null && $this->timeLeft > 0) {
            $this->timeLeft--;
            if ($this->timeLeft === 0) {
                $this->nextSection();
            }
        }

        if ($this->totalTimeLeft !== null && $this->totalTimeLeft > 0) {
            $this->totalTimeLeft--;
            if ($this->totalTimeLeft === 0) {
                $this->finish();
            }
        }
    }

    public function finish(): void
    {
        $this->isFinished = true;
        $this->grade();
    }

    protected function grade(): void
    {
        $gradingService = app(QuizGradingService::class);
        $this->results = $gradingService->gradeQuiz($this->quizData['sections'], $this->responses);
        $this->showReview = true;

        $this->dispatch('quiz-finished', results: $this->results);
    }

    public function render()
    {
        return view('livewire.shared.quiz-engine');
    }
}
