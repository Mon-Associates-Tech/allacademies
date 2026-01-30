<?php

namespace App\Models;

use App\Models\AcademicSubject as Subject;
use App\Models\AcademicSubtopic as Subtopic;
use App\Models\AcademicTopic as Topic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionable_id',
        'questionable_type', // 'multiple_choice', 'true_false', 'essay'
        'points',       // JSON array for multiple choice
        'topic_id',
        'subtopic_id',
        'points',
        'difficulty_level', // 'easy', 'medium', 'hard'
        'user_id', // the user who created the question
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

    public function questionable()
    {
        return $this->morphTo();
    }
}
