<?php

namespace App\Contracts;

use App\Models\CalendarEvent;

/**
 * Interface for models that can be displayed on the calendar.
 *
 * Implement this interface to allow any model (Notes, Assignments,
 * Assessments, Academic Events, etc.) to integrate with the calendar system.
 */
interface CalendarEventable
{
    /**
     * Get the calendar event associated with this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function calendarEvent();

    /**
     * Create a calendar event for this model.
     *
     * @param array $eventData
     * @return CalendarEvent
     */
    public function createCalendarEvent(array $eventData): CalendarEvent;

    /**
     * Sync the model's data with its calendar event.
     *
     * @return void
     */
    public function syncWithCalendarEvent(): void;

    /**
     * Get the title to display on the calendar.
     *
     * @return string
     */
    public function getCalendarTitle(): string;

    /**
     * Get the description to display on the calendar.
     *
     * @return string|null
     */
    public function getCalendarDescription(): ?string;

    /**
     * Get the default color for this event type on the calendar.
     *
     * @return string|null
     */
    public function getCalendarColor(): ?string;

    /**
     * Get the user ID who owns this eventable item.
     *
     * @return int
     */
    public function getCalendarUserId(): int;

    /**
     * Get the event type identifier for display purposes.
     * This is used to distinguish between different types of events on the calendar.
     *
     * @return string
     */
    public function getCalendarEventType(): string;

    /**
     * Get the URL to view this item's details.
     *
     * @return string|null
     */
    public function getCalendarEventUrl(): ?string;

    /**
     * Get additional metadata for the calendar event.
     * This can include any extra information needed for display or filtering.
     *
     * @return array
     */
    public function getCalendarMetadata(): array;
}
