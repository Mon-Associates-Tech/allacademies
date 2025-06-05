<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'student_group_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    public function borrowedBooks()
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student')->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * The books that this student has access to.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->withPivot('access_granted_at', 'access_expires_at')
            ->withTimestamps();
    }


}
