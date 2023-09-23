<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InstitutionType extends Component
{
    public $team;
    public $institution_type;
   
    public function mount($team)
    {
        $this->team = $team;
        $this->institution_type = is_null($this->team->metaData) ? 'institution_only' : $this->team->metaData->meta['institution_type'] ?? 'institution_only';
    }

    public function render()
    {
        return view('livewire.institution-type');
    }
}
