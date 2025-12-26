<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Note;
use App\Services\CalendarEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarEventsController extends Controller
{
    protected CalendarEventService $calendarEventService;

    public function __construct(CalendarEventService $calendarEventService)
    {
        $this->calendarEventService = $calendarEventService;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $events = $this->calendarEventService->getEventsForUser(Auth::id(), $startDate, $endDate);

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'all_day' => 'boolean',
            'color' => 'nullable|string',
            'visibility' => 'required|in:private,public,shared',
            'event_type' => 'nullable|string', // For linking to other models
            'event_id' => 'nullable|integer',   // For linking to other models
        ]);

        $eventData = [
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'all_day' => $request->boolean('all_day', false),
            'color' => $request->color,
            'visibility' => $request->visibility,
        ];

        // If event_type and event_id are provided, link to existing model
        $relatedModel = null;
        if ($request->filled('event_type') && $request->filled('event_id')) {
            $relatedModel = $request->event_type::find($request->event_id);
        }

        $event = $this->calendarEventService->createEvent($eventData, $relatedModel);

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    public function show(CalendarEvent $event)
    {
        if (!$event->canUserView(Auth::id())) {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'event' => $event->load('user', 'event')
        ]);
    }

    public function update(Request $request, CalendarEvent $event)
    {
        if (!$event->canUserEdit(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'all_day' => 'boolean',
            'color' => 'nullable|string',
            'visibility' => 'required|in:private,public,shared',
        ]);

        $eventData = [
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'all_day' => $request->boolean('all_day', false),
            'color' => $request->color,
            'visibility' => $request->visibility,
        ];

        $event = $this->calendarEventService->updateEvent($event, $eventData);

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    public function destroy(CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    public function createNoteFromEvent(Request $request, CalendarEvent $event)
    {
        if (!$event->canUserEdit(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'book_id' => 'nullable|exists:books,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'is_public' => 'boolean',
        ]);

        $note = Note::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'academic_subject_id' => $request->academic_subject_id,
            'is_public' => $request->boolean('is_public'),
        ]);

        // Update the event to reference the note
        $event->update([
            'event_type' => Note::class,
            'event_id' => $note->id,
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
            'event' => $event
        ]);
    }
}