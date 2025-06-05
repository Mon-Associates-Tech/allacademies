<?php

namespace App\Livewire\Students;

use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    #[Url]
    public $activeTab = 'dashboard';

    protected $listeners = ['studentTabChanged' => 'setActiveTab'];

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function mount(): void
    {
        if(!$this->activeTab){
            $this->activeTab = 'dashboard';
        }
    }

    public function render()
    {
        return view('livewire.students.dashboard', [

        ]);
    }
}
