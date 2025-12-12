<?php

namespace App\Livewire\Notes;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Note;
use App\Models\StudentGroup;
use App\Models\User;
use App\Services\NoteShareService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShareNote extends Component
{
    public Note $note;
    public string $shareType = 'individual';
    public array $selectedRecipients = [];
    public string $emailInput = '';
    public bool $canEdit = false;
    public bool $notifyRecipients = true;

    protected $listeners = [
        'selection-changed' => 'handleSelectionChanged',
    ];

    protected $rules = [
        'shareType' => 'required|in:individual,academic_group,academic_level,student_group,school_wide,email',
        'selectedRecipients' => 'required_unless:shareType,school_wide,email|array|min:1',
        'emailInput' => 'required_if:shareType,email|email',
        'canEdit' => 'boolean',
        'notifyRecipients' => 'boolean',
    ];

    protected $messages = [
        'selectedRecipients.required_unless' => 'Please select at least one recipient.',
        'selectedRecipients.min' => 'Please select at least one recipient.',
        'emailInput.required_if' => 'Please enter an email address.',
        'emailInput.email' => 'Please enter a valid email address.',
    ];

    public function mount(Note $note): void
    {
        $this->note = $note;
    }

    public function handleSelectionChanged($data): void
    {
        if (isset($data['name']) && $data['name'] === 'selectedRecipients') {
            $this->selectedRecipients = $data['selected'] ?? [];
        }
    }

    public function updatedShareType(): void
    {
        $this->selectedRecipients = [];
    }

    // Lazy loading configuration
    public function getModelClassProperty(): ?string
    {
        return match($this->shareType) {
            'individual' => User::class,
            'academic_group' => AcademicGroup::class,
            'academic_level' => AcademicLevel::class,
            'student_group' => StudentGroup::class,
            default => null,
        };
    }

    public function getSearchColumnsProperty(): array|string
    {
        return match($this->shareType) {
            'individual' => ['name', 'email'],
            'academic_group' => ['name', 'tag'],
            'academic_level' => ['name', 'label'],
            'student_group' => ['name', 'description'],
            default => 'name',
        };
    }

    public function getQueryMethodProperty(): string
    {
        return match($this->shareType) {
            'individual' => 'queryIndividuals',
            'academic_group' => 'queryAcademicGroups',
            'academic_level' => 'queryAcademicLevels',
            'student_group' => 'queryStudentGroups',
            default => '',
        };
    }

    public function getLabelFormatterProperty(): string
    {
        return match($this->shareType) {
            'individual' => 'formatUserLabel',
            'academic_group' => 'formatGroupLabel',
            'academic_level' => 'formatLevelLabel',
            'student_group' => 'formatStudentGroupLabel',
            default => '',
        };
    }

    // Query methods for each type
    public function queryIndividuals(Builder $query): Builder
    {
        return $query->where('school_id', auth()->user()->school_id)
            ->where('id', '!=', auth()->id())
            ->where('is_active', true);
    }

    public function queryAcademicGroups(Builder $query): Builder
    {
        $schoolId = auth()->user()->school_id;

        return $query->whereHas('school', fn($q) => $q->where('id', $schoolId))
            ->orWhereHas('students', fn($q) => $q->where('school_id', $schoolId))
            ->withCount(['students' => function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            }]);
    }

    public function queryAcademicLevels(Builder $query): Builder
    {
        $schoolId = auth()->user()->school_id;

        return $query->whereHas('academicGroup.school', fn($q) => $q->where('id', $schoolId))
            ->orWhereHas('students', fn($q) => $q->where('school_id', $schoolId))
            ->with('academicGroup')
            ->withCount(['students' => function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            }]);
    }

    public function queryStudentGroups(Builder $query): Builder
    {
        return $query->where('school_id', auth()->user()->school_id)
            ->with(['academicGroup', 'academicLevel', 'academicSubject', 'teacher.user'])
            ->withCount('students')
            ->where('is_active', true);
    }

    // Label formatters for each type
    public function formatUserLabel($item): string
    {
        return $item->name . ' (' . $item->email . ')';
    }

    public function formatGroupLabel($item): string
    {
        $count = $item->students_count ?? 0;
        return $item->name . ' (' . $count . ' students)';
    }

    public function formatLevelLabel($item): string
    {
        $count = $item->students_count ?? 0;
        return $item->name . ' (' . $count . ' students)';
    }

    public function formatStudentGroupLabel($item): string
    {
        return $item->getDisplayName();
    }

    public function updatedEmailInput(): void
    {
        $this->validateOnly('emailInput');

        if (!empty($this->emailInput) && filter_var($this->emailInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $this->emailInput)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            if ($user) {
                $this->dispatch('info', message: "This email belongs to {$user->name} in your school.");
            }
        }
    }

    public function getRecipientCountProperty(): int
    {
        if ($this->shareType === 'school_wide') {
            return User::where('school_id', auth()->user()->school_id)
                ->where('id', '!=', auth()->id())
                ->count();
        }

        if ($this->shareType === 'email') {
            return 1;
        }

        if (empty($this->selectedRecipients)) {
            return 0;
        }

        $shareService = app(NoteShareService::class);
        $recipients = $shareService->resolveRecipients(
            $this->shareType,
            $this->selectedRecipients,
            auth()->user()->school_id
        );

        return $recipients->count();
    }

    public function shareNote(): void
    {
        if ($this->shareType === 'school_wide') {
            $this->validate(['shareType' => 'required', 'canEdit' => 'boolean']);
            $this->selectedRecipients = [auth()->user()->school_id];
        } elseif ($this->shareType === 'email') {
            $this->validate(['shareType' => 'required', 'emailInput' => 'required|email', 'canEdit' => 'boolean']);
        } else {
            $this->validate();
        }

        try {
            $shareService = app(NoteShareService::class);

            $result = $shareService->shareNote(
                $this->note,
                $this->shareType,
                $this->shareType === 'email' ? [$this->emailInput] : $this->selectedRecipients,
                $this->canEdit
            );

            $this->dispatch('note-shared', [
                'shares_created' => $result['shares_created'],
                'users_notified' => $result['users_notified'],
            ]);

            $this->reset(['selectedRecipients', 'emailInput', 'canEdit']);
            $this->note->refresh();

            $this->dispatch('success',
                message: "Note shared with {$result['users_notified']} " .
                \Str::plural('recipient', $result['users_notified']) . " successfully!"
            );

        } catch (\Exception $e) {
            \Log::error('Note sharing failed: ' . $e->getMessage(), [
                'share_type' => $this->shareType,
                'selected_recipients' => $this->selectedRecipients,
                'email_input' => $this->emailInput,
                'exception' => $e->getTraceAsString()
            ]);
            $this->dispatch('error', message: 'Failed to share note. Please try again.');
        }
    }

    public function removeShare(int $shareId, string $shareType, $identifier): void
    {
        try {
            $shareService = app(NoteShareService::class);
            $shareService->unshare($this->note, $shareType, $identifier);

            $this->note->refresh();

            $this->dispatch('success', message: 'Share removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to remove share: ' . $e->getMessage());
            $this->dispatch('error', message: 'Failed to remove share. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.notes.share-note');
    }
}
