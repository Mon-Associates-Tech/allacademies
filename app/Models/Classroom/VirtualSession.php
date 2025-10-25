<?php

namespace App\Models\Classroom;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\School;
use App\Models\Teacher;
use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualSession extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchoolEnhanced;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'academic_level_id',
        'academic_group_id',
        'academic_subject_id',
        'title',
        'description',
        'type',
        'status',
        'meeting_id',
        'internal_meeting_id',
        'attendee_password',
        'moderator_password',
        'bbb_create_response',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'duration_minutes',
        'allow_guest_login',
        'auto_record',
        'mute_on_start',
        'webcams_only_for_moderator',
        'max_participants',
        'guest_policy',
        'join_url',
        'moderator_url',
        'settings',
        'metadata',
        // Recurring fields
        'is_recurring',
        'recurrence_pattern',
        'recurrence_days',
        'recurrence_interval',
        'recurrence_end_date',
        'recurrence_active',
        'parent_session_id',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'recurrence_end_date' => 'datetime',
        'allow_guest_login' => 'boolean',
        'auto_record' => 'boolean',
        'mute_on_start' => 'boolean',
        'webcams_only_for_moderator' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_active' => 'boolean',
        'settings' => 'array',
        'metadata' => 'array',
        'recurrence_days' => 'array',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(SessionParticipant::class);
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(SessionRecording::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(SessionInvitation::class);
    }

    // Recurring relationships
    public function parentSession(): BelongsTo
    {
        return $this->belongsTo(VirtualSession::class, 'parent_session_id');
    }

    public function childSessions(): HasMany
    {
        return $this->hasMany(VirtualSession::class, 'parent_session_id');
    }

    // Scopes
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_start', '>', now());
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeParentSessions($query)
    {
        return $query->whereNull('parent_session_id');
    }

    public function scopeActiveRecurring($query)
    {
        return $query->where('is_recurring', true)
            ->where('recurrence_active', true);
    }

    // Helper Methods
    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function canStart(): bool
    {
        return $this->isScheduled() &&
            $this->scheduled_start->lte(now()->addMinutes(15));
    }

    public function hasRecordings(): bool
    {
        return $this->recordings()->where('status', 'published')->exists();
    }

    public function getAttendeeCount(): int
    {
        return $this->participants()
            ->where('status', 'joined')
            ->count();
    }

    public function generateMeetingId(): string
    {
        return 'session-' . $this->id . '-' . time();
    }

    public function generatePassword(int $length = 12): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    // Recurring helpers
    public function isParentSession(): bool
    {
        return $this->is_recurring && is_null($this->parent_session_id);
    }

    public function isChildSession(): bool
    {
        return !is_null($this->parent_session_id);
    }

    public function stopRecurrence(): void
    {
        $this->update(['recurrence_active' => false]);

        // Optionally cancel future child sessions
        $this->childSessions()
            ->where('status', 'scheduled')
            ->where('scheduled_start', '>', now())
            ->update(['status' => 'cancelled']);
    }

    public function getRecurrenceSummary(): string
    {
        if (!$this->is_recurring) {
            return 'One-time session';
        }

        $summary = 'Repeats ';

        if ($this->recurrence_interval > 1) {
            $summary .= "every {$this->recurrence_interval} ";
        }

        switch ($this->recurrence_pattern) {
            case 'daily':
                $summary .= $this->recurrence_interval > 1 ? 'days' : 'daily';
                break;
            case 'weekly':
                $summary .= $this->recurrence_interval > 1 ? 'weeks' : 'weekly';
                if ($this->recurrence_days) {
                    $days = collect($this->recurrence_days)->map(function ($day) {
                        return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$day - 1];
                    })->implode(', ');
                    $summary .= " on {$days}";
                }
                break;
            case 'monthly':
                $summary .= $this->recurrence_interval > 1 ? 'months' : 'monthly';
                break;
        }

        if ($this->recurrence_end_date) {
            $summary .= " until " . $this->recurrence_end_date->format('M d, Y');
        } else {
            $summary .= " (no end date)";
        }

        return $summary;
    }
}
