<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentGroups()
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function studentsFromGroups()
    {
        return $this->hasManyThrough(Student::class, StudentGroup::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function lessonNotes()
    {
        return $this->hasMany(LessonNote::class);
    }

    public function subjects(){
        return $this->belongsToMany(AcademicSubject::class, 'subject_teacher', 'teacher_id', 'subject_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(GroupBookSubscription::class);
    }
}
