<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Librarian;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LibrarianManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $passwordConfirmation;
    public $position;
    public $department;
    public $phone;
    public $address;
    public $dateOfBirth;
    public $employeeId;
    public $hireDate;
    public $qualifications;
    public $specializations;
    public $isActive = true;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingLibrarianId;
    public $showDeleteModal = false;
    public $deletingLibrarianId;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $statusFilter = 'all';
    public $departmentFilter = '';
    public $positionFilter = '';
    public $showBulkActions = false;
    public $selectedLibrarians = [];
    public $selectAll = false;
    public $showFormModal = false;

    protected $queryString = ['searchTerm', 'sortField', 'sortDirection', 'statusFilter', 'departmentFilter', 'positionFilter'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'dateOfBirth' => 'nullable|date|before:today',
            'employeeId' => 'nullable|string|max:50|unique:librarians,employee_id',
            'hireDate' => 'nullable|date|before_or_equal:today',
            'qualifications' => 'nullable|string|max:1000',
            'specializations' => 'nullable|string|max:500',
            'isActive' => 'boolean',
        ];

        if ($this->isEditing) {
            $librarian = Librarian::with('user')->findOrFail($this->editingLibrarianId);
            $rules['email'] = 'required|email|unique:users,email,' . $librarian->user_id;
            $rules['employeeId'] = 'nullable|string|max:50|unique:librarians,employee_id,' . $this->editingLibrarianId;
            $rules['password'] = 'nullable|min:8|confirmed';
            $rules['passwordConfirmation'] = 'nullable|same:password';
        } else {
            $rules['password'] = 'required|min:8|confirmed';
            $rules['passwordConfirmation'] = 'required|same:password';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'The name field is required.',
        'name.min' => 'The name must be at least 3 characters.',
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters.',
        'password.confirmed' => 'The password confirmation does not match.',
        'employeeId.unique' => 'This employee ID is already in use.',
        'dateOfBirth.before' => 'Date of birth must be before today.',
        'hireDate.before_or_equal' => 'Hire date cannot be in the future.',
    ];

    public function openFormModal(): void
    {
//        $this->openModal('form-modal', [
//            'title' => 'Create Newd Librarian',
//            'size' => 'xl'
//        ]);
    }

    public function create()
    {
        $this->validate();

        DB::transaction(function () {
            // Create user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'librarian',
                'is_active' => $this->isActive,
                'email_verified_at' => now(), // Auto-verify librarian accounts
            ]);

            // Assign librarian role
            $librarianRole = Role::where('name', 'librarian')->first();
            if ($librarianRole) {
                $user->roles()->attach($librarianRole);
            }

            // Create librarian record
            Librarian::create([
                'user_id' => $user->id,
                'position' => $this->position,
                'department' => $this->department,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->dateOfBirth,
                'employee_id' => $this->employeeId,
                'hire_date' => $this->hireDate ?: now(),
                'qualifications' => $this->qualifications,
                'specializations' => $this->specializations,
            ]);
        });

        $this->resetForm();
        session()->flash('message', 'Librarian created successfully!');
        $this->dispatch('librarian-created');
        $this->closeModal();
    }

