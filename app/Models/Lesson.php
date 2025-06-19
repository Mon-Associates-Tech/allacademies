<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'academic_subject_id',
        'title',
        'description',
        'student_group_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        dd('i am called');
        return $this->belongsTo(AcademicSubject::class, 'subject_id', 'id');
    }

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function notes()
    {
        return $this->hasMany(LessonNote::class);
    }
}
