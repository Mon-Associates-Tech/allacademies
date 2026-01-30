<?php

namespace App\Livewire\Administrators;

use App\Models\Librarian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

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

    // Search and filters
    public $searchTerm = '';
    public $statusFilter = 'all';
    public $departmentFilter = '';
    public $positionFilter = '';

    // UI state
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
            $rules['email'] = 'required|email|unique:users,email,'.$librarian->user_id;
            $rules['employeeId'] = 'nullable|string|max:50|unique:librarians,employee_id,'.$this->editingLibrarianId;
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

    // Filter update methods
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->statusFilter = 'all';
        $this->departmentFilter = '';
        $this->positionFilter = '';
        $this->resetPage();
    }

    public function create()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before creating a librarian.');
            return;
        }

        $this->validate();

        DB::transaction(function () use ($schoolId) {
            // Create user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'librarian',
                'is_active' => $this->isActive,
                'email_verified_at' => now(),
            ]);

            // Assign librarian role
            $librarianRole = Role::where('name', 'librarian')->first();
            if ($librarianRole) {
                $user->roles()->attach($librarianRole);
            }

            // Create librarian record with school_id
            Librarian::create([
                'user_id' => $user->id,
                'school_id' => $schoolId,
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
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before updating a librarian.');
            return;
        }

        $librarian = Librarian::with('user')->findOrFail($this->editingLibrarianId);
        $this->validate();

        DB::transaction(function () use ($librarian, $schoolId) {
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
                'school_id' => $schoolId,
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
        $librarian->user->update(['is_active' => ! $librarian->user->is_active]);

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
        if (empty($this->selectedLibrarians)) {
            return;
        }

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
        if (empty($this->selectedLibrarians)) {
            return;
        }

        $librarians = Librarian::with('user')->whereIn('id', $this->selectedLibrarians)->get();
        foreach ($librarians as $librarian) {
            $librarian->user->update(['is_active' => false]);
        }

        $this->selectedLibrarians = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected librarians deactivated successfully!');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedLibrarians = $this->getLibrarians()->pluck('id')->toArray();
        } else {
            $this->selectedLibrarians = [];
        }
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
        $schoolId = getSchoolId();

        $query = Librarian::query();

        // Apply school context with explicit table name
        if ($schoolId) {
            $query->where('librarians.school_id', $schoolId);
        }

        return $query->whereHas('user', function($q) {
            if ($this->searchTerm) {
                $q->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            }
            if ($this->statusFilter === 'active') {
                $q->where('is_active', true);
            } elseif ($this->statusFilter === 'inactive') {
                $q->where('is_active', false);
            }
        })
            ->when($this->searchTerm, function($query) {
                $query->orWhere('librarians.position', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('librarians.department', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('librarians.employee_id', 'like', '%'.$this->searchTerm.'%');
            })
            ->when($this->departmentFilter, function($query) {
                $query->where('librarians.department', $this->departmentFilter);
            })
            ->when($this->positionFilter, function($query) {
                $query->where('librarians.position', $this->positionFilter);
            })
            ->with(['user', 'bookApprovals'])
            ->when($this->sortField === 'name', function ($query) {
                $query->join('users', 'librarians.user_id', '=', 'users.id')
                    ->orderBy('users.name', $this->sortDirection)
                    ->select('librarians.*');
            })
            ->when($this->sortField === 'email', function ($query) {
                $query->join('users', 'librarians.user_id', '=', 'users.id')
                    ->orderBy('users.email', $this->sortDirection)
                    ->select('librarians.*');
            })
            ->when(in_array($this->sortField, ['position', 'department', 'hire_date']), function($query) {
                $query->orderBy('librarians.' . $this->sortField, $this->sortDirection);
            });
    }
    public function render()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage librarians.');
        }

        $librarians = $this->getLibrarians()->paginate(10);

        // Get filter options scoped to current school
        $departments = $schoolId
            ? Librarian::where('school_id', $schoolId)
                ->whereNotNull('department')
                ->distinct()
                ->pluck('department')
                ->filter()
                ->sort()
            : collect();

        $positions = $schoolId
            ? Librarian::where('school_id', $schoolId)
                ->whereNotNull('position')
                ->distinct()
                ->pluck('position')
                ->filter()
                ->sort()
            : collect();

        // Stats scoped to current school
        $stats = [
            'total' => $schoolId ? Librarian::where('school_id', $schoolId)->count() : 0,
            'active' => $schoolId ? Librarian::where('school_id', $schoolId)
                ->whereHas('user', fn($q) => $q->where('is_active', true))->count() : 0,
            'inactive' => $schoolId ? Librarian::where('school_id', $schoolId)
                ->whereHas('user', fn($q) => $q->where('is_active', false))->count() : 0,
            'this_month' => $schoolId ? Librarian::where('school_id', $schoolId)
                ->whereMonth('created_at', now()->month)->count() : 0,
        ];

        // Count active filters
        $activeFiltersCount = collect([
            $this->statusFilter !== 'all' ? $this->statusFilter : null,
            $this->departmentFilter,
            $this->positionFilter
        ])->filter()->count();

        return view('livewire.administrators.librarian-management', [
            'librarians' => $librarians,
            'departments' => $departments,
            'positions' => $positions,
            'stats' => $stats,
            'activeFiltersCount' => $activeFiltersCount,
        ]);
    }
}
