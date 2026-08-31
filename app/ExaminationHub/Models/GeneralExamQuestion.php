<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExamQuestion extends Model
{
    use HasFactory;

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TRUE_FALSE      = 'true_false';
    public const TYPE_SHORT_ANSWER    = 'short_answer';
    public const TYPE_ESSAY           = 'essay';

    public const DIFFICULTY_EASY   = 'easy';
    public const DIFFICULTY_MEDIUM = 'medium';
    public const DIFFICULTY_HARD   = 'hard';

    protected $fillable = [
        'general_exam_id',
        'general_exam_section_id',
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
        'excluded_from_grading',
        'award_marks_on_exclusion',
        // ── Answer-key traceability ────────────────────────────────────────
        // PK in the source question-bank table (e.g. multiple_choice_questions.id).
        // NULL for AI-generated or manually authored questions.
        'source_question_id',
    ];

    protected function casts(): array
    {
        return [
            // 🌟 CRITICAL FIX: Add the Mark cast to preserve HTML/images
            'question'               => \App\Support\Mark::class,
            'options'            => 'array',
            'keywords'           => 'array',
            'ai_generated'          => 'boolean',
            'is_edited'             => 'boolean',
            'excluded_from_grading'    => 'boolean',
            'award_marks_on_exclusion' => 'boolean',
            'source_question_id'       => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSection::class, 'general_exam_section_id');
    }

    // ─── Type helpers ─────────────────────────────────────────────────────────

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

    // ─── Grading ─────────────────────────────────────────────────────────────

    /**
     * Grade a response for this question.
     *
     * @return array{is_correct: bool|null, points_earned: float, feedback: string|null}
     */
    public function gradeResponse(string $response): array
    {
        return match ($this->type) {
            self::TYPE_MULTIPLE_CHOICE => $this->gradeMultipleChoice($response),
            self::TYPE_TRUE_FALSE      => $this->gradeTrueFalse($response),
            self::TYPE_SHORT_ANSWER    => $this->gradeShortAnswer($response),
            self::TYPE_ESSAY           => $this->gradeEssay($response),
            default                    => ['is_correct' => false, 'points_earned' => 0, 'feedback' => null],
        };
    }

    protected function gradeMultipleChoice(string $response): array
    {
        $normalizedResponse = strtoupper(trim($response));
        $normalizedCorrect  = strtoupper(trim($this->correct_answer));
        $isCorrect          = $normalizedResponse === $normalizedCorrect;

        return [
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? $this->marks : 0,
            'feedback'      => $isCorrect ? null : "The correct answer is: {$this->correct_answer}",
        ];
    }

    protected function gradeTrueFalse(string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect  = strtolower(trim($this->correct_answer));

        $trueValues  = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseIsTrue  = in_array($normalizedResponse, $trueValues);
        $responseIsFalse = in_array($normalizedResponse, $falseValues);
        $correctIsTrue   = in_array($normalizedCorrect, $trueValues);

        $isCorrect = ($responseIsTrue && $correctIsTrue)
                  || ($responseIsFalse && ! $correctIsTrue);

        return [
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? $this->marks : 0,
            'feedback'      => $isCorrect ? null : "The correct answer is: {$this->correct_answer}",
        ];
    }

    protected function gradeShortAnswer(string $response): array
    {
        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect  = strtolower(trim($this->correct_answer ?? ''));

        if ($normalizedResponse === $normalizedCorrect) {
            return [
                'is_correct'    => true,
                'points_earned' => $this->marks,
                'feedback'      => null,
            ];
        }

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
                'is_correct'      => $keywordRatio >= 0.8,
                'points_earned'   => $pointsEarned,
                'feedback'        => "Matched {$matchedKeywords} of " . count($this->keywords) . ' expected keywords.',
                'requires_review' => true,
            ];
        }

        return [
            'is_correct'      => null,
            'points_earned'   => 0,
            'feedback'        => 'Requires manual review',
            'requires_review' => true,
        ];
    }

    protected function gradeEssay(string $response): array
    {
        return [
            'is_correct'      => null,
            'points_earned'   => 0,
            'feedback'        => 'Requires grading',
            'requires_review' => true,
        ];
    }

    // ─── Display helpers ──────────────────────────────────────────────────────

    /**
     * Returns options as an associative array: ['A' => 'text', 'B' => 'text', ...]
     */
    public function getOptionsForDisplay(): array
    {
        if (! $this->isMultipleChoice() || empty($this->options)) {
            return [];
        }

        // Current format: [{key: 'A', value: '...'}, ...]
        if (isset($this->options[0]) && is_array($this->options[0]) && isset($this->options[0]['key'])) {
            return collect($this->options)->pluck('value', 'key')->toArray();
        }

        // Legacy format: ['text1', 'text2', ...] — convert to keyed array
        $keyed = [];
        foreach ($this->options as $index => $value) {
            $keyed[chr(65 + $index)] = $value;
        }

        return $keyed;
    }

    public function getFormattedQuestion(): string
    {
        return nl2br(e($this->question));
    }

    // ─── Source traceability ──────────────────────────────────────────────────

    /**
     * Whether this question was pulled from the question bank
     * (as opposed to AI-generated or manually authored inline).
     */
    public function hasSourceQuestion(): bool
    {
        return $this->source_question_id !== null;
    }

    public function isExcludedFromGrading(): bool
    {
        return (bool) $this->excluded_from_grading;
    }
}
