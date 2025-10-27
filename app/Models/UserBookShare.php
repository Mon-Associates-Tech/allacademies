<?php

namespace App\Models;

use App\Mail\BookShareResponded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class UserBookShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_book_id',
        'shared_by_user_id',
        'shared_to_user_id',
        'shared_to_email',
        'academic_group_id',
        'academic_level_id',
        'student_group_id',
        'share_type',
        'status',
        'accepted_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function studentGroup(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class);
    }

    /**
     * Get all users who have access to this share
     */
    public function getAffectedUsers()
    {
        return match($this->share_type) {
            'individual' => collect([$this->sharedTo]),
            'academic_group' => $this->attributes['academic_group_id']
                ? AcademicGroup::find($this->attributes['academic_group_id'])?->students()->with('user')->get()->pluck('user') ?? collect([])
                : collect([]),
            'academic_level' => $this->attributes['academic_level_id']
                ? AcademicLevel::find($this->attributes['academic_level_id'])?->students()->with('user')->get()->pluck('user') ?? collect([])
                : collect([]),
            'student_group' => $this->attributes['student_group_id']
                ? StudentGroup::find($this->attributes['student_group_id'])?->students()->with('user')->get()->pluck('user') ?? collect([])
                : collect([]),
            default => collect([]),
        };
    }

    /**
     * Get count of affected users
     */
    public function getAffectedUsersCount(): int
    {
        return match($this->share_type) {
            'individual' => 1,
            'academic_group' => $this->academicGroup?->students()->count() ?? 0,
            'academic_level' => $this->academicLevel?->students()->count() ?? 0,
            'student_group' => $this->studentGroup?->students()->count() ?? 0,
            default => 0,
        };
    }

    /**
     * Check if a specific user has access through this share
     */
    public function userHasAccess(User $user): bool
    {
        if ($this->status !== 'accepted') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return match($this->share_type) {
            'individual' => $this->shared_to_user_id === $user->id,
            'academic_group' => $user->student?->academic_group_id === $this->academic_group_id,
            'academic_level' => $user->student?->academic_level_id === $this->academic_level_id,
            'student_group' => $user->student?->student_group_id === $this->student_group_id,
            default => false,
        };
    }

    /**
     * Get display name for the share target
     */
    public function getShareTargetName(): string
    {
        return match($this->share_type) {
            'individual' => $this->sharedTo?->name ?? $this->shared_to_email,
            'academic_group' => $this->academicGroup?->name ?? 'Unknown Group',
            'academic_level' => $this->academicLevel?->name ?? 'Unknown Level',
            'student_group' => $this->studentGroup?->name ?? 'Unknown Student Group',
            default => 'Unknown',
        };
    }

    /**
     * Scope for active shares (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'accepted')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for shares accessible by a specific user
     */
    public function scopeForUser($query, User $user)
    {
        $studentId = $user->student?->id;
        $academicGroupId = $user->student?->academic_group_id;
        $academicLevelId = $user->student?->academic_level_id;
        $studentGroupId = $user->student?->student_group_id;

        return $query->where(function ($q) use ($user, $academicGroupId, $academicLevelId, $studentGroupId) {
            $q->where(function ($subQ) use ($user) {
                $subQ->where('share_type', 'individual')
                    ->where('shared_to_user_id', $user->id);
            })
                ->orWhere(function ($subQ) use ($academicGroupId) {
                    if ($academicGroupId) {
                        $subQ->where('share_type', 'academic_group')
                            ->where('academic_group_id', $academicGroupId);
                    }
                })
                ->orWhere(function ($subQ) use ($academicLevelId) {
                    if ($academicLevelId) {
                        $subQ->where('share_type', 'academic_level')
                            ->where('academic_level_id', $academicLevelId);
                    }
                })
                ->orWhere(function ($subQ) use ($studentGroupId) {
                    if ($studentGroupId) {
                        $subQ->where('share_type', 'student_group')
                            ->where('student_group_id', $studentGroupId);
                    }
                });
        });
    }


    public function userBook(): BelongsTo
    {
        return $this->belongsTo(UserBook::class);
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function sharedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_to_user_id');
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $this->notifyOwner('accepted');
    }

    public function decline(): void
    {
        $this->update([
            'status' => 'declined',
        ]);

        $this->notifyOwner('declined');
    }

    public function notifyOwner($action): void
    {
        // Send email to the owner about the decision
        if ($this->sharedBy) {
            Mail::to($this->sharedBy->email)->send(
                new BookShareResponded($this, $action)
            );
        }
    }

   public function sharedWith()
   {
       return $this->belongsTo(User::class, 'shared_with_id');
   }


}

