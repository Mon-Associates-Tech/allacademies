<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Role;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\User;
use App\Services\StudentUsernameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManagement extends Component
{
    use WithPagination;

    public $firstName;

    public $lastName;

    public $email;

    public $password;

    public $username;

    public $parentEmail;

    // Extended user profile fields
    public $otherNames;
    public $phone;
    public $gender;
    public $countryCode;
    public $country;
    public $region;
    public $city;
    public $profileImageUrl;
    public $coverImage;
    public $userStatus = 'active';
    public $isActive = true;

    // Extended student profile fields
    public $dateOfBirth;
    public $bloodGroup;
    public $address;
    public $parentName;
    public $parentPhone;
    public $emergencyContact;
    public $idCardIssueDate;
    public $idCardExpiryDate;
    public $admissionDate;
    public $graduationDate;
    public $studentStatus = 'active';

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

    public $standaloneForm = false; // true when on dedicated create/edit pages

    public $showForm = false;

    public $formMode = 'create';

    public $showFormModal = false;

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

    // Filter properties
    public $filterAcademicGroup = '';

    public $filterAcademicLevel = '';

    public $filterStudentGroup = '';

    public $filterTeacher = '';

    public $filterSubject = '';

    // Collections
    public $studentGroups;

    public $academicGroups;

    public $academicLevels = [];

    public $levelSubjects = [];

    public $availableAdditionalSubjects = [];

    public $availableTeachers = [];

    public $allTeachers = [];

    public $viewMode = 'card'; // 'card' or 'list'

    protected $rules = [
        'firstName' => 'required|min:2',
        'lastName' => 'required|min:2',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'nullable|min:8',
        'otherNames' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:50',
        'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
        'countryCode' => 'nullable|string|max:5',
        'country' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'profileImageUrl' => 'nullable|url',
        'coverImage' => 'nullable|url',
        'userStatus' => 'nullable|string|max:50',
        'isActive' => 'boolean',
        'dateOfBirth' => 'nullable|date',
        'bloodGroup' => 'nullable|string|max:10',
        'address' => 'nullable|string|max:500',
        'parentName' => 'nullable|string|max:255',
        'parentPhone' => 'nullable|string|max:50',
        'emergencyContact' => 'nullable|string|max:255',
        'idCardIssueDate' => 'nullable|date',
        'idCardExpiryDate' => 'nullable|date|after_or_equal:idCardIssueDate',
        'admissionDate' => 'nullable|date',
        'graduationDate' => 'nullable|date|after_or_equal:admissionDate',
        'studentStatus' => 'nullable|string|max:50',
        'studentGroupId' => 'nullable|exists:student_groups,id',
        'academicGroupId' => 'required|exists:academic_groups,id',
        'academicLevelId' => 'required|exists:academic_levels,id',
        'parentEmail' => 'nullable|email',
        'primaryTeacherId' => 'nullable|exists:teachers,id',
        'selectedTeachers' => 'nullable|array',
        'selectedTeachers.*' => 'exists:teachers,id',
        'additionalSubjects' => 'nullable|array',
        'additionalSubjects.*' => 'exists:academic_subjects,id',
        'removedSubjects' => 'nullable|array',
        'removedSubjects.*' => 'exists:academic_subjects,id',
    ];

    public function mount(?int $student = null): void
    {
        $schoolId = getSchoolId();

        $routeName = request()?->route()?->getName();
        if ($routeName === 'admin.students.create') {
            $this->standaloneForm = true;
            $this->showFormModal = false;
            $this->formMode = 'create';
            $this->isEditing = false;
        }

        if ($routeName === 'admin.students.edit' && $student) {
            $this->standaloneForm = true;
            $this->showFormModal = false;
            $this->formMode = 'edit';
            $this->isEditing = true;
            // load student into form without opening modal
            $this->edit($student, true);
        }

        // Load school-scoped data
        if ($schoolId) {
            $this->studentGroups = StudentGroup::where('school_id', $schoolId)->get();

            // Get academic groups that this school has adopted
            $this->academicGroups = AcademicGroup::forSchool($schoolId)->get();

            $this->allTeachers = Teacher::where('school_id', $schoolId)->with('user')->get();
        } else {
            // For cross-school users without school context
            $this->studentGroups = collect();
            $this->academicGroups = collect();
            $this->allTeachers = collect();
        }
    }

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'card' ? 'list' : 'card';
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
        $this->formMode = 'create';
    }

    public function updatedFilterAcademicGroup(): void
    {
        $this->filterAcademicLevel = ''; // Reset level when group changes
        $this->resetPage();
    }

    public function updatedFilterAcademicLevel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStudentGroup(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTeacher(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSubject(): void
    {
        $this->resetPage();
    }

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterAcademicGroup = '';
        $this->filterAcademicLevel = '';
        $this->filterStudentGroup = '';
        $this->filterTeacher = '';
        $this->filterSubject = '';
        $this->searchTerm = '';
        $this->resetPage();
    }


    public function resetForm(): void
    {

        $this->showFormModal = false;
        $this->formMode = 'create';
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->password = 'pass1234';
        $this->username = '';
        $this->parentEmail = '';
        $this->otherNames = '';
        $this->phone = '';
        $this->gender = '';
        $this->countryCode = '';
        $this->country = '';
        $this->region = '';
        $this->city = '';
        $this->profileImageUrl = '';
        $this->coverImage = '';
        $this->userStatus = 'active';
        $this->isActive = true;
        $this->dateOfBirth = null;
        $this->bloodGroup = '';
        $this->address = '';
        $this->parentName = '';
        $this->parentPhone = '';
        $this->emergencyContact = '';
        $this->idCardIssueDate = null;
        $this->idCardExpiryDate = null;
        $this->admissionDate = null;
        $this->graduationDate = null;
        $this->studentStatus = 'active';
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

    public function hideForm(): void
    {
        if ($this->standaloneForm) {
            $this->resetForm();
            redirect()->route('admin.student-management')->send();
        } else {
            $this->showFormModal = false;
            $this->resetForm();
        }
    }

    public function updatedAcademicGroupId(): void
    {
        // Reset dependent fields
        $this->academicLevelId = '';
        $this->primaryTeacherId = '';
        $this->selectedTeachers = [];
        $this->additionalSubjects = [];
        $this->removedSubjects = [];
        $this->levelSubjects = [];
        $this->availableAdditionalSubjects = [];
        $this->availableTeachers = [];

        $schoolId = getSchoolId();

        if ($this->academicGroupId && $schoolId) {
            // Get academic levels for this group that the school has adopted
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();

            $this->academicLevels = $availableLevels->where('academic_group_id', $this->academicGroupId);
            $this->loadTeachersForGroupManagement();
        } else {
            $this->academicLevels = collect();
        }

        // Clear validation errors
        $this->resetValidation(['academicLevelId', 'primaryTeacherId']);

        // Force re-render of the component
        $this->dispatch('$refresh');
    }
    // Teacher Management Methods

    public function loadTeachersForGroupManagement(): void
    {
        if (! $this->academicGroupId) {
            return;
        }

        $schoolId = getSchoolId();
        if (!$schoolId) return;

        $group = AcademicGroup::with('teachers')->find($this->academicGroupId);
        $assignedTeacherIds = $group->teachers->pluck('id')->toArray();

        $this->teachersToAssignToGroup = Teacher::where('school_id', $schoolId)
            ->whereNotIn('id', $assignedTeacherIds)
            ->with('user')->get();
    }

    public function updatedAcademicLevelId(): void
    {
        $this->primaryTeacherId = '';
        $this->selectedTeachers = [];
        $this->additionalSubjects = [];
        $this->removedSubjects = [];

        $schoolId = getSchoolId();

        if ($this->academicLevelId && $schoolId) {
            // Load subjects for this academic level
            $this->levelSubjects = AcademicSubject::where('academic_level_id', $this->academicLevelId)->get();

            // Load subjects from other levels that the school has adopted
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();
            $availableLevelIds = $availableLevels->where('id', '!=', $this->academicLevelId)->pluck('id');

            $this->availableAdditionalSubjects = AcademicSubject::whereIn('academic_level_id', $availableLevelIds)->get();

            // Load teachers who belong to this academic level (school scoped)
            $this->availableTeachers = Teacher::where('school_id', $schoolId)
                ->whereHas('academicLevels', function ($query) {
                    $query->where('academic_level_id', $this->academicLevelId);
                })
                ->with('user')
                ->get();

            $this->loadTeachersForLevelManagement();
        } else {
            $this->levelSubjects = [];
            $this->availableAdditionalSubjects = [];
            $this->availableTeachers = [];
        }
    }
    public function loadTeachersForLevelManagement(): void
    {
        if (! $this->academicLevelId) {
            return;
        }

        $schoolId = getSchoolId();
        if (!$schoolId) return;

        $level = AcademicLevel::with('teachers')->find($this->academicLevelId);
        $assignedTeacherIds = $level->teachers->pluck('id')->toArray();

        $this->teachersToAssignToLevel = Teacher::where('school_id', $schoolId)
            ->whereNotIn('id', $assignedTeacherIds)
            ->with('user')->get();
    }

    public function showManageTeachersModal(): void
    {
        $this->showManageTeachers = true;
        $this->loadTeachersForGroupManagement();
        if ($this->academicLevelId) {
            $this->loadTeachersForLevelManagement();
        }
    }

    public function assignTeachersToGroup(): void
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

    public function assignTeachersToLevel(): void
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
        $this->availableTeachers = Teacher::whereHas('academicLevels', function ($query) {
            $query->where('academic_level_id', $this->academicLevelId);
        })->with('user')->get();

        session()->flash('message', 'Teachers assigned to academic level successfully!');
    }

    public function removeTeacherFromGroup($teacherId): void
    {
        $group = AcademicGroup::find($this->academicGroupId);
        $group->teachers()->detach($teacherId);

        $this->loadTeachersForGroupManagement();
        session()->flash('message', 'Teacher removed from academic group successfully!');
    }

    public function removeTeacherFromLevel($teacherId): void
    {
        $level = AcademicLevel::find($this->academicLevelId);
        $level->teachers()->detach($teacherId);

        $this->loadTeachersForLevelManagement();

        // Refresh available teachers for student assignment
        $this->availableTeachers = Teacher::whereHas('academicLevels', function ($query) {
            $query->where('academic_level_id', $this->academicLevelId);
        })->with('user')->get();

        session()->flash('message', 'Teacher removed from academic level successfully!');
    }

    public function showCreateTeacherModal(): void
    {
        $this->showTeacherModal = true;
        $this->isEditingTeacher = false;
        $this->resetTeacherForm();
    }

    public function resetTeacherForm(): void
    {
        $this->teacherName = '';
        $this->teacherEmail = '';
        $this->teacherPassword = '';
        $this->resetValidation(['teacherName', 'teacherEmail', 'teacherPassword']);
    }

    public function createTeacher(): void
    {

        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school before creating a teacher.');
            return;
        }

        $this->validate([
            'teacherName' => 'required|min:3',
            'teacherEmail' => 'required|email|unique:users,email',
            'teacherPassword' => 'required|min:8',
        ]);

        DB::transaction(function () use ($schoolId) {
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

            // Get school ID from context

            // Create teacher record
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'school_id' => $schoolId, // ✅ Explicitly set school_id
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
            $this->availableTeachers = Teacher::whereHas('academicLevels', function ($query) {
                $query->where('academic_level_id', $this->academicLevelId);
            })->with('user')->get();
        }

        session()->flash('message', 'Teacher created and assigned successfully!');

        $this->js('window.Modal.close("teacher-add-form")');
    }

    /**
     * @throws \Throwable
     */
    public function create(): void
    {
        // Default password if user leaves it blank
        if (empty($this->password)) {
            $this->password = 'pass1234';
        }

        //$this->validate();

        $schoolId = getSchoolId() ?? auth()->user()->school_id;

        if (! $schoolId) {
            session()->flash('error', 'Please select a school before creating a student.');
            return;
        }

        // NEW: Check subscription capacity (if school is available)
        $school = auth()->user()->school;
        if ($school && ! $school->canAddStudents(1)) {
            $remaining = $school->getRemainingStudentCapacity();
            $this->addError('general',
                "Cannot add more students. Remaining capacity: {$remaining}. ".
                'Please renew your subscription or remove some students.'
            );
           // return;
        }

        DB::transaction(function () use ($schoolId) {

            // Create user with username login type; email optional
            $user = User::create([
                'role' => 'student',
                'name' => trim("{$this->firstName} {$this->lastName}"),
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'other_names' => $this->otherNames,
                'email' => $this->email ?: null,
                'login_type' => $this->email ? 'email': 'username',
                'email_verified_at' => $this->email ? null : now(),
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
                'gender' => $this->gender,
                'country_code' => $this->countryCode,
                'country' => $this->country,
                'region' => $this->region,
                'city' => $this->city,
                'avatar' => $this->profileImageUrl,
                'cover_image' => $this->coverImage,
                'status' => $this->userStatus,
                'is_active' => (bool) $this->isActive,
            ]);

            // Assign student role
            $studentRole = Role::where('name', 'student')->first();
            $user->roles()->attach($studentRole);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'school_id' => $schoolId, // Explicitly set school_id
                'student_group_id' => $this->studentGroupId,
                'academic_group_id' => $this->academicGroupId,
                'academic_level_id' => $this->academicLevelId,
                'student_id' => Student::generateStudentId($schoolId),
                'parent_email' => $this->parentEmail,
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'other_name' => $this->otherNames,
                'date_of_birth' => $this->dateOfBirth,
                'blood_group' => $this->bloodGroup,
                'address' => $this->address,
                'parent_name' => $this->parentName,
                'parent_phone' => $this->parentPhone,
                'emergency_contact' => $this->emergencyContact,
                'id_card_issue_date' => $this->idCardIssueDate,
                'id_card_expiry_date' => $this->idCardExpiryDate,
                'admission_date' => $this->admissionDate,
                'graduation_date' => $this->graduationDate,
                'status' => $this->studentStatus,
            ]);

            if(!$user->username || !$this->email){
                // Generate and persist username now that student has an ID
                $generatedUsername = app(StudentUsernameService::class)->generate($student);
                $user->update([
                    'username' => $generatedUsername,
                    'login_type' => 'username',
                ]);
            }


            // Assign teachers to student
            if (! empty($this->selectedTeachers)) {
                $teacherData = [];
                foreach ($this->selectedTeachers as $teacherId) {
                    $teacherData[$teacherId] = [
                        'is_primary' => $teacherId === $this->primaryTeacherId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $student->teachers()->attach($teacherData);
            }

            // Handle individual subject assignments
            $this->assignIndividualSubjects($student);

            // Log activity
            $student->logActivity('create', 'Student Created', 'student', [
                'student_name' => trim("{$this->firstName} {$this->lastName}"),
                'username' => $generatedUsername,
                'email' => $this->email,
                'student_id' => $student->student_id,
                'academic_group_id' => $this->academicGroupId,
                'academic_level_id' => $this->academicLevelId,
                'student_group_id' => $this->studentGroupId,
                'teachers_assigned' => $this->selectedTeachers ?? [],
                'created_by' => auth()->user()?->name ?? 'Unknown',
            ]);
        });

        $this->resetForm();
        $this->resetPage();
        $this->dispatch('$refresh');
        session()->flash('message', 'Student created successfully! They have access to all subjects from their academic level plus any additional subjects assigned.');

        if ($this->standaloneForm) {
            $this->redirectRoute('admin.student-management');
        } else {
            $this->js('window.Modal.close("student-add-form")');
        }
    }

    private function assignIndividualSubjects($student): void
    {
        // Clear existing individual assignments
        $student->individualSubjects()->detach();

        $currentUserId = Auth::id();
        $now = now();

        // Add additional subjects (subjects not in their academic level)
        if (! empty($this->additionalSubjects)) {
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
        if (! empty($this->removedSubjects)) {
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

    public function closeManageTeachersModal(): void
    {
        $this->showManageTeachers = false;
        $this->selectedTeachersForGroup = [];
        $this->selectedTeachersForLevel = [];
    }

    public function closeTeacherModal(): void
    {
        $this->showTeacherModal = false;
        $this->resetTeacherForm();
    }

    public function updatedPrimaryTeacherId(): void
    {
        if ($this->primaryTeacherId && ! in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->selectedTeachers[] = $this->primaryTeacherId;
        }
    }

    public function updatedSelectedTeachers(): void
    {
        if ($this->primaryTeacherId && ! in_array($this->primaryTeacherId, $this->selectedTeachers)) {
            $this->primaryTeacherId = '';
        }
    }

    public function edit($studentId, bool $suppressModal = false): void
    {
        $this->formMode = 'edit';
        $this->showFormModal = $this->standaloneForm ? false : true;
        $this->isEditing = true;
        $this->editingStudentId = $studentId;

        // Load student with all necessary relationships
        $student = Student::with([
            'user',
            'individualSubjects',
            'teachers',
            'academicLevel.academicSubjects',
        ])->findOrFail($studentId);

        // Prefer user columns, fall back to student columns for legacy rows
        $this->firstName = $student->user->first_name ?? $student->first_name;
        $this->lastName = $student->user->last_name ?? $student->last_name;
        $this->otherNames = $student->user->other_names ?? $student->other_name;
        $this->email = $student->user->email;
        $this->username = $student->user->username;
        $this->parentEmail = $student->parent_email;
        $this->phone = $student->user->phone;
        $this->gender = $student->user->gender;
        $this->countryCode = $student->user->country_code;
        $this->country = $student->user->country;
        $this->region = $student->user->region;
        $this->city = $student->user->city;
        $this->profileImageUrl = $student->user->avatar;
        $this->coverImage = $student->user->cover_image;
        $this->userStatus = $student->user->status;
        $this->isActive = (bool) $student->user->is_active;
        $this->dateOfBirth = $student->date_of_birth;
        $this->bloodGroup = $student->blood_group;
        $this->address = $student->address;
        $this->parentName = $student->parent_name;
        $this->parentPhone = $student->parent_phone;
        $this->emergencyContact = $student->emergency_contact;
        $this->idCardIssueDate = $student->id_card_issue_date;
        $this->idCardExpiryDate = $student->id_card_expiry_date;
        $this->admissionDate = $student->admission_date;
        $this->graduationDate = $student->graduation_date;
        $this->studentStatus = $student->status;
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
            $this->availableTeachers = Teacher::whereHas('academicLevels', function ($query) {
                $query->where('academic_level_id', $this->academicLevelId);
            })->with('user')->get();
        }

        // Set selected teachers and primary teacher
        $this->selectedTeachers = $student->teachers()->pluck('teachers.id')->toArray();
        $primaryTeacher = $student->teachers->where('pivot.is_primary', true)->first();
        $this->primaryTeacherId = $primaryTeacher ? $primaryTeacher->id : '';

        // Set individual subject assignments
        $levelSubjectIds = collect($this->levelSubjects)->pluck('id')->toArray();

        $this->additionalSubjects = $student->individualSubjects()
            ->wherePivot('is_active', true)
            ->whereNotIn('academic_subjects.id', $levelSubjectIds)
            ->pluck('academic_subjects.id')
            ->toArray();

        $this->removedSubjects = $student->individualSubjects()
            ->wherePivot('is_active', false)
            ->pluck('academic_subjects.id')
            ->toArray();

        if (! $this->standaloneForm && ! $suppressModal) {
            $this->js('window.Modal.open("student-add-form")');
        }
    }

    public function update(): void
    {
        $student = Student::with(['user', 'teachers'])->findOrFail($this->editingStudentId);

        $this->validate([
            'firstName' => 'required|min:2',
            'lastName' => 'required|min:2',
            'email' => 'nullable|email|unique:users,email,'.$student->user_id,
            'academicGroupId' => 'required|exists:academic_groups,id',
            'academicLevelId' => 'required|exists:academic_levels,id',
            'primaryTeacherId' => 'nullable|exists:teachers,id',
            'selectedTeachers' => 'nullable|array',
            'selectedTeachers.*' => 'exists:teachers,id',
            'additionalSubjects' => 'nullable|array',
            'additionalSubjects.*' => 'exists:academic_subjects,id',
            'removedSubjects' => 'nullable|array',
            'removedSubjects.*' => 'exists:academic_subjects,id',
            'parentEmail' => 'nullable|email',
        ]);

        DB::transaction(function () use ($student) {
            // Update user first_name / last_name to keep denormalized name consistent
            $userData = [
                'name' => trim("{$this->firstName} {$this->lastName}"),
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'other_names' => $this->otherNames,
                'email' => $this->email ?: null,
                'login_type' => 'username',
                'phone' => $this->phone,
                'gender' => $this->gender,
                'country_code' => $this->countryCode,
                'country' => $this->country,
                'region' => $this->region,
                'city' => $this->city,
                'avatar' => $this->profileImageUrl,
                'cover_image' => $this->coverImage,
                'status' => $this->userStatus,
                'is_active' => (bool) $this->isActive,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            $student->user->update($userData);

            // Keep student table in sync for reporting queries using student.* columns
            $student->update([
                'student_group_id' => $this->studentGroupId,
                'academic_group_id' => $this->academicGroupId,
                'academic_level_id' => $this->academicLevelId,
                'parent_email' => $this->parentEmail,
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'other_name' => $this->otherNames,
                'date_of_birth' => $this->dateOfBirth,
                'blood_group' => $this->bloodGroup,
                'address' => $this->address,
                'parent_name' => $this->parentName,
                'parent_phone' => $this->parentPhone,
                'emergency_contact' => $this->emergencyContact,
                'id_card_issue_date' => $this->idCardIssueDate,
                'id_card_expiry_date' => $this->idCardExpiryDate,
                'admission_date' => $this->admissionDate,
                'graduation_date' => $this->graduationDate,
                'status' => $this->studentStatus,
            ]);

            // Update teacher assignments
            if (! empty($this->selectedTeachers)) {
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
        if ($this->standaloneForm) {
            $this->redirectRoute('admin.student-management');
        } else {
            $this->js('window.Modal.close("student-add-form")');
        }
    }

    public function delete($studentId): void
    {
        $student = Student::findOrFail($studentId);
        $userId = $student->user_id;
        $studentName = $student->user?->name ?? 'Unknown';
        $studentEmail = $student->user?->email ?? 'N/A';

        DB::transaction(function () use ($student, $userId, $studentName, $studentEmail) {
            $student->delete();
            User::destroy($userId);

            // Log activity
            Student::logActivityForModel('delete', 'Student Deleted', 'student', [
                'student_name' => $studentName,
                'student_email' => $studentEmail,
                'student_id' => $student->student_id,
                'deleted_by' => auth()->user()?->name ?? 'Unknown',
            ]);
        });

        session()->flash('message', 'Student deleted successfully!');
        $this->js('window.Modal.close("student-delete-form")');
    }

    public function render()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage students.');
        }

        // Start with base query
        $query = Student::query();

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
            $query->where('academic_group_id', $this->filterAcademicGroup);
        }

        // Academic Level filter
        if ($this->filterAcademicLevel) {
            $query->where('academic_level_id', $this->filterAcademicLevel);
        }

        // Student Group filter
        if ($this->filterStudentGroup) {
            $query->where('student_group_id', $this->filterStudentGroup);
        }

        // Teacher filter
        if ($this->filterTeacher) {
            $query->whereHas('teachers', function ($q) {
                $q->where('teachers.id', $this->filterTeacher);
            });
        }

        // Subject filter
        if ($this->filterSubject) {
            $query->where(function ($q) {
                // Students who have the subject through their academic level
                $q->whereHas('academicLevel.academicSubjects', function ($subQuery) {
                    $subQuery->where('academic_subjects.id', $this->filterSubject);
                })
                    // OR students who have the subject individually assigned
                    ->orWhereHas('individualSubjects', function ($subQuery) {
                        $subQuery->where('academic_subjects.id', $this->filterSubject)
                            ->where('student_subject.is_active', true);
                    });
            });
        }

        $students = $query->with([
            'user',
            'studentGroup',
            'academicGroup',
            'academicLevel.academicSubjects',
            'teachers.user',
            'individualSubjects',
        ])->paginate(10);

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

        // Get available filter options - SCOPED TO CURRENT SCHOOL
        // Academic groups that the school has adopted
        $filterAcademicGroups = $schoolId
            ? AcademicGroup::forSchool($schoolId)->get()
            : collect();

        // Academic levels - get from school's adopted groups
        if ($this->filterAcademicGroup && $schoolId) {
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();
            $filterAcademicLevels = $availableLevels->where('academic_group_id', $this->filterAcademicGroup);
        } elseif ($schoolId) {
            $school = School::find($schoolId);
            $filterAcademicLevels = $school->getAvailableAcademicLevels();
        } else {
            $filterAcademicLevels = collect();
        }

        // Student groups (school-specific)
        $filterStudentGroups = $schoolId
            ? StudentGroup::where('school_id', $schoolId)->get()
            : collect();

        // Teachers (school-specific)
        $filterTeachers = $schoolId
            ? Teacher::where('school_id', $schoolId)->with('user')->get()
            : collect();

        // Subjects - from school's adopted academic levels
        if ($schoolId) {
            $school = School::find($schoolId);
            $availableLevels = $school->getAvailableAcademicLevels();
            $levelIds = $availableLevels->pluck('id');
            $filterSubjects = AcademicSubject::with('academicLevel')
                ->whereIn('academic_level_id', $levelIds)
                ->get();
        } else {
            $filterSubjects = collect();
        }

        // Count active filters
        $activeFiltersCount = collect([
            $this->filterAcademicGroup,
            $this->filterAcademicLevel,
            $this->filterStudentGroup,
            $this->filterTeacher,
            $this->filterSubject,
        ])->filter()->count();

        return view('livewire.administrators.student-management', [
            'students' => $students,
            'groupTeachers' => $groupTeachers,
            'levelTeachers' => $levelTeachers,
            'filterAcademicGroups' => $filterAcademicGroups,
            'filterAcademicLevels' => $filterAcademicLevels,
            'filterStudentGroups' => $filterStudentGroups,
            'filterTeachers' => $filterTeachers,
            'filterSubjects' => $filterSubjects,
            'activeFiltersCount' => $activeFiltersCount,
        ]);
    }
}
