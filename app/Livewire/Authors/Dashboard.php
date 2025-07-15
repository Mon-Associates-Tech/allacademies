<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Author;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Dashboard extends Component
{
    use WithPagination, WithFileUploads;

    // Properties for book management
    public $showBookModal = false;
    public $showDeleteModal = false;
    public $editingBook = null;
    public $bookToDelete = null;

    // Book form fields
    public $title = '';
    public $edition = '';
    public $publisher = '';
    public $pages = 1;
    public $has_hardcopy = false;
    public $has_softcopy = false;
    public $additional_info = '';
    public $book_category_id = '';
    public $annual_subscription_fee = '';
    public $subscription_conditions = '';
    public $cover_image = null;
    public $content_url = null;

    // Filters and search
    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Stats
    public $totalBooks = 0;
    public $publishedBooks = 0;
    public $draftBooks = 0;
    public $totalSubscriptions = 0;
    public $totalRevenue = 0;

    public AuthorBookAction $authorBookAction;

    protected $rules = [
        'title' => 'required|string|max:255',
        'edition' => 'nullable|string|max:100',
        'publisher' => 'nullable|string|max:255',
        'pages' => 'nullable|integer|min:1',
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
        'additional_info' => 'nullable|string',
        'book_category_id' => 'required|exists:book_categories,id',
        'annual_subscription_fee' => 'nullable|numeric|min:0',
        'subscription_conditions' => 'nullable|string',
        'cover_image' => 'nullable|image|max:2048',
        'content_url' => 'nullable|file|mimes:pdf|max:10240',
    ];

    public function mount()
    {

        $this->loadStats();
    }

    public function loadStats()
    {
        $author = Auth::user()->author;
        if ($author) {
            $books = $author->books();
            $this->totalBooks = $books->count();
            $this->publishedBooks = $books->whereNotNull('cover_image')->count();
            $this->draftBooks = $books->whereNull('cover_image')->count();
            $this->totalSubscriptions = $books->withCount('subscriptions')->get()->sum('subscriptions_count');
            $this->totalRevenue = $books->get()->sum('annual_subscription_fee');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
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

    public function openBookModal($bookId = null)
    {
        $this->resetForm();

        if ($bookId) {
            $this->editingBook = Book::findOrFail($bookId);
            $this->title = $this->editingBook->title;
            $this->edition = $this->editingBook->edition;
            $this->publisher = $this->editingBook->publisher;
            $this->pages = $this->editingBook->pages;
            $this->has_hardcopy = $this->editingBook->has_hardcopy;
            $this->has_softcopy = $this->editingBook->has_softcopy;
            $this->additional_info = $this->editingBook->additional_info;
            $this->book_category_id = $this->editingBook->book_category_id;
            $this->annual_subscription_fee = $this->editingBook->annual_subscription_fee;
            $this->subscription_conditions = $this->editingBook->subscription_conditions;
        }

        $this->showBookModal = true;
    }

    public function closeBookModal()
    {
        $this->showBookModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingBook = null;
        $this->title = '';
        $this->edition = '';
        $this->publisher = '';
        $this->pages = '';
        $this->has_hardcopy = false;
        $this->has_softcopy = false;
        $this->additional_info = '';
        $this->book_category_id = '';
        $this->annual_subscription_fee = '';
        $this->subscription_conditions = '';
        $this->cover_image = null;
        $this->content_url = null;
        $this->resetValidation();
    }

    public function saveBook()
    {
        $this->validate();

        $author = Auth::user()->author;
        if (!$author) {
            session()->flash('error', 'You must be an author to create books.');
            return;
        }

        $bookData = [
            'title' => $this->title,
//            'slug' => Str::slug($this->title),
            'author_id' => $author->id,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => (int) $this->pages,
            'has_hardcopy' => $this->has_hardcopy,
            'has_softcopy' => $this->has_softcopy,
            'additional_info' => $this->additional_info,
            'book_category_id' => $this->book_category_id,
            'annual_subscription_fee' => $this->annual_subscription_fee ?: 0,
            'subscription_conditions' => $this->subscription_conditions,
        ];

        // Handle cover image upload
        if ($this->cover_image) {
            $coverPath = $this->cover_image->store('book-covers', 'public');
            $bookData['cover_image'] = $coverPath;
        }

        // Handle PDF file upload
        if ($this->content_url) {
            $pdfPath = $this->content_url->store('book-pdfs', 'public');
            $bookData['content_url'] = $pdfPath;
        }

        if ($this->editingBook) {
            $this->editingBook->update($bookData);
            session()->flash('success', 'Book updated successfully!');
        } else {
            Book::create($bookData);
            session()->flash('success', 'Book created successfully!');
        }

        $this->closeBookModal();
        $this->loadStats();
    }

    public function confirmDelete($bookId)
    {
        $this->bookToDelete = Book::findOrFail($bookId);
        $this->showDeleteModal = true;
    }

    public function deleteBook()
    {
        if ($this->bookToDelete) {
            // Delete associated files
            if ($this->bookToDelete->cover_image_path) {
                Storage::disk('public')->delete($this->bookToDelete->cover_image_path);
            }
            if ($this->bookToDelete->pdf_file_path) {
                Storage::disk('public')->delete($this->bookToDelete->pdf_file_path);
            }

            $this->bookToDelete->delete();
            session()->flash('success', 'Book deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->bookToDelete = null;
        $this->loadStats();
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->bookToDelete = null;
    }

    public function render()
    {
        $author = Auth::user()->author;
        $books = null;
        $categories = BookCategory::all();

        if ($author) {
            $query = $author->books()
                ->with(['bookCategory', 'subscriptions', 'borrowings'])
                ->withCount(['subscriptions', 'borrowings']);

            if ($this->search) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('publisher', 'like', '%' . $this->search . '%');
            }

            if ($this->categoryFilter) {
                $query->where('book_category_id', $this->categoryFilter);
            }

            if ($this->statusFilter) {
                if ($this->statusFilter === 'published') {
                    $query->whereNotNull('cover_image_path');
                } elseif ($this->statusFilter === 'draft') {
                    $query->whereNull('cover_image_path');
                }
            }

            $books = $query->orderBy($this->sortBy, $this->sortDirection)
                          ->paginate(10);
        }

        return view('livewire.authors.dashboard', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }
}
