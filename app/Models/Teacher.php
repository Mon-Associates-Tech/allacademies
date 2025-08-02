<?php

namespace App\Models;

use App\Traits\HasStudents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;

class Teacher extends Model
{
    use HasFactory;
    use HasStudents;

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
        return $this->belongsToMany(Student::class, 'teacher_student')
        ->withTimestamps()
        ->withPivot('is_primary', 'notes');
    }

    public function primaryStudents()
    {
        return $this->belongsToMany(Student::class, 'teacher_student')
        ->withTimestamps()
        ->withPivot('is_primary', 'notes')
        ->wherePivot('is_primary', true);
    }

    public function secondaryStudents()
    {
        return $this->belongsToMany(Student::class, 'teacher_student')
        ->withTimestamps()
        ->withPivot('is_primary', 'notes')
        ->wherePivot('is_primary', false);
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

    public function academicLevels(): BelongsToMany
    {
        return $this->belongsToMany(AcademicLevel::class, 'academic_level_teacher', 'teacher_id', 'academic_level_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function academicGroups(): BelongsToMany
    {
        return $this->belongsToMany(AcademicGroup::class, 'academic_group_teacher', 'teacher_id', 'academic_group_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    // Helper methods for primary relationships
    public function primaryAcademicLevels(): BelongsToMany
    {
        return $this->academicLevels()->wherePivot('is_primary', true);
    }

    public function primaryAcademicGroups(): BelongsToMany
    {
        return $this->academicGroups()->wherePivot('is_primary', true);
    }

    public function academicSubjects()
    {
        return $this->belongsToMany(AcademicSubject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function assignments(){
        return $this->hasMany(Assignment::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'teacher_book', 'teacher_id', 'book_id');
    }

}
