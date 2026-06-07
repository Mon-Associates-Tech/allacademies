<?php

namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamQuestion extends Model
{
    public const TYPE_MCQ    = 'multiple_choice';
    public const TYPE_TF     = 'true_false';
    public const TYPE_ESSAY  = 'essay';

    protected $fillable = [
        'mock_exam_section_id',
        'source_type',
        'source_id',
        'question_text',
        'options',
        'correct_answer',
        'answer_explanation',
        'answer_keywords',
        'marks',
        'order',
        'difficulty_level',
    ];

    protected function casts(): array
    {
        return [
            'options'          => 'array',
            'answer_keywords'  => 'array',
            'marks'            => 'float',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(MockExamSection::class, 'mock_exam_section_id');
    }

    // ─── Type checks ─────────────────────────────────────────────────────────

    public function isMultipleChoice(): bool { return $this->source_type === self::TYPE_MCQ; }
    public function isTrueFalse(): bool      { return $this->source_type === self::TYPE_TF; }
    public function isEssay(): bool          { return $this->source_type === self::TYPE_ESSAY; }

    public function canAutoGrade(): bool
    {
        return in_array($this->source_type, [self::TYPE_MCQ, self::TYPE_TF]);
    }

    // ─── Grading ─────────────────────────────────────────────────────────────

    /**
     * Grade a participant response.
     *
     * @return array{is_correct: bool|null, points_earned: float, feedback: string|null, requires_review?: bool}
     */
    public function gradeResponse(string $response): array
    {
        return match ($this->source_type) {
            self::TYPE_MCQ   => $this->gradeMultipleChoice($response),
            self::TYPE_TF    => $this->gradeTrueFalse($response),
            self::TYPE_ESSAY => $this->gradeEssay($response),
            default          => ['is_correct' => false, 'points_earned' => 0.0, 'feedback' => null],
        };
    }

    protected function gradeMultipleChoice(string $response): array
    {
        $isCorrect = strtoupper(trim($response)) === strtoupper(trim((string) $this->correct_answer));

        return [
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? (float) $this->marks : 0.0,
            'feedback'      => $isCorrect ? null : "Correct answer: {$this->correct_answer}",
        ];
    }

    protected function gradeTrueFalse(string $response): array
    {
        $trueValues  = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseNorm = strtolower(trim($response));
        $correctNorm  = strtolower(trim((string) $this->correct_answer));

        $responseIsTrue = in_array($responseNorm, $trueValues, true);
        $correctIsTrue  = in_array($correctNorm, $trueValues, true);

        $isCorrect = $responseIsTrue === $correctIsTrue;

        return [
            'is_correct'    => $isCorrect,
            'points_earned' => $isCorrect ? (float) $this->marks : 0.0,
            'feedback'      => $isCorrect ? null : "Correct answer: {$this->correct_answer}",
        ];
    }

    protected function gradeEssay(string $response): array
    {
        $normalizedResponse = strtolower(trim($response));

        // 1. Exact match against model answer
        $modelAnswer = strtolower(trim((string) $this->answer_explanation));
        if ($modelAnswer !== '' && $normalizedResponse === $modelAnswer) {
            return [
                'is_correct'    => true,
                'points_earned' => (float) $this->marks,
                'feedback'      => null,
            ];
        }

        // 2. Keyword matching
        if (! empty($this->answer_keywords)) {
            $matched = 0;
            foreach ($this->answer_keywords as $kw) {
                if (str_contains($normalizedResponse, strtolower(trim((string) $kw)))) {
                    $matched++;
                }
            }
            $total = count($this->answer_keywords);
            $ratio = $total > 0 ? $matched / $total : 0;

            return [
                'is_correct'      => $ratio >= 0.8,
                'points_earned'   => round((float) $this->marks * $ratio, 2),
                'feedback'        => "Matched {$matched} of {$total} expected keywords.",
                'requires_review' => true,
            ];
        }

        // 3. Falls back to manual review
        return [
            'is_correct'      => null,
            'points_earned'   => 0.0,
            'feedback'        => 'Requires manual review',
            'requires_review' => true,
        ];
    }

    // ─── Display helpers ──────────────────────────────────────────────────────

    public function getOptionsForDisplay(): array
    {
        return is_array($this->options) ? $this->options : [];
    }

    public function getTypeLabel(): string
    {
        return match ($this->source_type) {
            self::TYPE_MCQ   => 'Multiple Choice',
            self::TYPE_TF    => 'True / False',
            self::TYPE_ESSAY => 'Essay',
            default          => ucfirst($this->source_type),
        };
    }
}
