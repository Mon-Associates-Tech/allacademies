<?php

namespace App\Livewire\Common\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetUserPassword extends Component
{
    use AuthorizesRequests;

    public $userId;

    public $userName;

    public $password = '';

    public $password_confirmation = '';

    protected $rules = [
        'password' => ['required', 'string', 'confirmed'],
    ];

    protected $messages = [
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount($userId, $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function resetPassword()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);

            // Authorize the action (optional, depending on your policies)
            // $this->authorize('update', $user);

            $user->update([
                'password' => Hash::make($this->password),
                'remember_token' => null, // Invalidate remember tokens
            ]);

            session()->flash('success', 'Password for '.$this->userName.' has been reset successfully!');
            $this->reset(['password', 'password_confirmation']);
            $this->dispatch('passwordReset');
            $this->dispatch('close-modal', name: 'reset-user-password');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reset password. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.common.users.reset-user-password');
    }
}
