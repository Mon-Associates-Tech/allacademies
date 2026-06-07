<?php

namespace App\MockExam\Models;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockExamTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'academic_group_id',
        'academic_level_id',
        'academic_subject_id',
        'name',
        'description',
        'is_active',
        'default_duration_minutes',
        'topic_ids',
        'subtopic_ids',
        'sections_config',
    ];

    protected function casts(): array
    {
        return [
            'is_active'                => 'boolean',
            'topic_ids'                => 'array',
            'subtopic_ids'             => 'array',
            'sections_config'          => 'array',
            'default_duration_minutes' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope to only return active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter templates by academic subject.
     */
    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('academic_subject_id', $subjectId);
    }

    /**
     * Scope to filter templates by academic level.
     */
    public function scopeForLevel($query, int $levelId)
    {
        return $query->where('academic_level_id', $levelId);
    }

    /**
     * Scope to filter templates by academic group.
     */
    public function scopeForGroup($query, int $groupId)
    {
        return $query->where('academic_group_id', $groupId);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Get the display name for this template.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? ($this->academicSubject?->name . ' Template');
    }

    /**
     * Check if this template has topic filters configured.
     */
    public function hasTopicFilters(): bool
    {
        return !empty($this->topic_ids);
    }

    /**
     * Check if this template has subtopic filters configured.
     */
    public function hasSubtopicFilters(): bool
    {
        return !empty($this->subtopic_ids);
    }

    /**
     * Get the sections configuration as an array.
     * 
     * @return array Array of section configuration objects
     */
    public function getSectionsConfig(): array
    {
        return $this->sections_config ?? [];
    }

    /**
     * Get the total number of questions across all sections in this template.
     */
    public function getTotalQuestions(): int
    {
        return collect($this->getSectionsConfig())
            ->sum(fn ($section) => (int) ($section['question_count'] ?? 0));
    }

    /**
     * Get the total marks across all sections in this template.
     */
    public function getTotalMarks(): float
    {
        return collect($this->getSectionsConfig())
            ->sum(function ($section) {
                $count = (int) ($section['question_count'] ?? 0);
                $marks = (float) ($section['marks_per_question'] ?? 1.0);
                return $count * $marks;
            });
    }

    /**
     * Check if this template is applicable to a given subject.
     */
    public function appliesToSubject(int $subjectId): bool
    {
        return $this->academic_subject_id === $subjectId;
    }

    /**
     * Convert template to payload format compatible with MockExamCreationService.
     * 
     * @return array Payload for creating a subject exam
     */
    public function toSubjectExamPayload(): array
    {
        return [
            'academic_group_id'   => $this->academic_group_id,
            'academic_level_id'   => $this->academic_level_id,
            'academic_subject_id' => $this->academic_subject_id,
            'title'               => null, // Will be set by caller
            'instructions'        => null, // Will be set by caller
            'duration_in_minutes' => $this->default_duration_minutes,
            'topic_ids'           => $this->topic_ids ?? [],
            'subtopic_ids'        => $this->subtopic_ids ?? [],
            'sections'            => $this->getSectionsConfig(),
        ];
    }
}
