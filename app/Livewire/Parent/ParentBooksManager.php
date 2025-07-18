<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use App\Models\StudentParent;
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
            $this->selectedWardId = $wards->first()->id;
        }
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
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
        $existingSubscription = BookSubscription::where('student_id', $this->selectedWardId)
            ->where('book_id', $this->selectedBookId)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            session()->flash('error', 'Already subscribed to this book');
            return;
        }

        // Create subscription with pending payment status
        $subscription = BookSubscription::create([
            'student_id' => $this->selectedWardId,
            'book_id' => $this->selectedBookId,
            'subscription_type' => $this->subscriptionType,
            'status' => 'pending_payment',
            'subscribed_by' => Auth::id(),
            'starts_at' => now(),
            'expires_at' => $this->subscriptionType === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        $this->closeSubscriptionModal();
        session()->flash('success', 'Subscription created! Please complete payment to activate.');
    }

    public function cancelSubscription($subscriptionId)
    {
        $subscription = BookSubscription::where('id', $subscriptionId)
            ->where('student_id', $this->selectedWardId)
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            session()->flash('success', 'Subscription cancelled successfully!');
        }
    }

    public function getBookStatus($bookId)
    {
        if (!$this->selectedWardId) return null;

        $student = Student::find($this->selectedWardId);
        if (!$student) return null;

        $book = Book::find($bookId);
        if (!$book) return null;

        // Check if book is subscribed
        $subscription = BookSubscription::where('student_id', $this->selectedWardId)
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
        $borrowing = BookBorrowing::where('student_id', $this->selectedWardId)
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

    #[Computed]
    public function wards()
    {
        $students = StudentParent::where('user_id', Auth::id())
            ->with(['students.user', 'students.academicLevel.academicGroup', 'students.studentGroup'])
            ->get()
            ->flatMap(function($parent) {
                return $parent->students;
            })
            ->unique('id'); // Remove duplicates

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

        return Student::with([
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
    public function subscribedBooks()
    {
        if (!$this->selectedWardId) return collect();

        $subscribedBookIds = BookSubscription::where('student_id', $this->selectedWardId)
            ->where('status', 'paid')
            ->pluck('book_id');

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
    public function borrowedBooks()
    {
        if (!$this->selectedWardId) return collect();

        $borrowedBookIds = BookBorrowing::where('student_id', $this->selectedWardId)
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
    public function availableBooks()
    {
        if (!$this->selectedWardId) return collect();

        // Get IDs of books that are already subscribed or borrowed
        $subscribedBookIds = BookSubscription::where('student_id', $this->selectedWardId)
            ->where('status', 'active')
            ->pluck('book_id');

        $borrowedBookIds = BookBorrowing::where('student_id', $this->selectedWardId)
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
    public function books()
    {
        switch ($this->activeTab) {
            case 'subscribed':
                return $this->subscribedBooks();
            case 'borrowed':
                return $this->borrowedBooks();
            case 'available':
                return $this->availableBooks();
            default:
                return $this->availableBooks();
        }
    }

    #[Computed]
    public function activeSubscriptions()
    {
        if (!$this->selectedWardId) return collect();

        return BookSubscription::where('student_id', $this->selectedWardId)
            ->where('status', 'paid')
            ->with(['book.bookCategory', 'book.author'])
            ->get();
    }

    #[Computed]
    public function activeBorrowings()
    {
        if (!$this->selectedWardId) return collect();

        return BookBorrowing::where('student_id', $this->selectedWardId)
            ->where('status', 'active')
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

    #[Computed]
    public function subscriptionStats()
    {
        if (!$this->selectedWardId) return [];

        $subscriptions = BookSubscription::where('student_id', $this->selectedWardId)->get();
        $borrowings = BookBorrowing::where('student_id', $this->selectedWardId)->get();

        return [
            'active_subscriptions' => $subscriptions->where('status', 'paid')->count(),
            'active_borrowings' => $borrowings->where('status', 'active')->count(),
            'total_subscriptions' => $subscriptions->count(),
            'expired_subscriptions' => $subscriptions->where('status', 'expired')->count(),
            'cancelled_subscriptions' => $subscriptions->where('status', 'cancelled')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.parent.ParentBooksPage');
    }
}
