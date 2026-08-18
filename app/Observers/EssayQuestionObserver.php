<?php

namespace App\Observers;

use App\Jobs\ExaminationHub\SyncSourceQuestionJob;
use App\Models\EssayQuestion;
use App\Support\Mark;

class EssayQuestionObserver
{
    /**
     * Handle the EssayQuestion "creating" event.
     */
    public function creating(EssayQuestion $question): void
    {
        $this->validateQuestionContent($question);
    }

    /**
     * Handle the EssayQuestion "updating" event.
     */
    public function updating(EssayQuestion $question): void
    {
        $this->validateQuestionContent($question);
    }

    public function updated(EssayQuestion $question): void
    {
        SyncSourceQuestionJob::dispatch('essay', $question->id);
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