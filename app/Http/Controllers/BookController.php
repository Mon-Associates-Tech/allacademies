<?php

namespace App\Http\Controllers;

use App\Enums\PublishingStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Book;
use App\Models\BookAnnotation;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookReadingProgress;
use App\Models\BookSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Book::with([
            'author',
            'categories',
            'bookCategory',
        ])->whereStatus(PublishingStatus::PUBLISHED->value);

        // Search filter (title or author)
        if ($request->query('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')->orWhereHas(
                    'author',
                    function ($authorQuery) use ($searchTerm) {
                        $authorQuery->where(
                            'name',
                            'like',
                            '%'.$searchTerm.'%',
                        );
                    },
                );
            });
        }

        if ($request->filled('categories')) {
            $categories = is_array($request->categories)
                ? $request->categories
                : [$request->categories];
            $query->where(function ($q) use ($categories) {
                $q->whereHas('categories', function ($query) use ($categories) {
                    $query->whereIn('book_category.category_id', $categories);
                })->orWhereIn('book_category_id', $categories);
            });
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
                    $query
                        ->where('has_hardcopy', true)
                        ->where('has_softcopy', true);
                    break;
            }
        }

        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query
                    ->whereNull('annual_subscription_fee')
                    ->orWhere('annual_subscription_fee', 0);
            } elseif ($request->price === 'subscribed') {
                $query->whereHas('subscriptions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('status', 'paid');
                });
            } else {
                $query->where('annual_subscription_fee', '>', 0);
            }
        }

        if ($request->filled('age_groups')) {
            $ageGroups = is_array($request->age_groups)
                ? $request->age_groups
                : [$request->age_groups];
            $query->where(function ($q) use ($ageGroups) {
                foreach ($ageGroups as $ageGroup) {
                    $q->orWhereJsonContains('age_groups', $ageGroup);
                }
            });
        }

        if ($request->filled('academic_groups')) {
            $groups = is_array($request->academic_groups)
                ? $request->academic_groups
                : [$request->academic_groups];
            $query->where(function ($q) use ($groups) {
                foreach ($groups as $groupId) {
                    $q->orWhereJsonContains(
                        'academic_group_ids',
                        (int) $groupId,
                    );
                }
            });
        }

        if ($request->filled('academic_levels')) {
            $levels = is_array($request->academic_levels)
                ? $request->academic_levels
                : [$request->academic_levels];
            $query->where(function ($q) use ($levels) {
                foreach ($levels as $levelId) {
                    $q->orWhereJsonContains(
                        'academic_level_ids',
                        (int) $levelId,
                    );
                }
            });
        }

        if ($request->filled('academic_subjects')) {
            $subjects = is_array($request->academic_subjects)
                ? $request->academic_subjects
                : [$request->academic_subjects];
            $query->where(function ($q) use ($subjects) {
                foreach ($subjects as $subjectId) {
                    $q->orWhereJsonContains(
                        'academic_subject_ids',
                        (int) $subjectId,
                    );
                }
            });
        }

        $books = $query->orderBy('title')->paginate(12)->appends($request->query());
        $categories = BookCategory::all();
        $academicGroups = \App\Models\AcademicGroup::all();
        $academicLevels = \App\Models\AcademicLevel::with(
            'academicGroup',
        )->get();
        $academicSubjects = \App\Models\AcademicSubject::with([
            'academicLevel.academicGroup',
        ])->get();
        $ageGroups = ['1-5', '6-9', '10-12', '13-15', '16-18', '18+'];

        // Get top categories with books for homepage display
        $showCategories = !$request->hasAny([
            'search',
            'categories',
            'format',
            'price',
            'age_groups',
            'academic_groups',
            'academic_levels',
            'academic_subjects',
        ]) && (!$request->has('page') || $request->get('page') == 1);

        if ($showCategories) {
            $topCategories = BookCategory::withCount('books')
                ->having('books_count', '>', 6)
                ->orderBy('books_count', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($category) {
                    $category->books = Book::with([
                        'author',
                        'categories',
                        'bookCategory',
                    ])
                        ->whereStatus(PublishingStatus::PUBLISHED->value)
                        ->where(function ($query) use ($category) {
                            $query
                                ->whereHas('categories', function ($q) use (
                                    $category,
                                ) {
                                    $q->where(
                                        'book_category.category_id',
                                        $category->id,
                                    );
                                })
                                ->orWhere('book_category_id', $category->id);
                        })
                        ->limit(6)
                        ->get();

                    return $category;
                });
        } else {
            $topCategories = null;
        }

        // Get user's subscriptions and borrowings for status checking
        $subscribedBookIds =
            $user
                ->bookSubscriptions()
                ->where('status', 'paid')
                ->pluck('book_id')
                ->toArray() ?:
            [];

        $borrowedBookIds =
            $user
                ->borrowedBooks()
                ->where('status', 'borrowed')
                ->pluck('book_id')
                ->toArray() ?:
            [];

        return view(
            'books.index',
            compact(
                'books',
                'categories',
                'subscribedBookIds',
                'borrowedBookIds',
                'topCategories',
                'academicGroups',
                'academicLevels',
                'academicSubjects',
                'ageGroups',
            ),
        );
    }

    /**
     * Display kids books filtered by younger age groups
     */
    public function kidsBooks(Request $request)
    {
        $user = Auth::user();

        // Kids age groups: 1-5, 6-9, 10-12
        $kidsAgeGroups = ['0-3', '4-6', '7-9', '10-12', '13-15', '16-18', '18+'];

        $query = Book::with([
            'author',
            'categories',
            'bookCategory',
        ])
            ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->where(function ($q) {
                // Filter books that have any of the kids age groups
                
                foreach ([ '0-3', '4-6','7-9', '10-12', '13-15', '16-18', '18+'] as $ageGroup) {
                    $q->orWhereJsonContains('age_groups', $ageGroup);
                }
            });

        // Search filter (title or author)
        if ($request->query('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')->orWhereHas(
                    'author',
                    function ($authorQuery) use ($searchTerm) {
                        $authorQuery->where(
                            'name',
                            'like',
                            '%'.$searchTerm.'%',
                        );
                    },
                );
            });
        }

        // Age group filter - only for kids age groups
        if ($request->filled('age_groups')) {
            $ageGroups = is_array($request->age_groups)
                ? $request->age_groups
                : [$request->age_groups];
            // Only allow kids age groups
            $ageGroups = array_intersect($ageGroups, $kidsAgeGroups);
            if (!empty($ageGroups)) {
                $query->where(function ($q) use ($ageGroups) {
                    foreach ($ageGroups as $ageGroup) {
                        $q->orWhereJsonContains('age_groups', $ageGroup);
                    }
                });
            }
        }

        $books = $query->orderBy('title')->paginate(12)->appends($request->query());
        $ageGroups = $kidsAgeGroups;

        return view(
            'books.kids-index',
            compact(
                'books',
                'ageGroups',
            ),
        );
    }

    public function show(Book $book)
    {
        $book->load([
            'author',
            'bookCategory',
            'categories',
            'reviews' => function ($query) {
                $query->approved()->with('user')->latest()->limit(5);
            },
        ]);

        // Get related books from the same category
        $categoryIds = $book->categories->pluck('id');
        if ($categoryIds->isEmpty() && $book->book_category_id) {
            $categoryIds = collect([$book->book_category_id]);
        }

        $relatedBooks = Book::with(['author', 'categories', 'bookCategory'])
            ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->where('id', '!=', $book->id)
            ->where(function ($query) use ($categoryIds) {
                $query
                    ->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('book_category.category_id', $categoryIds);
                    })
                    ->orWhereIn('book_category_id', $categoryIds);
            })
            ->limit(4)
            ->get();

        $recentReviews = $book
            ->reviews()
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
        $userReadingProgress = null;
        $readingProgressPercentage = 0;

        if ($user) {
            $subscription = $user
                ->bookSubscriptions()
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->first();
            $isSubscribed = (bool) $subscription;

            $borrowing = $user
                ->borrowedBooks()
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            $isBorrowed = (bool) $borrowing;

            $userReadingProgress = BookReadingProgress::query()
                ->where('book_id', $book->id)
                ->where('user_id', $user->id)
                ->orderByDesc('last_read_at')
                ->orderByDesc('updated_at')
                ->first();

            if ($userReadingProgress) {
                $totalPages = max(
                    (int) ($userReadingProgress->total_pages ?: $book->pages ?: 1),
                    1
                );
                $currentPage = max((int) ($userReadingProgress->current_page ?? 0), 0);
                $currentPage = min($currentPage, $totalPages);
                $readingProgressPercentage = (int) round(($currentPage / $totalPages) * 100);
            }
        }

        $canRead =
            $isSubscribed ||
            ! $book->has_softcopy ||
            $book->author->user?->id === $user->id;

        return view(
            'books.show',
            compact(
                'book',
                'isSubscribed',
                'isBorrowed',
                'subscription',
                'borrowing',
                'canRead',
                'recentReviews',
                'userReadingProgress',
                'readingProgressPercentage',
            ),
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
        $existingSubscription = $user
            ->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', SubscriptionStatus::PAID)
            ->first();

        if ($existingSubscription) {
            return response()->json(
                ['error' => 'Already subscribed to this book'],
                400,
            );
        }

        // Free book - direct subscription
        if (
            ! $book->annual_subscription_fee ||
            $book->annual_subscription_fee == 0
        ) {
            $subscription = BookSubscription::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => SubscriptionStatus::PAID,
                'annual_fee' => 0,
                'reference' => 'FREE_'.uniqid(),
                'payment_completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully added to your library!',
                'subscription' => $subscription,
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
            'reference' => 'SUB_'.uniqid(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created. Please complete payment.',
            'subscription' => $subscription,
            'requires_payment' => true,
        ]);
    }

    public function create()
    {
        return view('books.create');
    }

    public function requestBorrow(Request $request, Book $book)
    {
        $user = Auth::user();

        if (! $book->has_hardcopy) {
            return response()->json(
                ['error' => 'This book is not available in hardcopy format'],
                400,
            );
        }

        // Check if already borrowed
        $existingBorrowing = $user
            ->bookBorrowings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'pending_approval'])
            ->first();

        if ($existingBorrowing) {
            return response()->json(
                ['error' => 'Book already borrowed or request pending'],
                400,
            );
        }

        $borrowing = BookBorrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'request_date' => now(),
            'status' => 'pending_approval',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Borrow request submitted successfully!',
            'borrowing' => $borrowing,
        ]);
    }

    public function read(Book $book)
    {
        $user = Auth::user();

        if (! $this->canReadBook($user, $book)) {
            return redirect()
                ->route('books.show', $book)
                ->with('error', 'Subscription required to read this book');
        }

        $annotations = BookAnnotation::query()
            ->where('book_id', $book->id)
            ->withCount('comments')
            ->get([
                'id',
                'book_id',
                'user_id',
                'page_number',
                'x_pct',
                'y_pct',
                'width_pct',
                'height_pct',
                'color',
                'resolved_at',
            ])
            ->map(fn (BookAnnotation $annotation) => [
                'id' => $annotation->id,
                'book_id' => $annotation->book_id,
                'user_id' => $annotation->user_id,
                'page_number' => $annotation->page_number,
                'x_pct' => (float) $annotation->x_pct,
                'y_pct' => (float) $annotation->y_pct,
                'width_pct' => (float) $annotation->width_pct,
                'height_pct' => (float) $annotation->height_pct,
                'color' => $annotation->color,
                'resolved_at' => $annotation->resolved_at?->toISOString(),
                'comments_count' => $annotation->comments_count,
            ]);

        $canDownload = $this->canDownloadBook($user, $book);

        $signedDownloadUrl = $canDownload
            ? URL::temporarySignedRoute('books.file.download', now()->addMinutes(10), ['book' => $book->id])
            : null;

        return view('books.read', [
            'book' => $book,
            'streamUrl' => route('books.file.stream', $book),
            'canDownload' => $canDownload,
            'signedDownloadUrl' => $signedDownloadUrl,
            'initialAnnotations' => $annotations,
        ]);
    }

    public function streamFile(Book $book): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($this->canReadBook($user, $book), 403);

        $path = $book->getRawOriginal('content_url');
        if (! $path) {
            $samplePath = public_path('sample.pdf');
            abort_unless(file_exists($samplePath), 404, 'PDF file not found');

            return response()->stream(function () use ($samplePath): void {
                $stream = fopen($samplePath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="sample.pdf"',
            ]);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'PDF file not found');
        }

        $stream = Storage::disk('public')->readStream($path);
        if (! $stream) {
            abort(404, 'Unable to open PDF file');
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ];

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }

    public function downloadFile(Book $book): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($this->canReadBook($user, $book), 403);
        abort_unless($this->canDownloadBook($user, $book), 403);

        $path = $book->getRawOriginal('content_url');
        if (! $path) {
            $samplePath = public_path('sample.pdf');
            abort_unless(file_exists($samplePath), 404, 'PDF file not found');

            return response()->streamDownload(function () use ($samplePath): void {
                $stream = fopen($samplePath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 'sample.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'PDF file not found');
        }

        return Storage::disk('public')->download($path, basename($path));
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
        $books = Book::with(['author', 'categories', 'bookCategory'])
            ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->where(function ($query) use ($category) {
                $query
                    ->whereHas('categories', function ($q) use ($category) {
                        $q->where('book_category.category_id', $category->id);
                    })
                    ->orWhere('book_category_id', $category->id);
            })
            ->when(
                $request->limit,
                function ($query, $limit) {
                    return $query->limit($limit);
                },
                function ($query) {
                    return $query->paginate(12);
                },
            )
            ->latest()
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'books' => $books,
                'category' => $category,
            ]);
        }

        return view('books.category', compact('books', 'category'));
    }

    /**
     * Get featured/popular books for homepage
     */
    public function getFeatured()
    {
        $featuredBooks = Book::with(['author', 'categories', 'bookCategory'])
            ->whereStatus(PublishingStatus::PUBLISHED->value)
            ->where('is_featured', true)
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

        $book->load(['author', 'bookCategory', 'categories']);

        // Get a few approved reviews without user data for privacy
        $recentReviews = $book
            ->reviews()
            ->approved()
            ->latest()
            ->limit(3)
            ->get();

        return view('books.public-show', compact('book', 'recentReviews'));
    }

    private function canReadBook($user, Book $book): bool
    {
        if (! $user) {
            return false;
        }

        if (! $book->has_softcopy) {
            return false;
        }

        if ($user->hasAnyRole(['owner', 'admin'])) {
            return true;
        }

        if ((float) $book->annual_subscription_fee <= 0) {
            return true;
        }

        $isBookAuthor = (int) ($book->author?->user?->id ?? 0) === (int) $user->id;
        if ($isBookAuthor) {
            return true;
        }

        return $user
            ->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', 'paid')
            ->exists();
    }

    private function canDownloadBook($user, Book $book): bool
    {
        if (! $this->canReadBook($user, $book)) {
            return false;
        }

        if ($user->hasAnyRole(['owner', 'admin'])) {
            return true;
        }

        $activeCycle = $user->getCurrentActiveCycle();
        if (! $activeCycle) {
            return false;
        }

        $activeCycle->loadMissing('pricingTier');

        return (bool) $activeCycle->pricingTier?->isPremium();
    }
}
