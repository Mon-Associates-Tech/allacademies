<?php

namespace App\Http\Livewire;

use App\Enums\SubscriptionPackage;
use App\Support\Pricer;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $team;
    public $teams;
    public $package;
    public $duration;
    public $teamsOptions;
    public $beneficiaries;
    public $academicLevels;
    public $academicGroups;
    public $academicSubjects;
    public $countSelectedSubjects;
    public $selectedAcademicGroupId;

    protected $rules = [
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

    public function updated()
    {
        $this->compile();
    }

    public function mount($academicGroups, $teams)
    {
        $this->teams = $teams;
        $this->academicGroups = $academicGroups;
        $this->academicSubjects = [];
        $this->teamsOptions = $this->teams->pluck('name', 'id')->all();
        $this->selectedAcademicGroupId = $this->academicGroups[0]['id'];

        $this->compile();
    }

    private function compile()
    {
        $selectedTeam = $this->teams->find($this->team) ?? $this->teams->first();
        $this->package = $selectedTeam->is_personal ? 'individual:full' : 'institution:full';

        $selectedGroup = collect($this->academicGroups)->firstWhere('id', $this->selectedAcademicGroupId);
        $this->academicLevels = $selectedGroup['academic_levels'];

        $this->countSelectedSubjects = count($this->academicSubjects);
    }

    public function updatedSelectedAcademicGroupId()
    {
        $this->academicSubjects = [];
        $this->countSelectedSubjects = 0;
    }

    public function render()
    {
        return view('livewire.subscription-form');
    }
}
