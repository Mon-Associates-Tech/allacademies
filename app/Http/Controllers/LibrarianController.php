<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookApprovalResource;
use App\Http\Resources\BookBorrowingResource;
use App\Http\Resources\GroupBookSubscriptionResource;
use App\Http\Resources\LibrarianCollection;
use App\Http\Resources\LibrarianResource;
use App\Models\Book;
use App\Models\BookApproval;
use App\Models\BookBorrowing;
use App\Models\GroupBookSubscription;
use App\Models\Librarian;
use App\Models\Student;
use App\Models\StudentGroup;
use Illuminate\Http\Request;

class LibrarianController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Librarian::class, 'librarian');
    }

    public function index()
    {
        return new LibrarianCollection(Librarian::with('user')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $librarian = Librarian::create($validated);

        return new LibrarianResource($librarian->load('user'));
    }

    public function show(Librarian $librarian)
    {
        return new LibrarianResource($librarian->load('user', 'bookApprovals'));
    }

    public function update(Request $request, Librarian $librarian)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $librarian->update($validated);

        return new LibrarianResource($librarian->load('user'));
    }

    public function destroy(Librarian $librarian)
    {
        $librarian->delete();

        return response()->noContent();
    }

    // Additional methods specific to librarians

    public function approveBook(Request $request, Librarian $librarian)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'status' => 'required|in:approved,rejected,pending',
            'comments' => 'nullable|string',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $this->authorize('approve', $book);

        $approval = BookApproval::create([
            'book_id' => $book->id,
            'librarian_id' => $librarian->id,
            'status' => $validated['status'],
            'comments' => $validated['comments'] ?? null,
        ]);

        return new BookApprovalResource($approval->load('book'));
    }

    public function lendBook(Request $request, Librarian $librarian)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $student = Student::findOrFail($validated['student_id']);

        if (! $book->has_hardcopy) {
            return response()->json(['message' => 'This book does not have a hardcopy available for lending'], 422);
        }

        $borrowing = BookBorrowing::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        return new BookBorrowingResource($borrowing->load('student', 'book'));
    }

    public function processBookReturn(Request $request, Librarian $librarian)
    {
        $validated = $request->validate([
            'borrowing_id' => 'required|exists:book_borrowings,id',
        ]);

        $borrowing = BookBorrowing::findOrFail($validated['borrowing_id']);

        if ($borrowing->status === 'returned') {
            return response()->json(['message' => 'This book has already been returned'], 422);
        }

        $borrowing->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        return new BookBorrowingResource($borrowing->load('student', 'book'));
    }

    public function subscribeGroupToBook(Request $request, Librarian $librarian)
    {
        $validated = $request->validate([
            'student_group_id' => 'required|exists:student_groups,id',
            'book_id' => 'required|exists:books,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $studentGroup = StudentGroup::findOrFail($validated['student_group_id']);
        $this->authorize('groupSubscribe', $book);

        if (! $book->has_softcopy) {
            return response()->json(['message' => 'This book does not have a softcopy available for subscription'], 422);
        }

        $subscription = GroupBookSubscription::create([
            'student_group_id' => $studentGroup->id,
            'book_id' => $book->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active',
            'subscribed_by_type' => 'App\Models\Librarian',
            'subscribed_by_id' => $librarian->id,
        ]);

        return new GroupBookSubscriptionResource($subscription->load('studentGroup', 'book'));
    }

    public function getBookApprovals(Librarian $librarian)
    {
        $approvals = $librarian->bookApprovals()->with('book')->paginate();

        return BookApprovalResource::collection($approvals);
    }

    public function getBookLendings(Librarian $librarian)
    {
        // While there's no direct relation, we can get all active borrowings
        $borrowings = BookBorrowing::where('status', 'borrowed')
            ->with('student', 'book')
            ->paginate();

        return BookBorrowingResource::collection($borrowings);
    }

    public function getGroupSubscriptions(Librarian $librarian)
    {
        $subscriptions = GroupBookSubscription::where('subscribed_by_type', 'App\Models\Librarian')
            ->where('subscribed_by_id', $librarian->id)
            ->with('studentGroup', 'book')
            ->paginate();

        return GroupBookSubscriptionResource::collection($subscriptions);
    }
}
