<?php

namespace App\Livewire;

use App\Enums\SubscriptionPackage;
use App\Models\AcademicGroup;
use App\Support\Pricer;
use Illuminate\Support\Arr;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $package;
    public $durationInMonths = 3;
    public $beneficiaries;
    public $academicLevels;
    public $academicGroups;
    public $academicGroupId;
    public $academicSubjects;

    public $academicGroupTag;

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
        $money = Pricer::calculate(
            SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            $this->getDurationInMonthsProperty(),
            count($this->academicSubjects) > 1 ? count($this->academicSubjects) : 1,
            (int) empty($this->beneficiaries) ? '1' : $this->beneficiaries,
            $this->getAcademicGroupTagProperty() ?? 'basic'
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

    public function getAcademicGroupTagProperty(){
        return AcademicGroup::find($this->academicGroupId)->tag;
    }

    /**
     * @return mixed
     */
    public function getDurationInMonthsProperty()
    {
        return (int) $this->durationInMonths;
    }

    public function updatedDurationInMonths(): void
    {
        $this->getAmountProperty();
    }
}
