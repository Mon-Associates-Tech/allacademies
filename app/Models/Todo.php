<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'todoable_type',
        'todoable_id',
        'priority',
        'status',
        'due_date',
        'completed_at',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Priority levels
     */
    public const PRIORITIES = ['low', 'medium', 'high'];

    /**
     * Status options
     */
    public const STATUSES = ['pending', 'in_progress', 'completed', 'cancelled'];

    /**
     * Get the user who created the todo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent todoable model (AcademicGroup, AcademicLevel, etc.)
     */
    public function todoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all shares for this todo
     */
    public function shares(): HasMany
    {
        return $this->hasMany(TodoShare::class);
    }

    /**
     * Check if todo is shared with a specific user
     */
    public function isSharedWith(int $userId): bool
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
                // Academic Groups
                $query->where(function ($q) use ($user) {
                    $q->where('share_type', 'academic_group')
                        ->where('shareable_type', AcademicGroup::class)
                        ->where('shareable_id', $user->student->academic_group_id);
                })
                // Academic Levels
                ->orWhere(function ($q) use ($user) {
                    $q->where('share_type', 'academic_level')
                        ->where('shareable_type', AcademicLevel::class)
                        ->where('shareable_id', $user->student->academic_level_id);
                })
                // Student Groups
                ->orWhere(function ($q) use ($user) {
                    $q->where('share_type', 'student_group')
                        ->where('shareable_type', StudentGroup::class)
                        ->where('shareable_id', $user->student->student_group_id);
                })
                // School-wide
                ->orWhere(function ($q) use ($user) {
                    $q->where('share_type', 'school_wide')
                        ->whereHas('todo.user', function ($todoUserQuery) use ($user) {
                            $todoUserQuery->where('school_id', $user->school_id);
                        });
                });
            })
            ->exists();
    }

    /**
     * Check if user can view this todo
     */
    public function canUserView(int $userId): bool
    {
        // Owner can always view
        if ($this->user_id === $userId) {
            return true;
        }

        // If not private, check if shared
        if (!$this->is_private) {
            return $this->isSharedWith($userId);
        }

        return false;
    }

    /**
     * Check if user can edit this todo
     */
    public function canUserEdit(int $userId): bool
    {
        // Owner can always edit
        if ($this->user_id === $userId) {
            return true;
        }

        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check individual shares with edit permission
        if ($this->shares()->where('shared_with_user_id', $userId)->where('can_edit', true)->exists()) {
            return true;
        }

        // Check group-based shares with edit permission
        if ($user->student) {
            return $this->shares()
                ->where('can_edit', true)
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('share_type', 'academic_group')
                            ->where('shareable_type', AcademicGroup::class)
                            ->where('shareable_id', $user->student->academic_group_id);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'academic_level')
                            ->where('shareable_type', AcademicLevel::class)
                            ->where('shareable_id', $user->student->academic_level_id);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'student_group')
                            ->where('shareable_type', StudentGroup::class)
                            ->where('shareable_id', $user->student->student_group_id);
                    });
                })
                ->exists();
        }

        return false;
    }

    /**
     * Mark todo as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark todo as pending
     */
    public function markAsPending(): void
    {
        $this->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }

    /**
     * Check if todo is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'completed') {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Check if todo is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'cancelled' => 'gray',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope to get todos for a specific user (owned or shared)
     */
    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            // User's own todos
            $q->where('user_id', $user->id)
            // Or shared todos
            ->orWhere(function ($subQ) use ($user) {
                $subQ->where('is_private', false)
                    ->whereHas('shares', function ($shareQ) use ($user) {
                        $shareQ->where('shared_with_user_id', $user->id);

                        if ($user->student) {
                            $shareQ->orWhere(function ($groupQ) use ($user) {
                                $groupQ->where('share_type', 'academic_group')
                                    ->where('shareable_type', AcademicGroup::class)
                                    ->where('shareable_id', $user->student->academic_group_id);
                            })
                            ->orWhere(function ($levelQ) use ($user) {
                                $levelQ->where('share_type', 'academic_level')
                                    ->where('shareable_type', AcademicLevel::class)
                                    ->where('shareable_id', $user->student->academic_level_id);
                            });
                        }
                    });
            });
        });
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by priority
     */
    public function scopeWithPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get overdue todos
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
                     ->where('due_date', '<', now())
                     ->where('status', '!=', 'completed');
    }

    /**
     * Scope to get todos due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    /**
     * Scope to filter by todoable
     */
    public function scopeForTodoable($query, string $type, int $id)
    {
        return $query->where('todoable_type', $type)
                     ->where('todoable_id', $id);
    }

    /**
     * Get the hierarchy path for breadcrumb
     */
    public function getHierarchyPathAttribute(): array
    {
        $path = [];
        $todoable = $this->todoable;

        if (!$todoable) {
            return $path;
        }

        if ($todoable instanceof AcademicSubtopic) {
            $topic = $todoable->academicTopic;
            $subject = $topic->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $topic->id, 'name' => $topic->name],
                ['type' => 'subtopic', 'id' => $todoable->id, 'name' => $todoable->name],
            ];
        } elseif ($todoable instanceof AcademicTopic) {
            $subject = $todoable->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $todoable->id, 'name' => $todoable->name],
            ];
        } elseif ($todoable instanceof AcademicSubject) {
            $level = $todoable->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $todoable->id, 'name' => $todoable->name],
            ];
        } elseif ($todoable instanceof AcademicLevel) {
            $group = $todoable->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $todoable->id, 'name' => $todoable->name],
            ];
        } elseif ($todoable instanceof AcademicGroup) {
            $path = [
                ['type' => 'group', 'id' => $todoable->id, 'name' => $todoable->name],
            ];
        }

        return $path;
    }

    /**
     * Convert todo to array for CSV export
     */
    public function toCsvArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description ?? '',
            'priority' => ucfirst($this->priority),
            'status' => ucfirst(str_replace('_', ' ', $this->status)),
            'due_date' => $this->due_date?->format('Y-m-d') ?? '',
            'created_by' => $this->user->name ?? 'Unknown',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s') ?? '',
            'is_private' => $this->is_private ? 'Yes' : 'No',
        ];
    }

    /**
     * Get CSV headers for export
     */
    public static function getCsvHeaders(): array
    {
        return [
            'Title',
            'Description',
            'Priority',
            'Status',
            'Due Date',
            'Created By',
            'Created At',
            'Completed At',
            'Private',
        ];
    }

    /**
     * Export collection of todos to CSV string
     */
    public static function collectionToCsv($todos): string
    {
        $output = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($output, self::getCsvHeaders());

        // Write data rows
        foreach ($todos as $todo) {
            fputcsv($output, $todo->toCsvArray());
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export single todo to CSV string
     */
    public function toCsv(): string
    {
        $output = fopen('php://temp', 'r+');

        // Write headers
        fputcsv($output, self::getCsvHeaders());

        // Write data row
        fputcsv($output, $this->toCsvArray());

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
