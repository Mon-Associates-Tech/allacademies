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

    public function submitForVerification($projectId)
    {
        $project = SponsorshipProject::where('user_id', Auth::id())
            ->where('status', SponsorshipProject::STATUS_DRAFT)
            ->findOrFail($projectId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->submitForVerification($project)) {
            session()->flash('message', 'Project submitted for verification successfully.');
        } else {
            session()->flash('error', 'Unable to submit for verification. Please ensure all required fields are filled.');
        }
    }

    public function deleteProject($projectId)
    {
        $project = SponsorshipProject::where('user_id', Auth::id())
            ->where('status', SponsorshipProject::STATUS_DRAFT)
            ->findOrFail($projectId);

        $project->beneficiaries()->delete();
        $project->delete();

        session()->flash('message', 'Project deleted successfully.');
    }

    public function cancelProject($projectId)
    {
        $project = SponsorshipProject::where('user_id', Auth::id())
            ->whereIn('status', [SponsorshipProject::STATUS_ACTIVE, SponsorshipProject::STATUS_PENDING_VERIFICATION])
            ->findOrFail($projectId);

        $project->update(['status' => SponsorshipProject::STATUS_CANCELLED]);

        session()->flash('message', 'Project cancelled successfully.');
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

        $projects = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total_projects' => SponsorshipProject::where('user_id', Auth::id())->count(),
            'active_projects' => SponsorshipProject::where('user_id', Auth::id())
                ->where('status', SponsorshipProject::STATUS_ACTIVE)->count(),
            'pending_verification' => SponsorshipProject::where('user_id', Auth::id())
                ->where('status', SponsorshipProject::STATUS_PENDING_VERIFICATION)->count(),
            'total_raised' => SponsorshipProject::where('user_id', Auth::id())->sum('amount_raised'),
            'total_contributions' => SponsorshipContribution::whereHas('sponsorshipProject', function ($q) {
                $q->where('user_id', Auth::id());
            })->where('status', SponsorshipContribution::STATUS_COMPLETED)->count(),
        ];

        return view('livewire.sponsorships.benefactor-dashboard', [
            'projects' => $projects,
            'stats' => $stats,
            'statuses' => SponsorshipProject::getStatuses(),
        ]);
    }
}
