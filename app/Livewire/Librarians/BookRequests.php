<?php

namespace App\Livewire\Librarians;

use App\Models\BookBorrowing;
use App\Models\Student;
use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class BookRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $selectedDate = '';
    public $showApprovalModal = false;
    public $selectedRequest = null;
    public $rejectionReason = '';
    public $customDueDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'pending'],
        'selectedDate' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function bookRequests()
    {
        $query = BookBorrowing::with(['student.user', 'book.bookCategory', 'book.author'])
            ->where('status', $this->statusFilter);

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('student.user', function($user) {
                    $user->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('book', function($book) {
                    $book->where('title', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->selectedDate) {
            $query->whereDate('created_at', $this->selectedDate);
        }

        return $query->latest()->paginate(10);
    }

    public function approveRequest($requestId)
    {
        $request = BookBorrowing::findOrFail($requestId);
        $this->selectedRequest = $request;
        $this->customDueDate = now()->addDays(14)->format('Y-m-d');
        $this->showApprovalModal = true;
    }

    public function confirmApproval()
    {
        $this->selectedRequest->update([
            'status' => 'active',
            'borrow_date' => now(),
            'due_date' => $this->customDueDate ? Carbon::parse($this->customDueDate) : now()->addDays(14),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->showApprovalModal = false;
        $this->selectedRequest = null;
        $this->customDueDate = '';

        session()->flash('success', 'Book request approved successfully!');
    }

    public function rejectRequest($requestId)
    {
        $request = BookBorrowing::findOrFail($requestId);
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
        ]);

        $this->rejectionReason = '';
        session()->flash('success', 'Book request rejected successfully!');
    }

    public function bulkApprove($requestIds)
    {
        BookBorrowing::whereIn('id', $requestIds)->update([
            'status' => 'active',
            'borrow_date' => now(),
            'due_date' => now()->addDays(14),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Selected requests approved successfully!');
    }

    public function render()
    {
        return view('livewire.librarians.book-requests');
    }
}
