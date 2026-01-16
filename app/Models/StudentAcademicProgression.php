<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicProgression extends Model
{
    protected $fillable = [
        'student_id',
        'academic_level_id',
        'start_date',
        'end_date',
        'status', // current, completed, promoted, etc.
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }
}
