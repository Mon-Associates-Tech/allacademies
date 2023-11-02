<?php

namespace App\Http\Livewire;

use App\Enums\SubscriptionPackage;
use App\Support\Pricer;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $team;
    public $teams;
    public $teamsOptions;
    public $package;
    public $duration;
    public $beneficiaries;
    public $academicGroups;
    public $academicSubjects;
    public $academicGroupsOptions;
    public $selectedAcademicGroup;

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

    public function mount($academicGroups, $teams)
    {
        $this->teams = $teams;
        $this->academicGroups = $academicGroups;
        $this->academicSubjects = [];
        $this->teamsOptions = $this->teams->pluck('name', 'id')->all();

        $columnsToExtract = ['id', 'name'];

        $this->academicGroupsOptions = array_reduce($this->academicGroups, function ($options, $item) use ($columnsToExtract) {
            $options[$item['id']] = $item['name'];
            return $options;
        }, []);
    }

    public function render()
    {
        $selectedTeam = $this->teams->find($this->team) ?? $this->teams->first();
        $this->package = $selectedTeam->is_personal ? 'individual:full' : 'institution:full';


        return view('livewire.subscription-form', [
            'package' => $this->package,
        ]);
    }
}
