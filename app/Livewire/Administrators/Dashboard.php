<?php

namespace App\Livewire\Administrators;

use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    #[Url]
    public $activeTab = 'overview';

    protected $listeners = ['adminTabChanged' => 'setActiveTab'];

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function mount(): void
    {
        if (! $this->activeTab) {
            $this->activeTab = 'overview';
        }
    }

    public function render()
    {
        return view('livewire.administrators.dashboard');
    }
}
