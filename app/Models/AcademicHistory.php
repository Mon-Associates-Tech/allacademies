<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'type',
        'recorded_by_id',
        'recorded_date',
        'academic_period',
        'achievement_score',
        'notes',
        'supporting_documents'
    ];

    protected $casts = [
        'recorded_date' => 'datetime',
        'supporting_documents' => 'array'
    ];

    // Relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relationship with User (who recorded the history)
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
