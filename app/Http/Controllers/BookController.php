<?php

namespace App\Http\Controllers;

use App\Enums\PublishingStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Book::with(['author', 'bookCategory'])->whereStatus(PublishingStatus::PUBLISHED->value);

        // Search filter (title or author)
        if ($request->query('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('author', function ($authorQuery) use ($searchTerm) {
                        $authorQuery->where('name', 'like', '%' . $searchTerm . '%');
                    });
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
            } elseif ($request->price === 'subscribed') {
                $query->whereHas('subscriptions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('status', 'paid');
                });
            } else {
                $query->where('annual_subscription_fee', '>', 0);
            }
        }

        $books = $query->paginate(12)->appends($request->query());
        $categories = BookCategory::all();

        // Get user's subscriptions and borrowings for status checking
        $subscribedBookIds = $user->bookSubscriptions()
            ->where('status', 'paid')
            ->pluck('book_id')->toArray() ?: [];

        $borrowedBookIds = $user->borrowedBooks()
            ->where('status', 'borrowed')
            ->pluck('book_id')->toArray() ?: [];

        return view('books.index', compact('books', 'categories', 'subscribedBookIds', 'borrowedBookIds'));
    }

    public function show(Book $book)
    {
        $book->load([
            'author',
            'bookCategory',
            'categories',
            'reviews' => function ($query) {
                $query->approved()
                    ->with('user')
                    ->latest()
                    ->limit(5);
            }
        ]);

        // Get related books from the same category
        $relatedBooks = Book::with(['author', 'categories']) // Changed from 'bookCategory' to 'categories'
        ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->whereHas('categories', function ($q) use ($book) { // Changed approach
                $q->whereIn('category_id', $book->categories->pluck('id'));
            })
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        $recentReviews = $book->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->limit(3)
            ->get();

        $user = Auth::user();

        $isSubscribed = false;
        $isBorrowed = false;
        $subscription = null;
        $borrowing = null;

        if ($user) {
            $subscription = $user->bookSubscriptions()
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->first();
            $isSubscribed = (bool)$subscription;

            $borrowing = $user->borrowedBooks()
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            $isBorrowed = (bool)$borrowing;
        }

        $canRead = $isSubscribed || !$book->has_softcopy || $book->author->user?->id === $user->id;

        return view('books.show',
            compact('book', 'isSubscribed', 'isBorrowed', 'subscription', 'borrowing', 'canRead', 'recentReviews')
        );
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function subscribe(Request $request, Book $book)
    {
        $user = Auth::user();
        $student = $user->student;


        // Check if already subscribed
        $existingSubscription = $user->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', SubscriptionStatus::PAID)
            ->first();

        if ($existingSubscription) {
            return response()->json(['error' => 'Already subscribed to this book'], 400);
        }

        // Free book - direct subscription
        if (!$book->annual_subscription_fee || $book->annual_subscription_fee == 0) {
            $subscription = BookSubscription::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => SubscriptionStatus::PAID,
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
            'user_id' => $user->id,
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

    public function create()
    {
        return view('books.create');
    }

    public function requestBorrow(Request $request, Book $book)
    {
        $user = Auth::user();

        if (!$book->has_hardcopy) {
            return response()->json(['error' => 'This book is not available in hardcopy format'], 400);
        }

        // Check if already borrowed
        $existingBorrowing = $user->bookBorrowings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'pending_approval'])
            ->first();

        if ($existingBorrowing) {
            return response()->json(['error' => 'Book already borrowed or request pending'], 400);
        }

        $borrowing = BookBorrowing::create([
            'user_id' => $user->id,
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

        if (!$book->has_softcopy) {
            // todo: uncomment
            // return redirect()->route('books.show', $book)->with('error', 'This book is not available for online reading');
        }

        // Check subscription for paid books
        if ($book->annual_subscription_fee > 0) {
            $subscription = $user->bookSubscriptions()
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->first();

            if (!$subscription && $book->author->user->id !== $user->id) {
                return redirect()->route('books.show', $book)->with('error', 'Subscription required to read this book');
            }
        }

        return view('books.read', compact('book'));
    }

    public function preview(Book $book)
    {
        return view('books.preview', compact('book'));
    }

    /**
     * Get books by category for AJAX requests
     */
    public function getByCategory(Request $request, BookCategory $category)
    {
        $books = Book::with(['author', 'categories']) // Changed from 'bookCategory' to 'categories'
        ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->whereHas('categories', function ($query) use ($category) { // Changed approach
                $query->where('category_id', $category->id);
            })
            ->when($request->limit, function ($query, $limit) {
                return $query->limit($limit);
            }, function ($query) {
                return $query->paginate(12);
            })
            ->latest()
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'books' => $books,
                'category' => $category
            ]);
        }

        return view('books.category', compact('books', 'category'));
    }

    /**
     * Get featured/popular books for homepage
     */
    public function getFeatured()
    {
        $featuredBooks = Book::with(['author', 'bookCategory'])
            ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->where('is_featured', true) // Assuming you have a featured flag
            ->orWhereHas('subscriptions', function ($query) {
                $query->where('status', 'paid');
            })
            ->latest()
            ->limit(8)
            ->get();

        return response()->json(['books' => $featuredBooks]);
    }

    /**
     * Public book view for unauthenticated users (shared links)
     */
    public function publicShow(Book $book)
    {
        // Only show published books
        if ($book->status !== PublishingStatus::PUBLISHED->value) {
            abort(404);
        }

        $book->load([
            'author',
            'bookCategory',
            'categories',
        ]);

        // Get a few approved reviews without user data for privacy
        $recentReviews = $book->reviews()
            ->approved()
            ->latest()
            ->limit(3)
            ->get();

        return view('books.public-show', compact('book', 'recentReviews'));
    }
}
