<?php

namespace App\Livewire\Chats;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TokenUsageHorizontal extends Component
{
    public $subscription;

    public $showAlert = false;

    public $showText = true; // New prop

    protected $listeners = ['tokenUsageUpdated' => 'loadSubscription'];

    public function mount($showText = true)
    {
        $this->showText = $showText;
        $this->loadSubscription();
    }

    public function loadSubscription()
    {
        $this->subscription = Auth::user()->activeSubscriptionCycle;

        if ($this->subscription && $this->subscription->isNearingDepletion()) {
            $this->showAlert = true;
        }
    }

    public function dismissAlert()
    {
        $this->showAlert = false;
    }

    public function render()
    {
        return view('livewire.chats.token-usage-horizontal');
    }
}
