<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookDetails extends Component
{
    use AuthorizesRequests;

    public Book $book;
    public $showPdfReader = false;
    public $currentPage = 1;

    public function mount(Book $book)
    {
        $this->authorize('view', $book);

        $this->book = $book->load([
            'author',
            'bookCategory',
            'subscriptions' => fn($query) => $query->latest()->take(5),
            'borrowings' => fn($query) => $query->latest()->take(5)
        ]);
    }

    public function openPdfReader()
    {
        if ($this->book->content_url) {
            $this->showPdfReader = true;

            Log::info('Opening PDF reader', [
                'content_url' => $this->book->content_url,
                'current_page' => $this->currentPage
            ]);

            // Dispatch event to trigger PDF reader
            $this->dispatch('openPdfReader',
                $this->book->content_url,
                $this->book->title,
                $this->currentPage
            );
        } else {
            Log::error('No content URL available for book', ['book_id' => $this->book->id]);
        }
    }

    public function closePdfReader()
    {
        $this->showPdfReader = false;
    }

    public function updateCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    public function render()
    {
        return view('livewire.authors.BookDetailsPage');
    }
}
