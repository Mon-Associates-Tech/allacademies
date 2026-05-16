<?php

namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MockExamSection extends Model
{
    protected $fillable = [
        'mock_exam_subject_exam_id',
        'title',
        'instructions',
        'order',
        'question_type',
        'question_count',
        'marks_per_question',
        'is_randomized',
    ];

    protected function casts(): array
    {
        return [
            'is_randomized'      => 'boolean',
            'marks_per_question' => 'float',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function subjectExam(): BelongsTo
    {
        return $this->belongsTo(MockExamSubjectExam::class, 'mock_exam_subject_exam_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(MockExamQuestion::class)->orderBy('order');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isMixed(): bool
    {
        return $this->question_type === 'mixed';
    }

    public function getTotalMarks(): float
    {
        return (float) $this->questions()->sum('marks');
    }

    /** Return questions, shuffled per-submission if needed. */
    public function getQuestionsForParticipant(?array $storedOrder = null): \Illuminate\Support\Collection
    {
        $questions = $this->questions;

        if ($this->is_randomized) {
            if ($storedOrder) {
                return collect($storedOrder)
                    ->map(fn ($id) => $questions->firstWhere('id', $id))
                    ->filter();
            }

            return $questions->shuffle();
        }

        return $questions;
    }
}
