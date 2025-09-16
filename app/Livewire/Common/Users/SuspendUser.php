<?php

namespace App\Livewire\Common\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuspendUser extends Component
{
    use AuthorizesRequests;

    public $userId;
    public $userName;
    public $reason = '';
    public $showConfirmation = false;

    protected $rules = [
        'reason' => 'nullable|string|max:500',
    ];

    public function mount($userId, $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function confirmSuspension()
    {
        $this->showConfirmation = true;
    }

    public function suspendUser()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);

            // Prevent suspending yourself
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot suspend your own account.');
                $this->dispatch('close-modal', name: 'suspend-user');
                return;
            }

            // Suspend the user by updating the status and reason
            $user->update([
                'status' => 'suspended',
                'suspension_reason' => $this->reason,
                'suspended_at' => now(),
                'suspended_by' => auth()->id(),
            ]);

            session()->flash('success', 'User ' . $this->userName . ' has been suspended successfully.');
            $this->dispatch('userSuspended');
            $this->dispatch('close-modal', name: 'suspend-user');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to suspend user. Please try again.');
        }
    }

    public function cancelSuspension()
    {
        $this->showConfirmation = false;
        $this->reset('reason');
    }

    public function render()
    {
        return view('livewire.common.users.suspend-user');
    }
}
