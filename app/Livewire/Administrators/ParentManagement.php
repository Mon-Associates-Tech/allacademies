<?php

namespace App\Livewire\Administrators;

use App\Livewire\AppComponent;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use App\Models\AcademicFeeStructure;
use Illuminate\Support\Facades\Auth;

class ParentManagement extends AppComponent ///
{
    use WithPagination;

    public $search = '';
    public $selectedParent = null;
    public $showDetailModal = false;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showAssociateModal = false;
    public $viewMode = 'list';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;
    public $statusFilter = 'all';

    // Form properties
    public $form = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
        'password_confirmation' => '',
        'relationship' => 'parent',
        'is_active' => true,
    ];

    // Student association properties
    public $selectedStudents = [];
    public $availableStudents = [];
    public $studentSearch = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'viewMode' => ['except' => 'grid'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount()
    {
       // $this->authorize('own');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function showParentDetails($parentId)
    {
        $this->selectedParent = StudentParent::with(['user', 'students.user', 'students.academicLevel', 'students.studentGroup'])
            ->findOrFail($parentId);
        $this->showDetailModal = true;
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function showEditForm($parentId)
    {
        $parent = StudentParent::with('user')->findOrFail($parentId);
        $this->selectedParent = $parent;

        $this->form = [
            'name' => $parent->user->name,
            'email' => $parent->user->email,
            'phone' => $parent->user->phone ?? '',
            'password' => '',
            'password_confirmation' => '',
            'relationship' => 'parent', // Default since it's now in pivot table
            'is_active' => $parent->user->is_active,
        ];

        $this->showEditModal = true;
    }

    public function loadAvailableStudents()
    {
        $query = Student::with(['user', 'academicLevel', 'studentGroup']);

        if ($this->studentSearch) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->studentSearch . '%');
            });
        }

        $this->availableStudents = $query->get();
    }

    public function showAssociateStudents($parentId)
    {
        $this->selectedParent = StudentParent::with(['students'])->findOrFail($parentId);
        $this->selectedStudents = $this->selectedParent->students->pluck('id')->toArray();
        $this->studentSearch = '';
        $this->loadAvailableStudents();
        $this->showAssociateModal = true;
    }

    public function updatedStudentSearch()
    {
        $this->loadAvailableStudents();
    }

    public function createParent()
    {
        $this->validateForm();

        try {
            // Create user
            $user = User::create([
                'name' => $this->form['name'],
                'email' => $this->form['email'],
                'phone' => $this->form['phone'],
                'password' => Hash::make($this->form['password']),
                'role' => 'parent',
                'is_active' => $this->form['is_active'],
                'email_verified_at' => now(),
            ]);

            // Create parent record - FIXED: Remove relationship field
            StudentParent::create([
                'user_id' => $user->id,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Parent created successfully!'
            ]);

            $this->closeCreateModal();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to create parent: ' . $e->getMessage()
            ]);
        }
    }

    public function updateParent()
    {
        $this->validateForm(true);

        try {
            $user = $this->selectedParent->user;

            // Update user
            $userData = [
                'name' => $this->form['name'],
                'email' => $this->form['email'],
                'phone' => $this->form['phone'],
                'is_active' => $this->form['is_active'],
            ];

            if (!empty($this->form['password'])) {
                $userData['password'] = Hash::make($this->form['password']);
            }

            $user->update($userData);

            // No need to update parent record anymore since relationship is in pivot table

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Parent updated successfully!'
            ]);

            $this->closeEditModal();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update parent: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStudentAssociations()
    {
        try {
            // Prepare sync data with relationship information
            $syncData = [];
            foreach ($this->selectedStudents as $studentId) {
                $syncData[$studentId] = [
                    'relationship' => $this->form['relationship'] ?? 'parent'
                ];
            }

            // Sync the students with the parent, including pivot data
            $this->selectedParent->students()->sync($syncData);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Student associations updated successfully!'
            ]);

            $this->closeAssociateModal();

        } catch (\Exception $e) {
        \Log::error('Failed to update student associations', [
            'error' => $e->getMessage(),
            'parent_id' => $this->selectedParent?->id,
            'selected_students' => $this->selectedStudents,
            'form_relationship' => $this->form['relationship'] ?? 'not set',
            'trace' => $e->getTraceAsString()
        ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update associations: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteParent($parentId)
    {
        try {
            $parent = StudentParent::with('user')->findOrFail($parentId);

            // Detach all students first
            $parent->students()->detach();

            // Delete parent and user records
            $user = $parent->user;
            $parent->delete();
            $user->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Parent deleted successfully!'
            ]);

            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete parent: ' . $e->getMessage()
            ]);
        }
    }

    protected function validateForm($isUpdate = false)
    {
        $rules = [
            'form.name' => 'required|string|max:255',
            'form.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($isUpdate ? $this->selectedParent->user->id : null)
            ],
            'form.phone' => 'nullable|string|max:20',
            'form.relationship' => 'required|string|in:parent,father,mother,guardian,other',
            'form.is_active' => 'boolean',
        ];

        if (!$isUpdate || !empty($this->form['password'])) {
            $rules['form.password'] = 'required|string|min:8|confirmed';
            $rules['form.password_confirmation'] = 'required';
        }

        $this->validate($rules, [
            'form.name.required' => 'Name is required.',
            'form.email.required' => 'Email is required.',
            'form.email.email' => 'Please enter a valid email address.',
            'form.email.unique' => 'This email is already registered.',
            'form.password.required' => 'Password is required.',
            'form.password.min' => 'Password must be at least 8 characters.',
            'form.password.confirmed' => 'Password confirmation does not match.',
            'form.relationship.required' => 'Relationship is required.',
        ]);
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'password_confirmation' => '',
            'relationship' => 'parent',
            'is_active' => true,
        ];
        $this->resetValidation();
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedParent = null;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedParent = null;
        $this->resetForm();
    }

    public function closeAssociateModal()
    {
        $this->showAssociateModal = false;
        $this->selectedParent = null;
        $this->selectedStudents = [];
        $this->availableStudents = [];
        $this->studentSearch = '';
    }

    public function getParentsProperty()
    {
        //dd(StudentParent::with(['user', 'students'])->get());
        return StudentParent::withoutGlobalScopes()->with(['user', 'students'])->get();

            // ->whereHas('user', function (Builder $query) {
            //     if ($this->search) {
            //         $query->where(function ($q) {
            //             $q->where('name', 'like', '%' . $this->search . '%')
            //                 ->orWhere('email', 'like', '%' . $this->search . '%')
            //                 ->orWhere('phone', 'like', '%' . $this->search . '%');
            //         });
            //     }

            //     if ($this->statusFilter !== 'all') {
            //         $isActive = $this->statusFilter === 'active';
            //         $query->where('is_active', $isActive);
            //     }
            // })
            // ->when($this->sortBy === 'name', function ($query) {
            //     $query->join('users', 'parents.user_id', '=', 'users.id')
            //         ->orderBy('users.name', $this->sortDirection)
            //         ->select('parents.*');
            // })
            // ->when($this->sortBy === 'students_count', function ($query) {
            //     $query->withCount('students')
            //         ->orderBy('students_count', $this->sortDirection);
            // })
            // ->when(!in_array($this->sortBy, ['name', 'students_count']), function ($query) {
            //     $query->orderBy($this->sortBy, $this->sortDirection);
            // })
            // ->paginate($this->perPage);
    }

