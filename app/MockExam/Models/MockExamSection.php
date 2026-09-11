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
        'time_limit_minutes',
        'is_randomized',
        'topic_ids',
        'subtopic_ids',
        'insert_blank_page',
        'blank_page_text',
        'attachment_original_name',
        'attachment_extension',
        'attachment_text',
        'attachment_image_path',
        'attachment_pdf_images',
    ];

    protected function casts(): array
    {
        return [
            'is_randomized'      => 'boolean',
            'marks_per_question' => 'float',
            'topic_ids'          => 'array',
            'subtopic_ids'       => 'array',
            'insert_blank_page'  => 'boolean',
            'blank_page_text'    => 'string',
            'attachment_pdf_images' => 'array',
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

    public function hasTimeLimit(): bool
    {
        return $this->time_limit_minutes !== null && $this->time_limit_minutes > 0;
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

        public function hasAttachment(): bool
    {
        return ! empty($this->attachment_extension);
    }

    public function isTextAttachment(): bool
    {
        return in_array($this->attachment_extension, ['txt', 'docx'], true);
    }

    public function isPdfAttachment(): bool
    {
        return $this->attachment_extension === 'pdf';
    }

    public function isImageAttachment(): bool
    {
        return in_array($this->attachment_extension, ['jpg', 'jpeg', 'png'], true);
    }
}
