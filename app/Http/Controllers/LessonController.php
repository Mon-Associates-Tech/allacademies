<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonCollection;
use App\Http\Resources\LessonNoteResource;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Lesson::class, 'lesson');
    }

    public function index(Request $request)
    {
        $query = Lesson::with('teacher', 'subject', 'studentGroup');

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('student_group_id')) {
            $query->where('student_group_id', $request->student_group_id);
        }

        return new LessonCollection($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'student_group_id' => 'required|exists:student_groups,id',
        ]);

        $lesson = Lesson::create($validated);

        return new LessonResource($lesson->load('teacher', 'subject', 'studentGroup'));
    }

    public function show(Lesson $lesson)
    {
        return new LessonResource($lesson->load('teacher', 'subject', 'studentGroup', 'notes'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'sometimes|required|exists:teachers,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'student_group_id' => 'sometimes|required|exists:student_groups,id',
        ]);

        $lesson->update($validated);

        return new LessonResource($lesson->load('teacher', 'subject', 'studentGroup'));
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return response()->noContent();
    }

    public function getNotes(Lesson $lesson)
    {
        $notes = $lesson->notes()->with('teacher', 'subject', 'topic')->paginate();

        return LessonNoteResource::collection($notes);
    }
}
