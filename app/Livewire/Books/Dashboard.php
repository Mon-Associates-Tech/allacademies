<?php

namespace App\Livewire\Books;

use Livewire\Component;
use App\Models\Book;
use App\Models\StudentGroup;
use App\Models\BookSubscription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $filterType = ''; // 'free', 'paid', 'all'
    public $sortBy = 'title';
    public $sortDirection = 'asc';

    public $subscribedBookIds = [];
    public $books = [];

    public function mount()
    {
        // Default filter to show all books
        $this->filterType = 'all';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getGroupsProperty()
    {
        // Get groups where the current user is an admin, teacher or librarian
        $user = Auth::user();

        if ($user->hasRole(['administrator', 'teacher', 'librarian'])) {
            return StudentGroup::where('creator_id', $user->id)->get();
        }

        return collect();
    }

    public function getUserSubscribedBooksProperty()
    {
        $user = Auth::user();

        if ($user->student) {
            // Individual subscriptions
            $this->subscribedBookIds = BookSubscription::where('student_id', $user->student->id)
                ->where('status', 'active')
                ->pluck('book_id')
                ->toArray();

            // Group subscriptions - books available through student's groups
            if ($user->student->studentGroup) {
                $groupSubscribedBooks = $user->student->studentGroup->groupBookSubscriptions()
                    ->where('status', 'active')
                    ->pluck('book_id')
                    ->toArray();

                $this->subscribedBookIds = array_merge($this->subscribedBookIds, $groupSubscribedBooks);
            }
        }

        return $this->subscribedBookIds;
    }

    public function render()
    {
        $booksQuery = Book::query()
            ->with(['author', 'author.user', 'category'])
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('author.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->category, function ($query) {
                return $query->whereHas('category', function ($q) {
                    $q->where('id', $this->category);
                });
            })
            ->when($this->filterType === 'free', function ($query) {
                return $query->where('is_free', true);
            })
            ->when($this->filterType === 'paid', function ($query) {
                return $query->where('is_free', false);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        // Store just the data you need
        $paginator = $booksQuery->paginate();
        $this->books = $paginator->items();
        $this->currentPage = $paginator->currentPage();
        $this->totalPages = $paginator->lastPage();
        $this->perPage = $paginator->perPage();
//        dd($this->books);


        return view('livewire.books.dashboard', [
            'books' => $this->books,
            'subscribedBookIds' => $this->userSubscribedBooks,
            'userGroups' => $this->groups,
        ]);
    }

    public function subscribeToBook($bookId)
    {
        $user = Auth::user();

        if (!$user->student) {
            $this->addError('subscription', 'Only students can subscribe to books');
            return;
        }

        // Check if already subscribed
        $existingSubscription = BookSubscription::where('book_id', $bookId)
            ->where('student_id', $user->student->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            $this->addError('subscription', 'You are already subscribed to this book');
            return;
        }

        // Create a new subscription
        BookSubscription::create([
            'book_id' => $bookId,
            'student_id' => $user->student->id,
            'status' => 'active',
            'subscription_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);

        $this->dispatch('book-subscribed');
    }

    public function subscribeGroupToBook($bookId, $groupId)
    {
        $user = Auth::user();

        // Check if a user has permission to subscribe for the group
        if (!$user->hasRole(['administrator', 'teacher', 'librarian'])) {
            $this->addError('groupSubscription', 'You do not have permission to subscribe groups');
            return;
        }

        $group = StudentGroup::find($groupId);

        if (!$group || $group->creator_id !== $user->id) {
            $this->addError('groupSubscription', 'You do not have permission to manage this group');
            return;
        }

        // Check if a group already subscribed
        $existingSubscription = $group->groupBookSubscriptions()
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            $this->addError('groupSubscription', 'This group is already subscribed to this book');
            return;
        }

        // Create a new group subscription
        $group->groupBookSubscriptions()->create([
            'book_id' => $bookId,
            'status' => 'active',
            'subscription_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);

        $this->dispatch('group-subscribed');
    }
}
