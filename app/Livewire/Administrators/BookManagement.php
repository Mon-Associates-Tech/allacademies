<?php

namespace App\Livewire\Administrators;

use App\Enums\PublishingStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BookManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $slug;
    public $authorId;
    public $bookCategoryId;
    public $edition;
    public $publisher;
    public $pages;
    public $hasHardcopy = false;
    public $hasSoftcopy = false;
    public $additionalInfo;
    public $coverImage;
    public $pdfFile;
    public $existingCover;
    public $existingPdf;
    public $annualSubscriptionFee = 0;
    public $subscriptionConditions;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingBookId;
    public $showForm = false;

    // Filtering and sorting
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterCategory = '';
    public $filterAuthor = '';
    public $filterFormat = '';

    // Bulk operations
    public $selectedBooks = [];
    public $selectAll = false;

    public $authors;
    public $bookCategories;
    public $isAdmin = true;
    public string $status = 'published';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'authorId' => 'required|exists:authors,id',
        'bookCategoryId' => 'required|exists:book_categories,id',
        'edition' => 'nullable|string|max:50',
        'publisher' => 'nullable|string|max:255',
        'pages' => 'nullable|integer|min:1|max:9999',
        'hasHardcopy' => 'boolean',
        'hasSoftcopy' => 'boolean',
        'additionalInfo' => 'nullable|string',
        'annualSubscriptionFee' => 'nullable|numeric|min:0|max:999999.99',
        'subscriptionConditions' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048', // 2MB Max
        'pdfFile' => 'nullable|mimes:pdf|max:10240', // 10MB Max
    ];

    protected $listeners = ['refreshBooks' => '$refresh', 'bookSaved' => '$refresh'];

    public function mount()
    {
        $this->authors = Author::with('user')->orderBy('id')->get();
        $this->bookCategories = BookCategory::orderBy('name')->get();
    }

    /**
     * Toggle the publishing status of a book
     */
    public function toggleBookStatus($bookId)
    {
        try {
            $book = Book::findOrFail($bookId);

            // Convert current status from legacy format if needed
            $currentStatus = PublishingStatus::fromLegacy($book->status);

            // Toggle between published and draft
            $newStatus = $currentStatus === PublishingStatus::PUBLISHED
                ? PublishingStatus::DRAFT
                : PublishingStatus::PUBLISHED;

            $book->update(['status' => $newStatus->value]);

            session()->flash('success', "Book status updated to {$newStatus->getLabel()} successfully!");

        } catch (Exception $e) {
            session()->flash('error', 'Failed to update book status. Please try again.');
        }
    }

    public function update()
    {
        $book = Book::findOrFail($this->editingBookId);

        $this->validate();

        // Validate that at least one format is selected
        if (!$this->hasHardcopy && !$this->hasSoftcopy) {
            $this->addError('hasHardcopy', 'Please select at least one format (hardcopy or softcopy).');
            return;
        }

        // Handle cover image
        $coverPath = $book->cover_image;
        if ($this->coverImage) {
            // Delete old cover if exists
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $this->coverImage->store('book-covers', 'public');
        }

        // Handle PDF file
        $pdfPath = $book->pdf_file;
        if ($this->pdfFile) {
            // Delete old PDF if exists
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }
            $pdfPath = $this->pdfFile->store('book-pdfs', 'public');
        }

        // Update book
        $book->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId,
            'book_category_id' => $this->bookCategoryId,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'status' => $this->status,
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'annual_subscription_fee' => $this->annualSubscriptionFee,
            'subscription_conditions' => $this->subscriptionConditions,
            'cover_image' => $coverPath,
            'content_url' => $pdfPath,
        ]);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Book updated successfully!');
        $this->dispatch('refreshBooks');
    }

    public function delete($bookId): void
    {
        $book = Book::findOrFail($bookId);

        if ($book->borrowings()->count() > 0 || $book->subscriptions()->count() > 0) {
            session()->flash('error', 'Cannot delete book with active borrowings or subscriptions.');
            // return;
        }


        $subscriptionIds = $book->subscriptions()->pluck('id');

        if ($subscriptionIds->isNotEmpty()) {
            Payment::whereIn('book_subscription_id', $subscriptionIds)->delete();
        }

        $book->subscriptions()->delete();

        $book->borrowings()->delete();

        $this->deleteBookFiles($book);

        $book->delete();

        session()->flash('message', 'Book deleted successfully!');
        $this->dispatch('refreshBooks');
    }

    private function deleteBookFiles($book)
    {
        // Delete cover image
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Delete PDF file
        if ($book->pdf_file && Storage::disk('public')->exists($book->pdf_file)) {
            Storage::disk('public')->delete($book->pdf_file);
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->slug = '';
        $this->authorId = '';
        $this->bookCategoryId = '';
        $this->edition = '';
        $this->publisher = '';
        $this->pages = null;
        $this->status = '';
        $this->hasHardcopy = false;
        $this->hasSoftcopy = false;
        $this->additionalInfo = '';
        $this->annualSubscriptionFee = 0;
        $this->subscriptionConditions = '';
        $this->coverImage = null;
        $this->pdfFile = null;
        $this->existingCover = null;
        $this->existingPdf = null;
        $this->isEditing = false;
        $this->editingBookId = null;
        $this->resetValidation();
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedBooks = $this->books->pluck('id')->toArray();
        } else {
            $this->selectedBooks = [];
        }
    }

    public function updatedSelectedBooks()
    {
        $this->selectAll = count($this->selectedBooks) === count($this->books);
    }

    public function showCreateForm()
    {
        return redirect(route('admin.books.create'));
        $this->showForm = true;
        $this->isEditing = false;
        $this->resetForm();
    }

    public function hideForm()
    {
        $this->showForm = false;
        $this->resetForm();
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

    public function create()
    {
        $this->validate();

        // Validate that at least one format is selected
        if (!$this->hasHardcopy && !$this->hasSoftcopy) {
            $this->addError('hasHardcopy', 'Please select at least one format (hardcopy or softcopy).');
            return;
        }

        // If softcopy is selected but no PDF file provided
        if ($this->hasSoftcopy && !$this->pdfFile) {
            $this->addError('pdfFile', 'PDF file is required for softcopy books.');
            return;
        }

        // Handle cover image
        $coverPath = null;
        if ($this->coverImage) {
            $coverPath = $this->coverImage->store('book-covers', 'public');
        }

        // Handle PDF file
        $pdfPath = null;
        if ($this->pdfFile) {
            $pdfPath = $this->pdfFile->store('book-pdfs', 'public');
        }

        // Create book
        Book::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'author_id' => $this->authorId,
            'book_category_id' => $this->bookCategoryId,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'status' => $this->status,
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'annual_subscription_fee' => $this->annualSubscriptionFee,
            'subscription_conditions' => $this->subscriptionConditions,
            'cover_image' => $coverPath,
            'content_url' => $pdfPath,
        ]);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Book created successfully!');
        $this->dispatch('refreshBooks');
    }

    public function edit($bookId)
    {
        $this->showForm = true;
        $this->isEditing = true;
        $this->editingBookId = $bookId;

        $book = Book::findOrFail($bookId);
        $this->title = $book->title;
        $this->slug = $book->slug;
        $this->authorId = $book->author_id;
        $this->bookCategoryId = $book->book_category_id;
        $this->edition = $book->edition;
        $this->publisher = $book->publisher;
        $this->pages = $book->pages;
        $this->hasHardcopy = $book->has_hardcopy;
        $this->hasSoftcopy = $book->has_softcopy;
        $this->additionalInfo = $book->additional_info;
        $this->annualSubscriptionFee = $book->annual_subscription_fee ?? 0;
        $this->subscriptionConditions = $book->subscription_conditions;
        $this->existingCover = $book->cover_image_path;
        $this->existingPdf = $book->pdf_file_path;
        $this->status = $book->status ?? 'draft';
    }

    public function bulkDelete()
    {
        if (empty($this->selectedBooks)) {
            session()->flash('error', 'Please select books to delete.');
            return;
        }

        $books = Book::whereIn('id', $this->selectedBooks)->get();
        $deletedCount = 0;
        $errors = [];

        foreach ($books as $book) {
            // Check if book has borrowings or subscriptions
            if ($book->borrowings()->count() > 0 || $book->subscriptions()->count() > 0) {
                $errors[] = "Cannot delete '{$book->title}' - has active borrowings or subscriptions.";
                continue;
            }

            $this->deleteBookFiles($book);
            $book->subscriptions()->delete();
            $book->borrowings()->delete();
            $book->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            session()->flash('message', "Successfully deleted {$deletedCount} book(s).");
        }

        if (!empty($errors)) {
            session()->flash('error', implode('<br>', $errors));
        }

        $this->selectedBooks = [];
        $this->selectAll = false;
        $this->dispatch('refreshBooks');
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->filterCategory = '';
        $this->filterAuthor = '';
        $this->filterFormat = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function getBooksProperty()
    {
        $query = Book::query()
            ->with(['author.user', 'bookCategory', 'borrowings', 'subscriptions']);

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('publisher', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('author.user', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('bookCategory', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        // Apply category filter
        if ($this->filterCategory) {
            $query->where('book_category_id', $this->filterCategory);
        }

        // Apply author filter
        if ($this->filterAuthor) {
            $query->where('author_id', $this->filterAuthor);
        }

        // Apply format filter
        if ($this->filterFormat) {
            if ($this->filterFormat === 'hardcopy') {
                $query->where('has_hardcopy', true);
            } elseif ($this->filterFormat === 'softcopy') {
                $query->where('has_softcopy', true);
            } elseif ($this->filterFormat === 'both') {
                $query->where('has_hardcopy', true)->where('has_softcopy', true);
            }
        }

        // Apply sorting
        if ($this->sortBy === 'title') {
            $query->orderBy('title', $this->sortDirection);
        } elseif ($this->sortBy === 'author') {
            $query->join('authors', 'books.author_id', '=', 'authors.id')
                ->join('users', 'authors.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection)
                ->select('books.*');
        } elseif ($this->sortBy === 'category') {
            $query->join('book_categories', 'books.book_category_id', '=', 'book_categories.id')
                ->orderBy('book_categories.name', $this->sortDirection)
                ->select('books.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.administrators.book-management', [
            'books' => $this->books
        ]);
    }
}
