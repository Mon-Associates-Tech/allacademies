<?php

namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamProctoringEvent extends Model
{
    protected $fillable = [
        'mock_exam_submission_id',
        'event_type',
        'occurred_at',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'details' => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function submission(): BelongsTo
    {
        return $this->belongsTo(MockExamSubmission::class, 'mock_exam_submission_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForSubmission($query, int $submissionId)
    {
        return $query->where('mock_exam_submission_id', $submissionId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }
}
