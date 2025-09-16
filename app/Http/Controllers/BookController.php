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
        $student = $user->student;

        // Get all categories for filter dropdown
        $categories = BookCategory::all();

        // Get top 3 categories with their books (only when no filters are applied)
        $topCategories = collect();
        if (!$request->hasAny(['search', 'category', 'format', 'price'])) {
            $topCategories = BookCategory::with(['books' => function($query) {
                $query->with(['author', 'categories']) // Changed from 'bookCategory' to 'categories'
                ->whereStatus(PublishingStatus::PUBLISHED->value)
                    ->latest()
                    ->limit(4);
            }])
                ->withCount('books')
                ->orderBy('books_count', 'desc')
                ->limit(3)
                ->get();
        }

        // Main books query
        $query = Book::with(['author', 'categories']);


        // Search filter (title, author, or genre)
        if ($request->query('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('author', function ($authorQuery) use ($searchTerm) {
                        $authorQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('categories', function ($categoryQuery) use ($searchTerm) { // Changed from 'bookCategory'
                        $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('book_category.id', $request->category);
            });
        }

        // Format filter
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

        // Price filter
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where(function($q) {
                    $q->whereNull('annual_subscription_fee')
                        ->orWhere('annual_subscription_fee', 0);
                });
            } elseif ($request->price === 'subscribed') {
                $query->whereHas('subscriptions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->where('status', 'paid');
                });
            } else {
                $query->where('annual_subscription_fee', '>', 0);
            }
        }

        // Order by latest for better user experience
        $query->latest();

        $books = $query->paginate(12)->appends($request->query());

        // Get user's subscriptions and borrowings for status checking
        $subscribedBookIds = $user->bookSubscriptions()
            ->where('status', 'paid')
            ->pluck('book_id')->toArray() ?: [];

        $borrowedBookIds = $user->borrowedBooks()
            ->where('status', 'borrowed')
            ->pluck('book_id')->toArray() ?: [];

        return view('books.index', compact(
            'books',
            'categories',
            'topCategories',
            'subscribedBookIds',
            'borrowedBookIds'
        ));
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
            ->whereHas('categories', function($q) use ($book) { // Changed approach
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
            $isSubscribed = (bool) $subscription;

            $borrowing = $user->borrowedBooks()
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            $isBorrowed = (bool) $borrowing;
        }

        $canRead = $isSubscribed || !$book->has_softcopy || $book->author->user->id === $user->id;
        $category = $book->categories()->first();

        return view('books.show',
            compact('book', 'isSubscribed', 'isBorrowed', 'subscription', 'borrowing', 'canRead', 'recentReviews', 'relatedBooks', 'category')
        );
    }

    public function create()
    {
        $categories = BookCategory::all();
        return view('books.create', compact('categories'));
    }

    public function edit(Book $book)
    {
        $categories = BookCategory::all();
        return view('books.edit', compact('book', 'categories'));
    }

    public function subscribe(Request $request, Book $book)
    {
        $user = Auth::user();

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
            ->whereHas('categories', function($query) use ($category) { // Changed approach
                $query->where('category_id', $category->id);
            })
            ->when($request->limit, function($query, $limit) {
                return $query->limit($limit);
            }, function($query) {
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
            ->orWhereHas('subscriptions', function($query) {
                $query->where('status', 'paid');
            })
            ->latest()
            ->limit(8)
            ->get();

        return response()->json(['books' => $featuredBooks]);
    }
}
