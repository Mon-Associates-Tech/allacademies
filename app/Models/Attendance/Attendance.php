<?php

namespace App\Models\Attendance;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'academic_level_id',
        'academic_subject_id',
        'date',
        'session', // e.g. 'morning', 'afternoon'
        'remarks'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}

