<?php

namespace App\Livewire\Chats;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TokenUsageCircular extends Component
{
    public $subscription;
    public $showAlert = false;

    protected $listeners = ['tokenUsageUpdated' => 'loadSubscription'];

    public function mount()
    {
        $this->loadSubscription();
    }

    public function loadSubscription()
    {
        $this->subscription = Auth::user()->activeTokenSubscription;
        
        if ($this->subscription && $this->subscription->isNearingDepletion()) {
            $this->showAlert = true;
        }
    }

    public function dismissAlert()
    {
        $this->showAlert = false;
    }

    public function getProgressColor()
    {
        if (!$this->subscription) {
            return '#9CA3AF';
        }

        $percentage = $this->subscription->usage_percentage;

        if ($percentage <= 25) return '#10b981';
        if ($percentage <= 50) return '#84cc16';
        if ($percentage <= 75) return '#eab308';
        if ($percentage <= 90) return '#f97316';
        return '#ef4444';
    }

    public function render()
    {
        return view('livewire.chats.token-usage-circular');
    }
}
