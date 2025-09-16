<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupBookSubscriptionResource;
use App\Http\Resources\LessonNoteResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\StudentGroupResource;
use App\Http\Resources\TeacherCollection;
use App\Http\Resources\TeacherResource;
use App\Models\Book;
use App\Models\GroupBookSubscription;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Teacher::class, 'teacher');
    }

    public function index()
    {
        return new TeacherCollection(Teacher::with('user')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $teacher = Teacher::create($validated);

        return new TeacherResource($teacher->load('user'));
    }

    public function show(Teacher $teacher)
    {
        return new TeacherResource($teacher->load('user', 'studentGroups', 'lessons', 'lessonNotes'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $teacher->update($validated);

        return new TeacherResource($teacher->load('user'));
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->noContent();
    }

    // Additional methods specific to teachers

    public function createStudentGroup(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $studentGroup = StudentGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => $teacher->id,
        ]);

        return new StudentGroupResource($studentGroup);
    }

    public function getStudentGroups(Teacher $teacher)
    {
        $groups = $teacher->studentGroups()->paginate();
        return StudentGroupResource::collection($groups);
    }

    public function createLesson(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'student_group_id' => 'required|exists:student_groups,id',
        ]);

        // Verify that the teacher owns this student group
        $studentGroup = StudentGroup::findOrFail($validated['student_group_id']);
        if ($studentGroup->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'This student group does not belong to this teacher'], 403);
        }

        $lesson = Lesson::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'student_group_id' => $validated['student_group_id'],
        ]);

        return new LessonResource($lesson->load('subject', 'studentGroup'));
    }

    public function uploadLessonNote(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Verify that the teacher owns this lesson
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        if ($lesson->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'This lesson does not belong to this teacher'], 403);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lesson-notes', 'public');
        }

        $lessonNote = LessonNote::create([
            'teacher_id' => $teacher->id,
            'lesson_id' => $validated['lesson_id'],
            'subject_id' => $validated['subject_id'],
            'topic_id' => $validated['topic_id'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
        ]);

        return new LessonNoteResource($lessonNote->load('lesson', 'subject', 'topic'));
    }

    public function subscribeGroupToBook(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'student_group_id' => 'required|exists:student_groups,id',
            'book_id' => 'required|exists:books,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Verify that the teacher owns this student group
        $studentGroup = StudentGroup::findOrFail($validated['student_group_id']);
        if ($studentGroup->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'This student group does not belong to this teacher'], 403);
        }

        $book = Book::findOrFail($validated['book_id']);
        $this->authorize('groupSubscribe', $book);

        if (!$book->has_softcopy) {
            return response()->json(['message' => 'This book does not have a softcopy available for subscription'], 422);
        }

        $subscription = GroupBookSubscription::create([
            'student_group_id' => $validated['student_group_id'],
            'book_id' => $validated['book_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active',
            'subscribed_by_type' => 'App\Models\Teacher',
            'subscribed_by_id' => $teacher->id,
        ]);

        return new GroupBookSubscriptionResource($subscription->load('studentGroup', 'book'));
    }

    public function getLessons(Teacher $teacher)
    {
        $lessons = $teacher->lessons()->with('subject', 'studentGroup')->paginate();
        return LessonResource::collection($lessons);
    }

    public function getLessonNotes(Teacher $teacher)
    {
        $notes = $teacher->lessonNotes()->with('lesson', 'subject', 'topic')->paginate();
        return LessonNoteResource::collection($notes);
    }

    public function getGroupSubscriptions(Teacher $teacher)
    {
        $subscriptions = GroupBookSubscription::where('subscribed_by_type', 'App\Models\Teacher')
            ->where('subscribed_by_id', $teacher->id)
            ->with('studentGroup', 'book')
            ->paginate();

        return GroupBookSubscriptionResource::collection($subscriptions);
    }
}
