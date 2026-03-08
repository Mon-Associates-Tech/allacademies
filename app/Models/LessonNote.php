<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'lesson_id',
        'subject_id',
        'topic_id',
        'title',
        'content',
        'file_path',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
