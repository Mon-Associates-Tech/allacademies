<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TodoShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'shared_with_user_id',
        'shareable_type',
        'shareable_id',
        'share_type',
        'can_edit',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
    ];

    /**
     * Share type options
     */
    public const SHARE_TYPES = [
        'individual',
        'academic_group',
        'academic_level',
        'student_group',
        'school_wide',
    ];

    /**
     * Get the todo that is shared
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    /**
     * Get the user this todo is shared with (for individual shares)
     */
    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    /**
     * Get the shareable model (AcademicGroup, AcademicLevel, StudentGroup, etc.)
     */
    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the display name for the share
     */
    public function getShareDisplayNameAttribute(): string
    {
        if ($this->share_type === 'individual' && $this->sharedWithUser) {
            return $this->sharedWithUser->name;
        }

        if ($this->shareable) {
            return match ($this->share_type) {
                'academic_group' => "Group: {$this->shareable->name}",
                'academic_level' => "Level: {$this->shareable->name}",
                'student_group' => "Student Group: {$this->shareable->name}",
                'school_wide' => 'Entire School',
                default => 'Unknown',
            };
        }

        return match ($this->share_type) {
            'school_wide' => 'Entire School',
            default => 'Unknown',
        };
    }

    /**
     * Scope to get individual shares
     */
    public function scopeIndividual($query)
    {
        return $query->where('share_type', 'individual');
    }

    /**
     * Scope to get group-based shares
     */
    public function scopeGroupBased($query)
    {
        return $query->whereIn('share_type', ['academic_group', 'academic_level', 'student_group', 'school_wide']);
    }

    /**
     * Scope to filter by share type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('share_type', $type);
    }
}
