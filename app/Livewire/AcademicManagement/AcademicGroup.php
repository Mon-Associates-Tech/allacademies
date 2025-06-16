<?php

namespace App\Livewire\AcademicManagement;

use App\Livewire\AppComponent;

class AcademicGroup extends AppComponent
{

    public function render()
    {
        return view('livewire.academic-groups.index', [
            'academicGroups' =>  \App\Models\AcademicGroup::query()->latest('id')->paginate()
        ]);
    }
}
