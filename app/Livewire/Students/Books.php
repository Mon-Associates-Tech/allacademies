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

        // Dispatch event to trigger PDF reader with consistent structure
        $this->dispatch('pdf-reader-open', [
            'pdfUrl' => $this->book->content_url,
            'title' => $this->book->title,
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
        $this->startLoading();
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            $this->endLoading();
            return;
        }

        $book = Book::findOrFail($bookId);

        if (!$book->has_softcopy) {
            session()->flash('error', 'This book is not available for subscription.');
            $this->endLoading();
            return;
        }

        $existingSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['active', 'pending_payment'])
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === 'active') {
                session()->flash('error', 'You are already subscribed to this book.');
            } else {
                session()->flash('error', 'You have a pending subscription for this book. Please complete payment.');
            }
            $this->endLoading();
            return;
        }

        // Create subscription with pending payment status
        $reference = 'BS' . time() . $student->id . $bookId;

        $subscription = BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'pending_payment',
            'reference' => $reference,
            'annual_fee' => $book->annual_subscription_fee ?? 50.00,
        ]);

        // Set subscription data for modal
        $this->subscriptionData = [
            'book_title' => $book->title,
            'amount' => $subscription->annual_fee,
            'reference' => $reference,
            'subscription_id' => $subscription->id
        ];

        // Log book subscription
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'initiated_book_subscription',
                'book_id' => $bookId,
                'book_title' => $book->title,
                'subscription_duration' => '1 year',
                'annual_fee' => $subscription->annual_fee,
                'reference' => $reference,
                'status' => 'pending_payment'
            ])
            ->log('Student initiated book subscription');

        $this->endLoading();
        $this->dispatch('showSubscriptionModal', $this->subscriptionData);
        session()->flash('success', 'Subscription initiated! Please proceed with payment to activate your access.');
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
            });

        return $query->paginate(12);
    }

    public function getSubscribedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        return Book::query()
            ->with(['author', 'bookCategory'])
            ->whereHas('subscriptions', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', 'active')
                    ->orWhere('status', 'pending_payment');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->paginate(12);
    }

    public function getBorrowedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        return Book::query()
            ->with(['author', 'bookCategory'])
            ->whereHas('borrowings', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', 'borrowed');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->paginate(12);
    }

    public function getReadingProgressProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        return BookReadingProgress::where('student_id', $student->id)
            ->with(['book', 'book.author'])
            ->latest('last_read_at')
            ->when($this->search, function ($query) {
                $query->whereHas('book', function ($bookQuery) {
                    $bookQuery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhereHas('author', function ($authorQuery) {
                            $authorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->paginate(12);
    }

    public function getFormatOptions()
    {
        return [
            '' => 'All Formats',
            'hardcopy' => 'Hardcopy Only',
            'softcopy' => 'Softcopy Only'
        ];
    }

    public function render()
    {
        $books = match ($this->bookTab) {
            'subscribed' => $this->subscribedBooks,
            'borrowed' => $this->borrowedBooks,
            'progress' => $this->readingProgress,
            default => $this->availableBooks,
        };

        return view('livewire.students.books', [
            'books' => $books,
            'formatOptions' => $this->getFormatOptions(),
        ]);
    }
}
