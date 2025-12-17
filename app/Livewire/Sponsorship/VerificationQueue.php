<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipProgram;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VerificationQueue extends Component
{
    use WithPagination;

    public $activeTab = 'pending'; // pending, verified, rejected
    public $search = '';
    public $selectedType = '';

    // Expanded programs tracking
    public $expandedPrograms = [];

    // Modal state
    public $showRejectModal = false;
    public $rejectingProgramId = null;
    public $rejectionReason = '';

    protected $rules = [
        'rejectionReason' => 'required|string|min:10|max:1000',
    ];

    protected $queryString = [
        'activeTab' => ['except' => 'pending'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }


    public function toggleExpand($programId)
    {
        if (in_array($programId, $this->expandedPrograms)) {
            $this->expandedPrograms = array_diff($this->expandedPrograms, [$programId]);
        } else {
            $this->expandedPrograms[] = $programId;
        }
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
        $this->expandedPrograms = []; // Reset expanded cards when changing tabs
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function approve($programId)
    {
        $program = SponsorshipProgram::pendingVerification()->findOrFail($programId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->verifyProgram($program, Auth::user())) {
            session()->flash('message', 'Program approved successfully.');
        } else {
            session()->flash('error', 'Unable to approve program. You may not have permission.');
        }
    }

    public function openRejectModal($programId)
    {
        $this->rejectingProgramId = $programId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingProgramId = null;
        $this->rejectionReason = '';
    }

    public function reject()
    {
        $this->validate();

        $program = SponsorshipProgram::pendingVerification()->findOrFail($this->rejectingProgramId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->rejectProgram($program, Auth::user(), $this->rejectionReason)) {
            session()->flash('message', 'Program rejected and returned to draft.');
            $this->closeRejectModal();
        } else {
            session()->flash('error', 'Unable to reject program. You may not have permission.');
        }
    }

    public function render()
    {
        // Build query based on active tab
        $query = SponsorshipProgram::query()->with(['user', 'beneficiaries', 'school']);

        switch ($this->activeTab) {
            case 'verified':
                $query->where('status', SponsorshipProgram::STATUS_ACTIVE);
                break;
            case 'rejected':
                $query->where('status', SponsorshipProgram::STATUS_DRAFT)
                    ->whereNotNull('rejected_at');
                break;
            default: // pending
                $query->where('status', SponsorshipProgram::STATUS_PENDING_VERIFICATION);
        }

        // Apply filters
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($this->selectedType) {
            $query->ofType($this->selectedType);
        }

        // Sort order
        if ($this->activeTab === 'pending') {
            $query->orderBy('created_at', 'asc'); // Oldest first for pending
        } else {
            $query->orderBy('verified_at', 'desc'); // Newest first for verified/rejected
        }

        $programs = $query->paginate(10);

        // Stats
        $stats = [
            'pending_count' => SponsorshipProgram::pendingVerification()->count(),
            'approved_today' => SponsorshipProgram::where('status', SponsorshipProgram::STATUS_ACTIVE)
                ->whereDate('verified_at', today())->count(),
            'verified_total' => SponsorshipProgram::where('status', SponsorshipProgram::STATUS_ACTIVE)->count(),
            'rejected_total' => SponsorshipProgram::where('status', SponsorshipProgram::STATUS_DRAFT)
                ->whereNotNull('rejected_at')->count(),
        ];

        return view('livewire.sponsorship.verification-queue', [
            'programs' => $programs,
            'types' => SponsorshipProgram::getTypes(),
            'stats' => $stats,
        ]);
    }
}
