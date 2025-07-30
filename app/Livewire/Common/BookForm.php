<?php

namespace App\Livewire\Common;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookForm extends Component
{
    use WithFileUploads;

    // Book properties
    public $title;
    public $slug;
    public $book_category_id;
    public $edition;
    public $publisher;
    public $pages;
    public $has_hardcopy = false;
    public $has_softcopy = false;
    public $additional_info;
    public $annual_subscription_fee = 0;
    public $subscription_conditions;
    public $cover_image;
    public $pdf_file;
    public $status = 'draft';

    // Author selection properties
    public $author_selection_type = 'existing'; // 'existing' or 'new'
    public $selected_author_id;

    // New author properties
    public $author_name;
    public $author_biography;
    public $author_website;
    public $author_social_links;
    public $author_writing_experience;
    public $author_education;
    public $author_awards;
    public $author_statement;
    public $author_pen_name;

    // Component state
    public $editingBook;
    public $showModal = false;
    public $isAdmin = false;

    // Data collections
    public $authors;
    public $categories;

    protected function rules()
    {
        $rules = [
            'title' => 'required|min:3|max:255',
            'book_category_id' => 'required|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1|max:9999',
            'has_hardcopy' => 'boolean',
            'has_softcopy' => 'boolean',
            'additional_info' => 'nullable|string',
            'annual_subscription_fee' => 'nullable|numeric|min:0|max:999999.99',
            'subscription_conditions' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ];

        if ($this->author_selection_type === 'existing') {
            $rules['selected_author_id'] = 'required|exists:authors,id';
        } else {
            $rules['author_name'] = 'required|string|max:255';
            $rules['author_biography'] = 'nullable|string';
            $rules['author_website'] = 'nullable|url|max:255';
            $rules['author_social_links'] = 'nullable|string';
            $rules['author_writing_experience'] = 'nullable|string';
            $rules['author_education'] = 'nullable|string';
            $rules['author_awards'] = 'nullable|string';
            $rules['author_statement'] = 'nullable|string';
            $rules['author_pen_name'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function mount($book = null, $isAdmin = false)
    {
        $this->isAdmin = $isAdmin;
        $this->authors = Author::with('user')->orderBy('id')->get();
        $this->categories = BookCategory::orderBy('name')->get();

        if ($book) {
            $this->editingBook = $book;
            $this->loadBookData($book);
        }

        // For authors, default to creating for themselves
        if (!$this->isAdmin && Auth::user()->author) {
            $this->author_selection_type = 'existing';
            $this->selected_author_id = Auth::user()->author->id;
        }
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedAuthorSelectionType()
    {
        $this->resetAuthorFields();
    }

    public function showForm($book = null)
    {
        $this->showModal = true;
        if ($book) {
            $this->editingBook = $book;
            $this->loadBookData($book);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->mount(null, $this->isAdmin);
    }

    public function saveBook()
    {
        $this->validate();

        // Validate that at least one format is selected
        if (!$this->has_hardcopy && !$this->has_softcopy) {
            $this->addError('has_hardcopy', 'Please select at least one format (hardcopy or softcopy).');
            return;
        }

        // If softcopy is selected but no PDF file provided (and not editing with existing PDF)
        if ($this->has_softcopy && !$this->pdf_file && (!$this->editingBook || !$this->editingBook->pdf_file_path)) {
            $this->addError('pdf_file', 'PDF file is required for softcopy books.');
            return;
        }

        DB::transaction(function () {
            // Handle author creation/selection
            $authorId = $this->handleAuthor();

            // Handle file uploads
            $coverPath = $this->handleCoverUpload();
            $pdfPath = $this->handlePdfUpload();

            // Prepare book data
            $bookData = [
                'title' => $this->title,
                'slug' => $this->slug ?: Str::slug($this->title),
                'author_id' => $authorId,
                'book_category_id' => $this->book_category_id,
                'edition' => $this->edition,
                'publisher' => $this->publisher,
                'pages' => $this->pages,
                'has_hardcopy' => $this->has_hardcopy,
                'has_softcopy' => $this->has_softcopy,
                'additional_info' => $this->additional_info,
                'annual_subscription_fee' => $this->annual_subscription_fee ?: 0,
                'subscription_conditions' => $this->subscription_conditions,
                'status' => $this->status,
            ];

            if ($coverPath !== null) {
                $bookData['cover_image_path'] = $coverPath;
            }

            if ($pdfPath !== null) {
                $bookData['pdf_file_path'] = $pdfPath;
            }

            // Create or update book
            if ($this->editingBook) {
                $this->editingBook->update($bookData);
                $message = 'Book updated successfully!';
            } else {
                Book::create($bookData);
                $message = 'Book created successfully!';
            }

            session()->flash('success', $message);
        });

        $this->closeModal();
        $this->dispatch('bookSaved');
    }

    private function handleAuthor()
    {
        if ($this->author_selection_type === 'existing') {
            return $this->selected_author_id;
        }

        // Create new author - first check if user already has author record
        $user = Auth::user();
        
        if ($user->author) {
            // Update existing author record with new information
            $user->author->update([
                'name' => $this->author_name,
                'biography' => $this->author_biography,
                'website' => $this->author_website,
                'social_links' => $this->author_social_links,
                'writing_experience' => $this->author_writing_experience,
                'education' => $this->author_education,
                'awards' => $this->author_awards,
                'author_statement' => $this->author_statement,
                'pen_name' => $this->author_pen_name,
            ]);
            
            return $user->author->id;
        }

        // Create new author record
        $author = Author::create([
            'user_id' => $user->id,
            'name' => $this->author_name,
            'biography' => $this->author_biography,
            'website' => $this->author_website,
            'social_links' => $this->author_social_links,
            'writing_experience' => $this->author_writing_experience,
            'education' => $this->author_education,
            'awards' => $this->author_awards,
            'author_statement' => $this->author_statement,
            'pen_name' => $this->author_pen_name,
        ]);

        return $author->id;
    }

    private function handleCoverUpload()
    {
        if ($this->cover_image) {
            // Delete old cover if editing
            if ($this->editingBook && $this->editingBook->cover_image_path && Storage::disk('public')->exists($this->editingBook->cover_image_path)) {
                Storage::disk('public')->delete($this->editingBook->cover_image_path);
            }
            
            return $this->cover_image->store('book-covers', 'public');
        }

        return $this->editingBook ? $this->editingBook->cover_image_path : null;
    }

    private function handlePdfUpload()
    {
        if ($this->pdf_file) {
            // Delete old PDF if editing
            if ($this->editingBook && $this->editingBook->pdf_file_path && Storage::disk('public')->exists($this->editingBook->pdf_file_path)) {
                Storage::disk('public')->delete($this->editingBook->pdf_file_path);
            }
            
            return $this->pdf_file->store('book-pdfs', 'public');
        }

        return $this->editingBook ? $this->editingBook->pdf_file_path : null;
    }

    private function loadBookData($book)
    {
        $this->title = $book->title;
        $this->slug = $book->slug;
        $this->book_category_id = $book->book_category_id;
        $this->edition = $book->edition;
        $this->publisher = $book->publisher;
        $this->pages = $book->pages;
        $this->has_hardcopy = $book->has_hardcopy;
        $this->has_softcopy = $book->has_softcopy;
        $this->additional_info = $book->additional_info;
        $this->annual_subscription_fee = $book->annual_subscription_fee;
        $this->subscription_conditions = $book->subscription_conditions;
        $this->status = $book->status ?? 'draft';
        
        // Set author selection
        $this->author_selection_type = 'existing';
        $this->selected_author_id = $book->author_id;
    }

    private function resetAuthorFields()
    {
        $this->selected_author_id = null;
        $this->author_name = null;
        $this->author_biography = null;
        $this->author_website = null;
        $this->author_social_links = null;
        $this->author_writing_experience = null;
        $this->author_education = null;
        $this->author_awards = null;
        $this->author_statement = null;
        $this->author_pen_name = null;
    }

    public function render()
    {
        return view('livewire.common.book-form');
    }
}
