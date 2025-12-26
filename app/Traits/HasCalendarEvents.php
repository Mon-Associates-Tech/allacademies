<?php

namespace App\Traits;

use App\Models\CalendarEvent;

/**
 * Trait for models that can be displayed on the calendar.
 *
 * Use this trait along with implementing the CalendarEventable interface
 * to enable calendar integration for any model (Notes, Assignments,
 * Assessments, Academic Events, etc.).
 *
 * Usage:
 * 1. Add `use HasCalendarEvents;` to your model
 * 2. Implement `CalendarEventable` interface
 * 3. Override methods as needed for your specific model
 */
trait HasCalendarEvents
{
    /**
     * Boot the trait - registers model events for calendar sync.
     */
    public static function bootHasCalendarEvents(): void
    {
        static::updated(function ($model) {
            $model->syncWithCalendarEvent();
        });

        static::deleting(function ($model) {
            if ($model->calendarEvent) {
                $model->calendarEvent->delete();
            }
        });
    }

    /**
     * Get the calendar event associated with this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function calendarEvent()
    {
        return $this->morphOne(CalendarEvent::class, 'event');
    }

    /**
     * Create a calendar event for this model.
     *
     * @param array $eventData
     * @return CalendarEvent
     */
    public function createCalendarEvent(array $eventData): CalendarEvent
    {
        return CalendarEvent::create([
            'title' => $eventData['title'] ?? $this->getCalendarTitle(),
            'description' => $eventData['description'] ?? $this->getCalendarDescription(),
            'start_date' => $eventData['start_date'],
            'end_date' => $eventData['end_date'] ?? null,
            'all_day' => $eventData['all_day'] ?? false,
            'color' => $eventData['color'] ?? $this->getCalendarColor(),
            'user_id' => $this->getCalendarUserId(),
            'event_type' => static::class,
            'event_id' => $this->id,
            'visibility' => $eventData['visibility'] ?? 'private',
            'sharing_settings' => $eventData['sharing_settings'] ?? null,
        ]);
    }

    /**
     * Update or create a calendar event for this model.
     *
     * @param array $eventData
     * @return CalendarEvent
     */
    public function updateOrCreateCalendarEvent(array $eventData): CalendarEvent
    {
        if ($this->calendarEvent) {
            $this->calendarEvent->update([
                'title' => $eventData['title'] ?? $this->getCalendarTitle(),
                'description' => $eventData['description'] ?? $this->getCalendarDescription(),
                'start_date' => $eventData['start_date'] ?? $this->calendarEvent->start_date,
                'end_date' => $eventData['end_date'] ?? $this->calendarEvent->end_date,
                'all_day' => $eventData['all_day'] ?? $this->calendarEvent->all_day,
                'color' => $eventData['color'] ?? $this->getCalendarColor(),
                'visibility' => $eventData['visibility'] ?? $this->calendarEvent->visibility,
                'sharing_settings' => $eventData['sharing_settings'] ?? $this->calendarEvent->sharing_settings,
            ]);

            return $this->calendarEvent;
        }

        return $this->createCalendarEvent($eventData);
    }

    /**
     * Sync the model's data with its calendar event.
     *
     * @return void
     */
    public function syncWithCalendarEvent(): void
    {
        if ($this->calendarEvent) {
            $this->calendarEvent->update([
                'title' => $this->getCalendarTitle(),
                'description' => $this->getCalendarDescription(),
                'color' => $this->getCalendarColor(),
            ]);
        }
    }

    /**
     * Remove the calendar event associated with this model.
     *
     * @return bool
     */
    public function removeCalendarEvent(): bool
    {
        if ($this->calendarEvent) {
            return $this->calendarEvent->delete();
        }

        return false;
    }

    /**
     * Check if this model has a calendar event.
     *
     * @return bool
     */
    public function hasCalendarEvent(): bool
    {
        return $this->calendarEvent()->exists();
    }

    /**
     * Get the title to display on the calendar.
     * Override this method in your model to customize.
     *
     * @return string
     */
    public function getCalendarTitle(): string
    {
        return $this->title ?? $this->name ?? 'Untitled Event';
    }

    /**
     * Get the description to display on the calendar.
     * Override this method in your model to customize.
     *
     * @return string|null
     */
    public function getCalendarDescription(): ?string
    {
        return $this->description ?? $this->content ?? null;
    }

    /**
     * Get the default color for this event type on the calendar.
     * Override this method in your model to customize.
     *
     * @return string|null
     */
    public function getCalendarColor(): ?string
    {
        return null;
    }

    /**
     * Get the user ID who owns this eventable item.
     * Override this method in your model if the user field has a different name.
     *
     * @return int
     */
    public function getCalendarUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Get the event type identifier for display purposes.
     * This returns a human-readable type name.
     *
     * @return string
     */
    public function getCalendarEventType(): string
    {
        return class_basename(static::class);
    }

    /**
     * Get the URL to view this item's details.
     * Override this method in your model to provide the correct route.
     *
     * @return string|null
     */
    public function getCalendarEventUrl(): ?string
    {
        return null;
    }

    /**
     * Get additional metadata for the calendar event.
     * Override this method to include model-specific data.
     *
     * @return array
     */
    public function getCalendarMetadata(): array
    {
        return [
            'model_type' => static::class,
            'model_id' => $this->id,
            'event_type' => $this->getCalendarEventType(),
        ];
    }

    /**
     * Convert the model to a calendar event array format.
     * Useful for API responses and JavaScript calendar libraries.
     *
     * @return array
     */
    public function toCalendarEventArray(): array
    {
        $calendarEvent = $this->calendarEvent;

        return [
            'id' => $calendarEvent?->id,
            'title' => $this->getCalendarTitle(),
            'description' => $this->getCalendarDescription(),
            'start' => $calendarEvent?->start_date?->toIso8601String(),
            'end' => $calendarEvent?->end_date?->toIso8601String(),
            'allDay' => $calendarEvent?->all_day ?? false,
            'color' => $calendarEvent?->color ?? $this->getCalendarColor(),
            'url' => $this->getCalendarEventUrl(),
            'eventType' => $this->getCalendarEventType(),
            'metadata' => $this->getCalendarMetadata(),
            'visibility' => $calendarEvent?->visibility ?? 'private',
        ];
    }
}
