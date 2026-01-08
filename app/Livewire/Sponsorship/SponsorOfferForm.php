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

    protected $listeners = ['markdown-updated' => 'handleMarkdownUpdate'];

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|max:5000',
        'amount_offered' => 'required|numeric|min:1',
        'criteria' => 'nullable|string|max:2000',
        'accepts_bids' => 'boolean',
        'expires_at' => 'nullable|date|after:today',
    ];

    public function mount($offer = null)
    {

        if ($offer) {
            $offerModel = SponsorOffer::where('user_id', Auth::id())
                ->findOrFail($offer);

            $this->offerId = $offerModel->id;
            $this->title = $offerModel->title;
            $this->description = $offerModel->description;
            $this->amount_offered = $offerModel->amount_offered;
            $this->criteria = $offerModel->criteria;
            $this->accepts_bids = $offerModel->accepts_bids;
            $this->expires_at = $offerModel->expires_at?->format('Y-m-d');
        }
    }

    public function handleMarkdownUpdate($data)
    {
        if (isset($data['name']) && isset($data['value'])) {
            $this->{$data['name']} = $data['value'];
        }
    }

    public function getIsEditingProperty()
    {
        return ! is_null($this->offerId);
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

        return redirect()->route('sponsorships.index');
    }

    public function render()
    {
        return view('livewire.sponsorship.sponsor-offer-form', [
            'isEditing' => ! is_null($this->offerId),
        ]);
    }
}
