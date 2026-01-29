<?php

namespace App\Models;

use App\Contracts\CalendarEventable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class CalendarEvent extends Model
{
    use HasFactory;

    /**
     * Default colors for different event types.
     * Models can override this by implementing getCalendarColor().
     */
    public const EVENT_TYPE_COLORS = [
        'Note' => '#3B82F6',        // Blue
        'Assignment' => '#EF4444',   // Red
        'Assessment' => '#F59E0B',   // Amber
        'AcademicEvent' => '#10B981', // Emerald
        'default' => '#6B7280',      // Gray
    ];

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'all_day',
        'color',
        'event_type',
        'event_id',
        'user_id',
        'visibility',
        'sharing_settings',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'all_day' => 'boolean',
        'sharing_settings' => 'array',
    ];

    protected $appends = ['event_type_name', 'display_color'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): MorphTo
    {
        return $this->morphTo('event', 'event_type', 'event_id');
    }

    /**
     * Get the human-readable event type name.
     */
    public function getEventTypeNameAttribute(): string
    {
        return class_basename($this->event_type);
    }

    /**
     * Get the display color for this event.
     * Uses the event's color if set, otherwise falls back to type-based color.
     */
    public function getDisplayColorAttribute(): string
    {
        if ($this->color) {
            return $this->color;
        }

        // Try to get color from the related model if it implements CalendarEventable
        if ($this->event && $this->event instanceof CalendarEventable) {
            $modelColor = $this->event->getCalendarColor();
            if ($modelColor) {
                return $modelColor;
            }
        }

        $typeName = $this->event_type_name;

        return self::EVENT_TYPE_COLORS[$typeName] ?? self::EVENT_TYPE_COLORS['default'];
    }

    /**
     * Scope to filter by event type (model class).
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by multiple event types.
     */
    public function scopeOfTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('event_type', $types);
    }

    /**
     * Scope to exclude certain event types.
     */
    public function scopeExcludeTypes(Builder $query, array $types): Builder
    {
        return $query->whereNotIn('event_type', $types);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->orWhere(function ($q) use ($userId) {
                $q->where('visibility', 'public')
                    ->orWhereHas('shares', function ($shareQuery) use ($userId) {
                        $shareQuery->where('shared_with_user_id', $userId);
                    });
            });
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    public function shares()
    {
        return $this->hasMany(CalendarEventShare::class);
    }

    /**
     * Get the reminders for this calendar event.
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(CalendarEventReminder::class);
    }

    /**
     * Get pending reminders for this event.
     */
    public function pendingReminders(): HasMany
    {
        return $this->reminders()->where('status', CalendarEventReminder::STATUS_PENDING);
    }

    /**
     * Create a reminder for this event.
     *
     * @param  array<string>  $channels
     */
    public function createReminder(
        int $userId,
        int $minutesBefore = 15,
        array $channels = ['email', 'database']
    ): CalendarEventReminder {
        return CalendarEventReminder::createForEvent($this, $userId, $minutesBefore, $channels);
    }

    /**
     * Create multiple reminders for this event.
     *
     * @param  array<int>  $minutesBeforeList
     * @param  array<string>  $channels
     * @return Collection<CalendarEventReminder>
     */
    public function createReminders(
        int $userId,
        array $minutesBeforeList = [15, 60, 1440],
        array $channels = ['email', 'database']
    ): Collection {
        $reminders = collect();

        foreach ($minutesBeforeList as $minutesBefore) {
            $remindAt = $this->start_date->copy()->subMinutes($minutesBefore);

            if ($remindAt->isFuture()) {
                $reminders->push(
                    $this->createReminder($userId, $minutesBefore, $channels)
                );
            }
        }

        return $reminders;
    }

    /**
     * Cancel all pending reminders for this event.
     */
    public function cancelAllReminders(): int
    {
        return $this->pendingReminders()->update([
            'status' => CalendarEventReminder::STATUS_CANCELLED,
        ]);
    }

    /**
     * Cancel reminders for a specific user.
     */
    public function cancelUserReminders(int $userId): int
    {
        return $this->pendingReminders()
            ->where('user_id', $userId)
            ->update(['status' => CalendarEventReminder::STATUS_CANCELLED]);
    }

    public function isSharedWith($userId): bool
    {
        // Check direct individual shares
        if ($this->shares()->where('shared_with_user_id', $userId)->exists()) {
            return true;
        }

        // Check group-based shares
        $user = User::find($userId);
        if (! $user || ! $user->student) {
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
                            ->whereHas('user', function ($noteUserQuery) use ($user) {
                                $noteUserQuery->where('school_id', $user->school_id);
                            });
                    });
            })
            ->exists();
    }

    public function canUserView($userId): bool
    {
        return $this->user_id === $userId ||
            $this->visibility === 'public' ||
            $this->isSharedWith($userId);
    }

    public function canUserEdit($userId): bool
    {
        if ($this->user_id === $userId) {
            return true;
        }

        $user = User::find($userId);
        if (! $user || ! $user->student) {
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

    /**
     * Convert the calendar event to an array format suitable for JavaScript calendar libraries.
     */
    public function toCalendarArray(): array
    {
        $url = null;

        // Try to get URL from the related model if it implements CalendarEventable
        if ($this->event && $this->event instanceof CalendarEventable) {
            $url = $this->event->getCalendarEventUrl();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start' => $this->start_date?->toIso8601String(),
            'end' => $this->end_date?->toIso8601String(),
            'allDay' => $this->all_day,
            'color' => $this->display_color,
            'eventType' => $this->event_type_name,
            'eventTypeClass' => $this->event_type,
            'eventId' => $this->event_id,
            'visibility' => $this->visibility,
            'url' => $url,
            'editable' => false, // Will be set by the controller based on permissions
            'extendedProps' => [
                'calendarEventId' => $this->id,
                'eventType' => $this->event_type_name,
                'eventTypeClass' => $this->event_type,
                'relatedModelId' => $this->event_id,
                'userId' => $this->user_id,
                'visibility' => $this->visibility,
            ],
        ];
    }

    /**
     * Create a calendar event from any model that implements CalendarEventable.
     */
    public static function createFromEventable(CalendarEventable $model, array $eventData): static
    {
        return static::create([
            'title' => $eventData['title'] ?? $model->getCalendarTitle(),
            'description' => $eventData['description'] ?? $model->getCalendarDescription(),
            'start_date' => $eventData['start_date'],
            'end_date' => $eventData['end_date'] ?? null,
            'all_day' => $eventData['all_day'] ?? false,
            'color' => $eventData['color'] ?? $model->getCalendarColor(),
            'user_id' => $model->getCalendarUserId(),
            'event_type' => get_class($model),
            'event_id' => $model->id,
            'visibility' => $eventData['visibility'] ?? 'private',
            'sharing_settings' => $eventData['sharing_settings'] ?? null,
        ]);
    }

    /**
     * Get all registered eventable model types.
     * This can be extended to dynamically discover eventable models.
     */
    public static function getEventableTypes(): array
    {
        return [
            'Note' => Note::class,
            // Add more types as they are created:
            // 'Assignment' => Assignment::class,
            // 'Assessment' => Assessment::class,
            // 'AcademicEvent' => AcademicEvent::class,
        ];
    }
}
