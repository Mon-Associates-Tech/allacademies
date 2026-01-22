<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentGroupCollection;
use App\Http\Resources\StudentGroupResource;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\StudentGroup;
use Illuminate\Http\Request;

class StudentGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StudentGroup::class, 'studentGroup');
    }

    public function index()
    {
        return new StudentGroupCollection(StudentGroup::with('teacher')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $studentGroup = StudentGroup::create($validated);

        return new StudentGroupResource($studentGroup->load('teacher'));
    }

    public function show(StudentGroup $studentGroup)
    {
        return new StudentGroupResource($studentGroup->load('teacher', 'students', 'subscriptions'));
    }

    public function update(Request $request, StudentGroup $studentGroup)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'sometimes|required|exists:teachers,id',
        ]);

        $studentGroup->update($validated);

        return new StudentGroupResource($studentGroup->load('teacher'));
    }

    public function destroy(StudentGroup $studentGroup)
    {
        $studentGroup->delete();

        return response()->noContent();
    }

    // Additional methods specific to student groups

    public function addStudent(Request $request, StudentGroup $studentGroup)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $this->authorize('addStudent', $studentGroup);

        $student->update([
            'student_group_id' => $studentGroup->id,
        ]);

        return new StudentResource($student->load('user', 'group'));
    }

    public function removeStudent(Request $request, StudentGroup $studentGroup)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $this->authorize('addStudent', $studentGroup);

        if ($student->student_group_id !== $studentGroup->id) {
            return response()->json(['message' => 'This student is not in this group'], 422);
        }

        $student->update([
            'student_group_id' => null,
        ]);

        return new StudentResource($student->load('user'));
    }

    public function getStudents(StudentGroup $studentGroup)
    {
        $students = $studentGroup->students()->with('user')->paginate();

        return StudentResource::collection($students);
    }
}
