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

    public function assignedStudents()
    {
        return $this->belongsToMany(Student::class, 'teacher_student')->withTimestamps();
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

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicLevels(){
        return $this->belongsToMany(AcademicLevel::class, 'teacher_academic_level', 'teacher_id', 'academic_level_id');
    }

    public function academicGroups()
    {
        return $this->belongsToMany(AcademicGroup::class, 'teacher_academic_group', 'teacher_id', 'academic_group_id');
    }

    public function academicSubjects()
    {
        return $this->belongsToMany(AcademicSubject::class, 'teacher_academic_subject', 'teacher_id', 'academic_subject_id');
    }

    public function assignments(){
        return $this->hasMany(Assignment::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'teacher_book', 'teacher_id', 'book_id');
    }
}
