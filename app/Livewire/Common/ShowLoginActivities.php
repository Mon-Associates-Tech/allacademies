<?php

namespace App\Livewire\Common;

use Auth;
use Livewire\Component;

class ShowLoginActivities extends Component
{
    public $activities;

    public function mount()
    {
        $this->activities = Auth::user()
            ->loginActivities()
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.common.show-login-activities');
    }
}
