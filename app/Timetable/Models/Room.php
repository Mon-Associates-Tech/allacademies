<?php
// app/Timetable/Models/Room.php

namespace App\Timetable\Models;

use App\Models\School;
use App\Traits\ActivityLoggable;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use ActivityLoggable, HasFactory, SoftDeletes, Trackable;

    protected $fillable = [
        'school_id', 'name', 'type', 'capacity', 'is_active', 'added_by', 'modified_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
        return $query->where('school_id', $schoolId);
    }

    public function getActivityCategory(): ?string
    {
        return 'academic';
    }
}
