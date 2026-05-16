<?php

namespace App\MockExam\Models;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MockExamSubjectExam extends Model
{
    protected $fillable = [
        'mock_exam_id',
        'academic_group_id',
        'academic_level_id',
        'academic_subject_id',
        'title',
        'instructions',
        'order',
        'duration_in_minutes',
        'topic_ids',
        'subtopic_ids',
    ];

    protected function casts(): array
    {
        return [
            'topic_ids'    => 'array',
            'subtopic_ids' => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function mockExam(): BelongsTo
    {
        return $this->belongsTo(MockExam::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(MockExamSection::class)->orderBy('order');
    }

    // ─── Aggregates ───────────────────────────────────────────────────────────

    public function getDisplayTitle(): string
    {
        return $this->title ?? $this->academicSubject?->name ?? 'Subject Exam';
    }

    public function getTotalMarks(): float
    {
        return (float) $this->sections->sum(fn ($s) => $s->questions->sum('marks'));
    }

    public function getTotalQuestions(): int
    {
        return $this->sections->sum(fn ($s) => $s->questions->count());
    }
}
