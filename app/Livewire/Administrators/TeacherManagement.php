<?php

namespace App\Livewire\Administrators;

use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicSubject;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherManagement extends Component
{
    use WithPagination;

    // Form fields
    public $name;

    public $email;

    public $password;

    public $specialization;

    public $biography;

    public $academicGroupId;

    public $academicLevelId;

    public $selectedSubjects = [];

    // Search and filters
    public $searchTerm = '';

    public $filterAcademicGroup = '';

    public $filterAcademicLevel = '';

    public $filterSubject = '';

    public $filterSpecialization = '';

    // UI state
    public $isEditing = false;

    public $editingTeacherId;

    public $showTeacherModal = false;

    public $showDeleteModal = false;

    public $selectedTeacher;

    public $teacherToDelete;

    public $existingUser = null;

    public $userExists = false;

    // Collections
    public $academicGroups;

    public $academicLevels = [];

    public $availableSubjects = [];

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email',
        'password' => 'nullable|string|min:8',
        'specialization' => 'nullable|string|max:255',
        'biography' => 'nullable|string|max:1000',
        'academicGroupId' => 'required|exists:academic_groups,id',
        'academicLevelId' => 'required|exists:academic_levels,id',
        'selectedSubjects' => 'nullable|array',
        'selectedSubjects.*' => 'exists:academic_subjects,id',
    ];

    protected $messages = [
        'name.required' => 'Teacher name is required.',
        'name.min' => 'Teacher name must be at least 3 characters.',
        'name.max' => 'Teacher name cannot exceed 255 characters.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.min' => 'Password must be at least 8 characters.',
        'specialization.max' => 'Specialization cannot exceed 255 characters.',
        'biography.max' => 'Biography cannot exceed 1000 characters.',
        'academicGroupId.required' => 'Please select an academic group.',
        'academicLevelId.required' => 'Please select an academic level.',
        'selectedSubjects.*.exists' => 'One or more selected subjects are invalid.',
    ];

    public function mount()
    {
        $schoolId = getSchoolId();

        if ($schoolId) {
            // Get academic groups that this school has adopted
            $this->academicGroups = AcademicGroup::forSchool($schoolId)
                ->orderBy('name')
                ->get();
        } else {
            $this->academicGroups = collect();
        }
    }

    // Filter methods
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterAcademicGroup()
    {
        $this->filterAcademicLevel = ''; // Reset level when group changes
        $this->resetPage();
    }

    public function updatedFilterAcademicLevel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSubject(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSpecialization(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterAcademicGroup = '';
        $this->filterAcademicLevel = '';
        $this->filterSubject = '';
        $this->filterSpecialization = '';
        $this->searchTerm = '';
        $this->resetPage();
    }


    public function updatedEmail(): void
    {
        $this->checkExistingUser();
    }

    public function updatedAcademicGroupId(): void
    {
        $this->academicLevelId = null;
        $this->selectedSubjects = [];
        $this->availableSubjects = [];
        $this->loadAcademicLevels();
    }

    public function updatedAcademicLevelId(): void
    {
        $this->selectedSubjects = [];
        $this->loadAvailableSubjects();
    }

    public function checkExistingUser()
    {
        if (empty($this->email)) {
            $this->existingUser = null;
            $this->userExists = false;

            return;
        }

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->existingUser = $user;
            $this->userExists = true;
            $this->name = $user->name;

            $hasTeacherRole = $user->roles()->where('name', 'teacher')->exists();
            if ($hasTeacherRole) {
                $this->addError('email', 'This user already has a teacher account.');
            }
        } else {
            $this->existingUser = null;
            $this->userExists = false;
        }
    }

    public function loadAcademicLevels(): void
    {
        $schoolId = getSchoolId();

        if ($this->academicGroupId && $schoolId) {
            // Get academic levels for this group that the school has adopted
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();

            $this->academicLevels = $availableLevels->where('academic_group_id', $this->academicGroupId)
                ->sortBy('name')
                ->values();
        } else {
            $this->academicLevels = [];
        }
    }

    public function loadAvailableSubjects(): void
    {
        if ($this->academicLevelId) {
            $this->availableSubjects = AcademicSubject::where('academic_level_id', $this->academicLevelId)
                ->orderBy('name')
                ->get();
        } else {
            $this->availableSubjects = [];
        }
    }

    public function create()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before creating a teacher.');
            return;
        }

        // Dynamic validation rules
        $rules = $this->rules;

        if (! $this->userExists) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        try {
            DB::transaction(function () use ($schoolId) {
                // Step 1: Create or get user
                if ($this->userExists && $this->existingUser) {
                    $user = $this->existingUser;

                    // Update name if different
                    if ($user->name !== $this->name) {
                        $user->update(['name' => $this->name]);
                    }
                } else {
                    // Create new user with role field
                    $user = User::create([
                        'name' => $this->name,
                        'email' => $this->email,
                        'password' => Hash::make($this->password),
                        'role' => UserRole::TEACHER->value,
                    ]);
                }

                // Step 2: Assign teacher role
                $teacherRole = Role::where('name', 'teacher')->first();
                if ($teacherRole && ! $user->roles()->where('name', 'teacher')->exists()) {
                    $user->roles()->attach($teacherRole);
                }

                // Step 3: Create teacher record with school_id
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'school_id' => $schoolId,
                    'specialization' => $this->specialization,
                ]);

                // Step 4: Associate with academic group
                if ($this->academicGroupId) {
                    $teacher->academicGroups()->attach($this->academicGroupId, [
                        'is_primary' => true,
                        'notes' => 'Primary group assigned during teacher creation',
                    ]);
                }

                // Step 5: Associate with academic level
                if ($this->academicLevelId) {
                    $teacher->academicLevels()->attach($this->academicLevelId, [
                        'is_primary' => true,
                        'notes' => 'Primary level assigned during teacher creation',
                    ]);
                }

                // Step 6: Associate with selected subjects
                if (! empty($this->selectedSubjects)) {
                    $subjectData = [];
                    foreach ($this->selectedSubjects as $subjectId) {
                        $subjectData[$subjectId] = [
                            'is_primary' => true,
                            'notes' => 'Assigned during teacher creation',
                        ];
                    }
                    $teacher->subjects()->attach($subjectData);
                }

                // Step 7: Auto-assign all students at the same level (school-scoped)
                $this->autoAssignStudents($teacher, $schoolId);
            });

            $this->resetForm();
            session()->flash('message', 'Teacher "'.$this->name.'" created successfully with automatic student assignments!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create teacher: '.$e->getMessage());
        }

        $this->js('window.Modal.close("teacher-form")');
    }
    private function autoAssignStudents($teacher, $schoolId = null): void
    {
        if (! $this->academicLevelId) {
            return;
        }

        if (!$schoolId) {
            $schoolId = getSchoolId();
        }

        if (!$schoolId) {
            return;
        }

        // Get all students at the same academic level AND same school
        $students = Student::where('academic_level_id', $this->academicLevelId)
            ->where('school_id', $schoolId)
            ->get();

        if ($students->isNotEmpty()) {
            $studentData = [];
            foreach ($students as $student) {
                $studentData[$student->id] = [
                    'is_primary' => false, // Set to false since there might be a primary teacher already
                    'notes' => 'Auto-assigned based on academic level during teacher creation',
                ];
            }

            $teacher->assignedStudents()->attach($studentData);
        }
    }

    public function edit($teacherId): void
    {
        $this->isEditing = true;
        $this->editingTeacherId = $teacherId;

        $teacher = Teacher::with([
            'user',
            'subjects',
            'academicGroups',
            'academicLevels',
        ])->findOrFail($teacherId);

        $this->name = $teacher->user->name;
        $this->email = $teacher->user->email;
        $this->password = '';
        $this->specialization = $teacher->specialization;
        $this->biography = $teacher->biography ?? '';

        // Load academic group and level
        $primaryGroup = $teacher->academicGroups()->wherePivot('is_primary', true)->first();
        $primaryLevel = $teacher->academicLevels()->wherePivot('is_primary', true)->first();

        if ($primaryGroup) {
            $this->academicGroupId = $primaryGroup->id;
            $this->loadAcademicLevels();
        }

        if ($primaryLevel) {
            $this->academicLevelId = $primaryLevel->id;
            $this->loadAvailableSubjects();
        }

        $this->selectedSubjects = $teacher->subjects->pluck('id')->toArray();

        $this->dispatch('scroll-to-form');
        $this->js('window.Modal.open("teacher-form")');
    }

    public function update()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before updating a teacher.');
            return;
        }

        $teacher = Teacher::with('user')->findOrFail($this->editingTeacherId);

        $rules = $this->rules;
        $rules['email'] = ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)];
        $rules['password'] = 'nullable|string|min:8';

        $this->validate($rules);

        try {
            DB::transaction(function () use ($teacher, $schoolId) {
                // Update user
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                ];

                if (! empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }

                $teacher->user->update($userData);

                // Update teacher
                $teacher->update([
                    'specialization' => $this->specialization,
                    'school_id' => $schoolId, // Ensure school_id is set
                ]);

                // Update academic group association
                $teacher->academicGroups()->detach();
                if ($this->academicGroupId) {
                    $teacher->academicGroups()->attach($this->academicGroupId, [
                        'is_primary' => true,
                        'notes' => 'Updated primary group',
                    ]);
                }

                // Update academic level association
                $teacher->academicLevels()->detach();
                if ($this->academicLevelId) {
                    $teacher->academicLevels()->attach($this->academicLevelId, [
                        'is_primary' => true,
                        'notes' => 'Updated primary level',
                    ]);
                }

                // Update subject associations
                $teacher->subjects()->detach();
                if (! empty($this->selectedSubjects)) {
                    $subjectData = [];
                    foreach ($this->selectedSubjects as $subjectId) {
                        $subjectData[$subjectId] = [
                            'is_primary' => true,
                            'notes' => 'Updated during teacher edit',
                        ];
                    }
                    $teacher->subjects()->attach($subjectData);
                }

                // Re-assign students based on new level (school-scoped)
                $teacher->assignedStudents()->detach();
                $this->autoAssignStudents($teacher, $schoolId);
            });

            $this->resetForm();
            session()->flash('message', 'Teacher "'.$teacher->user->name.'" updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update teacher: '.$e->getMessage());
        }

        $this->js('window.Modal.close("teacher-form")');
    }
    public function confirmDelete($teacherId): void
    {
        $this->teacherToDelete = Teacher::with('user')->findOrFail($teacherId);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        try {
            DB::transaction(function () {
                // Detach all relationships
                $this->teacherToDelete->subjects()->detach();
                $this->teacherToDelete->academicGroups()->detach();
                $this->teacherToDelete->academicLevels()->detach();
                $this->teacherToDelete->assignedStudents()->detach();

                // Delete teacher record
                $this->teacherToDelete->delete();

                // Optionally delete user if they have no other roles
                $user = $this->teacherToDelete->user;
                if ($user && $user->roles()->count() <= 1) {
                    $user->delete();
                }
            });

            $this->showDeleteModal = false;
            $this->teacherToDelete = null;
            session()->flash('message', 'Teacher deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete teacher: '.$e->getMessage());
        }
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->js('window.Modal.close("teacher-form")');
    }

    public function resetForm(): void
    {
        $this->reset([
            'name', 'email', 'password', 'specialization', 'biography',
            'academicGroupId', 'academicLevelId', 'selectedSubjects',
            'isEditing', 'editingTeacherId', 'existingUser', 'userExists',
        ]);
        $this->academicLevels = [];
        $this->availableSubjects = [];
    }

    public function render()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage teachers.');
        }

        // Build query with filters
        $query = Teacher::with(['user', 'academicGroups', 'academicLevels', 'subjects', 'assignedStudents']);

        // Apply school context
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        // Search filter
        if ($this->searchTerm) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            });
        }

        // Academic Group filter
        if ($this->filterAcademicGroup) {
            $query->whereHas('academicGroups', function ($q) {
                $q->where('academic_groups.id', $this->filterAcademicGroup);
            });
        }

        // Academic Level filter
        if ($this->filterAcademicLevel) {
            $query->whereHas('academicLevels', function ($q) {
                $q->where('academic_levels.id', $this->filterAcademicLevel);
            });
        }

        // Subject filter
        if ($this->filterSubject) {
            $query->whereHas('subjects', function ($q) {
                $q->where('academic_subjects.id', $this->filterSubject);
            });
        }

        // Specialization filter
        if ($this->filterSpecialization) {
            $query->where('specialization', 'like', '%'.$this->filterSpecialization.'%');
        }

        $teachers = $query->latest()->paginate(10);

        // Get filter options - SCOPED TO CURRENT SCHOOL
        // Academic groups that the school has adopted
        $filterAcademicGroups = $schoolId
            ? AcademicGroup::forSchool($schoolId)->orderBy('name')->get()
            : collect();

        // Academic levels - get from school's adopted groups
        if ($this->filterAcademicGroup && $schoolId) {
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();
            $filterAcademicLevels = $availableLevels->where('academic_group_id', $this->filterAcademicGroup)
                ->sortBy('name')
                ->values();
        } elseif ($schoolId) {
            $school = School::find($schoolId);
            $filterAcademicLevels = $school->getAvailableAcademicLevels()
                ->sortBy('name')
                ->values();
        } else {
            $filterAcademicLevels = collect();
        }

        // Subjects - from school's adopted academic levels
        if ($schoolId) {
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();
            $levelIds = $availableLevels->pluck('id');
            $filterSubjects = AcademicSubject::with('academicLevel')
                ->whereIn('academic_level_id', $levelIds)
                ->orderBy('name')
                ->get();
        } else {
            $filterSubjects = collect();
        }

        // Specializations - from teachers in current school
        $filterSpecializations = $schoolId
            ? Teacher::where('school_id', $schoolId)
                ->whereNotNull('specialization')
                ->distinct()
                ->pluck('specialization')
                ->filter()
                ->sort()
                ->values()
            : collect();

        // Count active filters
        $activeFiltersCount = collect([
            $this->filterAcademicGroup,
            $this->filterAcademicLevel,
            $this->filterSubject,
            $this->filterSpecialization,
        ])->filter()->count();

        return view('livewire.administrators.teacher-management', [
            'teachers' => $teachers,
            'filterAcademicGroups' => $filterAcademicGroups,
            'filterAcademicLevels' => $filterAcademicLevels,
            'filterSubjects' => $filterSubjects,
            'filterSpecializations' => $filterSpecializations,
            'activeFiltersCount' => $activeFiltersCount,
        ]);
    }
}
