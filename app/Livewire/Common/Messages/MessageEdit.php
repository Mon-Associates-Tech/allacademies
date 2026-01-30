<?php

namespace App\Livewire\Common\Messages;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Services\MessageService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class MessageEdit extends Component
{
    use WithFileUploads;

    public Message $message;

    public $subject;

    public $body;

    public $targetType;

    public $isUrgent;

    public $scheduledAt;

    public $sendNow = true;

    // Target criteria
    public $selectedRoles = [];

    public $selectedAcademicGroups = [];

    public $selectedAcademicLevels = [];

    public $selectedSubjects = [];

    public $selectedUsers = [];

    public $includeStudents = true;

    public $includeTeachers = true;

    // File uploads
    public $attachments = [];

    public $existingAttachments = [];

    // Preview
    public $showPreview = false;

    public $previewRecipients = [];

    public $recipientCount = 0;

    public $userSearch = '';

    public $searchedUsers = [];

    public $selectedUsersList = [];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'scheduledAt' => 'nullable|date|after:now',
        'attachments.*' => 'file|max:10240',
    ];

    public function mount(Message $message)
    {
        // Check if user can edit this message
        if ($message->sender_id !== auth()->id() && ! auth()->user()->hasRole(['admin', 'super-admin'])) {
            abort(403, 'You do not have permission to edit this message.');
        }

        // Only allow editing draft messages
        if ($message->status !== Message::STATUS_DRAFT) {
            abort(403, 'Only draft messages can be edited.');
        }

        $this->message = $message->load('attachments');
        $this->subject = $message->subject;
        $this->body = $message->body;
        $this->targetType = $message->target_type;
        $this->isUrgent = $message->is_urgent;
        $this->scheduledAt = $message->scheduled_at ? $message->scheduled_at->format('Y-m-d\TH:i') : '';
        $this->sendNow = empty($this->scheduledAt);

        // Load target criteria
        $criteria = $message->target_criteria ?? [];
        $this->selectedRoles = $criteria['roles'] ?? [];
        $this->selectedAcademicGroups = $criteria['academic_group_ids'] ?? [];
        $this->selectedAcademicLevels = $criteria['academic_level_ids'] ?? [];
        $this->selectedSubjects = $criteria['subject_ids'] ?? [];
        $this->selectedUsers = $criteria['user_ids'] ?? [];
        $this->includeStudents = $criteria['include_students'] ?? true;
        $this->includeTeachers = $criteria['include_teachers'] ?? true;
        $this->updateSelectedUsersList();

        // Load existing attachments
        $this->existingAttachments = $message->attachments->toArray();
    }

    protected function updateSelectedUsersList()
    {
        $this->selectedUsersList = User::whereIn('id', $this->selectedUsers)->get();
    }

    public function toggleAcademicGroup($groupId)
    {
        if (in_array($groupId, $this->selectedAcademicGroups)) {
            $this->removeAcademicGroup($groupId);
        } else {
            $this->addAcademicGroup($groupId);
        }
        $this->showPreview = false;
    }

    public function removeAcademicGroup($groupId)
    {
        $this->selectedAcademicGroups = array_filter($this->selectedAcademicGroups, fn ($id) => $id != $groupId);
    }

    public function addAcademicGroup($groupId)
    {
        if (! in_array($groupId, $this->selectedAcademicGroups)) {
            $this->selectedAcademicGroups[] = $groupId;
        }
    }

    public function toggleAcademicLevel($levelId)
    {
        if (in_array($levelId, $this->selectedAcademicLevels)) {
            $this->removeAcademicLevel($levelId);
        } else {
            $this->addAcademicLevel($levelId);
        }
        $this->showPreview = false;
    }

    public function removeAcademicLevel($levelId)
    {
        $this->selectedAcademicLevels = array_filter($this->selectedAcademicLevels, fn ($id) => $id != $levelId);
    }

    public function addAcademicLevel($levelId)
    {
        if (! in_array($levelId, $this->selectedAcademicLevels)) {
            $this->selectedAcademicLevels[] = $levelId;
        }
    }

    public function toggleSubject($subjectId)
    {
        if (in_array($subjectId, $this->selectedSubjects)) {
            $this->removeSubject($subjectId);
        } else {
            $this->addSubject($subjectId);
        }
        $this->showPreview = false;
    }

    // Add this method for user search functionality:

    public function removeSubject($subjectId)
    {
        $this->selectedSubjects = array_filter($this->selectedSubjects, fn ($id) => $id != $subjectId);
    }

    public function addSubject($subjectId)
    {
        if (! in_array($subjectId, $this->selectedSubjects)) {
            $this->selectedSubjects[] = $subjectId;
        }
    }

    public function toggleRole($role)
    {
        if (in_array($role, $this->selectedRoles)) {
            $this->removeRole($role);
        } else {
            $this->addRole($role);
        }
        $this->showPreview = false;
    }

    public function removeRole($role)
    {
        $this->selectedRoles = array_filter($this->selectedRoles, fn ($r) => $r !== $role);
    }

    public function addRole($role)
    {
        if (! in_array($role, $this->selectedRoles)) {
            $this->selectedRoles[] = $role;
        }
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->removeUser($userId);
        } else {
            $this->addUser($userId);
        }
        $this->showPreview = false;
    }

    public function removeUser($userId)
    {
        $this->selectedUsers = array_filter($this->selectedUsers, fn ($id) => $id != $userId);
        $this->selectedUsers = array_values($this->selectedUsers);
        $this->updateSelectedUsersList();
    }

    public function addUser($userId)
    {
        if (! in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
            $this->updateSelectedUsersList();
        }
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->searchedUsers = User::where('is_active', true)
                ->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->userSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->userSearch.'%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->searchedUsers = [];
        }
    }

    public function updatedSendNow()
    {
        if ($this->sendNow) {
            $this->scheduledAt = '';
        }
    }

    public function uploadAttachment()
    {
        $this->validate(['attachments.*' => 'file|max:10240']);

        foreach ($this->attachments as $attachment) {
            $filename = Str::uuid().'.'.$attachment->getClientOriginalExtension();
            $path = $attachment->storeAs('message-attachments', $filename, 'public');

            MessageAttachment::create([
                'attachable_id' => $this->message->id,
                'attachable_type' => Message::class,
                'filename' => $filename,
                'original_filename' => $attachment->getClientOriginalName(),
                'path' => $path,
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }

        $this->attachments = [];
        $this->existingAttachments = $this->message->fresh()->attachments->toArray();
    }

    public function removeExistingAttachment($attachmentId)
    {
        $attachment = MessageAttachment::find($attachmentId);

        if ($attachment && $attachment->attachable_id === $this->message->id) {
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }
            $attachment->delete();

            $this->existingAttachments = $this->message->fresh()->attachments->toArray();
        }
    }

    public function previewRecipients()
    {
        $messageService = app(MessageService::class);
        $criteria = $this->getTargetCriteria();

        $this->previewRecipients = $messageService->resolveRecipients($this->targetType, $criteria);
        $this->recipientCount = $this->previewRecipients->count();
        $this->showPreview = true;
    }

    protected function getTargetCriteria(): array
    {
        $criteria = [
            'include_students' => $this->includeStudents,
            'include_teachers' => $this->includeTeachers,
        ];

        switch ($this->targetType) {
            case 'role':
                $criteria['roles'] = $this->selectedRoles;
                break;
            case 'academic_group':
                $criteria['academic_group_ids'] = $this->selectedAcademicGroups;
                break;
            case 'academic_level':
                $criteria['academic_level_ids'] = $this->selectedAcademicLevels;
                break;
            case 'subject':
                $criteria['subject_ids'] = $this->selectedSubjects;
                break;
            case 'individual':
                $criteria['user_ids'] = $this->selectedUsers;
                break;
            case 'custom':
                $criteria = array_merge($criteria, [
                    'roles' => $this->selectedRoles,
                    'academic_group_ids' => $this->selectedAcademicGroups,
                    'academic_level_ids' => $this->selectedAcademicLevels,
                    'subject_ids' => $this->selectedSubjects,
                    'user_ids' => $this->selectedUsers,
                ]);
                break;
        }

        return $criteria;
    }

    public function updateAndSend()
    {
        $this->validate();

        if (! $this->sendNow && empty($this->scheduledAt)) {
            $this->addError('scheduledAt', 'Please select a scheduled time or choose to send now.');

            return;
        }

        $messageService = app(MessageService::class);

        $this->message->update([
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => $this->targetType,
            'target_criteria' => $this->getTargetCriteria(),
            'is_urgent' => $this->isUrgent,
            'scheduled_at' => $this->sendNow ? null : $this->scheduledAt,
            'status' => $this->sendNow ? Message::STATUS_SENDING : Message::STATUS_SCHEDULED,
        ]);

        if ($this->sendNow) {
            try {
                $messageService->sendMessage($this->message);
                session()->flash('success', 'Message updated and sent successfully!');
            } catch (Exception $e) {
                session()->flash('error', 'Message updated but failed to send. Please try again.');
            }
        } else {
            session()->flash('success', 'Message updated and scheduled successfully!');
        }

        return redirect()->route('admin.messages.index');
    }

    public function update()
    {
        $this->validate();

        if (! $this->sendNow && empty($this->scheduledAt)) {
            $this->addError('scheduledAt', 'Please select a scheduled time or choose to send now.');

            return;
        }

        $this->message->update([
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => $this->targetType,
            'target_criteria' => $this->getTargetCriteria(),
            'is_urgent' => $this->isUrgent,
            'scheduled_at' => $this->sendNow ? null : $this->scheduledAt,
            'status' => Message::STATUS_DRAFT,
        ]);

        session()->flash('success', 'Message updated successfully!');

        return redirect()->route('admin.messages.show', $this->message);
    }

    public function render()
    {
        $messageService = app(MessageService::class);

        return view('livewire.common.messages.message-edit', [
            'availableRoles' => $messageService->getAvailableRoles(),
            'academicGroups' => $messageService->getAcademicGroups(),
            'academicLevels' => $messageService->getAcademicLevels(),
            'academicSubjects' => $messageService->getAcademicSubjects(),
        ]);
    }
}
