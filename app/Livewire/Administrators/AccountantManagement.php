<?php

namespace App\Livewire\Administrators;

use App\Models\Accountant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class AccountantManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $phone;
    public $address;
    public $dateOfBirth;
    public $employeeId;
    public $hireDate;
    public $isActive = true;

    public $searchTerm = '';
    public $statusFilter = 'all';
    public $isEditing = false;
    public $editingAccountantId;
    public $showDeleteModal = false;
    public $deletingAccountantId;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showFormModal = false;

    const DEFAULT_PASSWORD = 'pass1234';

    protected $queryString = ['searchTerm', 'sortField', 'sortDirection', 'statusFilter'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'dateOfBirth' => 'nullable|date|before:today',
            'employeeId' => 'nullable|string|max:50|unique:accountants,employee_id',
            'hireDate' => 'nullable|date|before_or_equal:today',
            'isActive' => 'boolean',
        ];

        if ($this->isEditing) {
            $accountant = Accountant::with('user')->findOrFail($this->editingAccountantId);
            $rules['email'] = 'required|email|unique:users,email,'.$accountant->user_id;
            $rules['employeeId'] = 'nullable|string|max:50|unique:accountants,employee_id,'.$this->editingAccountantId;
        }

        return $rules;
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function create()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before creating an accountant.');
            return;
        }

        $this->validate();

        DB::transaction(function () use ($schoolId) {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => 'accountant',
                'is_active' => $this->isActive,
                'has_default_password' => true,
                'email_verified_at' => now(),
                'school_id' => $schoolId,
            ]);

            $accountantRole = Role::where('name', 'accountant')->first();
            if ($accountantRole) {
                $user->roles()->attach($accountantRole);
            }

            Accountant::create([
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->dateOfBirth,
                'employee_id' => $this->employeeId,
                'hire_date' => $this->hireDate ?: now(),
            ]);
        });

        $this->resetForm();
        session()->flash('message', 'Accountant created successfully! Default password: ' . self::DEFAULT_PASSWORD);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function edit($accountantId)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->editingAccountantId = $accountantId;
        $this->showFormModal = true;

        $accountant = Accountant::with('user')->findOrFail($accountantId);
        $this->name = $accountant->user->name;
        $this->email = $accountant->user->email;
        $this->phone = $accountant->phone ?? '';
        $this->address = $accountant->address ?? '';
        $this->dateOfBirth = $accountant->date_of_birth ? $accountant->date_of_birth->format('Y-m-d') : '';
        $this->employeeId = $accountant->employee_id ?? '';
        $this->hireDate = $accountant->hire_date ? $accountant->hire_date->format('Y-m-d') : '';
        $this->isActive = $accountant->user->is_active;
    }

    public function update()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before updating an accountant.');
            return;
        }

        $accountant = Accountant::with('user')->findOrFail($this->editingAccountantId);
        $this->validate();

        DB::transaction(function () use ($accountant, $schoolId) {
            $accountant->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->isActive,
            ]);

            $accountant->update([
                'school_id' => $schoolId,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->dateOfBirth,
                'employee_id' => $this->employeeId,
                'hire_date' => $this->hireDate,
            ]);
        });

        $this->resetForm();
        session()->flash('message', 'Accountant updated successfully!');
        $this->closeModal();
    }

    public function confirmDelete($accountantId)
    {
        $this->deletingAccountantId = $accountantId;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $accountant = Accountant::findOrFail($this->deletingAccountantId);
        $userId = $accountant->user_id;

        DB::transaction(function () use ($accountant, $userId) {
            $accountant->delete();
            User::destroy($userId);
        });

        $this->showDeleteModal = false;
        session()->flash('message', 'Accountant deleted successfully!');
    }

    public function toggleStatus($accountantId)
    {
        $accountant = Accountant::with('user')->findOrFail($accountantId);
        $accountant->user->update(['is_active' => ! $accountant->user->is_active]);

        $status = $accountant->user->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Accountant {$status} successfully!");
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

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->dateOfBirth = '';
        $this->employeeId = '';
        $this->hireDate = '';
        $this->isActive = true;
        $this->isEditing = false;
        $this->editingAccountantId = null;
        $this->resetValidation();
    }

    private function getAccountants()
    {
        $schoolId = getSchoolId();

        $query = Accountant::query();

        if ($schoolId) {
            $query->where('accountants.school_id', $schoolId);
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
                $query->orWhere('accountants.employee_id', 'like', '%'.$this->searchTerm.'%');
            })
            ->with(['user'])
            ->when($this->sortField === 'name', function ($query) {
                $query->join('users', 'accountants.user_id', '=', 'users.id')
                    ->orderBy('users.name', $this->sortDirection)
                    ->select('accountants.*');
            })
            ->when($this->sortField === 'email', function ($query) {
                $query->join('users', 'accountants.user_id', '=', 'users.id')
                    ->orderBy('users.email', $this->sortDirection)
                    ->select('accountants.*');
            })
            ->when($this->sortField === 'hire_date', function($query) {
                $query->orderBy('accountants.hire_date', $this->sortDirection);
            });
    }

    public function render()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage accountants.');
        }

        $accountants = $this->getAccountants()->paginate(10);

        $stats = [
            'total' => $schoolId ? Accountant::where('school_id', $schoolId)->count() : 0,
            'active' => $schoolId ? Accountant::where('school_id', $schoolId)
                ->whereHas('user', fn($q) => $q->where('is_active', true))->count() : 0,
            'inactive' => $schoolId ? Accountant::where('school_id', $schoolId)
                ->whereHas('user', fn($q) => $q->where('is_active', false))->count() : 0,
        ];

        return view('livewire.administrators.accountant-management', [
            'accountants' => $accountants,
            'stats' => $stats,
        ]);
    }
}
