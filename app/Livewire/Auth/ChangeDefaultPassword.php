<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangeDefaultPassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    ];

    public function changePassword()
    {
        $this->validate();

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->password),
            'has_default_password' => false,
        ]);

        session()->flash('message', 'Password changed successfully!');
        
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.change-default-password')->layout('layouts.guest');
    }
}
