<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorOffer;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SponsorOfferForm extends Component
{
    public $offerId = null;
    public $title = '';
    public $description = '';
    public $amount_offered = '';
    public $criteria = '';
    public $accepts_bids = true;
    public $expires_at = '';

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|max:5000',
        'amount_offered' => 'required|numeric|min:1',
        'criteria' => 'nullable|string|max:2000',
        'accepts_bids' => 'boolean',
        'expires_at' => 'nullable|date|after:today',
    ];

    public function mount($offerId = null)
    {
        if ($offerId) {
            $offer = SponsorOffer::where('user_id', Auth::id())
                ->findOrFail($offerId);

            $this->offerId = $offer->id;
            $this->title = $offer->title;
            $this->description = $offer->description;
            $this->amount_offered = $offer->amount_offered;
            $this->criteria = $offer->criteria;
            $this->accepts_bids = $offer->accepts_bids;
            $this->expires_at = $offer->expires_at?->format('Y-m-d');
        }
    }

    public function getIsEditingProperty()
    {
        return !is_null($this->offerId);
    }

    public function save()
    {
        $this->validate();

        $sponsorshipService = app(SponsorshipService::class);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'amount_offered' => $this->amount_offered,
            'criteria' => $this->criteria,
            'accepts_bids' => $this->accepts_bids,
            'expires_at' => $this->expires_at ?: null,
        ];

        if ($this->offerId) {
            $offer = SponsorOffer::where('user_id', Auth::id())
                ->findOrFail($this->offerId);

            $sponsorshipService->updateSponsorOffer($offer, $data);
            session()->flash('message', 'Offer updated successfully.');
        } else {
            $sponsorshipService->createSponsorOffer(Auth::user(), $data);
            session()->flash('message', 'Offer created successfully.');
        }

        return redirect()->route('sponsor.dashboard');
    }

    public function render()
    {
        return view('livewire.sponsorship.sponsor-offer-form', [
            'isEditing' => !is_null($this->offerId),
        ]);
    }
}
