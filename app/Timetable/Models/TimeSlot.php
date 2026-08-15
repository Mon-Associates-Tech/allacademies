<?php
// app/Timetable/Models/TimeSlot.php

namespace App\Timetable\Models;

use App\Models\School;
use App\Traits\ActivityLoggable;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use ActivityLoggable, HasFactory, SoftDeletes, Trackable;

    protected $fillable = [
        'school_id', 'label', 'starts_at', 'ends_at', 'order', 'is_break',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId)->orderBy('order');
    }

    /**
     * True if this slot's time range overlaps another slot's time range.
     * Used by ConflictDetectionService for cross-slot overlap checks
     * (e.g. if slots are ever irregular/custom rather than a fixed grid).
     */
    public function overlaps(self $other): bool
    {
        return $this->starts_at < $other->ends_at && $this->ends_at > $other->starts_at;
    }

    public function getActivityCategory(): ?string
    {
        return 'academic';
    }
}
