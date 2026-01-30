<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentLibraryManager extends AppComponent
{
    use WithPagination;

    public $selectedWardId = null;

    public $selectedCategoryId = null;

    public $searchTerm = '';

    public $sortBy = 'title';

    public $sortDirection = 'asc';

    public $viewMode = 'grid';

    public $readingFilter = 'all';

    public $showBookReader = false;

    public $selectedBookId = null;

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

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $this->resetPage();
    }

    public function filterByReading($filter)
    {
        $this->readingFilter = $filter;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function openBookReader($bookId)
    {
        $this->selectedBookId = $bookId;

        return redirect()->route('parent.library.read', ['book' => $bookId]);
    }

    public function addToFavorites($bookId)
    {
        // Implementation for adding to favorites
        $this->dispatch('book-added-to-favorites', bookId: $bookId);
        session()->flash('success', 'Book added to favorites!');
    }

    public function removeFromFavorites($bookId)
    {
        // Implementation for removing from favorites
        $this->dispatch('book-removed-from-favorites', bookId: $bookId);
        session()->flash('success', 'Book removed from favorites!');
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::where('user_id', Auth::id())
            ->with(['students.user', 'students.academicLevel.academicGroup', 'students.studentGroup'])
            ->get()
            ->flatMap(function ($parent) {
                return $parent->students;
            })
            ->unique('id'); // Remove duplicates

        if ($this->searchTerm) {
            $students = $students->filter(function ($student) {
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
        if (! $this->selectedWardId) {
            return null;
        }

        return Student::with([
            'user',
            'academicLevel.academicGroup',
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function availableBooks()
    {
        if (! $this->selectedWardId) {
            return collect();
        }

        $student = Student::findOrFail($this->selectedWardId);

        // Get books that the ward has access to through subscriptions (including parent subscriptions)
        $subscribedBookIds = BookSubscription::where(function ($query) use ($student) {
            $query->where('user_id', $student->user->id)
                ->orWhere(function ($q) use ($student) {
                    $q->where('subscribed_by', auth()->user()->id)
                        ->where('user_id', $student->user->id);
                });
        })
            ->where('status', 'active')
            ->pluck('book_id');

        $query = Book::with(['bookCategory', 'author'])
            ->whereIn('id', $subscribedBookIds);

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'LIKE', '%'.$this->searchTerm.'%')
                    ->orWhere('description', 'LIKE', '%'.$this->searchTerm.'%')
                    ->orWhereHas('author', function ($author) {
                        $author->where('name', 'LIKE', '%'.$this->searchTerm.'%');
                    });
            });
        }

        if ($this->selectedCategoryId) {
            $query->where('book_category_id', $this->selectedCategoryId);
        }

        if ($this->readingFilter !== 'all') {
            // Filter based on reading history - implement as needed
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Computed]
    public function categories()
    {
        if (! $this->selectedWardId) {
            return collect();
        }

        // Get categories of books the ward has access to
        $subscribedBookIds = BookSubscription::where('user_id', Student::findOrFail($this->selectedWardId)->user->id)
            ->where('status', 'active')
            ->pluck('book_id');

        return BookCategory::whereHas('books', function ($query) use ($subscribedBookIds) {
            $query->whereIn('id', $subscribedBookIds);
        })
            ->withCount(['books' => function ($query) use ($subscribedBookIds) {
                $query->whereIn('id', $subscribedBookIds);
            }])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function readingStats()
    {
        if (! $this->selectedWardId) {
            return [];
        }

        $subscribedBooks = BookSubscription::where('user_id', Student::findOrFail($this->selectedWardId)->user->id)
            ->where('status', 'active')
            ->count();

        return [
            'total_books' => $subscribedBooks,
            'books_read' => rand(5, 15), // Mock data
            'reading_time' => rand(10, 50).'h', // Mock data
            'favorite_books' => rand(2, 8), // Mock data
        ];
    }

    #[Computed]
    public function recentlyRead()
    {
        if (! $this->selectedWardId) {
            return collect();
        }

        // Mock implementation - in real app, track reading history
        $subscribedBookIds = BookSubscription::where('user_id', Student::findOrFail($this->selectedWardId)->user->id)
            ->where('status', 'active')
            ->pluck('book_id');

        return Book::with(['author'])
            ->whereIn('id', $subscribedBookIds)
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recommendations()
    {
        if (! $this->selectedWardId) {
            return collect();
        }

        // Mock implementation - in real app, use recommendation algorithm
        return Book::with(['author'])
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.parent.ParentLibraryPage');
    }
}
