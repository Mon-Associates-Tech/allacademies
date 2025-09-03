<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatGroup extends Model
{
    use BelongsToSchoolEnhanced;

    protected $fillable = [
        'name', 'description', 'type', 'school_id', 'created_by',
        'academic_level_id', 'academic_group_id', 'is_private',
        'is_active', 'settings'
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    protected $with = ['creator'];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_group_members')
            ->withPivot(['role', 'can_add_members', 'can_remove_members', 'joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function allMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_group_members')
            ->withPivot(['role', 'can_add_members', 'can_remove_members', 'joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->where('is_deleted', false);
    }

    public function allMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'admin');
    }

    public function moderators(): BelongsToMany
    {
        return $this->members()->whereIn('role', ['admin', 'moderator']);
    }

    // Helper methods
    public function addMember(User $user, string $role = 'member', bool $canAddMembers = false): void
    {
        $this->members()->attach($user->id, [
            'role' => $role,
            'can_add_members' => $canAddMembers,
            'can_remove_members' => in_array($role, ['admin', 'moderator']),
            'joined_at' => now(),
            'is_active' => true
        ]);
    }

    public function removeMember(User $user): void
    {
        $this->members()->updateExistingPivot($user->id, [
            'is_active' => false
        ]);
    }

    public function isUserMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function canUserAddMembers(User $user): bool
    {
        $membership = $this->allMembers()
            ->where('user_id', $user->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$membership) return false;

        return $membership->pivot->can_add_members ||
            in_array($membership->pivot->role, ['admin', 'moderator']) ||
            $user->id === $this->created_by;
    }

    public function getLastMessage(): ?ChatMessage
    {
        return $this->messages()->latest()->first();
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function getUnreadCount(User $user): int
    {
        $lastReadTime = $this->allMembers()
            ->where('user_id', $user->id)
            ->first()?->pivot?->last_read_at;

        if (!$lastReadTime) {
            return $this->messages()->count();
        }

        return $this->messages()->where('created_at', '>', $lastReadTime)->count();
    }

    public function markAsRead(User $user): void
    {
        $this->allMembers()->updateExistingPivot($user->id, [
            'last_read_at' => now()
        ]);
    }

    // Auto-populate members based on academic criteria
    public function populateAcademicMembers(): void
    {
        if ($this->type === 'academic_level' && $this->academic_level_id) {
            $students = Student::where('academic_level_id', $this->academic_level_id)
                ->where('school_id', $this->school_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($students as $student) {
                if ($student->user && !$this->isUserMember($student->user)) {
                    $this->addMember($student->user);
                }
            }

            // Add teachers for this level
            $teachers = Teacher::whereHas('academicLevels', function ($query) {
                $query->where('academic_level_id', $this->academic_level_id);
            })->where('school_id', $this->school_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($teachers as $teacher) {
                if ($teacher->user && !$this->isUserMember($teacher->user)) {
                    $this->addMember($teacher->user, 'moderator', true);
                }
            }
        }

        if ($this->type === 'academic_group' && $this->academic_group_id) {
            $students = Student::whereHas('academicLevel', function ($query) {
                $query->where('academic_group_id', $this->academic_group_id);
            })->where('school_id', $this->school_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($students as $student) {
                if ($student->user && !$this->isUserMember($student->user)) {
                    $this->addMember($student->user);
                }
            }
        }
    }

    // Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('members', function ($q) use ($user) {
            $q->where('users.id', $user->id) // Properly qualified
            ->where('chat_group_members.is_active', true); // Properly qualified
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
