<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'topic_id',
        'subtopic_id',
        'title',
        'total_score',
        'max_score',
        'percentage_score',
        'start_time',
        'end_time',
        'status', // 'in_progress', 'completed', 'graded' (for essays)
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function topic()
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function subtopic()
    {
        return $this->belongsTo(AcademicSubtopic::class);
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}
