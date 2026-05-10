<?php

namespace App\Livewire\Examinations;

use Livewire\Component;

class QuestionEditor extends Component
{
    public array $sections = [];
    public array $questions = [];
    public bool $hardenedMode = false;

    public function mount(array $sections, array $questions, bool $hardenedMode = false): void
    {
        $this->sections = $sections;
        $this->questions = $this->normalizeQuestions($questions);
        $this->hardenedMode = $hardenedMode;
    }

    private function normalizeQuestions(array $questions): array
    {
        foreach ($questions as $sectionIndex => $sectionQuestions) {
            if (!is_array($sectionQuestions)) {
                continue;
            }
            
            foreach ($sectionQuestions as $qIndex => $question) {
                if (!is_array($question)) {
                    continue;
                }
                
                // Normalize answer field to correct_answer
                if (isset($question['answer']) && !isset($question['correct_answer'])) {
                    $questions[$sectionIndex][$qIndex]['correct_answer'] = $question['answer'];
                }
                
                // Normalize points field to marks
                if (isset($question['points']) && !isset($question['marks'])) {
                    $questions[$sectionIndex][$qIndex]['marks'] = $question['points'];
                }
            }
        }
        
        return $questions;
    }

    public function updateQuestion(int $sectionIndex, int $questionIndex, string $field, $value): void
    {
        if (!isset($this->questions[$sectionIndex][$questionIndex])) {
            return;
        }

        $this->questions[$sectionIndex][$questionIndex][$field] = $value;
        $this->questions[$sectionIndex][$questionIndex]['is_edited'] = true;
    }

    public function updateOption(int $sectionIndex, int $questionIndex, int $optionIndex, string $value): void
    {
        if (!isset($this->questions[$sectionIndex][$questionIndex]['options'][$optionIndex])) {
            return;
        }

        $this->questions[$sectionIndex][$questionIndex]['options'][$optionIndex] = $value;
        $this->questions[$sectionIndex][$questionIndex]['is_edited'] = true;
    }

    public function removeQuestion(int $sectionIndex, int $questionIndex): void
    {
        if (isset($this->questions[$sectionIndex][$questionIndex])) {
            unset($this->questions[$sectionIndex][$questionIndex]);
            $this->questions[$sectionIndex] = array_values($this->questions[$sectionIndex]);
        }
    }

    public function addManualQuestion(int $sectionIndex): void
    {
        $questionType = $this->sections[$sectionIndex]['question_type'] ?? 'multiple_choice';
        
        $newQuestion = [
            'type' => $questionType,
            'question' => '',
            'marks' => 1,
            'is_edited' => false,
            'manual_entry' => true,
        ];

        if ($questionType === 'multiple_choice') {
            $newQuestion['options'] = ['', '', '', ''];
            $newQuestion['correct_answer'] = '';
        } elseif ($questionType === 'true_false') {
            $newQuestion['options'] = ['True', 'False'];
            $newQuestion['correct_answer'] = '';
        }

        $this->questions[$sectionIndex][] = $newQuestion;
    }

    public function getQuestionsJson(): string
    {
        \Illuminate\Support\Facades\Log::info('QuestionEditor getQuestionsJson', [
            'questions_count' => count($this->questions),
            'questions' => $this->questions,
        ]);
        
        return base64_encode(json_encode($this->questions, JSON_THROW_ON_ERROR));
    }

    public function render()
    {
        return view('livewire.examinations.question-editor');
    }
}
