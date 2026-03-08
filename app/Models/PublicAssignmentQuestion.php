<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicAssignmentQuestion extends Model
{
    use HasFactory;

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    public const TYPE_TRUE_FALSE = 'true_false';

    public const TYPE_SHORT_ANSWER = 'short_answer';

    public const TYPE_ESSAY = 'essay';

    public const DIFFICULTY_EASY = 'easy';

    public const DIFFICULTY_MEDIUM = 'medium';

    public const DIFFICULTY_HARD = 'hard';

    protected $fillable = [
        'public_assignment_id',
        'public_assignment_section_id',
        'type',
        'question',
        'explanation',
        'options',
        'correct_answer',
        'grading_rubric',
        'keywords',
        'marks',
        'difficulty',
        'order',
        'ai_generated',
        'is_edited',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'keywords' => 'array',
            'ai_generated' => 'boolean',
            'is_edited' => 'boolean',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PublicAssignment::class, 'public_assignment_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PublicAssignmentSection::class, 'public_assignment_section_id');
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === self::TYPE_MULTIPLE_CHOICE;
    }

    public function isTrueFalse(): bool
    {
        return $this->type === self::TYPE_TRUE_FALSE;
    }

    public function isShortAnswer(): bool
    {
        return $this->type === self::TYPE_SHORT_ANSWER;
    }

    public function isEssay(): bool
    {
        return $this->type === self::TYPE_ESSAY;
    }

    public function requiresManualGrading(): bool
    {
        return $this->isEssay() || $this->isShortAnswer();
    }

    public function canAutoGrade(): bool
    {
        return $this->isMultipleChoice() || $this->isTrueFalse();
    }

    /**
     * Grade a response for this question
     *
     * @return array{is_correct: bool, points_earned: float, feedback: string|null}
     */
    public function gradeResponse(string $response): array
    {
        return match ($this->type) {
            self::TYPE_MULTIPLE_CHOICE => $this->gradeMultipleChoice($response),
            self::TYPE_TRUE_FALSE => $this->gradeTrueFalse($response),
            self::TYPE_SHORT_ANSWER => $this->gradeShortAnswer($response),
            self::TYPE_ESSAY => $this->gradeEssay($response),
            default => ['is_correct' => false, 'points_earned' => 0, 'feedback' => null],
        };
    }

    protected function gradeMultipleChoice(string $response): array
    {
        $normalizedResponse = strtoupper(trim($response));
        $normalizedCorrect = strtoupper(trim($this->correct_answer));

        $isCorrect = $normalizedResponse === $normalizedCorrect;

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $isCorrect ? $this->marks : 0,
            'feedback' => $isCorrect ? null : "The correct answer is: {$this->correct_answer}",
        ];
    }

    protected function gradeTrueFalse(string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect = strtolower(trim($this->correct_answer));

        // Normalize various true/false representations
        $trueValues = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseIsTrue = in_array($normalizedResponse, $trueValues);
        $responseIsFalse = in_array($normalizedResponse, $falseValues);
        $correctIsTrue = in_array($normalizedCorrect, $trueValues);

        $isCorrect = ($responseIsTrue && $correctIsTrue) || ($responseIsFalse && ! $correctIsTrue);

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $isCorrect ? $this->marks : 0,
            'feedback' => $isCorrect ? null : "The correct answer is: {$this->correct_answer}",
        ];
    }

    protected function gradeShortAnswer(string $response): array
    {
        // Basic keyword matching for short answers
        // This will be enhanced by AI grading service
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect = strtolower(trim($this->correct_answer ?? ''));

        // Check for exact match first
        if ($normalizedResponse === $normalizedCorrect) {
            return [
                'is_correct' => true,
                'points_earned' => $this->marks,
                'feedback' => null,
            ];
        }

        // Check keywords if available
        if (! empty($this->keywords)) {
            $matchedKeywords = 0;
            foreach ($this->keywords as $keyword) {
                if (str_contains($normalizedResponse, strtolower($keyword))) {
                    $matchedKeywords++;
                }
            }

            $keywordRatio = $matchedKeywords / count($this->keywords);
            $pointsEarned = round($this->marks * $keywordRatio, 2);

            return [
                'is_correct' => $keywordRatio >= 0.8,
                'points_earned' => $pointsEarned,
                'feedback' => "Matched {$matchedKeywords} of ".count($this->keywords).' expected keywords.',
                'requires_review' => true,
            ];
        }

        // Mark for manual/AI review
        return [
            'is_correct' => null,
            'points_earned' => 0,
            'feedback' => 'Requires manual review',
            'requires_review' => true,
        ];
    }

    protected function gradeEssay(string $response): array
    {
        // Essays always require AI or manual grading
        return [
            'is_correct' => null,
            'points_earned' => 0,
            'feedback' => 'Requires grading',
            'requires_review' => true,
        ];
    }

    public function getOptionsForDisplay(): array
    {
        if (! $this->isMultipleChoice() || empty($this->options)) {
            return [];
        }

        return $this->options;
    }

    public function getFormattedQuestion(): string
    {
        return nl2br(e($this->question));
    }
}
