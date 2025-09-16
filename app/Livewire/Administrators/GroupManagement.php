<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Str;

class GroupManagement extends Component
{
    use WithPagination;

    public $name;
    public $slug;
    public $description;
    public $teacherId;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingGroupId;
    public $teachers;
    public $selectedGroupId;
    public $showStudents = false;
    public $studentsNotInGroup = [];
    public $selectedStudents = [];

    protected $rules = [
        'name' => 'required|min:3|unique:student_groups,name',
        'description' => 'nullable|string',
        'teacherId' => 'required|exists:teachers,id',
    ];

    protected $listeners = [
        'update-teacherId' => 'updateTeacherId',
    ];

    public function mount()
    {
        $this->teachers = Teacher::with('user')->get();
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function updateTeacherId($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function create()
    {
        $this->validate();

        StudentGroup::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'teacher_id' => $this->teacherId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student group created successfully!');
        $this->js('window.Modal.close("student-group-management-form")');
    }

    public function edit($groupId)
    {
        $this->isEditing = true;
        $this->editingGroupId = $groupId;

        $group = StudentGroup::findOrFail($groupId);
        $this->name = $group->name;
        $this->slug = $group->slug;
        $this->description = $group->description;
        $this->teacherId = $group->teacher_id;
        $this->js('window.Modal.open("student-group-management-form")');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3|unique:student_groups,name,'.$this->editingGroupId,
            'description' => 'nullable|string',
            'teacherId' => 'required|exists:teachers,id',
        ]);

        $group = StudentGroup::findOrFail($this->editingGroupId);

        $group->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'teacher_id' => $this->teacherId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student group updated successfully!');
        $this->js('window.Modal.close("student-group-management-form")');
    }

    public function delete($groupId)
    {
        $group = StudentGroup::findOrFail($groupId);

        // Check if students are in this group
        if ($group->students()->count() > 0) {
            session()->flash('error', 'Cannot delete group that has students. Please move students to another group first.');
            return;
        }

        $group->delete();
        session()->flash('message', 'Student group deleted successfully!');
    }

    public function showStudentsInGroup($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->showStudents = true;
        $this->loadStudentsNotInGroup();
    }

    public function loadStudentsNotInGroup()
    {
        $this->studentsNotInGroup = Student::where('student_group_id', '!=', $this->selectedGroupId)
            ->orWhereNull('student_group_id')
            ->with('user')
            ->get();
    }

    public function addStudentsToGroup()
    {
        if (empty($this->selectedStudents)) {
            session()->flash('error', 'No students selected.');
            return;
        }

        Student::whereIn('id', $this->selectedStudents)
            ->update(['student_group_id' => $this->selectedGroupId]);

        $this->selectedStudents = [];
        $this->loadStudentsNotInGroup();
        session()->flash('message', 'Students added to group successfully!');
    }

    public function removeStudentFromGroup($studentId)
    {
        $student = Student::findOrFail($studentId);
        $student->update(['student_group_id' => null]);

        $this->loadStudentsNotInGroup();
        session()->flash('message', 'Student removed from group successfully!');
    }

    public function closeStudentsModal()
    {
        $this->showStudents = false;
        $this->selectedGroupId = null;
        $this->selectedStudents = [];
    }

    public function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->teacherId = '';
        $this->isEditing = false;
        $this->editingGroupId = null;
        $this->resetValidation();
        $this->js('window.Modal.close("student-group-management-form")');
    }

    public function render()
    {
        $groups = StudentGroup::where('name', 'like', '%'.$this->searchTerm.'%')
            ->orWhere('description', 'like', '%'.$this->searchTerm.'%')
            ->orWhereHas('teacher.user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->with(['teacher.user', 'students'])
            ->paginate(10);

        $studentsInSelectedGroup = $this->selectedGroupId
            ? Student::where('student_group_id', $this->selectedGroupId)->with('user')->get()
            : collect();

        return view('livewire.administrators.group-management', [
            'groups' => $groups,
            'studentsInGroup' => $studentsInSelectedGroup
        ]);
    }
}
