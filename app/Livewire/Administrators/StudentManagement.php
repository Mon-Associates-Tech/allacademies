<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentGroup;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Teacher;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $studentGroupId;
    public $academicGroupId;
    public $academicLevelId;
    public $primaryTeacherId;
    public $selectedTeachers = [];
    public $additionalSubjects = []; // Subjects to add beyond academic level
    public $removedSubjects = []; // Subjects to remove from academic level
    public $searchTerm = '';
    public $isEditing = false;
    public $editingStudentId;
    public $showIndividualSubjects = false;

    // Teacher management
    public $showTeacherModal = false;
    public $teacherName;
    public $teacherEmail;
    public $teacherPassword;
    public $isEditingTeacher = false;
    public $editingTeacherId;
    public $showManageTeachers = false;
    public $teachersToAssignToGroup = [];
    public $teachersToAssignToLevel = [];
    public $selectedTeachersForGroup = [];
    public $selectedTeachersForLevel = [];

    // Collections
    public $studentGroups;
    public $academicGroups;
    public $academicLevels = [];
    public $levelSubjects = [];
    public $availableAdditionalSubjects = [];
    public $availableTeachers = [];
    public $allTeachers = [];

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'studentGroupId' => 'nullable|exists:student_groups,id',
        'academicGroupId' => 'required|exists:academic_groups,id',
        'academicLevelId' => 'required|exists:academic_levels,id',
        'primaryTeacherId' => 'nullable|exists:teachers,id',
        'selectedTeachers' => 'nullable|array',
        'selectedTeachers.*' => 'exists:teachers,id',
        'additionalSubjects' => 'nullable|array',
        'additionalSubjects.*' => 'exists:academic_subjects,id',
        'removedSubjects' => 'nullable|array',
        'removedSubjects.*' => 'exists:academic_subjects,id',
    ];

    public function mount()
    {
        $this->studentGroups = StudentGroup::all();
        $this->academicGroups = AcademicGroup::all();
        $this->allTeachers = Teacher::with('user')->get();
    }

    public function updatedAcademicGroupId()
    {
        logger('Academic Group ID updated to: ' . $this->academicGroupId);

        // Reset dependent fields
        $this->academicLevelId = '';
        $this->primaryTeacherId = '';
        $this->selectedTeachers = [];
        $this->additionalSubjects = [];
        $this->removedSubjects = [];
        $this->levelSubjects = [];
        $this->availableAdditionalSubjects = [];
        $this->availableTeachers = [];

        if ($this->academicGroupId) {
            $this->academicLevels = AcademicLevel::where('academic_group_id', $this->academicGroupId)->get();
            $this->loadTeachersForGroupManagement();
        } else {
            $this->academicLevels = collect();
        }

        // Clear validation errors
        $this->resetValidation(['academicLevelId', 'primaryTeacherId']);

        // Force re-render of the component
        $this->dispatch('$refresh');
    }

    public function updatedAcademicLevelId()
    {
        $this->primaryTeacherId = '';
        $this->selectedTeachers = [];
        $this->additionalSubjects = [];
        $this->removedSubjects = [];

        if ($this->academicLevelId) {
            // Load subjects for this academic level
            $this->levelSubjects = AcademicSubject::where('academic_level_id', $this->academicLevelId)->get();

            // Load subjects from other levels that can be added individually
            $this->availableAdditionalSubjects = AcademicSubject::where('academic_level_id', '!=', $this->academicLevelId)->get();

            // Load teachers who belong to this academic level
            $this->availableTeachers = Teacher::whereHas('academicLevels', function($query) {
                $query->where('academic_level_id', $this->academicLevelId);
            })->with('user')->get();

            $this->loadTeachersForLevelManagement();
        } else {
            $this->levelSubjects = [];
            $this->availableAdditionalSubjects = [];
            $this->availableTeachers = [];
        }
    }

    // Teacher Management Methods
    public function showManageTeachersModal()
    {
        $this->showManageTeachers = true;
        $this->loadTeachersForGroupManagement();
        if ($this->academicLevelId) {
            $this->loadTeachersForLevelManagement();
        }
    }

    public function loadTeachersForGroupManagement()
    {
        if (!$this->academicGroupId) return;

        $group = AcademicGroup::with('teachers')->find($this->academicGroupId);
        $assignedTeacherIds = $group->teachers->pluck('id')->toArray();

        $this->teachersToAssignToGroup = Teacher::whereNotIn('id', $assignedTeacherIds)
            ->with('user')->get();
    }

    public function loadTeachersForLevelManagement()
    {
        if (!$this->academicLevelId) return;

        $level = AcademicLevel::with('teachers')->find($this->academicLevelId);
        $assignedTeacherIds = $level->teachers->pluck('id')->toArray();

        $this->teachersToAssignToLevel = Teacher::whereNotIn('id', $assignedTeacherIds)
            ->with('user')->get();
    }

    public function assignTeachersToGroup()
    {
        if (empty($this->selectedTeachersForGroup)) {
            session()->flash('error', 'No teachers selected for group assignment.');
            return;
        }

        $group = AcademicGroup::find($this->academicGroupId);
        $teacherData = [];

        foreach ($this->selectedTeachersForGroup as $teacherId) {
            $teacherData[$teacherId] = [
                'is_primary' => false,
                'notes' => 'Assigned to academic group',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $group->teachers()->attach($teacherData);

        $this->selectedTeachersForGroup = [];
        $this->loadTeachersForGroupManagement();
        session()->flash('message', 'Teachers assigned to academic group successfully!');
    }

    public function assignTeachersToLevel()
    {
        if (empty($this->selectedTeachersForLevel)) {
            session()->flash('error', 'No teachers selected for level assignment.');
            return;
        }

        $level = AcademicLevel::find($this->academicLevelId);
        $teacherData = [];

        foreach ($this->selectedTeachersForLevel as $teacherId) {
            $teacherData[$teacherId] = [
                'is_primary' => false,
                'notes' => 'Assigned to academic level',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $level->teachers()->attach($teacherData);

        $this->selectedTeachersForLevel = [];
        $this->loadTeachersForLevelManagement();

        // Refresh available teachers for student assignment
        $this->availableTeachers = Teacher::whereHas('academicLevels', function($query) {
            $query->where('academic_level_id', $this->academicLevelId);
        })->with('user')->get();

        session()->flash('message', 'Teachers assigned to academic level successfully!');
    }

    public function removeTeacherFromGroup($teacherId)
    {
        $group = AcademicGroup::find($this->academicGroupId);
        $group->teachers()->detach($teacherId);

        $this->loadTeachersForGroupManagement();
        session()->flash('message', 'Teacher removed from academic group successfully!');
    }

    public function removeTeacherFromLevel($teacherId)
    {
        $level = AcademicLevel::find($this->academicLevelId);
        $level->teachers()->detach($teacherId);

        $this->loadTeachersForLevelManagement();

        // Refresh available teachers for student assignment
        $this->availableTeachers = Teacher::whereHas('academicLevels', function($query) {
            $query->where('academic_level_id', $this->academicLevelId);
        })->with('user')->get();

        session()->flash('message', 'Teacher removed from academic level successfully!');
    }

    public function showCreateTeacherModal()
    {
        $this->showTeacherModal = true;
        $this->isEditingTeacher = false;
        $this->resetTeacherForm();
    }

    public function createTeacher()
    {
        $this->validate([
            'teacherName' => 'required|min:3',
            'teacherEmail' => 'required|email|unique:users,email',
            'teacherPassword' => 'required|min:8',
        ]);

        DB::transaction(function () {
            // Create user
            $user = User::create([
                'role' => 'teacher',
                'name' => $this->teacherName,
                'email' => $this->teacherEmail,
                'password' => Hash::make($this->teacherPassword),
            ]);

            // Assign teacher role
            $teacherRole = Role::where('name', 'teacher')->first();
            $user->roles()->attach($teacherRole);

            // Create teacher record
            $teacher = Teacher::create([
                'user_id' => $user->id,
            ]);

            // Auto-assign to current academic group and level if selected
            if ($this->academicGroupId) {
                $group = AcademicGroup::find($this->academicGroupId);
                $group->teachers()->attach($teacher->id, [
                    'is_primary' => false,
                    'notes' => 'Auto-assigned during creation',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($this->academicLevelId) {
                $level = AcademicLevel::find($this->academicLevelId);
                $level->teachers()->attach($teacher->id, [
                    'is_primary' => false,
                    'notes' => 'Auto-assigned during creation',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->resetTeacherForm();
        $this->showTeacherModal = false;

        // Refresh data
        $this->allTeachers = Teacher::with('user')->get();
        $this->loadTeachersForGroupManagement();
        if ($this->academicLevelId) {
            $this->loadTeachersForLevelManagement();
            $this->availableTeachers = Teacher::whereHas('academicLevels', function($query) {
                $query->where('academic_level_id', $this->academicLevelId);
            })->with('user')->get();
        }

        session()->flash('message', 'Teacher created and assigned successfully!');
    }

    public function resetTeacherForm()
    {
        $this->teacherName = '';
        $this->teacherEmail = '';
        $this->teacherPassword = '';
        $this->resetValidation(['teacherName', 'teacherEmail', 'teacherPassword']);
    }

    public function closeManageTeachersModal()
    {
        $this->showManageTeachers = false;
        $this->selectedTeachersForGroup = [];
        $this->selectedTeachersForLevel = [];
    }

    public function closeTeacherModal()
    {
        $this->showTeacherModal = false;
        $this->resetTeacherForm();
    }

    // Existing methods (keeping them unchanged)
    public function updatedPrimaryTeacherId()
    {
        if ($this->primaryTeacherId && !in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->selectedTeachers[] = $this->primaryTeacherId;
        }
    }

    public function updatedSelectedTeachers()
    {
        if ($this->primaryTeacherId && !in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->primaryTeacherId = '';
        }
    }

    public function create()
    {
        $this->validate();

        DB::transaction(function () {
            // Create user
            $user = User::create([
                'role' => 'student',
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Assign student role
            $studentRole = Role::where('name', 'student')->first();
            $user->roles()->attach($studentRole);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'student_group_id' => $this->studentGroupId,
                'academic_group_id' => $this->academicGroupId,
                'academic_level_id' => $this->academicLevelId,
            ]);

            // Assign teachers to student
            if (!empty($this->selectedTeachers)) {
                $teacherData = [];
                foreach ($this->selectedTeachers as $teacherId) {
                    $teacherData[$teacherId] = [
                        'is_primary' => $teacherId == $this->primaryTeacherId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $student->teachers()->attach($teacherData);
            }

            // Handle individual subject assignments
            $this->assignIndividualSubjects($student);
        });

        $this->resetForm();
        session()->flash('message', 'Student created successfully! They have access to all subjects from their academic level plus any additional subjects assigned.');
    }

    public function edit($studentId)
    {
        $this->isEditing = true;
        $this->editingStudentId = $studentId;

        // Load student with all necessary relationships
        $student = Student::with([
            'user',
            'individualSubjects',
            'teachers',
            'academicLevel.academicSubjects'
        ])->findOrFail($studentId);

        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->password = '';
        $this->studentGroupId = $student->student_group_id;
        $this->academicGroupId = $student->academic_group_id;
        $this->academicLevelId = $student->academic_level_id;

        // Load dependent data
        if ($this->academicGroupId) {
            $this->academicLevels = AcademicLevel::where('academic_group_id', $this->academicGroupId)->get();
        }

        if ($this->academicLevelId) {
            $this->levelSubjects = AcademicSubject::where('academic_level_id', $this->academicLevelId)->get();
            $this->availableAdditionalSubjects = AcademicSubject::where('academic_level_id', '!=', $this->academicLevelId)->get();
            $this->availableTeachers = Teacher::whereHas('academicLevels', function($query) {
                $query->where('academic_level_id', $this->academicLevelId);
            })->with('user')->get();
        }

        // Set selected teachers and primary teacher
        $this->selectedTeachers = $student->teachers->pluck('id')->toArray();
        $primaryTeacher = $student->teachers->where('pivot.is_primary', true)->first();
        $this->primaryTeacherId = $primaryTeacher ? $primaryTeacher->id : '';

        // Set individual subject assignments
        $levelSubjectIds = $this->levelSubjects->pluck('id')->toArray();

        $this->additionalSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->whereNotIn('academic_subjects.id', $levelSubjectIds)
            ->pluck('academic_subjects.id')
            ->toArray();

        $this->removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', false)
            ->pluck('academic_subjects.id')
            ->toArray();
    }

    public function update()
    {
        $student = Student::with(['user', 'teachers'])->findOrFail($this->editingStudentId);

        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$student->user_id,
            'academicGroupId' => 'required|exists:academic_groups,id',
            'academicLevelId' => 'required|exists:academic_levels,id',
            'primaryTeacherId' => 'nullable|exists:teachers,id',
            'selectedTeachers' => 'nullable|array',
            'selectedTeachers.*' => 'exists:teachers,id',
            'additionalSubjects' => 'nullable|array',
            'additionalSubjects.*' => 'exists:academic_subjects,id',
            'removedSubjects' => 'nullable|array',
            'removedSubjects.*' => 'exists:academic_subjects,id',
        ]);

        DB::transaction(function () use ($student) {
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
                'academic_group_id' => $this->academicGroupId,
                'academic_level_id' => $this->academicLevelId,
            ]);

            // Update teacher assignments
            if (!empty($this->selectedTeachers)) {
                $teacherData = [];
                foreach ($this->selectedTeachers as $teacherId) {
                    $teacherData[$teacherId] = [
                        'is_primary' => $teacherId == $this->primaryTeacherId,
                        'updated_at' => now(),
                    ];
                }
                $student->teachers()->sync($teacherData);
            } else {
                $student->teachers()->detach();
            }

            // Update individual subject assignments
            $this->assignIndividualSubjects($student);
        });

        $this->resetForm();
        session()->flash('message', 'Student updated successfully!');
    }

    private function assignIndividualSubjects($student)
    {
        // Clear existing individual assignments
        $student->individualSubjects()->detach();

        $currentUserId = Auth::id();
        $now = now();

        // Add additional subjects (subjects not in their academic level)
        if (!empty($this->additionalSubjects)) {
            $additionalData = [];
            foreach ($this->additionalSubjects as $subjectId) {
                $additionalData[$subjectId] = [
                    'is_active' => true,
                    'assigned_by' => $currentUserId,
                    'notes' => 'Additional subject assignment',
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $student->individualSubjects()->attach($additionalData);
        }

        // Remove subjects from academic level access
        if (!empty($this->removedSubjects)) {
            $removedData = [];
            foreach ($this->removedSubjects as $subjectId) {
                $removedData[$subjectId] = [
                    'is_active' => false,
                    'assigned_by' => $currentUserId,
                    'notes' => 'Removed from academic level access',
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $student->individualSubjects()->attach($removedData);
        }
    }

    public function delete($studentId)
    {
        $student = Student::findOrFail($studentId);
        $userId = $student->user_id;

        DB::transaction(function () use ($student, $userId) {
            $student->delete();
            User::destroy($userId);
        });

        session()->flash('message', 'Student deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->studentGroupId = '';
        $this->academicGroupId = '';
        $this->academicLevelId = '';
        $this->primaryTeacherId = '';
        $this->selectedTeachers = [];
        $this->additionalSubjects = [];
        $this->removedSubjects = [];
        $this->isEditing = false;
        $this->editingStudentId = null;
        $this->showIndividualSubjects = false;
        $this->academicLevels = [];
        $this->levelSubjects = [];
        $this->availableAdditionalSubjects = [];
        $this->availableTeachers = [];
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
            ->orWhereHas('academicGroup', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhereHas('academicLevel', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%');
            })
            ->with([
                'user',
                'studentGroup',
                'academicGroup',
                'academicLevel.academicSubjects',
                'teachers.user',
                'individualSubjects'
            ])
            ->paginate(10);

        // Get teachers assigned to current academic group and level for display
        $groupTeachers = collect();
        $levelTeachers = collect();

        if ($this->academicGroupId) {
            $group = AcademicGroup::with('teachers.user')->find($this->academicGroupId);
            $groupTeachers = $group ? $group->teachers : collect();
        }

        if ($this->academicLevelId) {
            $level = AcademicLevel::with('teachers.user')->find($this->academicLevelId);
            $levelTeachers = $level ? $level->teachers : collect();
        }

        return view('livewire.administrators.student-management', [
            'students' => $students,
            'groupTeachers' => $groupTeachers,
            'levelTeachers' => $levelTeachers,
        ]);
    }
}
