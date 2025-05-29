<?php

namespace App\Livewire\Students;

use Livewire\Component;

class StudentNavigation extends Component
{
    public $activeTab = 'dashboard';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.students.student-navigation');
    }
}