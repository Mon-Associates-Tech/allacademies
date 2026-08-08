<?php

namespace App\Observers;

use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;

class TrueOrFalseQuestionObserver
{
    /**
     * Handle the TrueOrFalseQuestion "creating" event.
     */
    public function creating(TrueOrFalseQuestion $question): void
    {
        $this->validateQuestionContent($question);
    }

    /**
     * Handle the TrueOrFalseQuestion "updating" event.
     */
    public function updating(TrueOrFalseQuestion $question): void
    {
        $this->validateQuestionContent($question);
    }

    /**
     * Validate that the question has meaningful content
     */
    private function validateQuestionContent($question): void
    {
        $questionText = $question->question;
        
        // Extract text from the Mark object
        if ($questionText instanceof Mark) {
            $text = $this->extractTextFromMark($questionText);
        } else {
            $text = $questionText;
        }

        // Validate that the question text is not empty or just whitespace
        if (empty(trim($text))) {
            throw new \InvalidArgumentException('Question text cannot be empty or contain only whitespace.');
        }
    }

    /**
     * Extract the best available text from a Mark object
     */
    private function extractTextFromMark(Mark $mark): ?string
    {
        // Check 'down' first (usually contains the main content)
        if (!empty($mark->down) && trim($mark->down) !== '') {
            return $mark->down;
        }
        
        // Then check 'up'
        if (!empty($mark->up) && trim($mark->up) !== '') {
            return $mark->up;
        }
        
        // If neither is meaningful, return null
        return null;
    }
}