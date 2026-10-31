<?php

namespace App\ExaminationHub\Models;

use App\ExaminationHub\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAnswer extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'general_exam_submission_answers';

    protected $fillable = [
        'uuid',
        'general_exam_submission_id',
        'general_exam_question_id',
        'question_uuid',
        'response',
        'answered_at',
        'is_correct',
        'points_earned',
        'graded_revision',
    ];

    protected function casts(): array
    {
        return [
            'answered_at'   => 'datetime',
            'is_correct'    => 'boolean',
            'points_earned' => 'decimal:2',
            'graded_revision' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function submission(): BelongsTo
    {
        return $this->belongsTo(GeneralExamSubmission::class, 'general_exam_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(GeneralExamQuestion::class, 'general_exam_question_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForSubmission($query, GeneralExamSubmission $submission)
    {
        return $query->where('general_exam_submission_id', $submission->id);
    }

    public function scopeUngraded($query)
    {
        return $query->whereNull('graded_revision');
    }

    public function scopeGradedByRevision($query, int $revision)
    {
        return $query->where('graded_revision', $revision);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Check if this answer has been graded.
     */
    public function isGraded(): bool
    {
        return $this->graded_revision !== null;
    }

    /**
     * Upsert an answer for a submission + question combination.
     */
    public static function upsertAnswer(
        GeneralExamSubmission $submission,
        GeneralExamQuestion $question,
        string $response,
        ?\Illuminate\Support\Carbon $answeredAt = null
    ): self {
        return static::updateOrCreate(
            [
                'general_exam_submission_id' => $submission->id,
                'general_exam_question_id'   => $question->id,
            ],
            [
                'uuid'          => static::findByUuid($question->uuid)?->uuid ?? (string) \Illuminate\Support\Str::ulid(),
                'question_uuid' => $question->uuid,
                'response'      => strtoupper(trim($response)),
                'answered_at'   => $answeredAt ?? now(),
            ]
        );
    }
}
