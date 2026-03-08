<?php

namespace App\Livewire\Librarians;

use App\Models\AcademicLevel;
use App\Models\LibraryCard;
use App\Models\Student;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class LibraryCardManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $academicLevelFilter = 'all';

    // Modal states
    public $showCreateModal = false;

    public $showBulkActionModal = false;

    public $showRenewModal = false;

    // Form data
    public $selectedStudent = null;

    public $cardType = 'student';

    public $expiryDate = null;

    public $notes = '';

    public $renewCardId = null;

    // Bulk actions
    public $selectedCards = [];

    public $bulkAction = '';

    protected $rules = [
        'selectedStudent' => 'required|exists:students,id',
        'cardType' => 'required|in:student,premium',
        'expiryDate' => 'required|date|after:today',
        'notes' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->expiryDate = now()->addYear()->format('Y-m-d');
    }

    public function render()
    {
        $libraryCards = LibraryCard::query()
            ->with(['student.user', 'student.academicLevel'])
            ->when($this->search, function ($query) {
                $query->whereHas('student.user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('card_number', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->academicLevelFilter !== 'all', function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('academic_level_id', $this->academicLevelFilter);
                });
            })
            ->latest()
            ->paginate(20);

        $academicLevels = AcademicLevel::all();
        $availableStudents = Student::with(['user', 'academicLevel'])
            ->whereDoesntHave('libraryCard')
            ->orWhereHas('libraryCard', function ($query) {
                $query->where('status', 'expired');
            })
            ->get();

        return view('livewire.librarians.library-cards', [
            'libraryCards' => $libraryCards,
            'academicLevels' => $academicLevels,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function openCreateCardModal()
    {
        $this->reset(['selectedStudent', 'cardType', 'notes']);
        $this->expiryDate = now()->addYear()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['selectedStudent', 'cardType', 'notes']);
    }

    public function createCard()
    {
        $this->validate();

        $student = Student::find($this->selectedStudent);

        // Check if student already has an active card
        $existingCard = LibraryCard::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existingCard) {
            session()->flash('error', 'Student already has an active library card.');

            return;
        }

        // Generate unique card number and barcode
        do {
            $cardNumber = 'LC'.str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (LibraryCard::where('card_number', $cardNumber)->exists());

        $barcode = 'BC'.Str::random(10);

        LibraryCard::create([
            'student_id' => $student->id,
            'card_number' => $cardNumber,
            'barcode' => $barcode,
            'card_type' => $this->cardType,
            'status' => 'active',
            'issued_date' => now(),
            'expiry_date' => $this->expiryDate,
            'notes' => $this->notes,
            'issued_by' => auth()->id(),
        ]);

        session()->flash('success', 'Library card issued successfully!');
        $this->closeCreateModal();
        $this->dispatch('refresh-stats');
    }

    public function suspendCard($cardId)
    {
        $card = LibraryCard::find($cardId);

        if ($card) {
            $card->update([
                'status' => 'suspended',
                'suspended_at' => now(),
                'suspended_by' => auth()->id(),
            ]);

            session()->flash('success', 'Library card suspended successfully!');
        }
    }

    public function activateCard($cardId)
    {
        $card = LibraryCard::find($cardId);

        if ($card) {
            $card->update([
                'status' => 'active',
                'suspended_at' => null,
                'suspended_by' => null,
            ]);

            session()->flash('success', 'Library card activated successfully!');
        }
    }

    public function openRenewModal($cardId)
    {
        $this->renewCardId = $cardId;
        $card = LibraryCard::find($cardId);
        $this->expiryDate = $card->expiry_date->addYear()->format('Y-m-d');
        $this->showRenewModal = true;
    }

    public function closeRenewModal()
    {
        $this->showRenewModal = false;
        $this->reset(['renewCardId']);
    }

    public function renewCard()
    {
        $this->validate(['expiryDate' => 'required|date|after:today']);

        $card = LibraryCard::find($this->renewCardId);

        if ($card) {
            $card->update([
                'expiry_date' => $this->expiryDate,
                'status' => 'active',
                'renewed_at' => now(),
                'renewed_by' => auth()->id(),
            ]);

            session()->flash('success', 'Library card renewed successfully!');
            $this->closeRenewModal();
        }
    }

    public function printCard($cardId)
    {
        $card = LibraryCard::with(['student.user', 'student.academicLevel'])->find($cardId);

        if ($card) {
            // Generate PDF or redirect to print route
            return redirect()->route('librarian.library-cards.print', $card->id);
        }
    }

    public function openBulkActionModal()
    {
        $this->showBulkActionModal = true;
    }

    public function closeBulkActionModal()
    {
        $this->showBulkActionModal = false;
        $this->reset(['selectedCards', 'bulkAction']);
    }

    public function processBulkAction()
    {
        if (empty($this->selectedCards) || ! $this->bulkAction) {
            session()->flash('error', 'Please select cards and an action.');

            return;
        }

        $cards = LibraryCard::whereIn('id', $this->selectedCards);
        $count = $cards->count();

        switch ($this->bulkAction) {
            case 'suspend':
                $cards->update([
                    'status' => 'suspended',
                    'suspended_at' => now(),
                    'suspended_by' => auth()->id(),
                ]);
                session()->flash('success', "{$count} cards suspended successfully!");
                break;

            case 'activate':
                $cards->update([
                    'status' => 'active',
                    'suspended_at' => null,
                    'suspended_by' => null,
                ]);
                session()->flash('success', "{$count} cards activated successfully!");
                break;

            case 'renew':
                $cards->update([
                    'expiry_date' => now()->addYear(),
                    'status' => 'active',
                    'renewed_at' => now(),
                    'renewed_by' => auth()->id(),
                ]);
                session()->flash('success', "{$count} cards renewed successfully!");
                break;
        }

        $this->closeBulkActionModal();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'academicLevelFilter']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedAcademicLevelFilter()
    {
        $this->resetPage();
    }
}
