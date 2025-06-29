<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicSubject as Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $specialization;
    public $biography;
    public $subjectIds = [];
    public $searchTerm = '';
    public $isEditing = false;
    public $editingTeacherId;
    public $subjects;
    public $showTeacherModal = false;
    public $showDeleteModal = false;
    public $selectedTeacher;
    public $teacherToDelete;

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'specialization' => 'nullable|string|max:255',
        'biography' => 'nullable|string|max:1000',
        'subjectIds' => 'nullable|array',
        'subjectIds.*' => 'exists:academic_subjects,id',
    ];

    protected $messages = [
        'name.required' => 'Teacher name is required.',
        'name.min' => 'Teacher name must be at least 3 characters.',
        'name.max' => 'Teacher name cannot exceed 255 characters.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already registered.',
        'password.required' => 'Password is required for new teachers.',
        'password.min' => 'Password must be at least 8 characters.',
        'specialization.max' => 'Specialization cannot exceed 255 characters.',
        'biography.max' => 'Biography cannot exceed 1000 characters.',
        'subjectIds.*.exists' => 'One or more selected subjects are invalid.',
    ];

    public function mount()
    {
        $this->subjects = Subject::orderBy('name')->get();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        try {
            // Create user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
            ]);

            // Assign teacher role
            $teacherRole = Role::where('name', 'teacher')->first();
            if ($teacherRole) {
                $user->roles()->attach($teacherRole);
            }

            // Create teacher record
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'specialization' => $this->specialization,
                'biography' => $this->biography,
            ]);

            // Associate with subjects
            if (!empty($this->subjectIds)) {
                $teacher->subjects()->attach($this->subjectIds);
            }

            $this->resetForm();
            session()->flash('message', 'Teacher "' . $this->name . '" created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create teacher. Please try again.');
        }
    }

    public function edit($teacherId)
    {
        $this->isEditing = true;
        $this->editingTeacherId = $teacherId;

        $teacher = Teacher::with('user', 'subjects')->findOrFail($teacherId);
        $this->name = $teacher->user->name;
        $this->email = $teacher->user->email;
        $this->password = '';
        $this->specialization = $teacher->specialization;
        $this->biography = $teacher->biography;
        $this->subjectIds = $teacher->subjects->pluck('id')->toArray();

        // Scroll to form
        $this->dispatch('scroll-to-form');
    }

    public function update()
    {
        $teacher = Teacher::with('user')->findOrFail($this->editingTeacherId);

        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password' => 'nullable|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'biography' => 'nullable|string|max:1000',
            'subjectIds' => 'nullable|array',
            'subjectIds.*' => 'exists:academic_subjects,id',
        ]);

        try {
            // Update user
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            $teacher->user->update($userData);

            // Update teacher
            $teacher->update([
                'specialization' => $this->specialization,
                'biography' => $this->biography,
            ]);

            // Update subjects
            $teacher->subjects()->sync($this->subjectIds);

            $this->resetForm();
            session()->flash('message', 'Teacher "' . $this->name . '" updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update teacher. Please try again.');
        }
    }

    public function confirmDelete($teacherId)
    {
        $this->teacherToDelete = $teacherId;
        $this->showDeleteModal = true;
    }

    public function deleteTeacher()
    {
        try {
            $teacher = Teacher::with('user')->findOrFail($this->teacherToDelete);
            $teacherName = $teacher->user->name;

            // Check if teacher has student groups
            if ($teacher->studentGroups()->count() > 0) {
                session()->flash('error', 'Cannot delete teacher "' . $teacherName . '" because they have assigned student groups. Please reassign the groups first.');
                $this->closeDeleteModal();
                return;
            }

            // Check if teacher has assignments
            if (method_exists($teacher, 'assignments') && $teacher->assignments()->count() > 0) {
                session()->flash('error', 'Cannot delete teacher "' . $teacherName . '" because they have assignments. Please reassign or delete the assignments first.');
                $this->closeDeleteModal();
                return;
            }

            $userId = $teacher->user_id;

            // Delete teacher record and relationships
            $teacher->subjects()->detach();
            $teacher->delete();

            // Delete user
            User::destroy($userId);

            session()->flash('message', 'Teacher "' . $teacherName . '" deleted successfully!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete teacher. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function showTeacherDetails($teacherId)
    {
        $this->selectedTeacher = Teacher::with([
            'user',
            'subjects',
            'studentGroups.students',
            'studentsFromGroups'
        ])->findOrFail($teacherId);
        $this->showTeacherModal = true;
    }

    public function closeTeacherModal()
    {
        $this->showTeacherModal = false;
        $this->selectedTeacher = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->teacherToDelete = null;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->specialization = '';
        $this->biography = '';
        $this->subjectIds = [];
        $this->isEditing = false;
        $this->editingTeacherId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Teacher::query();

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                })
                    ->orWhere('specialization', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('subjects', function($subjectQuery) {
                        $subjectQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $teachers = $query->with([
            'user',
            'subjects',
            'studentGroups',
            'studentsFromGroups'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.administrators.teacher-management', [
            'teachers' => $teachers
        ]);
    }
}
