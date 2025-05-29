<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Book;
use App\Models\BookApproval;
use App\Models\Librarian;

class BookApprovalManagement extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filterStatus = '';
    public $selectedBookId;
    public $selectedBookTitle;
    public $approvalStatus;
    public $approvalComments;
    public $showApprovalModal = false;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openApprovalModal($bookId, $bookTitle)
    {
        $this->selectedBookId = $bookId;
        $this->selectedBookTitle = $bookTitle;
        $this->approvalStatus = '';
        $this->approvalComments = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
    }

    public function submitApproval()
    {
        $this->validate([
            'approvalStatus' => 'required|in:approved,rejected',
            'approvalComments' => 'nullable|string',
        ]);

        $book = Book::findOrFail($this->selectedBookId);

        // Find a librarian (in a real app, you'd use the authenticated librarian)
        $librarian = Librarian::first();

        BookApproval::create([
            'librarian_id' => $librarian->id,
            'book_id' => $this->selectedBookId,
            'approval_date' => now(),
            'status' => $this->approvalStatus,
            'comments' => $this->approvalComments,
        ]);

        $this->closeApprovalModal();
        session()->flash('message', "Book '{$this->selectedBookTitle}' has been {$this->approvalStatus}.");
    }

    public function render()
    {
        $query = Book::query()
            ->where(function($query) {
                $query->where('title', 'like', '%'.$this->searchTerm.'%')
                    ->orWhereHas('author.user', function($q) {
                        $q->where('name', 'like', '%'.$this->searchTerm.'%');
                    })
                    ->orWhereHas('bookCategory', function($q) {
                        $q->where('name', 'like', '%'.$this->searchTerm.'%');
                    });
            })
            ->with(['author.user', 'bookCategory', 'approvals']);

        // Filter by approval status
        if ($this->filterStatus === 'approved') {
            $query->whereHas('approvals', function($q) {
                $q->where('status', 'approved');
            });
        } elseif ($this->filterStatus === 'rejected') {
            $query->whereHas('approvals', function($q) {
                $q->where('status', 'rejected');
            });
        } elseif ($this->filterStatus === 'pending') {
            $query->whereDoesntHave('approvals')
                ->orWhereHas('approvals', function($q) {
                    $q->where('status', 'pending');
                });
        }

        $books = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.administrators.book-approval-management', [
            'books' => $books
        ]);
    }
}