//    public function openModal()
//    {
//        $this->showFormModal = true;
//        $this->resetForm();
//    }

    public function closeModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function edit($librarianId)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->editingLibrarianId = $librarianId;
        $this->showFormModal = true;

        $librarian = Librarian::with('user')->findOrFail($librarianId);
        $this->name = $librarian->user->name;
        $this->email = $librarian->user->email;
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->position = $librarian->position;
        $this->department = $librarian->department;
        $this->phone = $librarian->phone ?? '';
        $this->address = $librarian->address ?? '';
        $this->dateOfBirth = $librarian->date_of_birth ? $librarian->date_of_birth->format('Y-m-d') : '';
        $this->employeeId = $librarian->employee_id ?? '';
        $this->hireDate = $librarian->hire_date ? $librarian->hire_date->format('Y-m-d') : '';
        $this->qualifications = $librarian->qualifications ?? '';
        $this->specializations = $librarian->specializations ?? '';
        $this->isActive = $librarian->user->is_active;
        $this->js('window.Modal.open("librarian-form-modal")');
    }

    public function update()
    {
        $librarian = Librarian::with('user')->findOrFail($this->editingLibrarianId);
        $this->validate();

        DB::transaction(function () use ($librarian) {
            // Update user
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->isActive,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            $librarian->user->update($userData);

            // Update librarian
            $librarian->update([
                'position' => $this->position,
                'department' => $this->department,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->dateOfBirth,
                'employee_id' => $this->employeeId,
                'hire_date' => $this->hireDate,
                'qualifications' => $this->qualifications,
                'specializations' => $this->specializations,
            ]);
        });

        $this->resetForm();
        session()->flash('message', 'Librarian updated successfully!');
        $this->dispatch('librarian-updated');
        $this->closeModal();
    }

    public function confirmDelete($librarianId)
    {
        $this->deletingLibrarianId = $librarianId;
        $this->showDeleteModal = true;
        $this->js('window.Modal.open("librarian-delete-modal")');
    }

    public function delete()
    {
        $librarian = Librarian::findOrFail($this->deletingLibrarianId);
        $userId = $librarian->user_id;

        // Check if librarian has book approvals
        if ($librarian->bookApprovals()->count() > 0) {
            session()->flash('error', 'Cannot delete librarian who has book approvals. Please reassign the approvals first.');
            $this->showDeleteModal = false;
            return;
        }

        DB::transaction(function () use ($librarian, $userId) {
            $librarian->delete();
            User::destroy($userId);
        });

        $this->showDeleteModal = false;
        session()->flash('message', 'Librarian deleted successfully!');
        $this->dispatch('librarian-deleted');
        $this->js('window.Modal.close("librarian-delete-modal")');
    }

    public function toggleStatus($librarianId)
    {
        $librarian = Librarian::with('user')->findOrFail($librarianId);
        $librarian->user->update(['is_active' => !$librarian->user->is_active]);

        $status = $librarian->user->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Librarian {$status} successfully!");
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function bulkActivate()
    {
        if (empty($this->selectedLibrarians)) return;

        $librarians = Librarian::with('user')->whereIn('id', $this->selectedLibrarians)->get();
        foreach ($librarians as $librarian) {
            $librarian->user->update(['is_active' => true]);
        }

        $this->selectedLibrarians = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected librarians activated successfully!');
    }

    public function bulkDeactivate()
    {
        if (empty($this->selectedLibrarians)) return;

        $librarians = Librarian::with('user')->whereIn('id', $this->selectedLibrarians)->get();
        foreach ($librarians as $librarian) {
            $librarian->user->update(['is_active' => false]);
        }

        $this->selectedLibrarians = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected librarians deactivated successfully!');
    }

    public function exportLibrarians()
    {
        // This would typically export to CSV/Excel
        // For now, we'll just show a success message
        session()->flash('message', 'Export functionality would be implemented here.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedLibrarians = $this->getLibrarians()->pluck('id')->toArray();
        } else {
            $this->selectedLibrarians = [];
        }
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->statusFilter = 'all';
        $this->departmentFilter = '';
        $this->positionFilter = '';
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->position = '';
        $this->department = '';
        $this->phone = '';
        $this->address = '';
        $this->dateOfBirth = '';
        $this->employeeId = '';
        $this->hireDate = '';
        $this->qualifications = '';
        $this->specializations = '';
        $this->isActive = true;
        $this->isEditing = false;
        $this->editingLibrarianId = null;
        $this->resetValidation();
    }

    private function getLibrarians()
    {
        return Librarian::query()
            ->whereHas('user', function($query) {
                if ($this->searchTerm) {
                    $query->where('name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
                }
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($this->searchTerm, function($query) {
                $query->orWhere('position', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('department', 'like', '%'.$this->searchTerm.'%');
//                    ->orWhere('employee_id', 'like', '%'.$this->searchTerm.'%');
            })
            ->when($this->departmentFilter, function($query) {
                $query->where('department', $this->departmentFilter);
            })
            ->when($this->positionFilter, function($query) {
                $query->where('position', $this->positionFilter);
            })
            ->with(['user', 'bookApprovals'])
            ->when($this->sortField === 'name', function($query) {
                $query->join('users', 'librarians.user_id', '=', 'users.id')
                    ->orderBy('users.name', $this->sortDirection)
                    ->select('librarians.*');
            })
            ->when($this->sortField === 'email', function($query) {
                $query->join('users', 'librarians.user_id', '=', 'users.id')
                    ->orderBy('users.email', $this->sortDirection)
                    ->select('librarians.*');
            })
            ->when(in_array($this->sortField, ['position', 'department', 'hire_date']), function($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            });
    }

    public function render()
    {
        $librarians = $this->getLibrarians()->paginate(10);

        $departments = Librarian::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort();

        $positions = Librarian::whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->filter()
            ->sort();

        $stats = [
            'total' => Librarian::count(),
            'active' => Librarian::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'inactive' => Librarian::whereHas('user', fn($q) => $q->where('is_active', false))->count(),
            'this_month' => Librarian::whereMonth('created_at', now()->month)->count(),
        ];

        return view('livewire.administrators.librarian-management', [
            'librarians' => $librarians,
            'departments' => $departments,
            'positions' => $positions,
            'stats' => $stats,
        ]);
    }
}
