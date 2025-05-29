<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
