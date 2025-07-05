<?php

namespace App\Livewire\Students;

use Illuminate\Support\Facades\Request;
use Livewire\Component;

class StudentNavigation extends Component
{
    public $activeTab = 'overview';

    protected $listeners = ['studentTabChanged' => 'updateActiveTab'];

    public function mount($activeTab = 'overview')
    {
        $this->activeTab = Request::input('activeTab', $activeTab);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('studentTabChanged', $tab);
    }

    public function studentTabChanged($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.navigations.student-navigation');
    }
}
