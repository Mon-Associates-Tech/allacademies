<?php

namespace App\Livewire\Guests;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\StudentGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class StudyGroups extends Component
{
    public $showJoinModal = false;

    public $showCreateModal = false;

    public $joinCode = '';

    public $myGroups = [];

    public $publicGroups = [];

    // Create group form fields
    public $name = '';

    public $description = '';

    public $academicGroupId = '';

    public $academicLevelId = '';

    public $subjectId = '';

    public $isPrivate = false;

    public $academicGroups = [];

    public $academicLevels = [];

    public $subjects = [];

    public function mount()
    {
        $this->loadGroups();
        $this->loadFormData();
    }

    public function loadGroups()
    {
        $user = Auth::user();

        if ($user->student) {
            // Groups user is member of - fix the relationship
            $this->myGroups = StudentGroup::whereHas('students', function ($q) use ($user) {
                $q->where('students.id', $user->student->id);
            })->get();
        }

        // Public groups available to join
        $this->publicGroups = StudentGroup::where('is_approved', true)
            ->where('is_private', false)
            ->whereDoesntHave('students', function ($q) use ($user) {
                if ($user->student) {
                    $q->where('students.id', $user->student->id);
                }
            })
            ->take(10)
            ->get();
    }

    public function loadFormData()
    {
        $this->academicGroups = AcademicGroup::all();
        $this->academicLevels = AcademicLevel::all();
        $this->subjects = AcademicSubject::all();
    }

    public function openJoinModal()
    {
        $this->showJoinModal = true;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function createGroup()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'academicGroupId' => 'required|exists:academic_groups,id',
            'academicLevelId' => 'required|exists:academic_levels,id',
            'subjectId' => 'required|exists:academic_subjects,id',
        ]);

        $user = Auth::user();

        if (! $user->student) {
            session()->flash('error', 'Only students can create study groups.');

            return;
        }

        $group = StudentGroup::create([
            'name' => $this->name,
            'description' => $this->description,
            'creator_id' => $user->id,
            'academic_group_id' => $this->academicGroupId,
            'academic_level_id' => $this->academicLevelId,
            'subject_id' => $this->subjectId,
            'is_private' => $this->isPrivate,
            'is_approved' => false, // Requires approval
            'join_code' => $this->isPrivate ? Str::upper(Str::random(8)) : null,
        ]);

        session()->flash('success', 'Study group created successfully! It will be live once approved by an administrator.');
        $this->closeCreateModal();
        $this->loadGroups();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function resetCreateForm()
    {
        $this->name = '';
        $this->description = '';
        $this->academicGroupId = '';
        $this->academicLevelId = '';
        $this->subjectId = '';
        $this->isPrivate = false;
    }

    public function joinWithCode()
    {
        $user = Auth::user();

        if (! $user->student) {
            session()->flash('error', 'Only students can join study groups.');

            return;
        }

        $group = StudentGroup::where('join_code', $this->joinCode)
            ->where('is_approved', true)
            ->first();

        if (! $group) {
            session()->flash('error', 'Invalid join code or group not approved yet.');

            return;
        }

        // Check if already a member
        if ($group->students()->where('students.id', $user->student->id)->exists()) {
            session()->flash('error', 'You are already a member of this group.');

            return;
        }

        // Add student to group using the pivot table
        $group->students()->attach($user->student->id);

        session()->flash('success', 'Successfully joined the study group!');
        $this->closeJoinModal();
        $this->loadGroups();
    }

    public function closeJoinModal()
    {
        $this->showJoinModal = false;
        $this->joinCode = '';
    }

    public function joinPublicGroup($groupId)
    {
        $user = Auth::user();

        if (! $user->student) {
            session()->flash('error', 'Only students can join study groups.');

            return;
        }

        $group = StudentGroup::where('id', $groupId)
            ->where('is_approved', true)
            ->where('is_private', false)
            ->first();

        if (! $group) {
            session()->flash('error', 'Group not found or not available for joining.');

            return;
        }

        // Check if already a member
        if ($group->students()->where('students.id', $user->student->id)->exists()) {
            session()->flash('error', 'You are already a member of this group.');

            return;
        }

        // Add student to group using the pivot table
        $group->students()->attach($user->student->id);

        session()->flash('success', 'Successfully joined the study group!');
        $this->loadGroups();
    }

    public function render()
    {
        return view('livewire.guests.study-groups');
    }
}
