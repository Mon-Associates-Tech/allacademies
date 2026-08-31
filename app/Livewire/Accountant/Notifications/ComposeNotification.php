<?php

namespace App\Livewire\Accountant\Notifications;

use App\Models\Message;
use App\Models\MessageTemplate;
use App\Models\User;
use App\Services\AccountantNotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ComposeNotification extends Component
{
    use WithFileUploads;

    // ─── Template ────────────────────────────────────────────────────────────
    public ?int $templateId = null;

    public string $subject = '';

    public string $body = '';

    public string $smsBody = '';

    // ─── Channels ────────────────────────────────────────────────────────────
    public bool $channelEmail = true;

    public bool $channelSms = false;

    public bool $channelInApp = true;

    // ─── Recipients ──────────────────────────────────────────────────────────
    public string $targetType = 'all_unpaid'; // all_unpaid|all_overdue|all_partial|academic_group|academic_level|individual|role

    public bool $includeParents = true;

    public ?int $academicPeriodId = null;

    public array $selectedAcademicGroups = [];

    public array $selectedAcademicLevels = [];

    public array $selectedRoles = [];

    public array $selectedUsers = [];

    public array $selectedUsersList = [];

    public string $userSearch = '';

    public array $searchedUsers = [];

    // ─── Preview ─────────────────────────────────────────────────────────────
    public bool $showPreview = false;

    public array $previewRecipients = [];

    public int $recipientCount = 0;

    // ─── Options ─────────────────────────────────────────────────────────────
    public bool $isUrgent = false;

    public bool $sendNow = true;

    public string $scheduledAt = '';

    // ─── Attachments ─────────────────────────────────────────────────────────
    public array $attachments = [];

    public array $tempAttachments = [];

    private array $fileCache = [];

    // ─── Context data for template variable resolution ────────────────────────
    public array $extraVars = []; // key => value pairs for event/custom templates

    protected function rules(): array
    {
        return [
            'subject'          => 'required|string|max:255',
            'body'             => 'required|string',
            'smsBody'          => 'nullable|string|max:160',
            'scheduledAt'      => 'nullable|date|after:now',
            'attachments.*'    => 'file|max:10240',
        ];
    }

    public function mount(): void
    {
        $this->scheduledAt = now()->addMinutes(5)->format('Y-m-d\TH:i');
    }

    // ─── Template Selection ───────────────────────────────────────────────────

    public function updatedTemplateId(): void
    {
        if (! $this->templateId) {
            return;
        }

        $template = MessageTemplate::find($this->templateId);
        if (! $template) {
            return;
        }

        $this->subject = $template->subject;
        $this->body    = '';
        $this->smsBody = '';

        // Auto-set target type for fee templates
        if ($template->category === 'fee' && $this->targetType === 'individual') {
            $this->targetType = 'all_unpaid';
        }

        $this->showPreview = false;
    }

    // ─── Recipient Targeting ──────────────────────────────────────────────────

    public function updatedTargetType(): void
    {
        $this->resetTargetCriteria();
        $this->showPreview = false;
    }

    public function resetTargetCriteria(): void
    {
        $this->selectedAcademicGroups = [];
        $this->selectedAcademicLevels = [];
        $this->selectedRoles          = [];
        $this->selectedUsers          = [];
        $this->selectedUsersList      = [];
    }

    public function toggleAcademicGroup(int $id): void
    {
        $this->toggle($this->selectedAcademicGroups, $id);
        $this->showPreview = false;
    }

    public function toggleAcademicLevel(int $id): void
    {
        $this->toggle($this->selectedAcademicLevels, $id);
        $this->showPreview = false;
    }

    public function toggleRole(string $role): void
    {
        $this->toggle($this->selectedRoles, $role);
        $this->showPreview = false;
    }

    public function toggleUser(int $userId): void
    {
        $this->toggle($this->selectedUsers, $userId);
        $this->updateSelectedUsersList();
        $this->showPreview = false;
    }

    public function removeUser(int $userId): void
    {
        $this->selectedUsers     = array_values(array_filter($this->selectedUsers, fn ($id) => $id != $userId));
        $this->updateSelectedUsersList();
    }

    protected function toggle(array &$list, $value): void
    {
        if (in_array($value, $list)) {
            $list = array_values(array_filter($list, fn ($v) => $v != $value));
        } else {
            $list[] = $value;
        }
    }

    protected function updateSelectedUsersList(): void
    {
        $this->selectedUsersList = User::whereIn('id', $this->selectedUsers)
            ->get(['id', 'name', 'email', 'role'])
            ->toArray();
    }

    public function updatedUserSearch(): void
    {
        if (strlen($this->userSearch) < 2) {
            $this->searchedUsers = [];

            return;
        }

        $this->searchedUsers = User::where('is_active', true)
            ->where(fn ($q) => $q->where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%"))
            ->limit(10)
            ->get(['id', 'name', 'email', 'role'])
            ->toArray();
    }

    // ─── Preview ─────────────────────────────────────────────────────────────

    public function previewRecipients(): void
    {
        $service = app(AccountantNotificationService::class);

        $recipients = $service->resolveRecipients($this->targetType, $this->buildCriteria());

        if ($this->includeParents) {
            $recipients = $service->withParents($recipients);
        }

        $this->previewRecipients = $recipients->take(30)->map(fn ($u) => [
            'name'  => $u->name,
            'email' => $u->email,
            'role'  => $u->role,
        ])->toArray();

        $this->recipientCount = $recipients->count();
        $this->showPreview    = true;
    }

    protected function buildCriteria(): array
    {
        return [
            'academic_period_id'  => $this->academicPeriodId,
            'academic_group_ids'  => $this->selectedAcademicGroups,
            'academic_level_ids'  => $this->selectedAcademicLevels,
            'roles'               => $this->selectedRoles,
            'user_ids'            => $this->selectedUsers,
            'include_parents'     => $this->includeParents,
            'extra_vars'          => $this->extraVars,
        ];
    }

    protected function buildChannels(): array
    {
        $channels = [];
        if ($this->channelEmail) {
            $channels[] = 'email';
        }
        if ($this->channelSms) {
            $channels[] = 'sms';
        }
        if ($this->channelInApp) {
            $channels[] = 'in_app';
        }

        return $channels;
    }

    // ─── Attachments ─────────────────────────────────────────────────────────

    public function updatedAttachments($value): void
    {
        if (! $value) {
            return;
        }

        $file = is_array($value) ? $value[0] : $value;

        $this->validateOnly('attachments.*', ['attachments.*' => 'file|max:10240']);

        $tempId   = (string) Str::uuid();
        $tempPath = $file->storeAs('temp-message-attachments', $tempId.'.'.$file->getClientOriginalExtension(), 'public');

        $this->tempAttachments[] = [
            'id'                => $tempId,
            'original_filename' => $file->getClientOriginalName(),
            'size'              => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'human_size'        => $this->formatBytes($file->getSize()),
            'temp_path'         => $tempPath,
        ];

        $this->attachments = [];
    }

    public function removeAttachment(string $id): void
    {
        $att = collect($this->tempAttachments)->firstWhere('id', $id);
        if ($att && Storage::disk('public')->exists($att['temp_path'])) {
            Storage::disk('public')->delete($att['temp_path']);
        }
        $this->tempAttachments = array_values(array_filter($this->tempAttachments, fn ($a) => $a['id'] !== $id));
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }

    protected function saveAttachments(Message $message): void
    {
        foreach ($this->tempAttachments as $att) {
            if (! Storage::disk('public')->exists($att['temp_path'])) {
                continue;
            }
            $filename  = Str::uuid().'.'.pathinfo($att['original_filename'], PATHINFO_EXTENSION);
            $finalPath = 'message-attachments/'.$filename;
            Storage::disk('public')->move($att['temp_path'], $finalPath);

            $message->attachments()->create([
                'filename'          => $filename,
                'original_filename' => $att['original_filename'],
                'path'              => $finalPath,
                'size'              => $att['size'],
                'mime_type'         => $att['mime_type'],
            ]);
        }

        $this->tempAttachments = [];
    }

    // ─── Save / Send ─────────────────────────────────────────────────────────

    public function saveDraft(): void
    {
        $this->validate(['subject' => 'required|string|max:255']);

        $message = $this->createMessageRecord(Message::STATUS_DRAFT);
        $this->saveAttachments($message);

        session()->flash('success', 'Draft saved successfully.');
        $this->redirect(route('accountant.notifications.index'));
    }

    public function send(): void
    {
        $this->validate();

        if (! $this->sendNow && empty($this->scheduledAt)) {
            $this->addError('scheduledAt', 'Please select a scheduled time.');

            return;
        }

        $status  = $this->sendNow ? Message::STATUS_SENDING : Message::STATUS_SCHEDULED;
        $message = $this->createMessageRecord($status);
        $this->saveAttachments($message);

        if ($this->sendNow) {
            try {
                app(AccountantNotificationService::class)->sendMessage($message);
                session()->flash('success', 'Notification sent successfully.');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to send: '.$e->getMessage());

                return;
            }
        } else {
            session()->flash('success', 'Notification scheduled successfully.');
        }

        $this->redirect(route('accountant.notifications.index'));
    }

    protected function createMessageRecord(string $status): Message
    {
        return Message::create([
            'sender_id'      => auth()->id(),
            'template_id'    => $this->templateId,
            'subject'        => $this->subject,
            'body'           => $this->body,
            'target_type'    => $this->targetType,
            'target_criteria'=> $this->buildCriteria(),
            'channels'       => $this->buildChannels(),
            'context_type'   => $this->templateId
                ? MessageTemplate::find($this->templateId)?->category
                : null,
            'context_data'   => [
                'academic_period_id' => $this->academicPeriodId,
                'message_body'       => $this->body,
                'extra_vars'         => $this->extraVars,
                'sms_body'           => $this->smsBody,
            ],
            'is_urgent'      => $this->isUrgent,
            'scheduled_at'   => $this->sendNow ? null : $this->scheduledAt,
            'status'         => $status,
        ]);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $service = app(AccountantNotificationService::class);

        return view('livewire.accountant.notifications.compose-notification', [
            'templates'       => $service->getTemplatesForSchool(auth()->user()->school_id),
            'academicPeriods' => $service->getAcademicPeriods(),
            'academicGroups'  => $service->getAcademicGroups(),
            'academicLevels'  => $service->getAcademicLevels(),
            'availableRoles'  => [
                'student'   => 'Students',
                'teacher'   => 'Teachers',
                'parent'    => 'Parents',
                'librarian' => 'Librarians',
                'author'    => 'Authors',
                'guest'     => 'Guests',
            ],
        ]);
    }
}
