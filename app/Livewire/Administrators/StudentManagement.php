<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentGroup;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class StudentManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $studentGroupId;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingStudentId;
    public $studentGroups;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'studentGroupId' => 'required|exists:student_groups,id',
    ];

    public function mount()
    {
        $this->studentGroups = StudentGroup::all();
    }

    public function create()
    {
        $this->validate();

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Assign student role
        $studentRole = Role::where('name', 'student')->first();
        $user->roles()->attach($studentRole);

        // Create student record
        Student::create([
            'user_id' => $user->id,
            'student_group_id' => $this->studentGroupId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student created successfully!');
    }

    public function edit($studentId)
    {
        $this->isEditing = true;
        $this->editingStudentId = $studentId;

        $student = Student::with('user')->findOrFail($studentId);
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->password = '';
        $this->studentGroupId = $student->student_group_id;
    }

    public function update()
    {
        $student = Student::with('user')->findOrFail($this->editingStudentId);

        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$student->user_id,
            'studentGroupId' => 'required|exists:student_groups,id',
        ]);

        // Update user
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $student->user->update($userData);

        // Update student
        $student->update([
            'student_group_id' => $this->studentGroupId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student updated successfully!');
    }

    public function delete($studentId)
    {
        $student = Student::findOrFail($studentId);
        $userId = $student->user_id;

        // Delete student record
        $student->delete();

        // Delete user
        User::destroy($userId);

        session()->flash('message', 'Student deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->studentGroupId = '';
        $this->isEditing = false;
        $this->editingStudentId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $students = Student::whereHas('user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhereHas('studentGroup', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->with(['user', 'studentGroup'])
            ->paginate(10);

        return view('livewire.administrators.student-management', [
            'students' => $students
        ]);
    }
}
