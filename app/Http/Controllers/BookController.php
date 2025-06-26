<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $query = Book::with(['author', 'bookCategory']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('author', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('category')) {
            $query->where('book_category_id', $request->category);
        }

        if ($request->filled('format')) {
            switch ($request->format) {
                case 'hardcopy':
                    $query->where('has_hardcopy', true);
                    break;
                case 'softcopy':
                    $query->where('has_softcopy', true);
                    break;
                case 'both':
                    $query->where('has_hardcopy', true)->where('has_softcopy', true);
                    break;
            }
        }

        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->whereNull('annual_subscription_fee')->orWhere('annual_subscription_fee', 0);
            } else {
                $query->where('annual_subscription_fee', '>', 0);
            }
        }

        $books = $query->paginate(12);
        $categories = BookCategory::all();

        // Get user's subscriptions and borrowings for status checking
        $subscribedBookIds = $student ? $student->bookSubscriptions()
            ->where('status', 'active')
            ->pluck('book_id')->toArray() : [];

        $borrowedBookIds = $student ? $student->borrowedBooks()
            ->where('status', 'borrowed')
            ->pluck('book_id')->toArray() : [];

        return view('books.index', compact('books', 'categories', 'subscribedBookIds', 'borrowedBookIds'));
    }

    public function show(Book $book)
    {
        $book->load(['author', 'bookCategory']);

        $user = Auth::user();
        $student = $user->student ?? null;

        $isSubscribed = false;
        $isBorrowed = false;
        $subscription = null;
        $borrowing = null;

        if ($student) {
            $subscription = $student->bookSubscriptions()
                ->where('book_id', $book->id)
                ->where('status', 'active')
                ->first();
            $isSubscribed = (bool) $subscription;

            $borrowing = $student->borrowedBooks()
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            $isBorrowed = (bool) $borrowing;
        }

        return view('books.show', compact('book', 'isSubscribed', 'isBorrowed', 'subscription', 'borrowing'));
    }

    public function subscribe(Request $request, Book $book)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['error' => 'Student profile required'], 403);
        }

        // Check if already subscribed
        $existingSubscription = $student->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return response()->json(['error' => 'Already subscribed to this book'], 400);
        }

        // Free book - direct subscription
        if (!$book->annual_subscription_fee || $book->annual_subscription_fee == 0) {
            $subscription = BookSubscription::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
                'annual_fee' => 0,
                'reference' => 'FREE_' . uniqid(),
                'payment_completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully added to your library!',
                'subscription' => $subscription
            ]);
        }

        // Paid book - create pending subscription
        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'pending_payment',
            'annual_fee' => $book->annual_subscription_fee,
            'reference' => 'SUB_' . uniqid()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created. Please complete payment.',
            'subscription' => $subscription,
            'requires_payment' => true
        ]);
    }

    public function requestBorrow(Request $request, Book $book)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['error' => 'Student profile required'], 403);
        }

        if (!$book->has_hardcopy) {
            return response()->json(['error' => 'This book is not available in hardcopy format'], 400);
        }

        // Check if already borrowed
        $existingBorrowing = $student->bookBorrowings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'pending_approval'])
            ->first();

        if ($existingBorrowing) {
            return response()->json(['error' => 'Book already borrowed or request pending'], 400);
        }

        $borrowing = BookBorrowing::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'request_date' => now(),
            'status' => 'pending_approval',
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Borrow request submitted successfully!',
            'borrowing' => $borrowing
        ]);
    }

    public function read(Book $book)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('books.show', $book)->with('error', 'Student profile required');
        }

        if (!$book->has_softcopy) {
            return redirect()->route('books.show', $book)->with('error', 'This book is not available for online reading');
        }

        // Check subscription for paid books
        if ($book->annual_subscription_fee > 0) {
            $subscription = $student->bookSubscriptions()
                ->where('book_id', $book->id)
                ->where('status', 'active')
                ->first();

            if (!$subscription) {
                return redirect()->route('books.show', $book)->with('error', 'Subscription required to read this book');
            }
        }

        return view('books.read', compact('book'));
    }
}
