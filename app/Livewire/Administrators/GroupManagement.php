<?php

namespace App\Livewire\Administrators;

use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function mount(): void
    {
        $this->teachers = Teacher::with('user')->get();
    }

    public function updatedName(): void
    {
        $this->slug = Str::slug($this->name);
    }

    public function updateTeacherId($teacherId): void
    {
        $this->teacherId = $teacherId;
    }

    public function create(): void
    {

        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before creating a student group.');
            return;
        }


        $this->validate();

        StudentGroup::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'teacher_id' => $this->teacherId,
            'school_id' => $schoolId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student group created successfully!');
        $this->js('window.Modal.close("student-group-management-form")');
    }

    public function resetForm(): void
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

    public function edit($groupId): void
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

    public function delete($groupId): void
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

    public function showStudentsInGroup($groupId): void
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage group students.');
            return;
        }

        // Verify the group belongs to the current school context
        $group = StudentGroup::find($groupId);
        if (!$group || $group->school_id != $schoolId) {
            session()->flash('error', 'This group does not belong to the current school context.');
            return;
        }

        $this->selectedGroupId = $groupId;
        $this->showStudents = true;
        $this->loadStudentsNotInGroup();
    }

    public function loadStudentsNotInGroup(): void
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            $this->studentsNotInGroup = collect();
            return;
        }

        // Only load students from the same school context
        $this->studentsNotInGroup = Student::where('school_id', $schoolId)
            ->where(function ($query) {
                $query->where('student_group_id', '!=', $this->selectedGroupId)
                    ->orWhereNull('student_group_id');
            })
            ->with('user')
            ->get();
    }

    public function addStudentsToGroup(): void
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to add students to the group.');
            return;
        }

        if (empty($this->selectedStudents)) {
            session()->flash('error', 'No students selected.');
            return;
        }

        // Verify the group belongs to the current school
        $group = StudentGroup::find($this->selectedGroupId);
        if (!$group || $group->school_id != $schoolId) {
            session()->flash('error', 'Cannot add students to a group from a different school.');
            return;
        }

        // Verify all selected students belong to the same school
        $studentsToAdd = Student::whereIn('id', $this->selectedStudents)
            ->where('school_id', $schoolId)
            ->get();

        if ($studentsToAdd->count() !== count($this->selectedStudents)) {
            session()->flash('error', 'Some selected students do not belong to the current school context.');
            return;
        }

        // Update only the validated students
        Student::whereIn('id', $studentsToAdd->pluck('id'))
            ->update(['student_group_id' => $this->selectedGroupId]);

        $this->selectedStudents = [];
        $this->loadStudentsNotInGroup();
        session()->flash('message', 'Students added to group successfully!');
    }

    public function update(): void
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before updating a student group.');
            return;
        }

        $this->validate([
            'name' => 'required|min:3|unique:student_groups,name,' . $this->editingGroupId,
            'description' => 'nullable|string',
            'teacherId' => 'required|exists:teachers,id',
        ]);

        $group = StudentGroup::findOrFail($this->editingGroupId);

        $group->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'teacher_id' => $this->teacherId,
            'school_id' => $schoolId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Student group updated successfully!');
        $this->js('window.Modal.close("student-group-management-form")');
    }

    public function removeStudentFromGroup($studentId): void
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage group students.');
            return;
        }

        $student = Student::where('id', $studentId)
            ->where('school_id', $schoolId)
            ->first();

        if (!$student) {
            session()->flash('error', 'Student not found or does not belong to the current school context.');
            return;
        }

        $student->update(['student_group_id' => null]);

        $this->loadStudentsNotInGroup();
        session()->flash('message', 'Student removed from group successfully!');
    }

    public function closeStudentsModal(): void
    {
        $this->showStudents = false;
        $this->selectedGroupId = null;
        $this->selectedStudents = [];
    }

    public function render()
    {
        $schoolId = getSchoolId();

        $query = StudentGroup::query();

        // Apply school context filter
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        } else {
            // If no school context for cross-school users, show all
            // But the BelongsToSchoolEnhanced trait should handle this
        }

        $groups = $query->where(function($q) {
            $q->where('name', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('description', 'like', '%'.$this->searchTerm.'%')
                ->orWhereHas('teacher.user', function($query) {
                    $query->where('name', 'like', '%'.$this->searchTerm.'%');
                });
        })
            ->with(['teacher.user', 'students'])
            ->paginate(10);

        $studentsInSelectedGroup = $this->selectedGroupId
            ? Student::where('student_group_id', $this->selectedGroupId)
                ->where('school_id', $schoolId) // Ensure students match school context
                ->with('user')
                ->get()
            : collect();

        return view('livewire.administrators.group-management', [
            'groups' => $groups,
            'studentsInGroup' => $studentsInSelectedGroup
        ]);
    }
}
