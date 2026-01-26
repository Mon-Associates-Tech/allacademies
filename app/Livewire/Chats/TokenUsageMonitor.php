<?php

namespace App\Livewire\Chats;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\UserTokenSubscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TokenUsageMonitor extends Component
{
    public $subscription;

    public $packages;

    public $selectedPackage;

    public $showAlert = false;

    protected $listeners = ['tokenUsageUpdated' => '$refresh'];

    public function mount(): void
    {
        $this->loadSubscription();
        $this->packages = OpenAiTokenPackage::active()->get();
    }

    public function loadSubscription(): void
    {
        $this->subscription = Auth::user()->activeSubscriptionCycle;

        if ($this->subscription && $this->subscription->isNearingDepletion()) {
            $this->showAlert = true;
        }
    }

    public function selectPackage($packageId): void
    {
        $this->selectedPackage = OpenAiTokenPackage::find($packageId);
    }

    public function subscribe(): void
    {
        if (! $this->selectedPackage) {
            session()->flash('error', 'Please select a package first.');

            return;
        }

        // Create a new subscription
        $subscription = UserTokenSubscription::create([
            'user_id' => Auth::id(),
            'package_id' => $this->selectedPackage->id,
            'tokens_purchased' => $this->selectedPackage->token_limit,
            'tokens_used' => 0,
            'tokens_remaining' => $this->selectedPackage->token_limit,
            'purchased_at' => now(),
            'status' => 'active',
        ]);

        session()->flash('success', 'Successfully subscribed to '.$this->selectedPackage->name.' package!');

        $this->selectedPackage = null;
        $this->loadSubscription();
        $this->dispatch('subscription-updated');
    }

    public function dismissAlert(): void
    {
        $this->showAlert = false;
    }

    public function getProgressColor(): string
    {
        if (! $this->subscription) {
            return 'gray';
        }

        $percentage = $this->subscription->usage_percentage;

        // Green to Red gradient based on usage
        if ($percentage <= 25) {
            return 'green';
        } elseif ($percentage <= 50) {
            return 'lime';
        } elseif ($percentage <= 75) {
            return 'yellow';
        } elseif ($percentage <= 90) {
            return 'orange';
        } else {
            return 'red';
        }
    }

    public function render()
    {
        return view('livewire.chats.token-usage-monitor');
    }
}
