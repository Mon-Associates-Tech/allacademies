<?php

namespace App\Livewire\Administrators;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $name;

    public $email;

    public $password;

    public $roleIds = [];

    public $userSearchTerm = '';

    public $isEditing = false;

    public $editingUserId;

    public $roles;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'roleIds' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function create()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->roles()->sync($this->roleIds);

        // Log activity
        $user->logActivity('create', 'User Created', 'user', [
            'user_name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roleIds,
            'created_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        $this->resetForm();
        session()->flash('message', 'User created successfully!');
    }

    public function edit($userId)
    {
        $this->isEditing = true;
        $this->editingUserId = $userId;

        $user = User::findOrFail($userId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->roleIds = $user->roles->pluck('id')->toArray();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$this->editingUserId,
            'roleIds' => 'required|array|min:1',
        ]);

        $user = User::findOrFail($this->editingUserId);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $user->update($userData);
        $user->roles()->sync($this->roleIds);

        $this->resetForm();
        session()->flash('message', 'User updated successfully!');
    }

    public function delete($userId)
    {
        $user = User::findOrFail($userId);
        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        // Log activity
        User::logActivityForModel('delete', 'User Deleted', 'user', [
            'user_name' => $userName,
            'user_email' => $userEmail,
            'deleted_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        session()->flash('message', 'User deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->roleIds = [];
        $this->isEditing = false;
        $this->editingUserId = null;
        $this->resetValidation();
    }

    #[Computed]
    public function getUsersProperty()
    {

        return User::when($this->userSearchTerm, function ($query) {
            return $query->where('name', 'like', '%'.$this->userSearchTerm.'%')
                ->orWhere('email', 'like', '%'.$this->userSearchTerm.'%');
        })
            ->with('roles')
            ->paginate(10);
    }

    public function updatedUserSearchTerm()
    {
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    public function render()
    {

        return view('livewire.administrators.user-management');
    }
}
