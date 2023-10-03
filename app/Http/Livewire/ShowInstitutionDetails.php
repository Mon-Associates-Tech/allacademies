<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;

class ShowInstitutionDetails extends Component
{
    public $team;
    public $details;
    public $institutionDetails;
    public $numChanges;
    public $canAdd;
    public $canMinus;

    public function plus()
    {
        if (count($this->details) < count($this->institutionDetails)) {
            array_push($this->details, [
                'name' => '',
                'type' => '',
                'institution' => '',
                'college' => '',
                'school' => '',
                'faculty' => '',
                'department' => '',
                'logo' => ''

            ]);
        }
    }

    public function minus()
    {
        if (count($this->details) > 1) {
            array_pop($this->details);
        }
    }

    public function mount($team, $institutionDetails)
    {
        $this->team = $team;
        $this->institutionDetails = $institutionDetails['meta'];
        $this->numChanges = count($this->institutionDetails);

        $this->details = old('sections') ?? [
            [
                'name' => '',
                'type' => '',
                'institution' => '',
                'college' => '',
                'school' => '',
                'faculty' => '',
                'department' => '',
                'logo' => ''
            ]
        ];
    }

    public function render()
    {
        if (count($this->details) > 1) {
            $this->canMinus = true;
        } else {
            $this->canMinus = false;
        }

        if (count($this->details) == count($this->institutionDetails)) {
            $this->canAdd = false;
        } else {
            $this->canAdd = true;
        }
        return view('livewire.show-institution-details');
    }
}
