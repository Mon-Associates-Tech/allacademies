<?php

namespace App\Livewire\Students;

use App\Livewire\AppComponent;
use App\Models\BookCategory;
use App\Models\BookReadingProgress;
use Livewire\WithPagination;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Books extends AppComponent
{
    use WithPagination;

    public $bookTab = 'available';
    public $search = '';
    public $selectedPrice = '';
    public $selectedCategory = '';
    public $selectedFormat = '';
    public $categories = [];

    public $showPdfReader = false;
    public $currentBookId = null;
    public $currentPage = 1;
    public $totalPages = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedFormat' => ['except' => ''],
        'bookTab' => ['except' => 'available'],
        'selectedPrice' => ['except' => ''],
    ];

    public $isLoading = false;

    public $showSubscriptionModal = false;
    public $subscriptionData = [];

    protected function getListeners()
    {
        return [
            'bookPageChanged' => 'updateCurrentPage',
            'closePdfReader' => 'closePdfReader',
        ];
    }

    public function mount()
    {
        $this->categories = BookCategory::pluck('name', 'id')->toArray();

        // Log books page access
        activity()
            ->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'accessed_books_page',
                'page' => 'books'
            ])
            ->log('Student accessed books page');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedFormat()
    {
        $this->resetPage();
    }

    public function updatingSelectedPrice()
    {
        $this->resetPage();
    }

    public function changeTab($tab)
    {
        $this->bookTab = $tab;
        $this->resetPage();

        // Log tab change
        activity()
            ->performedOn(auth()->user()->student)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'changed_books_tab',
                'tab' => $tab
            ])
            ->log('Student changed books tab');
    }

    public function hasBookAccess($bookId)
    {
        $student = Auth::user()->student;
        if (!$student) return false;

        // Check individual subscription
        $hasIndividualSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->exists();

        if ($hasIndividualSubscription) {
            return 'subscribed';
        }

        // Check group subscription if student has a group
        if ($student->studentGroup) {
            $hasGroupSubscription = $student->studentGroup->subscriptions()
                ->where('book_id', $bookId)
                ->where('status', 'active')
                ->exists();

            if ($hasGroupSubscription) {
                return 'group_subscribed';
            }
        }

        // Check if book is free (no subscription fee required)
        $book = Book::find($bookId);
        if ($book && ($book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee))) {
            return 'free';
        }

        return false;
    }

    public function getBookStatus($bookId)
    {
        $student = Auth::user()->student;
        if (!$student) return null;

        $book = Book::find($bookId);
        if (!$book) return null;

        // Check if book is free
        if ($book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee)) {
            return [
                'type' => 'free',
                'label' => 'Added',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
            ];
        }

        // Check individual subscription
        $individualSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if ($individualSubscription) {
            return [
                'type' => 'subscribed',
                'label' => 'Subscribed',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
            ];
        }

        // Check pending payment
        $pendingSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'pending_payment')
            ->first();

        if ($pendingSubscription) {
            return [
                'type' => 'pending',
                'label' => 'Pending Payment',
                'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100'
            ];
        }

        // Check group subscription
        if ($student->studentGroup) {
            $groupSubscription = $student->studentGroup->subscriptions()
                ->where('book_id', $bookId)
                ->where('status', 'active')
                ->first();

            if ($groupSubscription) {
                return [
                    'type' => 'group_subscribed',
                    'label' => 'Group Access',
                    'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100'
                ];
            }
        }

        return null;
    }

    public function isBookFree($bookId)
    {
        $book = Book::find($bookId);
        return $book && ($book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee));
    }

    // Update the existing isBookSubscribed method to include group subscriptions
    public function isBookSubscribed($bookId)
    {
        $student = Auth::user()->student;
        if (!$student) return false;

        // Check individual subscription
        $hasIndividualSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->exists();

        if ($hasIndividualSubscription) {
            return true;
        }

        // Check group subscription
        if ($student->studentGroup) {
            $hasGroupSubscription = $student->studentGroup->groupBookSubscriptions()
                ->where('book_id', $bookId)
                ->where('status', 'active')
                ->exists();

            if ($hasGroupSubscription) {
                return true;
            }
        }

        return false;
    }

    public function isBookBorrowed($bookId)
    {
        $student = Auth::user()->student;
        if (!$student) return false;

        return BookBorrowing::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'borrowed')
            ->exists();
    }

    public function getBookBorrowing($bookId)
    {
        $student = Auth::user()->student;
        if (!$student) return null;

        return BookBorrowing::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'borrowed')
            ->first();
    }

    // Update the openPdfReader method to check for all types of access
    public function openPdfReader($bookId)
    {
        $book = Book::findOrFail($bookId);
        $student = Auth::user()->student;

        // Check if user has access to this book (individual, group, or free)
        $hasAccess = false;

        // Check individual subscription
        $hasIndividualAccess = BookSubscription::where('book_id', $bookId)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        // Check group subscription
        $hasGroupAccess = false;
        if ($student->studentGroup) {
            $hasGroupAccess = $student->studentGroup->groupBookSubscriptions()
                ->where('book_id', $bookId)
                ->where('status', 'active')
                ->exists();
        }

        // Check if book is free
        $isFree = $book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee);

        $hasAccess = $hasIndividualAccess || $hasGroupAccess || $isFree;

        if (!$hasAccess) {
            session()->flash('error', 'You need to subscribe to this book first.');
            return;
        }

        // Get saved progress if exists
        $progress = BookReadingProgress::where('book_id', $bookId)
            ->where('student_id', $student->id)
            ->first();

        $this->currentPage = $progress ? $progress->current_page : 1;
        $this->currentBookId = $bookId;
        $this->showPdfReader = true;

        \Log::info('Opening PDF reader', [
            'bookId' => $bookId,
            'pdfUrl' => $book->content_url,
            'currentPage' => $this->currentPage
        ]);

        // Log book opening
        activity()
            ->performedOn($book)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'opened_book_reader',
                'book_id' => $bookId,
                'book_title' => $book->title,
                'starting_page' => $this->currentPage,
                'has_previous_progress' => $progress ? true : false,
                'access_type' => $hasIndividualAccess ? 'individual' : ($hasGroupAccess ? 'group' : 'free')
            ])
            ->log('Student opened book reader');

        $this->dispatch('openPdfReader', [
            'pdfUrl' => $book->content_url,
            'currentPage' => $this->currentPage
        ]);
    }

    public function closePdfReader()
    {
        $this->showPdfReader = false;
        $this->currentBookId = null;
        $this->currentPage = 1;
        $this->totalPages = 0;
    }

    public function updateCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function saveReadingProgress($page, $totalPages)
    {
        if (!$this->currentBookId) {
            return;
        }

        $progress = BookReadingProgress::updateOrCreate(
            [
                'book_id' => $this->currentBookId,
                'student_id' => auth()->user()->student->id
            ],
            [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'last_read_at' => now()
            ]
        );

        // Log reading progress
        $book = Book::find($this->currentBookId);
        activity()
            ->performedOn($progress)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'updated_reading_progress',
                'book_id' => $this->currentBookId,
                'book_title' => $book->title ?? 'Unknown',
                'current_page' => $page,
                'total_pages' => $totalPages,
                'progress_percentage' => $totalPages > 0 ? round(($page / $totalPages) * 100, 2) : 0
            ])
            ->log('Student updated reading progress');

        session()->flash('success', 'Reading progress saved!');
    }

