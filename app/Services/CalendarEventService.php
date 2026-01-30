<?php

namespace App\Services;

use App\Contracts\CalendarEventable;
use App\Models\CalendarEvent;
use App\Models\Note;
use App\Models\CalendarEventShare;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CalendarEventService
{
    /**
     * Create a calendar event, optionally linked to a related model.
     *
     * @param array $data
     * @param Model|CalendarEventable|null $relatedModel
     * @return CalendarEvent
     */
    public function createEvent(array $data, $relatedModel = null): CalendarEvent
    {
        $eventData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'all_day' => $data['all_day'] ?? false,
            'color' => $data['color'] ?? null,
            'user_id' => Auth::id(),
            'visibility' => $data['visibility'] ?? 'private',
            'sharing_settings' => $data['sharing_settings'] ?? null,
        ];

        if ($relatedModel) {
            $eventData['event_type'] = get_class($relatedModel);
            $eventData['event_id'] = $relatedModel->id;

            // If the model implements CalendarEventable, use its methods for defaults
            if ($relatedModel instanceof CalendarEventable) {
                $eventData['title'] = $data['title'] ?? $relatedModel->getCalendarTitle();
                $eventData['description'] = $data['description'] ?? $relatedModel->getCalendarDescription();
                $eventData['color'] = $data['color'] ?? $relatedModel->getCalendarColor();
            }
        } else {
            $eventData['event_type'] = CalendarEvent::class;
            $eventData['event_id'] = 0; // Placeholder for standalone events
        }

        return CalendarEvent::create($eventData);
    }

    /**
     * Create a calendar event from any model that implements CalendarEventable.
     *
     * @param CalendarEventable $model
     * @param array $eventData
     * @return CalendarEvent
     */
    public function createEventFromEventable(CalendarEventable $model, array $eventData): CalendarEvent
    {
        return CalendarEvent::createFromEventable($model, $eventData);
    }

    public function updateEvent(CalendarEvent $event, array $data, $relatedModel = null): CalendarEvent
    {
        $eventData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'all_day' => $data['all_day'] ?? false,
            'color' => $data['color'] ?? null,
            'visibility' => $data['visibility'] ?? 'private',
            'sharing_settings' => $data['sharing_settings'] ?? null,
        ];

        if ($relatedModel) {
            $eventData['event_type'] = get_class($relatedModel);
            $eventData['event_id'] = $relatedModel->id;
        }

        $event->update($eventData);

        return $event;
    }

    public function deleteEvent(CalendarEvent $event): bool
    {
        return $event->delete();
    }

    public function getEventsForUser($userId, $startDate = null, $endDate = null)
    {
        $query = CalendarEvent::forUser($userId);

        if ($startDate && $endDate) {
            $query = $query->forDateRange($startDate, $endDate);
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    public function shareEvent(CalendarEvent $event, string $shareType, array $recipientIds, bool $canEdit = false): array
    {
        $usersNotified = 0;

        foreach ($recipientIds as $recipientId) {
            $share = CalendarEventShare::create([
                'calendar_event_id' => $event->id,
                'shared_with_user_id' => $recipientId,
                'share_type' => $shareType,
                'can_edit' => $canEdit,
            ]);

            // You can add notification logic here if needed
            $usersNotified++;
        }

        return [
            'event' => $event,
            'users_notified' => $usersNotified
        ];
    }

    public function unshare(CalendarEvent $event, string $shareType, $identifier): void
    {
        $event->shares()
            ->where('share_type', $shareType)
            ->where(function ($query) use ($identifier) {
                $query->where('shared_with_user_id', $identifier)
                    ->orWhere('shareable_id', $identifier);
            })
            ->delete();
    }

    public function createNoteFromEvent(array $noteData, CalendarEvent $event): Note
    {
        $note = Note::create([
            'title' => $noteData['title'],
            'content' => $noteData['content'] ?? '',
            'user_id' => Auth::id(),
            'book_id' => $noteData['book_id'] ?? null,
            'academic_subject_id' => $noteData['academic_subject_id'] ?? null,
            'is_public' => $noteData['is_public'] ?? false,
        ]);

        // Update the event to reference the note
        $event->update([
            'event_type' => Note::class,
            'event_id' => $note->id,
        ]);

        return $note;
    }

    public function createEventFromNote(Note $note, array $eventData): CalendarEvent
    {
        return CalendarEvent::create([
            'title' => $eventData['title'] ?? $note->title,
            'description' => $eventData['description'] ?? $note->content,
            'start_date' => $eventData['start_date'],
            'end_date' => $eventData['end_date'] ?? null,
            'all_day' => $eventData['all_day'] ?? false,
            'color' => $eventData['color'] ?? null,
            'user_id' => $note->user_id,
            'event_type' => Note::class,
            'event_id' => $note->id,
            'visibility' => $eventData['visibility'] ?? 'private',
            'sharing_settings' => $eventData['sharing_settings'] ?? null,
        ]);
    }

    /**
     * Get events filtered by a single event type.
     *
     * @param int $userId
     * @param string|null $eventType Full class name of the event type
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getEventsByType($userId, $eventType, $startDate = null, $endDate = null): Collection
    {
        $query = CalendarEvent::forUser($userId);

        if ($eventType) {
            $query->ofType($eventType);
        }

        if ($startDate && $endDate) {
            $query->forDateRange($startDate, $endDate);
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    /**
     * Get events filtered by multiple event types.
     *
     * @param int $userId
     * @param array $eventTypes Array of full class names
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getEventsByTypes(int $userId, array $eventTypes, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = CalendarEvent::forUser($userId);

        if (!empty($eventTypes)) {
            $query->ofTypes($eventTypes);
        }

        if ($startDate && $endDate) {
            $query->forDateRange($startDate, $endDate);
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    /**
     * Get events for user formatted for JavaScript calendar libraries.
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param array|null $eventTypes Filter by specific event types (null = all types)
     * @return Collection
     */
    public function getEventsForCalendar(int $userId, ?string $startDate = null, ?string $endDate = null, ?array $eventTypes = null): Collection
    {
        $query = CalendarEvent::forUser($userId);

        if ($startDate && $endDate) {
            $query->forDateRange($startDate, $endDate);
        }

        if ($eventTypes !== null && !empty($eventTypes)) {
            $query->ofTypes($eventTypes);
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    /**
     * Get available event types for filtering.
     *
     * @return array
     */
    public function getAvailableEventTypes(): array
    {
        return CalendarEvent::getEventableTypes();
    }

    /**
     * Get event type colors for the calendar legend.
     *
     * @return array
     */
    public function getEventTypeColors(): array
    {
        return CalendarEvent::EVENT_TYPE_COLORS;
    }

    /**
     * Get events count by type for a user.
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getEventsCountByType(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = CalendarEvent::forUser($userId);

        if ($startDate && $endDate) {
            $query->forDateRange($startDate, $endDate);
        }

        return $query->get()
            ->groupBy('event_type_name')
            ->map(fn($events) => $events->count())
            ->toArray();
    }
}
