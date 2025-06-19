<?php

namespace App\Livewire;

use Livewire\Component;

class AppComponent extends Component
{
    protected function startLoading(): void
    {
        $this->dispatch('start-loading');
    }

    protected function endLoading(): void
    {
        $this->dispatch('end-loading');
    }
}
