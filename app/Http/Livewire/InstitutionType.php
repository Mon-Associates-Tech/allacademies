<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InstitutionType extends Component
{
    public $type;
    public $metaData;

    public function mount($team)
    {
        $this->metaData = is_null($team->metaData) ? null : $team->metaData->meta ?? null;

        $this->metaData = is_null($this->metaData) ? null : $this->metaData[count($this->metaData) - 1] ?? null;

        $this->type = is_null($this->metaData) ? 'institution_only' : $this->metaData['type'];
    }

    public function render()
    {
        return view('livewire.institution-type');
    }
}
