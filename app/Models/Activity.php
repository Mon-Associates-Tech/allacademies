<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'academic_activities';

    protected $fillable = [
        'title',
        'description',
        'activity_type', // 'assessment', 'book_reading', 'group_meeting', 'quiz', 'exam', etc.
        'subject_id',
        'start_time',
        'end_time',
        'location',
        'status', // 'scheduled', 'in_progress', 'completed', 'cancelled'
        'is_group_activity',
        'group_id',
        'created_by',
        'metadata', // JSON field for additional type-specific data
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_group_activity' => 'boolean',
        'metadata' => 'array',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function group()
    {
        return $this->belongsTo(StudentGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'activity_participants')
                    ->withPivot('status', 'score', 'attendance')
                    ->withTimestamps();
    }

    // Scope for upcoming events (activities in the future)
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                     ->where('status', '!=', 'cancelled')
                     ->orderBy('start_time');
    }

    // Scope for past events
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now())
                     ->orderBy('end_time', 'desc');
    }

    // Scope for ongoing events
    public function scopeOngoing($query)
    {
        return $query->where('start_time', '<=', now())
                     ->where('end_time', '>=', now())
                     ->where('status', '!=', 'cancelled');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where(function($q) use ($studentId) {
            // Individual activities created by the student
            $q->where(function($subQ) use ($studentId) {
                $subQ->where('created_by', $studentId)
                     ->where('is_group_activity', false);
            })
            // OR Group activities where student is in the group
            ->orWhere(function($subQ) use ($studentId) {
                $subQ->where('is_group_activity', true)
                     ->whereExists(function($existsQ) use ($studentId) {
                         $existsQ->select(\DB::raw(1))
                                 ->from('student_groups')
                                 ->whereColumn('academic_activities.group_id', 'student_groups.id')
                                 ->whereExists(function($innerQ) use ($studentId) {
                                     $innerQ->select(\DB::raw(1))
                                            ->from('students')
                                            ->whereColumn('student_groups.id', 'students.student_group_id')
                                            ->where('students.id', $studentId); // or 'user_id' depending on your schema
                                 });
                     });
            })
            // OR Activities where student is a direct participant
            ->orWhereExists(function($existsQ) use ($studentId) {
                $existsQ->select(\DB::raw(1))
                        ->from('activity_participants')
                        ->whereColumn('academic_activities.id', 'activity_participants.activity_id')
                        ->where('activity_participants.user_id', $studentId);
            });
        });
    }
}
