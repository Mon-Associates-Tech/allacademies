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
    public $academicGroups;
    public $academicSubjects;

    protected $rules = [
        'academicGroups.*.is_open' => ['boolean'],
        'academicGroups.*.academicLevels.*.is_open' => ['boolean'],
    ];

    public function getAmountProperty()
    {
        $money = Pricer::calculate(
            SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            (int) empty($this->duration) ? '3' : $this->duration,
            count($this->academicSubjects) > 1 ? count($this->academicSubjects) : 1,
            (int) empty($this->beneficiaries) ? '1' : $this->beneficiaries
        );

        return (string) $money->getAmount();
    }

    public function mount($academicGroups)
    {
        $this->academicGroups = $academicGroups;
        $this->academicSubjects = [];
    }

    public function render()
    {
        return view('livewire.subscription-form');
    }
}
