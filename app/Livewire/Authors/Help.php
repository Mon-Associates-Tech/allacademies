<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Contracts\View\View;

class Help extends AppComponent
{


    public function render(): View
    {
        return view('livewire.authors.help');
    }
}
