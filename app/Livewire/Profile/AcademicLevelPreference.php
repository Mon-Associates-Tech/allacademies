<?php

namespace App\Livewire\Profile;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\User;
use App\Support\GradingSystemResolver;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcademicLevelPreference extends Component
{
    public ?int $selectedAcademicLevelId = null;

    public ?int $targetUserId = null;

    public ?User $targetUser = null;

    public bool $isEditingOwnProfile = true;

    public bool $canEdit = false;

    public string $currentGradingSystem = '';

    public array $academicGroups = [];

    protected $rules = [
        'selectedAcademicLevelId' => 'nullable|exists:academic_levels,id',
    ];

    public function mount(?int $userId = null): void
    {
        // Determine which user we're editing
        if ($userId && $userId !== Auth::id()) {
            $this->targetUserId = $userId;
            $this->targetUser = User::with(['student', 'preferredAcademicLevel.academicGroup'])->find($userId);
            $this->isEditingOwnProfile = false;
        } else {
            $this->targetUserId = Auth::id();
            $this->targetUser = Auth::user()->load(['student', 'preferredAcademicLevel.academicGroup']);
            $this->isEditingOwnProfile = true;
        }

        // Determine if user can edit
        $this->canEdit = $this->determineCanEdit();

        // Load current selection
        $this->loadCurrentSelection();

        // Load available academic levels
        $this->loadAcademicLevels();

        // Get current grading system
        $this->currentGradingSystem = GradingSystemResolver::getSystemName($this->targetUser);
    }

    protected function determineCanEdit(): bool
    {
        $authUser = Auth::user();

        // If editing own profile
        if ($this->isEditingOwnProfile) {
            // Students cannot set their own academic level
            return $authUser->canSetOwnAcademicLevel();
        }

        // If editing another user's profile
        // Only teachers, admins, and owners can edit student academic levels
        if (in_array($authUser->role->value ?? $authUser->role, ['teacher', 'admin', 'owner'])) {
            // Can only edit if target user is a student
            return $this->targetUser && $this->targetUser->student;
        }

        return false;
    }

    protected function loadCurrentSelection(): void
    {
        if (! $this->targetUser) {
            return;
        }

        // For students, show their assigned academic level (from student record)
        if ($this->targetUser->student && $this->targetUser->student->academic_level_id) {
            $this->selectedAcademicLevelId = $this->targetUser->student->academic_level_id;
        } else {
            // For non-students, show their preferred academic level
            $this->selectedAcademicLevelId = $this->targetUser->preferred_academic_level_id;
        }
    }

    protected function loadAcademicLevels(): void
    {
        $this->academicGroups = AcademicGroup::with(['academicLevels' => function ($query) {
            $query->orderBy('name');
        }])
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'tag' => $group->tag,
                    'levels' => $group->academicLevels->map(function ($level) {
                        return [
                            'id' => $level->id,
                            'name' => $level->name,
                            'label' => $level->label,
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();
    }

    public function updatedSelectedAcademicLevelId(): void
    {
        $this->updateGradingSystemPreview();
    }

    protected function updateGradingSystemPreview(): void
    {
        if ($this->selectedAcademicLevelId) {
            $level = AcademicLevel::with('academicGroup')->find($this->selectedAcademicLevelId);
            if ($level && $level->academicGroup) {
                $tag = $level->academicGroup->tag;
                $this->currentGradingSystem = match ($tag) {
                    'basic' => 'BECE Grading System',
                    'senior' => 'WASSCE Grading System',
                    'university' => 'University Grading System',
                    default => 'BECE Grading System',
                };
            }
        } else {
            $this->currentGradingSystem = 'BECE Grading System (Default)';
        }
    }

    public function save(): void
    {
        if (! $this->canEdit) {
            session()->flash('error', 'You do not have permission to change this setting.');

            return;
        }

        $this->validate();

        if (! $this->targetUser) {
            session()->flash('error', 'User not found.');

            return;
        }

        // If target is a student and we're a teacher/admin, update the student's academic level
        if ($this->targetUser->student && ! $this->isEditingOwnProfile) {
            $this->targetUser->student->update([
                'academic_level_id' => $this->selectedAcademicLevelId,
            ]);
            session()->flash('success', 'Student academic level updated successfully.');
        } else {
            // Update the user's preferred academic level
            $this->targetUser->update([
                'preferred_academic_level_id' => $this->selectedAcademicLevelId,
            ]);
            session()->flash('success', 'Academic level preference saved successfully.');
        }

        // Refresh the grading system display
        $this->currentGradingSystem = GradingSystemResolver::getSystemName($this->targetUser->fresh());
    }

    public function getEffectiveAcademicLevelProperty(): ?AcademicLevel
    {
        return $this->targetUser?->getEffectiveAcademicLevel();
    }

    public function getIsStudentProperty(): bool
    {
        return $this->targetUser && $this->targetUser->student;
    }

    public function render()
    {
        return view('livewire.profile.academic-level-preference');
    }
}