public function getParentsProperty_old()
{
    return StudentParent::with(['user', 'students.academicLevel', 'students.academicGroup'])
        ->get()
        ->map(function ($parent) {
            $wards = $parent->students->map(function ($student) {
                $feeStructure = AcademicFeeStructure::where('school_id', $student->school_id)
                    ->where('academic_group_id', $student->academic_group_id)
                    ->where('academic_level_id', $student->academic_level_id)
                    ->latest()
                    ->first();

                return [
                    'student' => $student,
                    'totalAmount' => $feeStructure->amount ?? 0,
                    'paymentMethod' => $feeStructure->payment_method ?? 'Momo',
                    'dueDate' => $feeStructure->due_date,
                    'amountPaid' => $student->amount_paid ?? 0,
                    'remainingAmount' => ($feeStructure->amount ?? 0) - ($student->amount_paid ?? 0),
                    'feeStatus' => $student->fee_status ?? 'Pending',
                ];
            });

            return [
                'parent' => $parent,
                'wards' => $wards
            ];
        });
}




    public function getParentStatsProperty()
    {
        return [
            'total' => StudentParent::count(),
            'active' => StudentParent::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'inactive' => StudentParent::whereHas('user', fn($q) => $q->where('is_active', false))->count(),
            'with_multiple_children' => StudentParent::has('students', '>', 1)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.administrators.parent-management', [
            'parents' => $this->parents,
            'parentStats' => $this->parentStats,
        ]);
    }
}
