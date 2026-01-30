<?php

namespace App\Livewire\Notes;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Note;
use App\Models\StudentGroup;
use App\Models\User;
use App\Services\NoteShareService;
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
        // Reset selected recipients when share type changes
        $this->selectedRecipients = [];
    }

    public function updatedEmailInput(): void
    {
        // Check if user exists in database
        $this->validateOnly('emailInput');

        if (! empty($this->emailInput) && filter_var($this->emailInput, FILTER_VALIDATE_EMAIL)) {
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

        if (empty($this->selectedRecipients) || ! is_array($this->selectedRecipients)) {
            return 0;
        }

        try {
            $shareService = app(NoteShareService::class);
            $recipients = $shareService->resolveRecipients(
                $this->shareType,
                $this->selectedRecipients,
                $this->note->user->school_id
            );

            return $recipients->count();
        } catch (\Exception $e) {
            \Log::error('Failed to get recipient count', [
                'share_type' => $this->shareType,
                'selected_recipients' => $this->selectedRecipients,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    // Lazy loading configuration methods
    public function getModelClassProperty(): ?string
    {
        return match ($this->shareType) {
            'individual' => User::class,
            'academic_group' => AcademicGroup::class,
            'academic_level' => AcademicLevel::class,
            'student_group' => StudentGroup::class,
            default => null,
        };
    }

    public function getSearchColumnsProperty(): array
    {
        return match ($this->shareType) {
            'individual' => ['name', 'email'],
            'academic_group' => ['name', 'tag'],
            'academic_level' => ['name', 'label'],
            'student_group' => ['name', 'description'],
            default => ['name'],
        };
    }

    public function getLabelFormatProperty(): string
    {
        return match ($this->shareType) {
            'individual' => 'name_email',
            'academic_group' => 'name_count',
            'academic_level' => 'name_count',
            'student_group' => 'display_name',
            default => 'name',
        };
    }

    public function shareNote(): void
    {
        // For school-wide, we don't need recipients
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

            // Reset form
            $this->reset(['selectedRecipients', 'emailInput', 'canEdit']);

            // Refresh the note to show updated shares
            $this->note->refresh();

            $this->dispatch('success',
                message: "Note shared with {$result['users_notified']} ".
                \Str::plural('recipient', $result['users_notified']).' successfully!'
            );

        } catch (\Exception $e) {
            \Log::error('Note sharing failed: '.$e->getMessage(), [
                'share_type' => $this->shareType,
                'selected_recipients' => $this->selectedRecipients,
                'email_input' => $this->emailInput,
                'exception' => $e->getTraceAsString(),
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
            \Log::error('Failed to remove share: '.$e->getMessage());
            $this->dispatch('error', message: 'Failed to remove share. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.notes.share-note');
    }
}
