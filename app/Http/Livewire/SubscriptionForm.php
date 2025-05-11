<?php

namespace App\Http\Livewire;

use App\Enums\SubscriptionPackage;
use App\Support\Pricer;
use App\Support\SubscriptionAmount;
use App\Support\SubscriptionPackageAmount;
use Illuminate\Support\Arr;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $package;
    public $durationInMonths;
    public $beneficiaries;
    public $academicLevels;
    public $academicGroups;
    public $academicGroupId;
    public $academicSubjects;

    public function mount($academicGroups, $currentTeam)
    {
        $this->package = $currentTeam->is_personal ? SubscriptionPackage::INDIVIDUAL_FULL->value : SubscriptionPackage::INSTITUTION_FULL->value;
        $this->academicGroups = $academicGroups;
        $this->academicGroupId = $academicGroups[0]['id'];
        $this->academicLevels = $academicGroups[0]['academic_levels'];
        $this->academicSubjects = [];
    }

    public function updatedAcademicGroupId($value)
    {
        $this->academicSubjects = [];
        $this->academicLevels = Arr::first($this->academicGroups, function ($group) use ($value) {
            return $group['id'] == $value;
        })['academic_levels'];
    }

    public function getAmountProperty()
    {


         SubscriptionPackageAmount::subscriptionPrice(SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            (int) empty($this->durationInMonths) ? '3' : $this->durationInMonths,
            count($this->academicSubjects) > 1 ? count($this->academicSubjects) : 1,
            (int) empty($this->beneficiaries) ? '1' : $this->beneficiaries
        );



        $money = Pricer::calculate(
            SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            (int) empty($this->durationInMonths) ? '3' : $this->durationInMonths,
            count($this->academicSubjects) > 1 ? count($this->academicSubjects) : 1,
            (int) empty($this->beneficiaries) ? '1' : $this->beneficiaries
        );

        return (string) $money->getAmount();
    }

    public function getSubjectsCountProperty()
    {
        return count($this->academicSubjects);
    }

    public function render()
    {
        return view('livewire.subscription-form');
    }
}
