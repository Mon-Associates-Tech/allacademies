<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'book_id',
        'academic_subject_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'note_shares', 'note_id', 'shared_with_user_id')
            ->withPivot('can_edit', 'share_type')
            ->withTimestamps();
    }

    public function shares(): HasMany
    {
        return $this->hasMany(NoteShare::class);
    }

    public function isSharedWith($userId): bool
    {
        // Check direct individual shares
        if ($this->shares()->where('shared_with_user_id', $userId)->exists()) {
            return true;
        }

        // Check group-based shares
        $user = User::find($userId);
        if (!$user || !$user->student) {
            return false;
        }

        return $this->shares()
            ->where(function ($query) use ($user) {
                // Academic Groups - check if user's student belongs to shared academic group
                $query->where(function ($q) use ($user) {
                    $q->where('share_type', 'academic_group')
                        ->where('shareable_type', AcademicGroup::class)
                        ->where('shareable_id', $user->student->academic_group_id);
                })
                    // Academic Levels - check if user's student belongs to shared academic level
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'academic_level')
                            ->where('shareable_type', AcademicLevel::class)
                            ->where('shareable_id', $user->student->academic_level_id);
                    })
                    // Student Groups - check if user's student belongs to shared student group
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'student_group')
                            ->where('shareable_type', StudentGroup::class)
                            ->where('shareable_id', $user->student->student_group_id);
                    })
                    // School-wide - check if same school
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'school_wide')
                            ->whereHas('note.user', function ($noteUserQuery) use ($user) {
                                $noteUserQuery->where('school_id', $user->school_id);
                            });
                    });
            })
            ->exists();
    }

    public function canUserEdit($userId): bool
    {
        if ($this->user_id === $userId) {
            return true;
        }

        $user = User::find($userId);
        if (!$user || !$user->student) {
            return false;
        }

        return $this->shares()
            ->where('can_edit', true)
            ->where(function ($query) use ($userId, $user) {
                // Check individual shares
                $query->where('shared_with_user_id', $userId)
                    // Or check group-based shares with the same logic as isSharedWith
                    ->orWhere(function ($q) use ($user) {
                        $q->where(function ($subQ) use ($user) {
                            $subQ->where('share_type', 'academic_group')
                                ->where('shareable_type', AcademicGroup::class)
                                ->where('shareable_id', $user->student->academic_group_id);
                        })
                            ->orWhere(function ($subQ) use ($user) {
                                $subQ->where('share_type', 'academic_level')
                                    ->where('shareable_type', AcademicLevel::class)
                                    ->where('shareable_id', $user->student->academic_level_id);
                            })
                            ->orWhere(function ($subQ) use ($user) {
                                $subQ->where('share_type', 'student_group')
                                    ->where('shareable_type', StudentGroup::class)
                                    ->where('shareable_id', $user->student->student_group_id);
                            });
                    });
            })
            ->exists();
    }
    public function canUserView($userId): bool
    {
        return $this->user_id === $userId ||
            $this->is_public ||
            $this->isSharedWith($userId);
    }

    private function checkGroupEditPermissions($query, $user): void
    {
        $query->whereHasMorph('shareable', [AcademicGroup::class], function ($q) use ($user) {
            $q->whereHas('users', function ($userQuery) use ($user) {
                $userQuery->where('users.id', $user->id);
            });
        })
            ->orWhereHasMorph('shareable', [AcademicLevel::class], function ($q) use ($user) {
                $q->where('id', $user->academic_level_id);
            })
            ->orWhereHasMorph('shareable', [StudentGroup::class], function ($q) use ($user) {
                $q->whereHas('students', function ($studentQuery) use ($user) {
                    $studentQuery->where('user_id', $user->id);
                });
            });
    }

    public function shareable()
    {
        return $this->morphTo();
    }
}
