<?php

namespace App\Livewire\Chats;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TokenUsageVertical extends Component
{
    public $subscription;
    public $showAlert = false;

    protected $listeners = ['tokenUsageUpdated' => 'loadSubscription'];

    public function mount(): void
    {
        $this->loadSubscription();
    }

    public function loadSubscription(): void
    {
        $this->subscription = Auth::user()->activeTokenSubscription;

        if ($this->subscription && $this->subscription->isNearingDepletion()) {
            $this->showAlert = true;
        }
    }

    public function dismissAlert(): void
    {
        $this->showAlert = false;
    }

    public function render()
    {
        return view('livewire.chats.token-usage-vertical');
    }
}
