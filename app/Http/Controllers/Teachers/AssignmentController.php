<?php

namespace App\Http\Controllers\Teachers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AcademicSubject;
use App\Models\AssignmentNotification;
use App\Events\AssignmentCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        $assignments = Assignment::where('teacher_id', $teacher->id)
            ->with(['academicSubject', 'academicGroups', 'academicLevels', 'students'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return view('models.teachers.assignments.index', compact('assignments'));
    }

    public function create(Request $request)
    {
        $teacher = auth()->user()->teacher;

        // Get teacher's subjects
        $subjects = $teacher->subjects()->with('academicLevel.academicGroup')->get();

        // Get teacher's academic groups and levels
        $academicGroups = $teacher->academicGroups;
        $academicLevels = $teacher->academicLevels;
        $studentGroups = $teacher->studentGroups;

        // Pre-select subject if provided
        $selectedSubject = null;
        if ($request->has('subject')) {
            $selectedSubject = AcademicSubject::find($request->subject);
        }

        return view('teachers.assignments.create', compact(
            'subjects',
            'academicGroups',
            'academicLevels',
            'studentGroups',
            'selectedSubject'
        ));
    }

    public function store(Request $request)
    {
        $teacher = auth()->user()->teacher;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,examination',
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'duration_in_minutes' => 'required|integer|min:1',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'is_randomized' => 'boolean',
            'instructions' => 'nullable|string',
            'assignment_targets' => 'required|array',
            'assignment_targets.*' => 'required|string',
            'topics' => 'nullable|array',
            'topics.*' => 'exists:academic_topics,id',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string',
            'sections.*.instructions' => 'nullable|string',
            'sections.*.question_type' => 'required|in:multiple_choice,true_false,essay',
            'sections.*.question_count' => 'required|integer|min:1',
            'sections.*.marks_per_question' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $teacher, $request) {
            // Create assignment
            $assignment = Assignment::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'academic_subject_id' => $validated['academic_subject_id'],
                'teacher_id' => $teacher->id,
                'duration_in_minutes' => $validated['duration_in_minutes'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'is_randomized' => $validated['is_randomized'] ?? false,
                'instructions' => $validated['instructions'],
                'status' => 'published',
                'total_marks' => collect($validated['sections'])->sum(function ($section) {
                    return $section['question_count'] * $section['marks_per_question'];
                }),
            ]);

            // Create assignment sections
            foreach ($validated['sections'] as $index => $section) {
                $assignment->assignmentSections()->create([
                    'title' => $section['title'],
                    'instructions' => $section['instructions'],
                    'question_type' => $section['question_type'],
                    'question_count' => $section['question_count'],
                    'marks_per_question' => $section['marks_per_question'],
                    'order' => $index + 1,
                ]);
            }

            // Attach targets (groups, levels, students)
            foreach ($validated['assignment_targets'] as $target) {
                [$type, $id] = explode(':', $target);

                switch ($type) {
                    case 'academic_group':
                        $assignment->academicGroups()->attach($id);
                        break;
                    case 'academic_level':
                        $assignment->academicLevels()->attach($id);
                        break;
                    case 'student_group':
                        $assignment->studentGroups()->attach($id);
                        break;
                    case 'student':
                        $assignment->students()->attach($id);
                        break;
                }
            }

            // Attach topics if provided
            if (!empty($validated['topics'])) {
                $assignment->topics()->attach($validated['topics']);
            }

            // Create notifications for eligible students
            $this->createStudentNotifications($assignment);

            // Dispatch event
            AssignmentCreated::dispatch($assignment);
        });

        return redirect()->route('teachers.assignments.index')
            ->with('success', 'Assignment created successfully and students have been notified.');
    }

    private function createStudentNotifications(Assignment $assignment)
    {
        $students = $assignment->getEligibleStudents();

        foreach ($students as $student) {
            AssignmentNotification::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'notified_at' => now(),
                'message' => "New {$assignment->type} assigned: {$assignment->title} for {$assignment->academicSubject->name}. Due: {$assignment->ends_at->format('M d, Y h:i A')}",
            ]);
        }
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load([
            'academicSubject',
            'assignmentSections',
            'academicGroups',
            'academicLevels',
            'studentGroups',
            'students.user',
            'submissions.student.user'
        ]);

        $submissionStats = [
            'total_students' => $assignment->getEligibleStudents()->count(),
            'submitted' => $assignment->submissions()->where('status', 'submitted')->count(),
            'in_progress' => $assignment->submissions()->where('status', 'in_progress')->count(),
            'not_started' => $assignment->getEligibleStudents()->count() - $assignment->submissions()->count(),
        ];

        return view('teachers.assignments.show', compact('assignment', 'submissionStats'));
    }
}
