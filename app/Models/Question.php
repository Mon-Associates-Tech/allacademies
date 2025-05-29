<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subtopic_id',
        'question_text',
        'question_type', // 'multiple_choice', 'true_false', 'essay'
        'options',       // JSON array for multiple choice
        'correct_answer',
        'points',
        'difficulty_level', // 'easy', 'medium', 'hard'
    ];
    
    protected $casts = [
        'options' => 'array',
    ];
    
    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class);
    }
    
    public function topic()
    {
        return $this->hasOneThrough(Topic::class, Subtopic::class, 'id', 'id', 'subtopic_id', 'topic_id');
    }
    
    public function subject()
    {
        return $this->hasOneThrough(Subject::class, Topic::class, 'id', 'id', 'topic_id', 'subject_id');
    }
    
    public function assessmentResponses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}