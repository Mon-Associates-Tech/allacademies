<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorOffer;
use App\Models\SponsorshipBid;
use App\Models\SponsorshipContribution;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SponsorDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'offers';
    public $search = '';
    public $statusFilter = '';

    // Bid management
    public $showBidModal = false;
    public $selectedOfferId = null;
    public $selectedBidId = null;
    public $rejectReason = '';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function closeOffer($offerId)
    {
        $offer = SponsorOffer::where('user_id', Auth::id())->findOrFail($offerId);
        $offer->close();
        session()->flash('message', 'Offer closed successfully.');
    }

    public function reopenOffer($offerId)
    {
        $offer = SponsorOffer::where('user_id', Auth::id())->findOrFail($offerId);
        $offer->reopen();
        session()->flash('message', 'Offer reopened successfully.');
    }

    public function deleteOffer($offerId)
    {
        $offer = SponsorOffer::where('user_id', Auth::id())
            ->where('status', SponsorOffer::STATUS_CLOSED)
            ->findOrFail($offerId);

        $offer->bids()->delete();
        $offer->delete();
        session()->flash('message', 'Offer deleted successfully.');
    }

    public function viewBids($offerId)
    {
        $this->selectedOfferId = $offerId;
        $this->showBidModal = true;
    }

    public function closeBidModal()
    {
        $this->showBidModal = false;
        $this->selectedOfferId = null;
        $this->selectedBidId = null;
        $this->rejectReason = '';
    }

    public function acceptBid($bidId)
    {
        $bid = SponsorshipBid::whereHas('sponsorOffer', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($bidId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->acceptBid($bid, Auth::user())) {
            session()->flash('message', 'Bid accepted successfully.');
        } else {
            session()->flash('error', 'Unable to accept bid.');
        }
    }

    public function rejectBid($bidId)
    {
        $bid = SponsorshipBid::whereHas('sponsorOffer', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($bidId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->rejectBid($bid, Auth::user(), $this->rejectReason ?: null)) {
            session()->flash('message', 'Bid rejected.');
            $this->rejectReason = '';
        } else {
            session()->flash('error', 'Unable to reject bid.');
        }
    }

    public function render()
    {
        $offers = collect();
        $bids = collect();
        $bidsForModal = collect();

        if ($this->activeTab === 'offers') {
            $query = SponsorOffer::where('user_id', Auth::id())
                ->withCount(['bids', 'pendingBids', 'acceptedBids']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            $offers = $query->orderBy('created_at', 'desc')->paginate(10);
        } elseif ($this->activeTab === 'bids') {
            $query = SponsorshipBid::whereHas('sponsorOffer', function ($q) {
                $q->where('user_id', Auth::id());
            })->with(['sponsorshipProgram.user', 'sponsorOffer', 'user']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->whereHas('sponsorshipProgram', function ($pq) {
                        $pq->where('name', 'like', "%{$this->search}%");
                    })->orWhereHas('sponsorOffer', function ($oq) {
                        $oq->where('title', 'like', "%{$this->search}%");
                    });
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            $bids = $query->orderBy('created_at', 'desc')->paginate(10);
        }

        // Load bids for modal
        if ($this->selectedOfferId) {
            $bidsForModal = SponsorshipBid::where('sponsor_offer_id', $this->selectedOfferId)
                ->with(['sponsorshipProgram.user', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Stats - passing individual variables instead of array
        $totalOffers = SponsorOffer::where('user_id', Auth::id())->count();
        $openOffers = SponsorOffer::where('user_id', Auth::id())
            ->where('status', SponsorOffer::STATUS_OPEN)->count();
        $pendingBids = SponsorshipBid::whereHas('sponsorOffer', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', SponsorshipBid::STATUS_PENDING)->count();
        $totalCommitted = SponsorOffer::where('user_id', Auth::id())->sum('amount_offered');

        return view('livewire.sponsorship.sponsor-dashboard', [
            'offers' => $offers,
            'bids' => $bids,
            'bidsForModal' => $bidsForModal,
            'totalOffers' => $totalOffers,
            'openOffers' => $openOffers,
            'pendingBids' => $pendingBids,
            'totalCommitted' => $totalCommitted,
            'offerStatuses' => SponsorOffer::getStatuses(),
        ]);
    }
}
