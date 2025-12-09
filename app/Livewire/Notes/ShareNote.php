<?php

namespace App\Livewire\Notes;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Note;
use App\Models\StudentGroup;
use App\Models\User;
use App\Services\NoteShareService;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShareNote extends Component
{
    public Note $note;
    public string $shareType = 'individual';
    public array $selectedRecipients = [];
    public bool $canEdit = false;
    public bool $notifyRecipients = true;

    // Available options
    public Collection $individuals;
    public Collection $academicGroups;
    public Collection $academicLevels;
    public Collection $studentGroups;

    protected $listeners = [
        'selection-changed' => 'handleSelectionChanged',
    ];

    protected $rules = [
        'shareType' => 'required|in:individual,academic_group,academic_level,student_group,school_wide',
        'selectedRecipients' => 'required_unless:shareType,school_wide|array|min:1',
        'canEdit' => 'boolean',
        'notifyRecipients' => 'boolean',
    ];

    protected $messages = [
        'selectedRecipients.required_unless' => 'Please select at least one recipient.',
        'selectedRecipients.min' => 'Please select at least one recipient.',
    ];

    public function mount(Note $note): void
    {
        $this->note = $note;
        $this->loadRecipientOptions();
    }

    public function handleSelectionChanged($data): void
    {
        if (isset($data['name']) && $data['name'] === 'selectedRecipients') {
            $this->selectedRecipients = $data['selected'] ?? [];
        }
    }

    public function loadRecipientOptions(): void
    {
        $schoolId = auth()->user()->school_id;

        // Load individuals - all users in the same school
        $this->individuals = User::where('school_id', $schoolId)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name . ' (' . $user->email . ')',
                'email' => $user->email,
                'role' => $user->role,
            ]);

        // Load academic groups - only groups associated with this school
        $this->academicGroups = AcademicGroup::forSchool($schoolId)
            ->withCount(['students' => function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            }])
            ->orderBy('name')
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name . ' (' . $group->students_count . ' students)',
                'tag' => $group->tag ?? null,
            ]);

        // Load academic levels - only levels associated with this school
        $this->academicLevels = AcademicLevel::forSchool($schoolId)
            ->with('academicGroup')
            ->withCount(['students' => function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            }])
            ->orderBy('name')
            ->get()
            ->map(fn($level) => [
                'id' => $level->id,
                'name' => $level->name . ' (' . $level->students_count . ' students)',
                'label' => $level->label ?? null,
                'group' => $level->academicGroup?->name ?? 'N/A',
            ]);

        // Load student groups - properly filtered by school with relationships
        $this->studentGroups = StudentGroup::where('school_id', $schoolId)
            ->with(['academicGroup', 'academicLevel', 'academicSubject', 'teacher.user'])
            ->withCount('students')
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->getDisplayName(),
                'description' => $group->description ?? null,
                'students_count' => $group->students_count,
                'academic_group' => $group->academicGroup?->name,
                'academic_level' => $group->academicLevel?->name,
                'academic_subject' => $group->academicSubject?->name,
                'teacher' => $group->teacher?->user->name,
            ]);
    }

    public function updatedShareType(): void
    {
        // Reset selected recipients when share type changes
        $this->selectedRecipients = [];
    }

    public function getRecipientsProperty(): array
    {
        return match($this->shareType) {
            'individual' => $this->individuals->toArray(),
            'academic_group' => $this->academicGroups->toArray(),
            'academic_level' => $this->academicLevels->toArray(),
            'student_group' => $this->studentGroups->toArray(),
            'school_wide' => [],
            default => [],
        };
    }

    public function getRecipientCountProperty(): int
    {
        if ($this->shareType === 'school_wide') {
            return User::where('school_id', auth()->user()->school_id)
                ->where('id', '!=', auth()->id())
                ->count();
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
        // For school-wide, we don't need recipients
        if ($this->shareType !== 'school_wide') {
            $this->validate();
        } else {
            $this->validate(['shareType' => 'required', 'canEdit' => 'boolean']);
            $this->selectedRecipients = [auth()->user()->school_id];
        }

        try {
            $shareService = app(NoteShareService::class);

            $result = $shareService->shareNote(
                $this->note,
                $this->shareType,
                $this->selectedRecipients,
                $this->canEdit
            );

            $this->dispatch('note-shared', [
                'shares_created' => $result['shares_created'],
                'users_notified' => $result['users_notified'],
            ]);

            // Reset form
            $this->reset(['selectedRecipients', 'canEdit']);

            // Refresh the note to show updated shares
            $this->note->refresh();

            $this->dispatch('success',
                message: "Note shared with {$result['users_notified']} " .
                \Str::plural('recipient', $result['users_notified']) . " successfully!"
            );

        } catch (\Exception $e) {
            \Log::error('Note sharing failed: ' . $e->getMessage(), [
                'share_type' => $this->shareType,
                'selected_recipients' => $this->selectedRecipients,
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
