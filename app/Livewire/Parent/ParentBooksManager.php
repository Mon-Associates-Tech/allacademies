<?php

namespace App\Livewire\Parent;

use App\Enums\SubscriptionStatus;
use App\Livewire\AppComponent;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentBooksManager extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;
    public $selectedCategoryId = null;
    public $searchTerm = '';
    public $sortBy = 'title';
    public $sortDirection = 'asc';
    public $viewMode = 'grid';
    public $activeTab = 'subscribed'; // New tab property
    public $showSubscriptionModal = false;
    public $selectedBookId = null;
    public $subscriptionType = 'monthly';

    public function mount()
    {
        $wards = $this->wards;
        if ($wards->isNotEmpty()) {
           // $this->selectedWardId = $wards->first()->id;
        }
    }


    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
        $this->resetPage();
    }

    public function viewAll()
    {
        $this->selectedWardId = null;
        $this->resetPage();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function showSubscriptionModal($bookId)
    {
        $this->selectedBookId = $bookId;
        $this->showSubscriptionModal = true;
    }

    public function closeSubscriptionModal()
    {
        $this->showSubscriptionModal = false;
        $this->selectedBookId = null;
        $this->subscriptionType = 'monthly';
    }

    public function cancelSubscription($subscriptionId)
    {
        $subscription = BookSubscription::where('id', $subscriptionId)
            ->where('user_id', Student::findOrFail($this->selectedWardId)->user->id)
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            session()->flash('success', 'Subscription cancelled successfully!');
        }
    }

    public function getBookStatus($bookId)
    {
        if (!$this->selectedWardId) return null;

        $student = getStudent(student_id: $this->selectedWardId, withoutScopes: true); //Student::find($this->selectedWardId);
        if (!$student) return null;

        $book = Book::find($bookId);
        if (!$book) return null;

        // Check if book is subscribed
        $subscription = BookSubscription::where('user_id', $student->user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            return [
                'type' => 'subscribed',
                'label' => 'Subscribed',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
            ];
        }

        // Check if book is borrowed
        $borrowing = BookBorrowing::where('user_id', $student->user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if ($borrowing) {
            return [
                'type' => 'borrowed',
                'label' => 'Borrowed',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
            ];
        }

        // Check if book is free
        if ($book->annual_subscription_fee == 0 || is_null($book->annual_subscription_fee)) {
            return [
                'type' => 'free',
                'label' => 'Free',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
            ];
        }

        return null;
    }

    public function subscribeToBook()
    {
        if (!$this->selectedBookId || !$this->selectedWardId) {
            session()->flash('error', 'Please select a book and ward');
            return;
        }

        $book = Book::findOrFail($this->selectedBookId);
        $student = Student::findOrFail($this->selectedWardId);

        // Check authorization using the policy
        if (!auth()->user()->can('subscribe', $book)) {
            session()->flash('error', 'You are not authorized to subscribe to this book');
            return;
        }

        // Check if student has softcopy access
        if (!$book->has_softcopy) {
            session()->flash('error', 'This book does not have a digital version available');
            return;
        }

        // Check if already subscribed
        $existingSubscription = BookSubscription::where('user_id', $student->user->id)
            ->where('book_id', $this->selectedBookId)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            session()->flash('error', 'Already subscribed to this book');
            return;
        }

        // Create subscription with pending payment status
        $subscription = BookSubscription::create([
            'user_id' => $student->user->id,
            'book_id' => $this->selectedBookId,
            'subscription_type' => $this->subscriptionType,
            'status' => 'pending_payment',
            'subscribed_by' => Auth::id(), // Add parent as subscriber
            'starts_at' => now(),
            'expires_at' => $this->subscriptionType === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        $this->closeSubscriptionModal();
        session()->flash('success', 'Subscription created! Please complete payment to activate.');
    }


    #[Computed]
    public function subscribedBooks()
    {
        if (!$this->selectedWardId) {
            return $this->allSubscribedBooks();
        }

        $student = Student::withoutGlobalScopes()->find($this->selectedWardId);

        if (!$student) return collect();

        // Get books subscribed by the ward OR by the parent for this ward
        $subscribedBookIds = BookSubscription::where(function($query) use ($student) {
            $query->where('user_id', $student->user->id)
                ->orWhere(function($q) use ($student) {
                    // Parent subscribed for this ward
                    $q->where('subscribed_by', auth()->user()->id)
                        ->where('user_id', $student->user->id);
                });
        })
            ->where('status', 'paid')
            ->pluck('book_id')
            ->unique();

        $query = Book::with(['bookCategory', 'author'])
            ->whereIn('id', $subscribedBookIds);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author', function($author) {
                        $author->where('name', 'LIKE', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }


    #[Computed]
    public function allSubscribedBooks()
    {
        // Get all wards with their user relationship eagerly loaded
        $wards = $this->wards;

        if ($wards->isEmpty()) {
            \Log::info('ParentBooksManager: No wards found for parent', [
                'parent_user_id' => auth()->id()
            ]);
            return collect();
        }

        // Extract ward user IDs
        $wardUserIds = $wards->map(function($ward) {
            return $ward->user ? $ward->user->id : null;
        })->filter()->toArray();

        \Log::info('ParentBooksManager allSubscribedBooks', [
            'parent_user_id' => auth()->id(),
            'wards_count' => $wards->count(),
            'ward_user_ids' => $wardUserIds,
        ]);

        if (empty($wardUserIds)) {
            \Log::warning('ParentBooksManager: No ward user IDs found');
            return collect();
        }

        // Get subscriptions for all wards OR made by parent OR parent's own subscriptions
        $subscribedBookIds = BookSubscription::where(function($query) use ($wardUserIds) {
            $query->whereIn('user_id', $wardUserIds) // All wards' subscriptions
            ->orWhere('subscribed_by', auth()->user()->id) // Parent subscribed
            ->orWhere('user_id', auth()->user()->id); // Parent's own subscriptions
        })
            ->where('status', 'paid')
            ->pluck('book_id')
            ->unique();

        \Log::info('ParentBooksManager subscribed books found', [
            'subscribed_book_ids' => $subscribedBookIds->toArray(),
            'count' => $subscribedBookIds->count()
        ]);

        if ($subscribedBookIds->isEmpty()) {
            return collect();
        }

        $query = Book::with(['bookCategory', 'author'])
            ->whereIn('id', $subscribedBookIds);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author', function($author) {
                        $author->where('name', 'LIKE', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }



    #[Computed]
    public function activeSubscriptions()
    {
        // If no ward selected, show all subscriptions
        if (!$this->selectedWardId) {
            $wards = $this->wards;

            if ($wards->isEmpty()) {
                return collect();
            }

            $wardUserIds = $wards->map(function($ward) {
                return $ward->user ? $ward->user->id : null;
            })->filter()->toArray();

            return BookSubscription::where(function($query) use ($wardUserIds) {
                if (!empty($wardUserIds)) {
                    $query->whereIn('user_id', $wardUserIds)
                        ->orWhere('subscribed_by', auth()->user()->id)
                        ->orWhere('user_id', auth()->user()->id);
                } else {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('subscribed_by', auth()->user()->id);
                }
            })
                ->where('status', SubscriptionStatus::PAID->value)
                ->with(['book.bookCategory', 'book.author', 'subscribedBy', 'user'])
                ->get();
        }

        $student = Student::withoutGlobalScopes()->find($this->selectedWardId);

        if (!$student) return collect();

        // Include subscriptions made by parent for this ward
        return BookSubscription::where(function($query) use ($student) {
            $query->where('user_id', $student->user->id)
                ->orWhere(function($q) use ($student) {
                    $q->where('subscribed_by', auth()->user()->id)
                        ->where('user_id', $student->user->id);
                });
        })
            ->where('status', SubscriptionStatus::PAID->value)
            ->with(['book.bookCategory', 'book.author', 'subscribedBy', 'user'])
            ->get();
    }
    #[Computed]
    public function subscriptionStats(): array
    {
        if (!$this->selectedWardId) {
            return $this->allSubscriptionStats();
        }

        $student = Student::withoutGlobalScopes()->find($this->selectedWardId);

        if (!$student) {
            return [
                'active_subscriptions' => 0,
                'active_borrowings' => 0,
                'total_subscriptions' => 0,
                'expired_subscriptions' => 0,
                'cancelled_subscriptions' => 0,
            ];
        }

        // Include subscriptions made by parent
        $subscriptions = BookSubscription::where(function($query) use ($student) {
            $query->where('user_id', $student->user->id)
                ->orWhere(function($q) use ($student) {
                    $q->where('subscribed_by', auth()->user()->id)
                        ->where('user_id', $student->user->id);
                });
        })->get();

        $borrowings = BookBorrowing::where('user_id', $student->user->id)->get();

        return [
            'active_subscriptions' => $subscriptions->where('status', SubscriptionStatus::PAID->value)->count(),
            'active_borrowings' => $borrowings->where('status', 'active')->count(),
            'total_subscriptions' => $subscriptions->count(),
            'expired_subscriptions' => $subscriptions->where('status', SubscriptionStatus::UNPAID->value)->count(),
            'cancelled_subscriptions' => $subscriptions->where('status', 'cancelled')->count(),
        ];
    }

    #[Computed]
    public function allSubscriptionStats(): array
    {
        // Get all wards
        $wards = $this->wards;

        if ($wards->isEmpty()) {
            return [
                'active_subscriptions' => 0,
                'active_borrowings' => 0,
                'total_subscriptions' => 0,
                'expired_subscriptions' => 0,
                'cancelled_subscriptions' => 0,
            ];
        }

        // Get all ward user IDs
        $wardUserIds = $wards->map(function($ward) {
            return $ward->user ? $ward->user->id : null;
        })->filter()->toArray();

        // Get all subscriptions for wards and parent
        $subscriptions = BookSubscription::where(function($query) use ($wardUserIds) {
            if (!empty($wardUserIds)) {
                $query->whereIn('user_id', $wardUserIds)
                    ->orWhere('subscribed_by', auth()->user()->id)
                    ->orWhere('user_id', auth()->user()->id);
            } else {
                $query->where('user_id', auth()->user()->id)
                    ->orWhere('subscribed_by', auth()->user()->id);
            }
        })->get();

        // Get all borrowings for wards and parent
        $borrowings = BookBorrowing::where(function($query) use ($wardUserIds) {
            if (!empty($wardUserIds)) {
                $query->whereIn('user_id', $wardUserIds)
                    ->orWhere('user_id', auth()->user()->id);
            } else {
                $query->where('user_id', auth()->user()->id);
            }
        })->get();

        return [
            'active_subscriptions' => $subscriptions->where('status', SubscriptionStatus::PAID->value)->count(),
            'active_borrowings' => $borrowings->where('status', 'active')->count(),
            'total_subscriptions' => $subscriptions->count(),
            'expired_subscriptions' => $subscriptions->where('status', SubscriptionStatus::UNPAID->value)->count(),
            'cancelled_subscriptions' => $subscriptions->where('status', 'cancelled')->count(),
        ];
    }

    #[Computed]
    public function availableBooks(): array|LengthAwarePaginator|Collection
    {
        if (!$this->selectedWardId) return collect();

        $student = Student::withoutGlobalScopes()->find($this->selectedWardId);

        if (!$student) return collect();

        // Get IDs of books that are already subscribed or borrowed
        $subscribedBookIds = BookSubscription::where(function($query) use ($student) {
            $query->where('user_id', $student->user->id)
                ->orWhere(function($q) use ($student) {
                    $q->where('subscribed_by', auth()->user()->id)
                        ->where('user_id', $student->user->id);
                });
        })
            ->where('status', SubscriptionStatus::PAID->value)
            ->pluck('book_id');

        $borrowedBookIds = BookBorrowing::where('user_id', $student->user->id)
            ->where('status', 'active')
            ->pluck('book_id');

        $unavailableBookIds = $subscribedBookIds->merge($borrowedBookIds)->unique();

        $query = Book::with(['bookCategory', 'author'])
            ->whereNotIn('id', $unavailableBookIds);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author', function($author) {
                        $author->where('name', 'LIKE', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Computed]
    public function borrowedBooks()
    {
        if (!$this->selectedWardId) {
            return $this->allBorrowedBooks();
        }

        $student = Student::withoutGlobalScopes()->find($this->selectedWardId);

        if (!$student) return collect();

        $borrowedBookIds = BookBorrowing::where('user_id', $student->user->id)
            ->where('status', 'active')
            ->pluck('book_id');

        $query = Book::with(['bookCategory', 'author'])
            ->whereIn('id', $borrowedBookIds);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author', function($author) {
                        $author->where('name', 'LIKE', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Computed]
    public function allBorrowedBooks()
    {
        // Get all wards with their user relationship
        $wards = $this->wards;

        if ($wards->isEmpty()) {
            return collect();
        }

        // Extract ward user IDs
        $wardUserIds = $wards->map(function($ward) {
            return $ward->user ? $ward->user->id : null;
        })->filter()->toArray();

        if (empty($wardUserIds)) {
            return collect();
        }

        // Get borrowings for all wards OR parent's own borrowings
        $borrowedBookIds = BookBorrowing::where(function($query) use ($wardUserIds) {
            $query->whereIn('user_id', $wardUserIds) // All wards' borrowings
            ->orWhere('user_id', auth()->user()->id); // Parent's own borrowings
        })
            ->where('status', 'active')
            ->pluck('book_id')
            ->unique();

        if ($borrowedBookIds->isEmpty()) {
            return collect();
        }

        $query = Book::with(['bookCategory', 'author'])
            ->whereIn('id', $borrowedBookIds);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author', function($author) {
                        $author->where('name', 'LIKE', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Computed]
    public function wards()
    {
        // Get the parent record first
        $parent = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->first();

        if (!$parent) {
            return collect();
        }

        // Get students through the pivot table
        $students = $parent->students()
            ->withoutGlobalScopes()
            ->with(['user', 'academicLevel.academicGroup', 'studentGroup'])
            ->get();

        if ($this->searchTerm) {
            $students = $students->filter(function($student) {
                return stripos($student->user->name, $this->searchTerm) !== false ||
                    stripos($student->academicLevel->name ?? '', $this->searchTerm) !== false ||
                    stripos($student->academicLevel->academicGroup->name ?? '', $this->searchTerm) !== false;
            });
        }

        return $students->sortBy($this->sortBy === 'name' ? 'user.name' : $this->sortBy,
            SORT_REGULAR, $this->sortDirection === 'desc');
    }


    #[Computed]
    public function selectedWard()
    {
        if (!$this->selectedWardId) return null;

        return Student::withoutGlobalScopes()
            ->with([
                'user',
                'academicLevel.academicGroup'
            ])->find($this->selectedWardId);
    }

    #[Computed]
    public function selectedBook()
    {
        if (!$this->selectedBookId) return null;

        return Book::with(['bookCategory', 'author'])->find($this->selectedBookId);
    }

    #[Computed]
    public function books()
    {
        return match ($this->activeTab) {
            'subscribed' => $this->subscribedBooks(),
            'borrowed' => $this->borrowedBooks(),
            default => $this->availableBooks(),
        };
    }


    #[Computed]
    public function activeBorrowings()
    {
        if (!$this->selectedWardId) return collect();
        $student = getStudent(student_id: $this->selectedWardId, withoutScopes: true);
        return BookBorrowing::where('user_id', $student->user->id)
            ->where('status', SubscriptionStatus::PAID->value)
            ->with(['book.bookCategory', 'book.author'])
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return BookCategory::withCount('books')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.parent.ParentBooksPage');
    }
}