public function subscribeToBook($bookId)
{
    $book = Book::findOrFail($bookId);
    $student = Auth::user()->student;

    // Check if already subscribed
    $existingSubscription = BookSubscription::where('student_id', $student->id)
        ->where('book_id', $bookId)
        ->first();

    if ($existingSubscription) {
        if ($existingSubscription->status === 'active') {
            session()->flash('error', 'You are already subscribed to this book.');
            return;
        } elseif ($existingSubscription->status === 'pending_payment') {
            session()->flash('error', 'You have a pending subscription for this book. Please complete payment or cancel the existing subscription.');
            return;
        }
    }

    // Check if book is free
    $isFree = $book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee);

    if ($isFree) {
        // For free books, create active subscription immediately
        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'annual_fee' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'reference' => 'FREE-' . strtoupper(uniqid()),
        ]);

        session()->flash('success', 'Free book added to your subscriptions successfully!');

        // Log the free subscription
        activity()
            ->performedOn($book)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'free_book_subscribed',
                'book_id' => $bookId,
                'subscription_id' => $subscription->id,
            ])
            ->log('Student subscribed to free book');

    } else {
        // For paid books, prepare subscription data for modal
        $subscriptionData = [
            'book_id' => $bookId,
            'book_title' => $book->title,
            'amount' => $book->annual_subscription_fee,
            'author' => $book->author ?? null,
        ];

        // Log the subscription attempt
        activity()
            ->performedOn($book)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'subscription_modal_opened',
                'book_id' => $bookId,
                'book_title' => $book->title,
                'amount' => $book->annual_subscription_fee,
            ])
            ->log('Student opened subscription modal');

        // Emit event to show subscription modal
        $this->dispatch('showSubscriptionModal', $subscriptionData);
    }
}

