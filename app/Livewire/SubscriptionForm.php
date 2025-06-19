<?php

namespace App\Livewire;

use App\Enums\SubscriptionPackage;
use App\Models\AcademicGroup;
use App\Support\Pricer;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

    public function mount($academicGroups, $currentTeam): void
    {
        $this->package = $currentTeam->is_personal ? SubscriptionPackage::INDIVIDUAL_FULL->value : SubscriptionPackage::INSTITUTION_FULL->value;
        $this->academicGroups = $academicGroups;
        $this->academicGroupId = $academicGroups[0]['id'];
        $this->academicLevels = $academicGroups[0]['academic_levels'];
        $this->academicSubjects = [];
    }

    public function updatedAcademicGroupId($value): void
    {
        $this->academicSubjects = [];
        $this->academicLevels = Arr::first($this->academicGroups, static function ($group) use ($value) {
            return $group['id'] === $value;
        })['academic_levels'];

        // Emit event to update button state
        $this->dispatch('subjectsUpdated', count($this->academicSubjects));
    }

    public function updatedAcademicSubjects(): void
    {
        // Emit event to update the button state whenever subjects are updated
        $this->dispatch('subjectsUpdated', count($this->academicSubjects));
    }

    /**
     * @throws RoundingNecessaryException
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public function getAmountProperty(): string
    {
        $money = Pricer::calculate(
            SubscriptionPackage::tryFrom($this->package) ?? SubscriptionPackage::INDIVIDUAL_FULL,
            $this->getDurationInMonthsProperty(),
            max(count($this->academicSubjects), 1),
            (int) empty($this->beneficiaries) ? '1' : $this->beneficiaries,
            $this->getAcademicGroupTagProperty() ?? 'basic'
        );

        return (string) $money->getAmount();
    }

    public function getSubjectsCountProperty(): int
    {
        return count($this->academicSubjects);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.subscription-form');
    }

    public function getAcademicGroupTagProperty(){
        return AcademicGroup::find($this->academicGroupId)->tag;
    }

    /**
     * @return int
     */
    public function getDurationInMonthsProperty(): int
    {
        return (int) $this->durationInMonths;
    }

    /**
     * @throws RoundingNecessaryException
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public function updatedDurationInMonths(): void
    {
        $this->getAmountProperty();
    }
}
