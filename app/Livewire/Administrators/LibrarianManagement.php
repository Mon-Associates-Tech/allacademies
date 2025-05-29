<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Librarian;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class LibrarianManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $position;
    public $department;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingLibrarianId;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'position' => 'nullable|string',
        'department' => 'nullable|string',
    ];

    public function create()
    {
        $this->validate();

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Assign librarian role
        $librarianRole = Role::where('name', 'librarian')->first();
        $user->roles()->attach($librarianRole);

        // Create librarian record
        Librarian::create([
            'user_id' => $user->id,
            'position' => $this->position,
            'department' => $this->department,
        ]);

        $this->resetForm();
        session()->flash('message', 'Librarian created successfully!');
    }

    public function edit($librarianId)
    {
        $this->isEditing = true;
        $this->editingLibrarianId = $librarianId;

        $librarian = Librarian::with('user')->findOrFail($librarianId);
        $this->name = $librarian->user->name;
        $this->email = $librarian->user->email;
        $this->password = '';
        $this->position = $librarian->position;
        $this->department = $librarian->department;
    }

    public function update()
    {
        $librarian = Librarian::with('user')->findOrFail($this->editingLibrarianId);

        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$librarian->user_id,
            'position' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        // Update user
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $librarian->user->update($userData);

        // Update librarian
        $librarian->update([
            'position' => $this->position,
            'department' => $this->department,
        ]);

        $this->resetForm();
        session()->flash('message', 'Librarian updated successfully!');
    }

    public function delete($librarianId)
    {
        $librarian = Librarian::findOrFail($librarianId);
        $userId = $librarian->user_id;

        // Check if librarian has book approvals
        if ($librarian->bookApprovals()->count() > 0) {
            session()->flash('error', 'Cannot delete librarian who has book approvals. Please reassign the approvals first.');
            return;
        }

        // Delete librarian record
        $librarian->delete();

        // Delete user
        User::destroy($userId);

        session()->flash('message', 'Librarian deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->position = '';
        $this->department = '';
        $this->isEditing = false;
        $this->editingLibrarianId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $librarians = Librarian::whereHas('user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhere('position', 'like', '%'.$this->searchTerm.'%')
            ->orWhere('department', 'like', '%'.$this->searchTerm.'%')
            ->with(['user', 'bookApprovals'])
            ->paginate(10);

        return view('livewire.administrators.librarian-management', [
            'librarians' => $librarians
        ]);
    }
}
