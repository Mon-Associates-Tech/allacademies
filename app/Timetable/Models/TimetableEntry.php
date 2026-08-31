<?php
// app/Timetable/Models/TimetableEntry.php

namespace App\Timetable\Models;

use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicSubject;
use App\Models\School;
use App\Models\Teacher;
use App\Traits\ActivityLoggable;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableEntry extends Model
{
    use ActivityLoggable, HasFactory, SoftDeletes, Trackable;

    protected $fillable = [
        'school_id', 'academic_period_id', 'academic_level_id', 'academic_subject_id',
        'teacher_id', 'room_id', 'time_slot_id', 'day_of_week',
        'added_by', 'modified_by',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeForPeriod($query, $academicPeriodId)
    {
        return $query->where('academic_period_id', $academicPeriodId);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForClass($query, $academicLevelId)
    {
        return $query->where('academic_level_id', $academicLevelId);
    }

    public function getActivityCategory(): ?string
    {
        return 'academic';
    }

    public function getActivityMetadata(): array
    {
        return [
            'academic_level_id' => $this->academic_level_id,
            'academic_period_id' => $this->academic_period_id,
            'teacher_id' => $this->teacher_id,
            'room_id' => $this->room_id,
            'time_slot_id' => $this->time_slot_id,
            'day_of_week' => $this->day_of_week,
        ];
    }
}
