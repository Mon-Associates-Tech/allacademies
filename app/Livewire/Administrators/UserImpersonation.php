<?php

namespace App\Livewire\Administrators;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserImpersonation extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRole = '';
    public $showModal = false;

    protected $queryString = ['search', 'selectedRole'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedRole()
    {
        $this->resetPage();
    }

    public function impersonateUser($userId)
    {
        impersonateUser($userId);
    }

    public function stopImpersonation()
    {
        if (session()->has('impersonator')) {
            return redirect()->route('impersonate.leave');
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedRole, function ($query) {
                $query->where('role', $this->selectedRole);
            })
            ->whereNotNull('email_verified_at')
            ->where('id', '!=', Auth::id()) // Don't show current user
            ->orderBy('name')
            ->paginate(10);

        $roles = ['student', 'teacher', 'librarian', 'author', 'parent', 'moderator', 'subscriber'];

        return view('livewire.administrators.user-impersonation', compact('users', 'roles'));
    }
}
