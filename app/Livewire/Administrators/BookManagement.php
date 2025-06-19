<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Book;
use App\Models\Author;
use App\Models\BookCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public $searchTerm = '';
    public $isEditing = false;
    public $editingBookId;

    public $authors;
    public $bookCategories;

    protected $rules = [
        'title' => 'required|min:3',
        'authorId' => 'required|exists:authors,id',
        'bookCategoryId' => 'required|exists:book_categories,id',
        'edition' => 'nullable|string',
        'publisher' => 'nullable|string',
        'pages' => 'nullable|integer|min:1',
        'hasHardcopy' => 'boolean',
        'hasSoftcopy' => 'boolean',
        'additionalInfo' => 'nullable|string',
        'coverImage' => 'nullable|image|max:2048', // 2MB Max
        'pdfFile' => 'nullable|mimes:pdf|max:10240', // 10MB Max
    ];

    public function mount()
    {
        $this->authors = Author::with('user')->get();
        $this->bookCategories = BookCategory::all();
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function create()
    {
        $this->validate();

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
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'cover_image_path' => $coverPath,
            'pdf_file_path' => $pdfPath,
        ]);

        $this->resetForm();
        session()->flash('message', 'Book created successfully!');
    }

    public function edit($bookId)
    {
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
        $this->existingCover = $book->cover_image_path;
        $this->existingPdf = $book->pdf_file_path;
    }

    public function update()
    {
        $book = Book::findOrFail($this->editingBookId);

        $this->validate([
            'title' => 'required|min:3',
            'authorId' => 'required|exists:authors,id',
            'bookCategoryId' => 'required|exists:book_categories,id',
            'edition' => 'nullable|string',
            'publisher' => 'nullable|string',
            'pages' => 'nullable|integer|min:1',
            'hasHardcopy' => 'boolean',
            'hasSoftcopy' => 'boolean',
            'additionalInfo' => 'nullable|string',
            'coverImage' => 'nullable|image|max:2048',
            'pdfFile' => 'nullable|mimes:pdf|max:10240',
        ]);

        // Handle cover image
        $coverPath = $book->cover_image_path;
        if ($this->coverImage) {
            // Delete old cover if exists
            if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                Storage::disk('public')->delete($coverPath);
            }

            $coverPath = $this->coverImage->store('book-covers', 'public');
        }

        // Handle PDF file
        $pdfPath = $book->pdf_file_path;
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
            'has_hardcopy' => $this->hasHardcopy,
            'has_softcopy' => $this->hasSoftcopy,
            'additional_info' => $this->additionalInfo,
            'cover_image_path' => $coverPath,
            'pdf_file_path' => $pdfPath,
        ]);

        $this->resetForm();
        session()->flash('message', 'Book updated successfully!');
    }

    public function delete($bookId)
    {
        $book = Book::findOrFail($bookId);

        // Check if book has borrowings or subscriptions
        if ($book->borrowings()->count() > 0 || $book->subscriptions()->count() > 0) {
            session()->flash('error', 'Cannot delete book with active borrowings or subscriptions.');
            return;
        }

        // Delete cover image
        if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
            Storage::disk('public')->delete($book->cover_image_path);
        }

        // Delete PDF file
        if ($book->pdf_file_path && Storage::disk('public')->exists($book->pdf_file_path)) {
            Storage::disk('public')->delete($book->pdf_file_path);
        }

        $book->delete();
        session()->flash('message', 'Book deleted successfully!');
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
        $this->hasHardcopy = false;
        $this->hasSoftcopy = false;
        $this->additionalInfo = '';
        $this->coverImage = null;
        $this->pdfFile = null;
        $this->existingCover = null;
        $this->existingPdf = null;
        $this->isEditing = false;
        $this->editingBookId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $books = Book::where('title', 'like', '%'.$this->searchTerm.'%')
            ->orWhere('publisher', 'like', '%'.$this->searchTerm.'%')
            ->orWhereHas('author.user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhereHas('bookCategory', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->with(['author.user', 'bookCategory', 'borrowings', 'subscriptions'])
            ->paginate(10);

        return view('livewire.administrators.book-management', [
            'books' => $books
        ]);
    }
}