public function confirmSubscription($bookId)
{
    $book = Book::findOrFail($bookId);
    $student = Auth::user()->student;

    // Check if book is free
    $isFree = $book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee);

    if ($isFree) {
        // For free books, subscribe immediately
        $this->subscribeToBook($bookId);
    } else {
        // For paid books, show the confirmation and conditions
        $subscriptionData = [
            'book_id' => $bookId,
            'book_title' => $book->title,
            'amount' => $book->annual_subscription_fee,
            'author' => $book->author->user->name ?? 'Unknown',
            'description' => $book->description,
        ];

        // You can emit to show a confirmation modal first, then call subscribeToBook
        $this->subscriptionData = $subscriptionData;
        $this->showSubscriptionModal = true;
    }
}

    public function showSubscriptionDetails($subscriptionId)
    {
        $subscription = BookSubscription::with('book')->findOrFail($subscriptionId);

        $this->subscriptionData = [
            'book_title' => $subscription->book->title,
            'amount' => $subscription->annual_fee,
            'reference' => $subscription->reference,
            'subscription_id' => $subscription->id
        ];

        $this->dispatch('showSubscriptionModal', $this->subscriptionData);

    }

    public function closeSubscriptionModal()
    {
        $this->showSubscriptionModal = false;
        $this->subscriptionData = [];
    }

    public function unsubscribeFromBook($bookId)
    {
        $this->startLoading();
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            $this->endLoading();
            return;
        }

        $subscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            session()->flash('error', 'No active subscription found.');
            $this->endLoading();
            return;
        }

        $book = Book::find($bookId);
        $subscription->update(['status' => 'cancelled']);

        // Log book unsubscription
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'unsubscribed_from_book',
                'book_id' => $bookId,
                'book_title' => $book->title ?? 'Unknown',
                'cancelled_at' => now()->toDateTimeString()
            ])
            ->log('Student unsubscribed from book');

        $this->endLoading();
        session()->flash('success', 'Successfully unsubscribed from book!');
    }

    public function borrowBook($bookId)
    {
        $this->startLoading();
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            $this->endLoading();
            return;
        }

        $book = Book::findOrFail($bookId);

        if (!$book->has_hardcopy) {
            session()->flash('error', 'This book is not available for borrowing.');
            $this->endLoading();
            return;
        }

        $existingBorrowing = BookBorrowing::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'borrowed')
            ->first();

        if ($existingBorrowing) {
            session()->flash('error', 'You have already borrowed this book.');
            $this->endLoading();
            return;
        }

        $borrowing = BookBorrowing::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'borrowed'
        ]);

        // Log book borrowing
        activity()
            ->performedOn($borrowing)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'borrowed_book',
                'book_id' => $bookId,
                'book_title' => $book->title,
                'borrowed_at' => now()->toDateTimeString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'borrowing_duration' => '14 days'
            ])
            ->log('Student borrowed book');

        $this->endLoading();
        session()->flash('success', 'Successfully borrowed book!');
    }

    public function returnBook($bookId)
    {
        $this->startLoading();
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            $this->endLoading();
            return;
        }

        $borrowing = BookBorrowing::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'borrowed')
            ->first();

        if (!$borrowing) {
            session()->flash('error', 'No active borrowing found.');
            $this->endLoading();
            return;
        }

        $book = Book::find($bookId);
        $borrowing->update([
            'status' => 'returned',
            'returned_at' => now()
        ]);

        // Log book return
        activity()
            ->performedOn($borrowing)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'returned_book',
                'book_id' => $bookId,
                'book_title' => $book->title ?? 'Unknown',
                'returned_at' => now()->toDateTimeString(),
                'was_overdue' => $borrowing->due_date < now()
            ])
            ->log('Student returned book');

        $this->endLoading();
        session()->flash('success', 'Successfully returned book!');
    }

    public function getAvailableBooksProperty()
    {
        $query = Book::query()
            ->with(['author', 'bookCategory'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('isbn', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('bookCategory', function ($categoryQuery) {
                            $categoryQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('book_category_id', $this->selectedCategory);
            })
            ->when($this->selectedFormat, function ($query) {
                if ($this->selectedFormat === 'hardcopy') {
                    $query->where('has_hardcopy', true);
                } elseif ($this->selectedFormat === 'softcopy') {
                    $query->where('has_softcopy', true);
                }
            })
            ->when($this->selectedPrice, function ($query) {
                switch ($this->selectedPrice) {
                    case 'free':
                        $query->where(function ($q) {
                            $q->whereNull('annual_subscription_fee')
                              ->orWhere('annual_subscription_fee', 0);
                        });
                        break;
                    case 'paid':
                        $query->where('annual_subscription_fee', '>', 0);
                        break;
                    case 'low':
                        $query->whereBetween('annual_subscription_fee', [1, 25]);
                        break;
                    case 'medium':
                        $query->whereBetween('annual_subscription_fee', [26, 75]);
                        break;
                    case 'high':
                        $query->where('annual_subscription_fee', '>', 75);
                        break;
                }
            })
            ->orderBy('title');

        return $query->paginate(15); // Increased from 12 for better grid layout
    }

    public function getSubscribedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect()->paginate(15);

        $query = Book::query()
            ->with(['author', 'bookCategory', 'subscriptions' => function ($query) use ($student) {
                $query->where('student_id', $student->id)->where('status', 'active');
            }])
            ->whereHas('subscriptions', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', 'active');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('isbn', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('title');

        return $query->paginate(15);
    }

    public function getBorrowedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect()->paginate(15);

        $query = Book::query()
            ->with(['author', 'bookCategory', 'borrowings' => function ($query) use ($student) {
                $query->where('student_id', $student->id)->where('status', 'borrowed');
            }])
            ->whereHas('borrowings', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', 'borrowed');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('isbn', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('title');

        return $query->paginate(15);
    }

    public function getReadingProgressProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect()->paginate(15);

        $query = BookReadingProgress::where('student_id', $student->id)
            ->with(['book', 'book.author', 'book.bookCategory'])
            ->latest('last_read_at')
            ->when($this->search, function ($query) {
                $query->whereHas('book', function ($bookQuery) {
                    $bookQuery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('isbn', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            });

        return $query->paginate(15);
    }

    public function getFormatOptions()
    {
        return [
            '' => 'All Formats',
            'hardcopy' => 'Hardcopy Only',
            'softcopy' => 'Softcopy Only'
        ];
    }

    public function getPriceOptions()
    {
        return [
            '' => 'All Prices',
            'free' => 'Free Books',
            'paid' => 'Paid Books',
            'low' => 'Low Price (≤$25)',
            'medium' => 'Medium Price ($26-$75)',
            'high' => 'High Price (>$75)'
        ];
    }

    // Add method to get pagination info
    public function getPaginationInfo($books)
    {
        if (!$books instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return null;
        }

        return [
            'current_page' => $books->currentPage(),
            'last_page' => $books->lastPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
            'from' => $books->firstItem(),
            'to' => $books->lastItem(),
        ];
    }

    // Add method for better performance tracking
    public function updatingBookTab()
    {
        $this->resetPage();

        // Clear any cached data when switching tabs
        $this->reset(['search', 'selectedCategory', 'selectedFormat', 'selectedPrice']);
    }

    public function render()
    {
        $student = Auth::user()->student;

        // Calculate statistics
        $totalBooks = Book::where('status', 'approved')->count();

        // Calculate subscribed books count (individual + group subscriptions)
        $individualSubscriptions = BookSubscription::where('student_id', $student->id)
            ->where('status', 'active')
            ->count();

        $groupSubscriptions = 0;
        if ($student->studentGroup) {
            $groupSubscriptions = $student->studentGroup->subscriptions()
                ->where('status', 'active')
                ->count();
        }

        $subscribedCount = $individualSubscriptions + $groupSubscriptions;

        // Calculate borrowed books count (only currently borrowed books)
        $borrowedCount = BookBorrowing::where('student_id', $student->id)
            ->where('status', 'borrowed')
            ->count();

        // Calculate available count for the tab badge
        $availableCount = $totalBooks;

        // Get books based on current tab
        $books = $this->getBooks();

        return view('livewire.students.books', [
            'books' => $books,
            'totalBooks' => $totalBooks,
            'subscribedCount' => $subscribedCount,
            'borrowedCount' => $borrowedCount,
            'availableCount' => $availableCount,
            'paginationInfo' => $this->getPaginationInfo($books),
            'formatOptions' => $this->getFormatOptions(),
            'priceOptions' => $this->getPriceOptions(),
            'readingProgress' => $this->getReadingProgressProperty(),
            'categories' => $this->categories,


        ]);
    }

    private function getBooks()
    {
        $student = Auth::user()->student;

        switch ($this->bookTab) {
            case 'available':
                return $this->getAvailableBooks();
            case 'subscribed':
                return $this->getSubscribedBooks($student);
            case 'borrowed':
                return $this->getBorrowedBooks($student);
            default:
                return collect();
        }
    }

    private function getAvailableBooks()
    {
        $query = Book::with(['author.user', 'bookCategory']);
//            ->where('status', 'approved');

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('author.user', function($authorQuery) {
                      $authorQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->selectedCategory) {
            $query->where('book_category_id', $this->selectedCategory);
        }

        if ($this->selectedFormat) {
            $query->where('format', $this->selectedFormat);
        }

        if ($this->selectedPrice) {
            switch ($this->selectedPrice) {
                case 'free':
                    $query->where(function($q) {
                        $q->whereNull('annual_subscription_fee')
                          ->orWhere('annual_subscription_fee', 0);
                    });
                    break;
                case 'paid':
                    $query->where('annual_subscription_fee', '>', 0);
                    break;
            }
        }

        return $query->paginate(12);
    }

    private function getSubscribedBooks($student)
    {
        // Get individually subscribed books
        $individualBooks = Book::whereHas('subscriptions', function($query) use ($student) {
            $query->where('student_id', $student->id)
                  ->where('status', 'active');
        })->with(['author.user', 'bookCategory']);

        // Get group subscribed books
        $groupBooks = collect();
        if ($student->studentGroup) {
            $groupBooks = Book::whereHas('groupSubscriptions', function($query) use ($student) {
                $query->where('student_group_id', $student->studentGroup->id)
                      ->where('status', 'active');
            })->with(['author.user', 'bookCategory']);
        }

        // Combine and paginate
        $allSubscribedBooks = $individualBooks->union($groupBooks->toBase());

        return $allSubscribedBooks->paginate(12);
    }

    private function getBorrowedBooks($student)
    {
        return Book::whereHas('borrowings', function($query) use ($student) {
            $query->where('student_id', $student->id)
                  ->where('status', 'borrowed');
        })->with(['author.user', 'bookCategory'])
          ->paginate(12);
    }
}
