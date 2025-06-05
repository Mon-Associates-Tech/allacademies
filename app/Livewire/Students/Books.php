<?php

namespace App\Livewire\Students;

use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\BookBorrowing;
use Illuminate\Support\Facades\Auth;

class Books extends Component
{
    use WithPagination;

    public $activeTab = 'available';
    public $search = '';
    public $selectedCategory = '';
    public $selectedFormat = '';
    public $categories = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedFormat' => ['except' => ''],
        'activeTab' => ['except' => 'available']
    ];

    public function mount()
    {
        $this->categories = BookCategory::pluck('name', 'id')->toArray();
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

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function subscribeToBook($bookId)
    {
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            return;
        }

        $book = Book::findOrFail($bookId);

        if (!$book->has_softcopy) {
            session()->flash('error', 'This book is not available for subscription.');
            return;
        }

        $existingSubscription = BookSubscription::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            session()->flash('error', 'You are already subscribed to this book.');
            return;
        }

        BookSubscription::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'subscribed_at' => now(),
            'status' => 'active'
        ]);

        session()->flash('success', 'Successfully subscribed to book!');
    }

    public function borrowBook($bookId)
    {
        $student = Auth::user()->student;

        if (!$student) {
            session()->flash('error', 'Student profile not found.');
            return;
        }

        $book = Book::findOrFail($bookId);

        if (!$book->has_hardcopy || $book->available_copies <= 0) {
            session()->flash('error', 'This book is not available for borrowing.');
            return;
        }

        $existingBorrowing = BookBorrowing::where('student_id', $student->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingBorrowing) {
            session()->flash('error', 'You have already requested or borrowed this book.');
            return;
        }

        BookBorrowing::create([
            'student_id' => $student->id,
            'book_id' => $bookId,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'pending'
        ]);

        session()->flash('success', 'Borrowing request submitted successfully!');
    }

    public function getAvailableBooksProperty()
    {
        return Book::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('author', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedFormat, function($query) {
                if ($this->selectedFormat === 'softcopy') {
                    $query->where('has_softcopy', true);
                } elseif ($this->selectedFormat === 'hardcopy') {
                    $query->where('has_hardcopy', true);
                }
            })
            ->with('bookCategory')
            ->paginate(12);
    }

    public function getSubscribedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        return BookSubscription::where('student_id', $student->id)
            ->where('status', 'active')
            ->with('book.bookCategory')
            ->latest()
            ->paginate(12);
    }

    public function getBorrowedBooksProperty()
    {
        $student = Auth::user()->student;
        if (!$student) return collect();

        return BookBorrowing::where('student_id', $student->id)
            ->with('book.bookCategory')
            ->latest()
            ->paginate(12);
    }

    public function render()
    {
        $books = collect();

        switch ($this->activeTab) {
            case 'available':
                $books = $this->availableBooks;
                break;
            case 'subscribed':
                $books = $this->subscribedBooks;
                break;
            case 'borrowed':
                $books = $this->borrowedBooks;
                break;
        }

        return view('livewire.students.books', [
            'books' => $books,
            'categories' => $this->categories
        ]);
    }
}
