<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardGrade extends Model
{
    protected $fillable = [
        'report_card_id',
        'subject_id',
        'teacher_id',
        'assessments_score',
        'quizzes_score',
        'final_exam_score',
        'total_score',
        'grade_label',
        'remarks',
        'scores',
        'is_locked',
        'last_modified_by',
        'last_modified_at',
    ];

    protected $casts = [
        'scores' => 'array',
        'is_locked' => 'boolean',
        'last_modified_at' => 'datetime',
    ];

    public function reportCard(): BelongsTo
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($this->is_locked) {
            return $user->isSuperAdmin() || $user->hasRole('admin');
        }

        $teacher = $user->teacher;
        if (!$teacher) {
            return $user->isSuperAdmin() || $user->hasRole('admin');
        }

        // Check if teacher teaches this subject
        if ($this->teacher_id === $teacher->id) {
            return true;
        }

        // Check if primary teacher for the student
        $student = $this->reportCard->student;
        if ($student->primary_teacher && $student->primary_teacher->id === $teacher->id) {
            return true;
        }

        return false;
    }

    public function calculateTotal(): float
    {
        if ($this->scores) {
            return array_sum(array_values($this->scores));
        }

        return ($this->assessments_score ?? 0) + ($this->quizzes_score ?? 0) + ($this->final_exam_score ?? 0);
    }

    public function assignGrade($gradeScale = null): void
    {
        $total = $this->calculateTotal();
        
        if (!$gradeScale) {
            $level = $this->reportCard->student->academic_level_id;
            $gradeScale = GradeScale::where('school_id', getSchoolId())
                ->where(function ($q) use ($level) {
                    $q->where('academic_level_id', $level)
                        ->orWhere(function ($q2) {
                            $q2->whereNull('academic_level_id')->where('is_default', true);
                        });
                })
                ->where('min_score', '<=', $total)
                ->where('max_score', '>=', $total)
                ->first();
        }

        if ($gradeScale) {
            $this->grade_label = $gradeScale->letter_grade;
            $this->remarks = $this->remarks ?? $gradeScale->remarks;
        }
    }
}
