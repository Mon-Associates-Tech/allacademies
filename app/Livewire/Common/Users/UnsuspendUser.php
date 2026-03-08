<?php

namespace App\Livewire\Common\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class UnsuspendUser extends Component
{
    use AuthorizesRequests;

    public $userId;

    public $userName;

    public $showConfirmation = false;

    public function mount($userId, $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function confirmUnsuspension()
    {
        $this->showConfirmation = true;
    }

    public function unsuspendUser()
    {
        try {
            $user = User::findOrFail($this->userId);

            // Use the model's unsuspend method
            $user->unsuspend();

            session()->flash('success', 'User '.$this->userName.' has been unsuspended successfully.');
            $this->dispatch('userUnsuspended');
            $this->dispatch('close-modal', name: 'unsuspend-user');

            // Refresh the page to show updated status
            return redirect()->route('users.show', $user);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to unsuspend user. Please try again.');
        }
    }

    public function cancelUnsuspension()
    {
        $this->showConfirmation = false;
    }

    public function render()
    {
        return view('livewire.common.users.unsuspend-user');
    }
}
