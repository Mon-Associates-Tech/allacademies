<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCardGrade extends Model
{
    protected $fillable = [
        'report_card_id',
        'subject_id',
        'assessments_score',
        'quizzes_score',
        'final_exam_score',
        'total_score',
        'grade_label',
        'remarks',
    ];

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject()
    {
        return $this->belongsTo(AcademicSubject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
