<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
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
    
    // Scope for student's events
    public function scopeForStudent($query, $studentId)
    {
        return $query->where(function($q) use ($studentId) {
            // Individual activities
            $q->where('created_by', $studentId)
              ->where('is_group_activity', false);
        })->orWhere(function($q) use ($studentId) {
            // Group activities - student is member of group
            $q->where('is_group_activity', true)
              ->whereHas('group.students', function($sq) use ($studentId) {
                  $sq->where('student_id', $studentId);
              });
        })->orWhere(function($q) use ($studentId) {
            // Activities where student is a participant
            $q->whereHas('participants', function($sq) use ($studentId) {
                $sq->where('user_id', $studentId);
            });
        });
    }
}