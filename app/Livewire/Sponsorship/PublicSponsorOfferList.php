<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipOffer;
use App\Models\SponsorshipProject;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PublicSponsorOfferList extends Component
{
    use WithPagination;

    public $search = '';

    public $sortBy = 'latest';

    // Bid submission
    public $showBidModal = false;

    public $selectedOfferId = null;

    public $selectedProjectId = '';

    public $bidMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openBidModal($offerId)
    {
        if (!Auth::check()) {
            return redirect()->route('sign-in');
        }

        $this->selectedOfferId = $offerId;
        $this->selectedProjectId = '';
        $this->bidMessage = '';
        $this->showBidModal = true;
    }

    public function submitBid()
    {
        $this->validate([
            'selectedProjectId' => 'required|exists:sponsorship_projects,id',
            'bidMessage' => 'nullable|string|max:1000',
        ]);

        $offer = SponsorshipOffer::open()->findOrFail($this->selectedOfferId);
        $project = SponsorshipProject::where('user_id', Auth::id())
            ->active()
            ->findOrFail($this->selectedProjectId);

        $sponsorshipService = app(SponsorshipService::class);

        $bid = $sponsorshipService->submitBid($offer, $project, Auth::user(), $this->bidMessage);

        if ($bid) {
            session()->flash('message', 'Bid submitted successfully. The sponsor will review your application.');
            $this->closeBidModal();
        } else {
            session()->flash('error', 'Unable to submit bid. You may have already submitted a bid for this offer.');
        }
    }

    public function closeBidModal()
    {
        $this->showBidModal = false;
        $this->selectedOfferId = null;
        $this->selectedProjectId = '';
        $this->bidMessage = '';
    }

    public function render()
    {
        $query = SponsorshipOffer::open()
            ->with('user')
            ->withCount(['bids', 'acceptedBids']);

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'amount_high':
                $query->orderBy('amount_offered', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('amount_offered', 'asc');
                break;
            case 'expiring':
                $query->whereNotNull('expires_at')->orderBy('expires_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $offers = $query->paginate(12);

        // Get user's active projects for bidding
        $userProjects = collect();
        if (Auth::check()) {
            $userProjects = SponsorshipProject::where('user_id', Auth::id())
                ->active()
                ->get();
        }

        return view('livewire.sponsorships.public-sponsor-offer-list', [
            'offers' => $offers,
            'userProjects' => $userProjects,
        ])->layout('components.layouts.guest', ['pageName' => 'Sponsor Offers']);
    }
}
