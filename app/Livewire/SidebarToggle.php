<?php

namespace App\Livewire;

use Livewire\Component;

class SidebarToggle extends Component
{
    public function toggle()
    {
        $this->dispatch('toggle-sidebar');
    }

    public function render()
    {
        return view('livewire.theme.sidebar-toggle');
    }
}
