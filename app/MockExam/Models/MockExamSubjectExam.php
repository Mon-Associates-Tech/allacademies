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
        'template_id',
        'academic_group_id',
        'academic_level_id',
        'academic_subject_id',
        'title',
        'instructions',
        'order',
        'duration_in_minutes',
        'topic_ids',
        'subtopic_ids',
        'layout_config',
        'uses_random_questions',
    ];

    protected function casts(): array
    {
        return [
            'topic_ids'             => 'array',
            'subtopic_ids'          => 'array',
            'layout_config'         => 'array',
            'uses_random_questions' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function mockExam(): BelongsTo
    {
        return $this->belongsTo(MockExam::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MockExamTemplate::class, 'template_id');
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

    public function submissionQuestions(): HasMany
    {
        return $this->hasMany(MockExamSubmissionQuestion::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function usesRandomQuestions(): bool
    {
        return (bool) $this->uses_random_questions;
    }

    // ─── Aggregates ───────────────────────────────────────────────────────────

    public function getDisplayTitle(): string
    {
        // If a custom title is set and it differs from the template name, use it
        if (!empty($this->title)) {
            // If the title matches the template name, prefer the academic subject name
            if ($this->template !== null && $this->title === $this->template->name) {
                return $this->academicSubject?->name ?? 'Subject Exam';
            }
            return $this->title;
        }
        return $this->academicSubject?->name ?? 'Subject Exam';
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