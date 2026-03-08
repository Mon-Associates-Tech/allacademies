<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssessmentResource;
use App\Http\Resources\BookBorrowingResource;
use App\Http\Resources\BookSubscriptionResource;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\StudentResource;
use App\Models\AcademicLevel;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends BaseSchoolController
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Student::class, 'student');
    }

    public function index()
    {
        return view('students.index');
        // return new StudentCollection(Student::with('user', 'group')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'student_group_id' => 'nullable|exists:student_groups,id',
        ]);

        $student = Student::create($validated);

        return new StudentResource($student->load('user', 'group'));
    }

    public function show(Student $student)
    {
        return new StudentResource($student->load('user', 'group', 'borrowedBooks', 'subscriptions', 'assessments'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'student_group_id' => 'nullable|exists:student_groups,id',
        ]);

        $student->update($validated);

        return new StudentResource($student->load('user', 'group'));
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->noContent();
    }

    // Additional methods specific to students

    public function borrowBook(Request $request, Student $student)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $this->authorize('borrow', $book);

        if (! $book->has_hardcopy) {
            return response()->json(['message' => 'This book does not have a hardcopy available for borrowing'], 422);
        }

        $borrowing = BookBorrowing::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        return new BookBorrowingResource($borrowing);
    }

    public function returnBook(Request $request, Student $student, BookBorrowing $borrowing)
    {
        $this->authorize('return', $borrowing);

        if ($borrowing->student_id !== $student->id) {
            return response()->json(['message' => 'This book was not borrowed by this student'], 403);
        }

        if ($borrowing->status === 'returned') {
            return response()->json(['message' => 'This book has already been returned'], 422);
        }

        $borrowing->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        return new BookBorrowingResource($borrowing);
    }

    public function subscribeToBook(Request $request, Student $student)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $this->authorize('subscribe', $book);

        if (! $book->has_softcopy) {
            return response()->json(['message' => 'This book does not have a softcopy available for subscription'], 422);
        }

        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active',
        ]);

        return new BookSubscriptionResource($subscription);
    }

    public function cancelSubscription(Student $student, BookSubscription $subscription)
    {
        $this->authorize('update', $subscription);

        if ($subscription->student_id !== $student->id) {
            return response()->json(['message' => 'This subscription does not belong to this student'], 403);
        }

        $subscription->update([
            'status' => 'cancelled',
        ]);

        return new BookSubscriptionResource($subscription);
    }

    public function createAssessment(Request $request, Student $student)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'score' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        // Check if student has borrowed or subscribed to this book
        $hasAccess = $student->borrowedBooks()->where('book_id', $validated['book_id'])->exists() ||
                     $student->subscriptions()->where('book_id', $validated['book_id'])->exists();

        if (! $hasAccess) {
            return response()->json(['message' => 'Student must borrow or subscribe to this book before creating an assessment'], 422);
        }

        $assessment = Assessment::create([
            'student_id' => $student->id,
            'book_id' => $validated['book_id'],
            'score' => $validated['score'],
            'comments' => $validated['comments'] ?? null,
        ]);

        return new AssessmentResource($assessment);
    }

    public function getBorrowedBooks(Student $student)
    {
        $borrowings = $student->borrowedBooks()->with('book')->paginate();

        return BookBorrowingResource::collection($borrowings);
    }

    public function getSubscriptions(Student $student)
    {
        $subscriptions = $student->subscriptions()->with('book')->paginate();

        return BookSubscriptionResource::collection($subscriptions);
    }

    public function getAssessments(Student $student)
    {
        $assessments = $student->assessments()->with('book')->paginate();

        return AssessmentResource::collection($assessments);
    }

    public function indexNew()
    {
        $this->authorize('students.view');

        $students = Student::with(['user', 'academicLevel', 'academicGroup'])
            ->when(request('academic_level'), function ($query, $level) {
                $query->where('academic_level_id', $level);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(20);

        $academicLevels = AcademicLevel::with('academicGroup')->get();

        return view('students.index', compact('students', 'academicLevels'));
    }

    public function storeNew(Request $request)
    {
        $this->authorize('students.create');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'academic_level_id' => 'required|exists:academic_levels,id',
            'academic_group_id' => 'required|exists:academic_groups,id',
        ]);

        // Ensure academic level belongs to current school
        $academicLevel = AcademicLevel::where('school_id', $this->school->id)
            ->findOrFail($validated['academic_level_id']);

        \DB::transaction(function () use ($validated) {
            // Create user
            $user = \App\Models\User::create([
                'school_id' => $this->school->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Hash::make('temporary_password'),
                'role' => 'student',
                'status' => 'active',
            ]);

            // Student record is auto-created by User model observer
            $student = $user->student;
            $student->update([
                'academic_level_id' => $validated['academic_level_id'],
                'academic_group_id' => $validated['academic_group_id'],
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully');
    }
}
