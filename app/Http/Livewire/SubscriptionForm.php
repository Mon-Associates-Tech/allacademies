<?php

namespace App\Http\Livewire;

use App\Enums\SubscriptionPackage;
use App\Support\Pricer;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $package;
    public $duration;
    public $beneficiaries;
    public $groups;
    public $subjects;

    public function getAmountProperty()
    {
        $money = Pricer::calculate(
            SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            (int) is_null($this->duration) ? '3' : $this->duration,
            count($this->subjects) > 1 ? count($this->subjects) : 1,
            (int) is_null($this->beneficiaries) ? '1' : $this->beneficiaries
        );

        return (string) $money->getAmount();
    }

    public function mount($groups)
    {
        $this->groups = $groups;
        $this->subjects = [];
    }

    public function render()
    {
        return view('livewire.subscription-form');
    }
}
