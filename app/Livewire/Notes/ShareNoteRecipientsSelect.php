<?php

namespace App\Livewire\Notes;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\StudentGroup;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShareNoteRecipientsSelect extends Component
{
    public string $search = '';
    public array $selected = [];
    public string $shareType = 'individual';
    public string $placeholder = 'Search and select recipients...';
    public string $name = 'selectedRecipients';
    public ?int $schoolId = null;

    public bool $dropdownOpen = false;

    protected $listeners = [
        'reset-component' => 'resetComponent',
    ];

    public function mount(
        string $shareType,
        array $selected = [],
        ?int $schoolId = null,
        string $placeholder = 'Search and select recipients...',
        string $name = 'selectedRecipients'
    ): void
    {
        $this->shareType = $shareType;
        $this->selected = $selected;
        $this->schoolId = $schoolId;
        $this->placeholder = $placeholder;
        $this->name = $name;
    }

    #[Computed]
    public function filteredItems(): array
    {
        // Don't load anything if dropdown is closed and no search
        if (!$this->dropdownOpen && empty($this->search)) {
            return [];
        }

        return match($this->shareType) {
            'individual' => $this->loadIndividuals(),
            'academic_group' => $this->loadAcademicGroups(),
            'academic_level' => $this->loadAcademicLevels(),
            'student_group' => $this->loadStudentGroups(),
            default => [],
        };
    }

    #[Computed]
    public function selectedItems(): array
    {
        if (empty($this->selected)) {
            return [];
        }

        return match($this->shareType) {
            'individual' => $this->loadSelectedIndividuals(),
            'academic_group' => $this->loadSelectedAcademicGroups(),
            'academic_level' => $this->loadSelectedAcademicLevels(),
            'student_group' => $this->loadSelectedStudentGroups(),
            default => [],
        };
    }

    private function loadIndividuals(): array
    {
        $query = User::query()
            ->where('id', '!=', auth()->id())
            ->where('is_active', true);

        // Only filter by school if provided; for individual searches schoolId is optional
        if (!empty($this->schoolId)) {
            $query->where('school_id', $this->schoolId);
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name . ' (' . $user->email . ')',
            ])
            ->toArray();
    }

    private function loadSelectedIndividuals(): array
    {
        return User::whereIn('id', $this->selected)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name . ' (' . $user->email . ')',
            ])
            ->toArray();
    }

    private function loadAcademicGroups(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        $query = AcademicGroup::forSchool($this->schoolId);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('tag', 'LIKE', '%' . $this->search . '%');
            });
        }

        return $query->withCount(['students' => function($q) {
                $q->where('school_id', $this->schoolId);
            }])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name . ' (' . $group->students_count . ' students)',
            ])
            ->toArray();
    }

    private function loadSelectedAcademicGroups(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        return AcademicGroup::whereIn('id', $this->selected)
            ->withCount(['students' => function($q) {
                $q->where('school_id', $this->schoolId);
            }])
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name . ' (' . $group->students_count . ' students)',
            ])
            ->toArray();
    }

    private function loadAcademicLevels(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        $query = AcademicLevel::forSchool($this->schoolId)
            ->with('academicGroup');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('label', 'LIKE', '%' . $this->search . '%');
            });
        }

        return $query->withCount(['students' => function($q) {
                $q->where('school_id', $this->schoolId);
            }])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn($level) => [
                'id' => $level->id,
                'name' => $level->name . ' (' . $level->students_count . ' students)',
            ])
            ->toArray();
    }

    private function loadSelectedAcademicLevels(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        return AcademicLevel::whereIn('id', $this->selected)
            ->withCount(['students' => function($q) {
                $q->where('school_id', $this->schoolId);
            }])
            ->get()
            ->map(fn($level) => [
                'id' => $level->id,
                'name' => $level->name . ' (' . $level->students_count . ' students)',
            ])
            ->toArray();
    }

    private function loadStudentGroups(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        $query = StudentGroup::where('school_id', $this->schoolId)
            ->with(['academicGroup', 'academicLevel', 'academicSubject', 'teacher.user'])
            ->active();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $this->search . '%');
            });
        }

        return $query->withCount('students')
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->getDisplayName(),
            ])
            ->toArray();
    }

    private function loadSelectedStudentGroups(): array
    {
        if (empty($this->schoolId)) {
            return [];
        }

        return StudentGroup::whereIn('id', $this->selected)
            ->with(['academicGroup', 'academicLevel', 'academicSubject', 'teacher.user'])
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->getDisplayName(),
            ])
            ->toArray();
    }

    public function toggleDropdown(): void
    {
        $this->dropdownOpen = !$this->dropdownOpen;
    }

    public function closeDropdown(): void
    {
        $this->dropdownOpen = false;
        $this->search = '';
    }

    public function selectItem($value): void
    {
        if (in_array($value, $this->selected)) {
            $this->selected = array_values(array_filter($this->selected, fn($v) => $v !== $value));
        } else {
            $this->selected[] = $value;
        }
        $this->search = '';

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function removeItem($value): void
    {
        $this->selected = array_values(array_filter($this->selected, fn($v) => $v !== $value));

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function clearAll(): void
    {
        $this->selected = [];
        $this->search = '';

        $this->dispatch('selection-changed', [
            'name' => $this->name,
            'selected' => $this->selected,
        ]);
    }

    public function updatedSearch(): void
    {
        if (!$this->dropdownOpen) {
            $this->dropdownOpen = true;
        }
    }

    public function resetComponent(): void
    {
        $this->selected = [];
        $this->search = '';
        $this->dropdownOpen = false;
    }

    public function render()
    {
        return view('livewire.notes.share-note-recipients-select');
    }
}
