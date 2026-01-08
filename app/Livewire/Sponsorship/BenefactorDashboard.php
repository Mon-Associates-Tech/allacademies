<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipContribution;
use App\Models\SponsorshipProject;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BenefactorDashboard extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function submitForVerification($programId)
    {
        $program = SponsorshipProject::where('user_id', Auth::id())
            ->where('status', SponsorshipProject::STATUS_DRAFT)
            ->findOrFail($programId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->submitForVerification($program)) {
            session()->flash('message', 'Program submitted for verification successfully.');
        } else {
            session()->flash('error', 'Unable to submit for verification. Please ensure all required fields are filled.');
        }
    }

    public function deleteProgram($programId)
    {
        $program = SponsorshipProject::where('user_id', Auth::id())
            ->where('status', SponsorshipProject::STATUS_DRAFT)
            ->findOrFail($programId);

        $program->beneficiaries()->delete();
        $program->delete();

        session()->flash('message', 'Program deleted successfully.');
    }

    public function cancelProgram($programId)
    {
        $program = SponsorshipProject::where('user_id', Auth::id())
            ->whereIn('status', [SponsorshipProject::STATUS_ACTIVE, SponsorshipProject::STATUS_PENDING_VERIFICATION])
            ->findOrFail($programId);

        $program->update(['status' => SponsorshipProject::STATUS_CANCELLED]);

        session()->flash('message', 'Program cancelled successfully.');
    }

    public function render()
    {
        $query = SponsorshipProject::where('user_id', Auth::id())
            ->with(['beneficiaries', 'contributions']);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $programs = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total_programs' => SponsorshipProject::where('user_id', Auth::id())->count(),
            'active_programs' => SponsorshipProject::where('user_id', Auth::id())
                ->where('status', SponsorshipProject::STATUS_ACTIVE)->count(),
            'pending_verification' => SponsorshipProject::where('user_id', Auth::id())
                ->where('status', SponsorshipProject::STATUS_PENDING_VERIFICATION)->count(),
            'total_raised' => SponsorshipProject::where('user_id', Auth::id())->sum('amount_raised'),
            'total_contributions' => SponsorshipContribution::whereHas('sponsorshipProject', function ($q) {
                $q->where('user_id', Auth::id());
            })->where('status', SponsorshipContribution::STATUS_COMPLETED)->count(),
        ];

        return view('livewire.sponsorship.benefactor-dashboard', [
            'programs' => $programs,
            'stats' => $stats,
            'statuses' => SponsorshipProject::getStatuses(),
        ]);
    }
}
