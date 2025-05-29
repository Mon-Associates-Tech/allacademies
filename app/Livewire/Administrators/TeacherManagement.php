<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicSubject as Subject;;
use Illuminate\Support\Facades\Hash;

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

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'specialization' => 'nullable|string',
        'biography' => 'nullable|string',
        'subjectIds' => 'nullable|array',
    ];

    public function mount()
    {
        $this->subjects = Subject::all();
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

        // Assign teacher role
        $teacherRole = Role::where('name', 'teacher')->first();
        $user->roles()->attach($teacherRole);

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
        session()->flash('message', 'Teacher created successfully!');
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
    }

    public function update()
    {
        $teacher = Teacher::with('user')->findOrFail($this->editingTeacherId);

        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$teacher->user_id,
            'specialization' => 'nullable|string',
            'biography' => 'nullable|string',
            'subjectIds' => 'nullable|array',
        ]);

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
        session()->flash('message', 'Teacher updated successfully!');
    }

    public function delete($teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $userId = $teacher->user_id;

        // Check if teacher has student groups
        if ($teacher->studentGroups()->count() > 0) {
            session()->flash('error', 'Cannot delete teacher who has assigned student groups. Please reassign the groups first.');
            return;
        }

        // Check if teacher has lessons
        if ($teacher->lessons()->count() > 0) {
            session()->flash('error', 'Cannot delete teacher who has lessons. Please reassign or delete the lessons first.');
            return;
        }

        // Delete teacher record
        $teacher->subjects()->detach();
        $teacher->delete();

        // Delete user
        User::destroy($userId);

        session()->flash('message', 'Teacher deleted successfully!');
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
        $teachers = Teacher::whereHas('user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhere('specialization', 'like', '%'.$this->searchTerm.'%')
            ->with(['user', 'subjects', 'studentGroups'])
            ->paginate(10);

        return view('livewire.administrators.teacher-management', [
            'teachers' => $teachers
        ]);
    }
}
