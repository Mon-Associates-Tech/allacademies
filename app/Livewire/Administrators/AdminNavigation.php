<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Illuminate\Support\Facades\Request;

class AdminNavigation extends Component
{
    public $activeTab = 'overview';

    protected $listeners = ['tabChanged' => 'updateActiveTab'];

    public function mount($activeTab = 'overview')
    {
        // Check if activeTab is set in the query parameters
        $this->activeTab = Request::input('activeTab', $activeTab);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('adminTabChanged', $tab);
    }

    public function updateActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.administrators.admin-navigation');
    }
}