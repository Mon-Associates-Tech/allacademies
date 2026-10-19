<?php

namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamSubmissionQuestion extends Model
{
    protected $fillable = [
        'mock_exam_submission_id',
        'mock_exam_section_id',
        'source_question_id',
        'source_type',
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
            'source_question_id' => 'integer',
            'options'            => 'array',
            'answer_keywords'    => 'array',
            'marks'              => 'float',
            'order'              => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function submission(): BelongsTo
    {
        return $this->belongsTo(MockExamSubmission::class, 'mock_exam_submission_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(MockExamSection::class);
    }

    // ─── Type checks ─────────────────────────────────────────────────────────

    public function isMultipleChoice(): bool { return $this->source_type === 'multiple_choice'; }
    public function isTrueFalse(): bool      { return $this->source_type === 'true_false'; }
    public function isEssay(): bool          { return $this->source_type === 'essay'; }

    public function canAutoGrade(): bool
    {
        return in_array($this->source_type, ['multiple_choice', 'true_false']);
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
            'multiple_choice' => $this->gradeMultipleChoice($response),
            'true_false'      => $this->gradeTrueFalse($response),
            'essay'           => $this->gradeEssay($response),
            default           => ['is_correct' => false, 'points_earned' => 0.0, 'feedback' => null],
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

        $modelAnswer = strtolower(trim((string) $this->answer_explanation));
        if ($modelAnswer !== '' && $normalizedResponse === $modelAnswer) {
            return [
                'is_correct'    => true,
                'points_earned' => (float) $this->marks,
                'feedback'      => null,
            ];
        }

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

        return [
            'is_correct'      => null,
            'points_earned'   => 0.0,
            'feedback'        => 'Requires manual review',
            'requires_review' => true,
        ];
    }
}
