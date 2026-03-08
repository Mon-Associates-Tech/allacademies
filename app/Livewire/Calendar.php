<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\Note;
use App\Services\CalendarEventService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Calendar extends Component
{
    public $view = 'month'; // month, week, day, list, year

    public $startDate;

    public $endDate;

    public $events = [];

    // Search and filter properties
    public $searchQuery = '';

    public $timeFilter = 'all'; // all, past, upcoming

    public $customStartDate = null;

    public $customEndDate = null;

    public $showCustomDateRange = false;

    public $selectedEvent = null;

    public $showEventModal = false;

    public $showViewModal = false;

    public $showEditModal = false;

    public $showCreateModal = false;

    public $showCreateNoteModal = false;

    public $eventTitle = '';

    public $eventDescription = '';

    public $eventStartDate;

    public $eventEndDate;

    public $eventAllDay = false;

    public $eventColor = '';

    public $eventVisibility = 'private';

    public $eventTypeId = null; // For specifying the type of related model

    public $eventType = null; // For specifying the type of event

    // Reminder fields
    public $enableReminder = false;

    public $reminderMinutesBefore = 15;

    public $reminderChannels = ['email', 'database'];

    // Available reminder time options (in minutes)
    public $reminderTimeOptions = [
        5 => '5 minutes before',
        10 => '10 minutes before',
        15 => '15 minutes before',
        30 => '30 minutes before',
        60 => '1 hour before',
        120 => '2 hours before',
        1440 => '1 day before',
        2880 => '2 days before',
        10080 => '1 week before',
    ];

    // Note creation fields
    public $noteTitle = '';

    public $noteContent = '';

    public $noteBookId = null;

    public $noteSubjectId = null;

    public $noteIsPublic = false;

    // Event type filtering
    public $availableEventTypes = [];

    public $selectedEventTypes = []; // Empty means show all

    public $eventTypeColors = [];

    public $eventCounts = [];

    protected $listeners = [
        'refreshCalendar' => 'render',
        'createEventFromCalendar' => 'createEventFromCalendar',
        'updateEventFromCalendar' => 'updateEventFromCalendar',
        'filterByEventType' => 'filterByEventType',
        'markdown-updated' => 'handleMarkdownUpdate',
    ];

    protected $rules = [
        'eventTitle' => 'required|string|max:255',
        'eventDescription' => 'nullable|string',
        'eventStartDate' => 'required|date',
        'eventEndDate' => 'nullable|date|after_or_equal:eventStartDate',
        'eventAllDay' => 'boolean',
        'eventColor' => 'nullable|string',
        'eventVisibility' => 'required|in:private,public,shared',
        // Note rules
        'noteTitle' => 'required|string|max:255',
        'noteContent' => 'required|string',
        'noteBookId' => 'nullable|exists:books,id',
        'noteSubjectId' => 'nullable|exists:academic_subjects,id',
        'noteIsPublic' => 'boolean',
    ];

    public function mount($view = 'month', $startDate = null, $endDate = null, $eventTypes = null)
    {
        $this->view = $view;
        $this->setDateRange($startDate, $endDate);
        $this->initializeEventTypes();

        // If specific event types are passed, filter by them
        if ($eventTypes !== null) {
            $this->selectedEventTypes = is_array($eventTypes) ? $eventTypes : [$eventTypes];
        }

        $this->loadEvents();
    }

    /**
     * Initialize available event types and their colors.
     */
    protected function initializeEventTypes(): void
    {
        $service = app(CalendarEventService::class);
        $this->availableEventTypes = $service->getAvailableEventTypes();
        $this->eventTypeColors = $service->getEventTypeColors();
    }

    public function setDateRange($startDate = null, $endDate = null)
    {
        $user = auth()->user();
        $now = Carbon::now();

        if (! $startDate) {
            switch ($this->view) {
                case 'day':
                    $this->startDate = $now->copy()->startOfDay();
                    $this->endDate = $now->copy()->endOfDay();
                    break;
                case 'week':
                    $this->startDate = $now->copy()->startOfWeek();
                    $this->endDate = $now->copy()->endOfWeek();
                    break;
                case 'year':
                    $this->startDate = $now->copy()->startOfYear();
                    $this->endDate = $now->copy()->endOfYear();
                    break;
                case 'list':
                    $this->startDate = $now->copy()->subMonths(6);
                    $this->endDate = $now->copy()->addMonths(6);
                    break;
                default: // month
                    $this->startDate = $now->copy()->startOfMonth();
                    $this->endDate = $now->copy()->endOfMonth();
            }
        } else {
            $this->startDate = Carbon::parse($startDate);
            $this->endDate = $endDate ? Carbon::parse($endDate) : $this->startDate->copy()->addDays(30);
        }
    }

    public function loadEvents()
    {
        $service = app(CalendarEventService::class);

        // Convert selected event types to full class names for filtering
        $filterTypes = null;
        if (! empty($this->selectedEventTypes)) {
            $filterTypes = array_map(function ($typeName) {
                return $this->availableEventTypes[$typeName] ?? $typeName;
            }, $this->selectedEventTypes);
        }

        // Get events formatted for calendar display
        $this->events = $service->getEventsForCalendar(
            auth()->id(),
            $this->startDate?->toDateTimeString(),
            $this->endDate?->toDateTimeString(),
            $filterTypes
        );

        // Update event counts by type
        $this->eventCounts = $service->getEventsCountByType(
            auth()->id(),
            $this->startDate?->toDateTimeString(),
            $this->endDate?->toDateTimeString()
        );
    }

    /**
     * Toggle filtering by a specific event type.
     */
    public function toggleEventType(string $eventType): void
    {
        if (in_array($eventType, $this->selectedEventTypes)) {
            $this->selectedEventTypes = array_values(array_diff($this->selectedEventTypes, [$eventType]));
        } else {
            $this->selectedEventTypes[] = $eventType;
        }

        $this->loadEvents();
    }

    /**
     * Filter by a specific event type (replaces current filter).
     */
    public function filterByEventType(string $eventType): void
    {
        $this->selectedEventTypes = [$eventType];
        $this->loadEvents();
    }

    /**
     * Filter by multiple event types.
     */
    public function filterByEventTypes(array $eventTypes): void
    {
        $this->selectedEventTypes = $eventTypes;
        $this->loadEvents();
    }

    /**
     * Clear all event type filters (show all events).
     */
    public function clearEventTypeFilter(): void
    {
        $this->selectedEventTypes = [];
        $this->loadEvents();
    }

    /**
     * Check if a specific event type is currently selected.
     */
    public function isEventTypeSelected(string $eventType): bool
    {
        return empty($this->selectedEventTypes) || in_array($eventType, $this->selectedEventTypes);
    }

    /**
     * Update search query and reload events.
     */
    public function updatedSearchQuery(): void
    {
        $this->loadEvents();
    }

    /**
     * Update time filter and reload events.
     */
    public function updatedTimeFilter(): void
    {
        $this->loadEvents();
    }

    /**
     * Set custom date range for filtering.
     */
    public function applyCustomDateRange(): void
    {
        if ($this->customStartDate && $this->customEndDate) {
            $this->startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $this->endDate = Carbon::parse($this->customEndDate)->endOfDay();
            $this->loadEvents();
        }
    }

    /**
     * Clear custom date range and reset to current view's default range.
     */
    public function clearCustomDateRange(): void
    {
        $this->customStartDate = null;
        $this->customEndDate = null;
        $this->showCustomDateRange = false;
        $this->setDateRange();
        $this->loadEvents();
    }

    /**
     * Toggle custom date range picker visibility.
     */
    public function toggleCustomDateRange(): void
    {
        $this->showCustomDateRange = ! $this->showCustomDateRange;
        if (! $this->showCustomDateRange) {
            $this->clearCustomDateRange();
        }
    }

    /**
     * Clear all filters (search, time filter, event types).
     */
    public function clearAllFilters(): void
    {
        $this->searchQuery = '';
        $this->timeFilter = 'all';
        $this->selectedEventTypes = [];
        $this->clearCustomDateRange();
    }

    /**
     * Check if any filters are active.
     */
    public function hasActiveFilters(): bool
    {
        return ! empty($this->searchQuery)
            || $this->timeFilter !== 'all'
            || ! empty($this->selectedEventTypes)
            || $this->showCustomDateRange;
    }

    /**
     * Apply search and time filters to the events collection.
     */
    protected function applyFilters($events)
    {
        $now = Carbon::now();

        // Apply search filter
        if (! empty($this->searchQuery)) {
            $searchTerm = strtolower($this->searchQuery);
            $events = $events->filter(function ($event) use ($searchTerm) {
                return str_contains(strtolower($event->title ?? ''), $searchTerm)
                    || str_contains(strtolower($event->description ?? ''), $searchTerm);
            });
        }

        // Apply time filter
        if ($this->timeFilter === 'past') {
            $events = $events->filter(function ($event) use ($now) {
                return $event->start_date && $event->start_date->lt($now);
            });
        } elseif ($this->timeFilter === 'upcoming') {
            $events = $events->filter(function ($event) use ($now) {
                return $event->start_date && $event->start_date->gte($now);
            });
        }

        return $events->values();
    }

    public function render()
    {
        $service = app(CalendarEventService::class);

        // Convert selected event types to full class names for filtering
        $filterTypes = null;
        if (! empty($this->selectedEventTypes)) {
            $filterTypes = array_map(function ($typeName) {
                return $this->availableEventTypes[$typeName] ?? $typeName;
            }, $this->selectedEventTypes);
        }

        // Determine date range based on time filter
        $fetchStartDate = $this->startDate?->toDateTimeString();
        $fetchEndDate = $this->endDate?->toDateTimeString();

        // Adjust date range when time filter is active to fetch all relevant events
        if ($this->timeFilter === 'past') {
            // Fetch events from far past to now
            $fetchStartDate = Carbon::now()->subYears(10)->toDateTimeString();
            $fetchEndDate = Carbon::now()->toDateTimeString();
        } elseif ($this->timeFilter === 'upcoming') {
            // Fetch events from now to far future
            $fetchStartDate = Carbon::now()->toDateTimeString();
            $fetchEndDate = Carbon::now()->addYears(10)->toDateTimeString();
        }

        // Get events as CalendarEvent models (Collection) for the view
        $events = $service->getEventsForCalendar(
            auth()->id(),
            $fetchStartDate,
            $fetchEndDate,
            $filterTypes
        );

        // Apply search and time filters
        $events = $this->applyFilters($events);

        // Update event counts by type
        $this->eventCounts = $service->getEventsCountByType(
            auth()->id(),
            $this->startDate?->toDateTimeString(),
            $this->endDate?->toDateTimeString()
        );

        return view('livewire.calendar', [
            'events' => $events,
            'currentView' => $this->view,
            'currentDate' => $this->startDate,
            'availableEventTypes' => $this->availableEventTypes,
            'selectedEventTypes' => $this->selectedEventTypes,
            'eventTypeColors' => $this->eventTypeColors,
            'eventCounts' => $this->eventCounts,
            'hasActiveFilters' => $this->hasActiveFilters(),
        ]);
    }

    public function changeView($view)
    {
        $this->view = $view;
        $this->setDateRange();
        $this->loadEvents();
    }

    public function previousPeriod()
    {
        switch ($this->view) {
            case 'day':
                $this->startDate->subDay();
                $this->endDate = $this->startDate->copy()->endOfDay();
                break;
            case 'week':
                $this->startDate->subWeek();
                $this->endDate = $this->startDate->copy()->endOfWeek();
                break;
            case 'year':
                $this->startDate->subYear();
                $this->endDate = $this->startDate->copy()->endOfYear();
                break;
            default: // month
                $this->startDate->subMonth();
                $this->endDate = $this->startDate->copy()->endOfMonth();
        }

        $this->loadEvents();
    }

    public function nextPeriod()
    {
        switch ($this->view) {
            case 'day':
                $this->startDate->addDay();
                $this->endDate = $this->startDate->copy()->endOfDay();
                break;
            case 'week':
                $this->startDate->addWeek();
                $this->endDate = $this->startDate->copy()->endOfWeek();
                break;
            case 'year':
                $this->startDate->addYear();
                $this->endDate = $this->startDate->copy()->endOfYear();
                break;
            default: // month
                $this->startDate->addMonth();
                $this->endDate = $this->startDate->copy()->endOfMonth();
        }

        $this->loadEvents();
    }

    public function today()
    {
        $this->setDateRange();
        $this->loadEvents();
    }

    public function createEventFromCalendar($eventData)
    {
        $this->resetValidation();
        $this->resetEventForm();

        $this->eventTitle = $eventData['title'] ?? '';
        $this->eventDescription = $eventData['description'] ?? '';
        $this->eventStartDate = $eventData['start_date'] ?? now();
        $this->eventEndDate = $eventData['end_date'] ?? null;
        $this->eventAllDay = $eventData['all_day'] ?? false;
        $this->eventColor = $eventData['color'] ?? '';
        $this->eventVisibility = $eventData['visibility'] ?? 'private';
        $this->eventTypeId = $eventData['event_type_id'] ?? null;
        $this->eventType = $eventData['event_type'] ?? null;

        $this->showCreateModal = true;
    }

    public function resetEventForm()
    {
        $this->reset([
            'eventTitle',
            'eventDescription',
            'eventStartDate',
            'eventEndDate',
            'eventAllDay',
            'eventColor',
            'eventVisibility',
            'selectedEvent',
            'eventTypeId',
            'eventType',
            'enableReminder',
            'reminderMinutesBefore',
            'reminderChannels',
        ]);
        // Reset reminder defaults
        $this->reminderMinutesBefore = 15;
        $this->reminderChannels = ['email', 'database'];
    }

    public function createEvent()
    {
        $this->validate([
            'eventTitle' => 'required|string|max:255',
            'eventDescription' => 'nullable|string',
            'eventStartDate' => 'required|date',
            'eventEndDate' => 'nullable|date|after_or_equal:eventStartDate',
            'eventAllDay' => 'boolean',
            'eventColor' => 'nullable|string',
            'eventVisibility' => 'required|in:private,public,shared',
        ]);

        $eventData = [
            'title' => $this->eventTitle,
            'description' => $this->eventDescription,
            'start_date' => $this->eventStartDate,
            'end_date' => $this->eventEndDate,
            'all_day' => $this->eventAllDay,
            'color' => $this->eventColor,
            'visibility' => $this->eventVisibility,
            'user_id' => auth()->id(),
            'event_type_id' => $this->eventTypeId,
            'event_type' => $this->eventType,
        ];

        $service = app(CalendarEventService::class);
        $event = $service->createEvent($eventData);

        // Create reminder if enabled
        if ($this->enableReminder && $event) {
            $event->createReminder(
                auth()->id(),
                (int) $this->reminderMinutesBefore,
                $this->reminderChannels
            );
        }

        $this->resetEventForm();
        $this->showCreateModal = false;
        $this->loadEvents();

        // Dispatch close modal event
        $this->dispatch('close-modal', name: 'create-event-modal');
        $this->dispatch('calendarEventCreated');

        // Show success message
        session()->flash('success', 'Event created successfully!');
    }

    public function updateEventFromCalendar($eventId)
    {
        $event = CalendarEvent::find($eventId);
        if ($event && $event->canUserEdit(auth()->id())) {
            $this->selectedEvent = $event;
            $this->eventTitle = $event->title;
            $this->eventDescription = $event->description;
            $this->eventStartDate = $event->start_date;
            $this->eventEndDate = $event->end_date;
            $this->eventAllDay = $event->all_day;
            $this->eventColor = $event->color;
            $this->eventVisibility = $event->visibility;
            $this->showEventModal = true;
        }
    }

    public function updateEvent()
    {
        $this->validate([
            'eventTitle' => 'required|string|max:255',
            'eventDescription' => 'nullable|string',
            'eventStartDate' => 'required|date',
            'eventEndDate' => 'nullable|date|after_or_equal:eventStartDate',
            'eventAllDay' => 'boolean',
            'eventColor' => 'nullable|string',
            'eventVisibility' => 'required|in:private,public,shared',
        ]);

        if ($this->selectedEvent && $this->selectedEvent->canUserEdit(auth()->id())) {
            $eventData = [
                'title' => $this->eventTitle,
                'description' => $this->eventDescription,
                'start_date' => $this->eventStartDate,
                'end_date' => $this->eventEndDate,
                'all_day' => $this->eventAllDay,
                'color' => $this->eventColor,
                'visibility' => $this->eventVisibility,
                'user_id' => auth()->id(),
                'event_type_id' => $this->eventTypeId,
                'event_type' => $this->eventType,
            ];

            app(CalendarEventService::class)->updateEvent($this->selectedEvent, $eventData);

            $this->resetEventForm();
            $this->showEditModal = false;
            $this->showEventModal = false;
            $this->loadEvents();

            $this->dispatch('calendarEventUpdated');
            $this->dispatch('close-modal', name: 'edit-event-modal');
        }
    }

    public function deleteEvent()
    {
        if ($this->selectedEvent && $this->selectedEvent->user_id === auth()->id()) {
            app(CalendarEventService::class)->deleteEvent($this->selectedEvent);

            $this->resetEventForm();
            $this->showEditModal = false;
            $this->showViewModal = false;
            $this->showEventModal = false;
            $this->loadEvents();

            $this->dispatch('calendarEventDeleted');
        }
    }

    public function selectEvent($eventId)
    {
        $this->selectedEvent = CalendarEvent::find($eventId);
        if ($this->selectedEvent && $this->selectedEvent->canUserView(auth()->id())) {
            $this->eventTitle = $this->selectedEvent->title;
            $this->eventDescription = $this->selectedEvent->description;
            $this->eventStartDate = $this->selectedEvent->start_date;
            $this->eventEndDate = $this->selectedEvent->end_date;
            $this->eventAllDay = $this->selectedEvent->all_day;
            $this->eventColor = $this->selectedEvent->color;
            $this->eventVisibility = $this->selectedEvent->visibility;

            $this->showViewModal = true;
        }
    }

    public function openEditModal()
    {
        if ($this->selectedEvent && $this->selectedEvent->canUserEdit(auth()->id())) {
            $this->showViewModal = false;
            $this->showEditModal = true;
        }
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedEvent = null;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetEventForm();
    }

    public function closeAllModals()
    {
        $this->showViewModal = false;
        $this->showEditModal = false;
        $this->showCreateModal = false;
        $this->showCreateNoteModal = false;
        $this->showEventModal = false;
        $this->selectedEvent = null;
    }

    public function createNoteFromCalendar($date = null)
    {
        $this->resetValidation();
        $this->resetNoteForm();

        if ($date) {
            // Convert the date string to the proper format for datetime-local input
            if (is_string($date) && strlen($date) > 10) {
                // If it's in Y-m-d\TH:i format, use it as is
                $this->eventStartDate = $date;
            } else {
                // If it's just a date, set to start of day
                $carbonDate = Carbon::parse($date);
                $this->eventStartDate = $carbonDate->format('Y-m-d\\TH:i');
            }
        }

        $this->showCreateNoteModal = true;
    }

    public function resetNoteForm()
    {
        $this->reset([
            'noteTitle',
            'noteContent',
            'noteBookId',
            'noteSubjectId',
            'noteIsPublic',
            'eventStartDate',
            'eventEndDate',
            'eventAllDay',
            'eventColor',
            'eventVisibility',
            'enableReminder',
            'reminderMinutesBefore',
            'reminderChannels',
        ]);
        // Reset reminder defaults
        $this->reminderMinutesBefore = 15;
        $this->reminderChannels = ['email', 'database'];
    }

    public function createNote()
    {
        $this->validate([
            'noteTitle' => 'required|string|max:255',
            'noteContent' => 'required|string',
            'noteBookId' => 'nullable|exists:books,id',
            'noteSubjectId' => 'nullable|exists:academic_subjects,id',
            'noteIsPublic' => 'boolean',
        ]);

        $note = Note::create([
            'title' => $this->noteTitle,
            'content' => $this->noteContent,
            'user_id' => auth()->id(),
            'book_id' => $this->noteBookId,
            'academic_subject_id' => $this->noteSubjectId,
            'is_public' => $this->noteIsPublic,
            'event_type_id' => $this->eventTypeId,
            'event_type' => $this->eventType,
        ]);

        // If calendar integration is requested
        if ($this->eventStartDate) {
            $eventData = [
                'title' => $this->noteTitle,
                'description' => $this->noteContent,
                'start_date' => $this->eventStartDate,
                'end_date' => $this->eventEndDate,
                'all_day' => $this->eventAllDay,
                'color' => $this->eventColor,
                'visibility' => $this->eventVisibility,
                'event_type_id' => $this->eventTypeId,
                'event_type' => $this->eventType,
                'user_id' => auth()->id(),
            ];
            $calendarEvent = $note->createCalendarEvent($eventData);

            // Create reminder if enabled
            if ($this->enableReminder && $calendarEvent) {
                $calendarEvent->createReminder(
                    auth()->id(),
                    (int) $this->reminderMinutesBefore,
                    $this->reminderChannels
                );
            }
        }

        $this->resetNoteForm();
        $this->showCreateNoteModal = false;
        $this->loadEvents(); // Reload events to show the new one

        // Dispatch close modal event
        $this->dispatch('close-modal', name: 'create-note-modal');
        $this->dispatch('noteCreated');

        // Show success message
        session()->flash('success', 'Note created successfully!');
    }

    public function toggleCalendarIntegration()
    {
        // This method will be called from the view totoggle calendar integration fields
        // We'll handle the visibility of calendar fields in JavaScript
    }

    /**
     * Handle markdown editor updates from Alpine.js
     */
    public function handleMarkdownUpdate($data)
    {
        $name = $data['name'] ?? null;
        $value = $data['value'] ?? '';

        if ($name && property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }
}
