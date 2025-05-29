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
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class);
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}

class AssessmentResponse extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',
        'response',
        'score',
        'max_score',
        'is_correct',
        'feedback',
    ];

    protected $casts = [
        'response' => 'array', // For multiple choice selections
        'is_correct' => 'boolean',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
