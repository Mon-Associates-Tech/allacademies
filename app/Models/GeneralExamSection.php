<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralExamSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_exam_id',
        'title',
        'description',
        'instructions',
        'order',
        'time_limit_minutes',
        'source_type',
        'question_type',
        'question_count',
        'academic_group_id',
        'academic_level_id',
        'academic_subject_id',
        'topic_ids',
        'subtopic_ids',
        'total_marks',
        'is_randomized',
    ];

    protected function casts(): array
    {
        return [
            'is_randomized' => 'boolean',
            'topic_ids' => 'array',
            'subtopic_ids' => 'array',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'academic_group_id');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(GeneralExamQuestion::class)->orderBy('order');
    }

    public function recalculateTotalMarks(): void
    {
        $total = $this->questions()->sum('marks');
        $this->update(['total_marks' => $total]);
    }

    public function getQuestionsForDisplay(): \Illuminate\Database\Eloquent\Collection
    {
        $questions = $this->questions;

        if ($this->is_randomized) {
            return $questions->shuffle();
        }

        return $questions;
    }
}
