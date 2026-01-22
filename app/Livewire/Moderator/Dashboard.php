<?php

namespace App\Livewire\Moderator;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.moderator.dashboard');
    }
}
