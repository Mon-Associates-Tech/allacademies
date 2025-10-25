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
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'allow_guest_login' => 'boolean',
        'auto_record' => 'boolean',
        'mute_on_start' => 'boolean',
        'webcams_only_for_moderator' => 'boolean',
        'settings' => 'array',
        'metadata' => 'array',
    ];

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
}
