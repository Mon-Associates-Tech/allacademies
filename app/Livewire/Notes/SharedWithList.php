<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteShare;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\StudentGroup;
use App\Services\NoteShareService;
use Livewire\Component;
use Livewire\WithPagination;

class SharedWithList extends Component
{
    use WithPagination;

    public Note $note;
    public int $perPage = 10;

    // Filter properties
    public string $filterShareType = 'all';
    public string $filterRole = 'all';
    public ?int $filterAcademicGroup = null;
    public ?int $filterAcademicLevel = null;
    public ?int $filterStudentGroup = null;
    public string $searchTerm = '';
    public bool $filtersOpen = false;

    protected $listeners = [
        'note-shared' => '$refresh',
    ];

    protected $queryString = [
        'filterShareType' => ['except' => 'all'],
        'filterRole' => ['except' => 'all'],
        'searchTerm' => ['except' => ''],
    ];

    public function mount(Note $note): void
    {
        $this->note = $note;
    }

    public function updatedFilterShareType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAcademicGroup(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAcademicLevel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStudentGroup(): void
    {
        $this->resetPage();
    }

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterShareType = 'all';
        $this->filterRole = 'all';
        $this->filterAcademicGroup = null;
        $this->filterAcademicLevel = null;
        $this->filterStudentGroup = null;
        $this->searchTerm = '';
        $this->resetPage();
    }

    public function removeShare(int $shareId, string $shareType, $identifier): void
    {
        try {
            // Verify ownership
            if ($this->note->user_id !== auth()->id()) {
                $this->dispatch('error', message: 'Unauthorized action.');
                return;
            }

            $shareService = app(NoteShareService::class);
            $success = $shareService->unshare($this->note, $shareType, $identifier);

            if ($success) {
                $this->note->refresh();
                $this->resetPage();
                $this->dispatch('success', message: 'Share removed successfully!');
            } else {
                $this->dispatch('error', message: 'Failed to remove share.');
            }

        } catch (\Exception $e) {
            \Log::error('Failed to remove share', [
                'share_id' => $shareId,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('error', message: 'Failed to remove share. Please try again.');
        }
    }

    public function getTotalSharesProperty(): int
    {
        return $this->note->shares()->count();
    }

    public function getUniqueRecipientsCountProperty(): int
    {
        $schoolId = auth()->user()->school_id;
        $shareService = app(NoteShareService::class);
        $totalUsers = 0;

        // Get all shares for this note
        $shares = $this->note->shares()->get();

        foreach ($shares as $share) {
            if ($share->share_type === 'individual') {
                $totalUsers++;
            } else {
                // For group shares, resolve the actual user count
                $recipients = $shareService->resolveRecipients(
                    $share->share_type,
                    [$share->shareable_id ?? $schoolId],
                    $schoolId
                );
                $totalUsers += $recipients->count();
            }
        }

        return $totalUsers;
    }

    public function getAcademicGroupsProperty()
    {
        return AcademicGroup::forSchool(auth()->user()->school_id)
            ->orderBy('name')
            ->get();
    }

    public function getAcademicLevelsProperty()
    {
        return AcademicLevel::forSchool(auth()->user()->school_id)
            ->orderBy('name')
            ->get();
    }

    public function getStudentGroupsProperty()
    {
        return StudentGroup::where('school_id', auth()->user()->school_id)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;
        if ($this->filterShareType !== 'all') $count++;
        if ($this->filterRole !== 'all') $count++;
        if ($this->filterAcademicGroup) $count++;
        if ($this->filterAcademicLevel) $count++;
        if ($this->filterStudentGroup) $count++;
        if (!empty($this->searchTerm)) $count++;
        return $count;
    }

    public function render()
    {
        $query = $this->note->shares()
            ->with(['sharedWithUser', 'shareable']);

        // Filter by share type
        if ($this->filterShareType !== 'all') {
            $query->where('share_type', $this->filterShareType);
        }

        // Filter by role (only for individual shares)
        if ($this->filterRole !== 'all') {
            $query->where('share_type', 'individual')
                ->whereHas('sharedWithUser', function ($q) {
                    $q->where('role', $this->filterRole);
                });
        }

        // Filter by academic group
        if ($this->filterAcademicGroup) {
            $query->where(function ($q) {
                // Either it's a direct group share
                $q->where(function ($subQ) {
                    $subQ->where('share_type', 'academic_group')
                        ->where('shareable_id', $this->filterAcademicGroup);
                })
                    // Or it's an individual user in that group
                    ->orWhere(function ($subQ) {
                        $subQ->where('share_type', 'individual')
                            ->whereHas('sharedWithUser.student', function ($userQ) {
                                $userQ->where('academic_group_id', $this->filterAcademicGroup);
                            });
                    });
            });
        }

        // Filter by academic level
        if ($this->filterAcademicLevel) {
            $query->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('share_type', 'academic_level')
                        ->where('shareable_id', $this->filterAcademicLevel);
                })
                    ->orWhere(function ($subQ) {
                        $subQ->where('share_type', 'individual')
                            ->whereHas('sharedWithUser.student', function ($userQ) {
                                $userQ->where('academic_level_id', $this->filterAcademicLevel);
                            });
                    });
            });
        }

        // Filter by student group
        if ($this->filterStudentGroup) {
            $query->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('share_type', 'student_group')
                        ->where('shareable_id', $this->filterStudentGroup);
                })
                    ->orWhere(function ($subQ) {
                        $subQ->where('share_type', 'individual')
                            ->whereHas('sharedWithUser.student', function ($userQ) {
                                $userQ->where('student_group_id', $this->filterStudentGroup);
                            });
                    });
            });
        }

        // Search by name or email
        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('share_type', 'individual')
                    ->whereHas('sharedWithUser', function ($userQ) {
                        $userQ->where('name', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHasMorph('shareable', [AcademicGroup::class, AcademicLevel::class, StudentGroup::class], function ($morphQ) {
                        $morphQ->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $shares = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.notes.shared-with-list', [
            'shares' => $shares,
        ]);
    }
}
