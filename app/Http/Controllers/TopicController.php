<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonNoteResource;
use App\Http\Resources\TopicCollection;
use App\Http\Resources\TopicResource;
use App\Models\AcademicTopic as Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Topic::class, 'topic');
    }

    public function index(Request $request)
    {
        $query = Topic::with('subject');

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        return new TopicCollection($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $topic = Topic::create($validated);

        return new TopicResource($topic->load('subject'));
    }

    public function show(Topic $topic)
    {
        return new TopicResource($topic->load('subject', 'lessonNotes'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'sometimes|required|exists:subjects,id',
        ]);

        $topic->update($validated);

        return new TopicResource($topic->load('subject'));
    }

    public function destroy(Topic $topic)
    {
        // Check if topic has lesson notes
        if ($topic->lessonNotes()->exists()) {
            return response()->json(['message' => 'Cannot delete topic that has lesson notes assigned to it'], 422);
        }

        $topic->delete();

        return response()->noContent();
    }

    public function getLessonNotes(Topic $topic)
    {
        $notes = $topic->lessonNotes()->with('teacher', 'lesson', 'subject')->paginate();

        return LessonNoteResource::collection($notes);
    }
}
