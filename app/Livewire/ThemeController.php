<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeController extends Component
{
    public $darkMode = false;
    public $sidebarExpanded = false;

    public function mount()
    {
        // Initialize states from localStorage values
        $this->darkMode = session('dark-mode', false);
        $this->sidebarExpanded = session('sidebar-expanded', false);
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['dark-mode' => $this->darkMode]);
        $this->dispatch('dark-mode-toggled', ['darkMode' => $this->darkMode]);
    }

    public function toggleSidebar()
    {
        $this->sidebarExpanded = !$this->sidebarExpanded;
        session(['sidebar-expanded' => $this->sidebarExpanded]);
        $this->dispatch('sidebar-toggled', ['sidebarExpanded' => $this->sidebarExpanded]);
    }

    public function render()
    {
        return view('livewire.theme-controller');
    }
}
