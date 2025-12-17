<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorOffer;
use App\Models\SponsorshipProgram;
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
    public $selectedProgramId = '';
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
            return redirect()->route('login');
        }

        $this->selectedOfferId = $offerId;
        $this->selectedProgramId = '';
        $this->bidMessage = '';
        $this->showBidModal = true;
    }

    public function closeBidModal()
    {
        $this->showBidModal = false;
        $this->selectedOfferId = null;
        $this->selectedProgramId = '';
        $this->bidMessage = '';
    }

    public function submitBid()
    {
        $this->validate([
            'selectedProgramId' => 'required|exists:sponsorship_programs,id',
            'bidMessage' => 'nullable|string|max:1000',
        ]);

        $offer = SponsorOffer::open()->findOrFail($this->selectedOfferId);
        $program = SponsorshipProgram::where('user_id', Auth::id())
            ->active()
            ->findOrFail($this->selectedProgramId);

        $sponsorshipService = app(SponsorshipService::class);

        $bid = $sponsorshipService->submitBid($offer, $program, Auth::user(), $this->bidMessage);

        if ($bid) {
            session()->flash('message', 'Bid submitted successfully. The sponsor will review your application.');
            $this->closeBidModal();
        } else {
            session()->flash('error', 'Unable to submit bid. You may have already submitted a bid for this offer.');
        }
    }

    public function render()
    {
        $query = SponsorOffer::open()
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

        // Get user's active programs for bidding
        $userPrograms = collect();
        if (Auth::check()) {
            $userPrograms = SponsorshipProgram::where('user_id', Auth::id())
                ->active()
                ->get();
        }

        return view('livewire.sponsorship.public-sponsor-offer-list', [
            'offers' => $offers,
            'userPrograms' => $userPrograms,
        ])->layout('components.layouts.guest', ['pageName' => 'Sponsor Offers']);
    }
}
