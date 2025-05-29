<?php

namespace App\Http\Controllers;

use App\Models\LessonNote;
use Illuminate\Http\Request;
use App\Http\Resources\LessonNoteResource;
use App\Http\Resources\LessonNoteCollection;

class LessonNoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LessonNote::class, 'lessonNote');
    }

    public function index(Request $request)
    {
        $query = LessonNote::with('teacher', 'lesson', 'subject', 'topic');
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }
        
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }
        
        return new LessonNoteCollection($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'lesson_id' => 'required|exists:lessons,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lesson-notes', 'public');
        }
        
        $lessonNote = LessonNote::create([
            'teacher_id' => $validated['teacher_id'],
            'lesson_id' => $validated['lesson_id'],
            'subject_id' => $validated['subject_id'],
            'topic_id' => $validated['topic_id'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
        ]);

        return new LessonNoteResource($lessonNote->load('teacher', 'lesson', 'subject', 'topic'));
    }

    public function show(LessonNote $lessonNote)
    {
        return new LessonNoteResource($lessonNote->load('teacher', 'lesson', 'subject', 'topic'));
    }

    public function update(Request $request, LessonNote $lessonNote)
    {
        $validated = $request->validate([
            'teacher_id' => 'sometimes|required|exists:teachers,id',
            'lesson_id' => 'sometimes|required|exists:lessons,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'topic_id' => 'sometimes|required|exists:topics,id',
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($lessonNote->file_path) {
                Storage::disk('public')->delete($lessonNote->file_path);
            }
            
            $validated['file_path'] = $request->file('file')->store('lesson-notes', 'public');
        }
        
        $lessonNote->update($validated);

        return new LessonNoteResource($lessonNote->load('teacher', 'lesson', 'subject', 'topic'));
    }

    public function destroy(LessonNote $lessonNote)
    {
        // Delete file if exists
        if ($lessonNote->file_path) {
            Storage::disk('public')->delete($lessonNote->file_path);
        }
        
        $lessonNote->delete();

        return response()->noContent();
    }
}