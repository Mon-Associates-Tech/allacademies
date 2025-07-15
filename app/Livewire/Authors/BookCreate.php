<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookCreate extends Component
{
    use WithFileUploads;

    public $title = '';
    public $edition = '';
    public $publisher = '';
    public $pages = '';
    public $has_hardcopy = false;
    public $has_softcopy = false;
    public $additional_info = '';
    public $book_category_id = '';
    public $annual_subscription_fee = '';
    public $subscription_conditions = '';
    public $cover_image = null;
    public $pdf_file = null;
    public $status = 'draft';

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
        'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
        'status' => 'required|in:draft,published',
    ];

    public function createBook()
    {
        $this->validate();

        $author = Auth::user()->author;
        if (!$author) {
            session()->flash('error', 'You must be an author to create books.');
            return;
        }

        $bookData = [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'author_id' => $author->id,
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'has_hardcopy' => $this->has_hardcopy,
            'has_softcopy' => $this->has_softcopy,
            'additional_info' => $this->additional_info,
            'book_category_id' => $this->book_category_id,
            'annual_subscription_fee' => $this->annual_subscription_fee ?: 0,
            'subscription_conditions' => $this->subscription_conditions,
            'status' => $this->status,
        ];

        // Handle cover image upload
        if ($this->cover_image) {
            $coverPath = $this->cover_image->store('book-covers', 'public');
            $bookData['cover_image_path'] = $coverPath;
        }

        // Handle PDF file upload
        if ($this->pdf_file) {
            $pdfPath = $this->pdf_file->store('book-pdfs', 'public');
            $bookData['pdf_file_path'] = $pdfPath;
        }

        $book = Book::create($bookData);

        session()->flash('success', 'Book created successfully!');

        return redirect()->route('author.books.index');
    }

    public function render()
    {
        return view('livewire.authors.book-create', [
            'categories' => BookCategory::all(),
        ]);
    }
}
