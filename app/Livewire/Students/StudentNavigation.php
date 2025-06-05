<?php

namespace App\Livewire\Students;

use Livewire\Component;

class StudentNavigation extends Component
{
    public $activeTab = 'overview';

    protected $listeners = ['tabChanged' => 'updateActiveTab'];

    public function mount($activeTab = 'overview')
    {
        $this->activeTab = $activeTab;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('studentTabChanged', $tab);
    }

    public function updateActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.students.student-navigation');
    }
}
