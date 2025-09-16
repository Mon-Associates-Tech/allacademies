<?php

namespace App\Livewire\Common\Messages;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Services\MessageService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComposeMessage extends Component
{
    use WithFileUploads;

    public $subject = '';
    public $body = '';
    public $targetType = 'role';
    public $isUrgent = false;
    public $scheduledAt = '';
    public $sendNow = true;

    // Target criteria
    public $selectedRoles = [];
    public $selectedAcademicGroups = [];
    public $selectedAcademicLevels = [];
    public $selectedSubjects = [];
    public $selectedUsers = [];
    public $includeStudents = true;
    public $includeTeachers = true;

    // File uploads - changed approach
    public $attachments = [];
    public $tempAttachments = []; // Store temporarily selected files with preview data
    private $fileCache = []; // Private cache for actual file objects

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
        'targetType' => 'required|in:role,academic_group,academic_level,subject,individual,custom',
        'scheduledAt' => 'nullable|date|after:now',
        'attachments.*' => 'file|max:10240', // 10MB max
    ];

    protected $messages = [
        'subject.required' => 'Please enter a subject for your message.',
        'body.required' => 'Please enter the message content.',
        'attachments.*.max' => 'Each attachment must be smaller than 10MB.',
    ];

    protected $listeners = [
        'attachmentsUpdated' => 'handleAttachmentsUpdate'
    ];

    public function mount()
    {
        $this->scheduledAt = now()->addMinutes(5)->format('Y-m-d\TH:i');
    }

    public function updatedAttachments()
    {
        // Automatically handle new file selections
        $this->handleNewAttachments();
    }

    protected function handleNewAttachments()
    {
        if (empty($this->attachments)) {
            return;
        }

        // Validate each new attachment
        $this->validate([
            'attachments.*' => 'file|max:10240'
        ]);

        foreach ($this->attachments as $attachment) {
            // Immediately store the file temporarily to preserve it across requests
            $tempId = (string) Str::uuid();
            $tempFilename = $tempId . '.' . $attachment->getClientOriginalExtension();

            // Store in a temporary location first
            $tempPath = $attachment->storeAs('temp-message-attachments', $tempFilename, 'public');

            // Store file metadata including the temporary path
            $this->tempAttachments[] = [
                'id' => $tempId,
                'original_filename' => $attachment->getClientOriginalName(),
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
                'human_size' => $this->formatFileSize($attachment->getSize()),
                'temp_path' => $tempPath, // Store the temporary path
                'is_temp' => true,
            ];
        }

        // Clear the attachments array to allow new selections
        $this->attachments = [];
    }

    protected function formatFileSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
        }
    }

    public function removeAttachment($attachmentId)
    {
        // Ensure attachmentId is a string
        $attachmentId = (string) $attachmentId;

        // Find the attachment to get its temp path
        $attachmentToRemove = collect($this->tempAttachments)->firstWhere('id', $attachmentId);

        // Remove temporary file if it exists
        if ($attachmentToRemove && isset($attachmentToRemove['temp_path'])) {
            if (Storage::disk('public')->exists($attachmentToRemove['temp_path'])) {
                Storage::disk('public')->delete($attachmentToRemove['temp_path']);
            }
        }

        // Remove from temporary attachments
        $this->tempAttachments = array_filter(
            $this->tempAttachments,
            fn($att) => (string) $att['id'] !== $attachmentId
        );

        // Remove from file cache (if it exists)
        if (isset($this->fileCache[$attachmentId])) {
            unset($this->fileCache[$attachmentId]);
        }

        // Re-index array
        $this->tempAttachments = array_values($this->tempAttachments);
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->searchedUsers = User::where('is_active', true)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->userSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->userSearch . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->searchedUsers = [];
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

    public function addUser($userId)
    {
        if (!in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
            $this->updateSelectedUsersList();
        }
    }

    public function removeUser($userId)
    {
        $this->selectedUsers = array_filter($this->selectedUsers, fn($id) => $id != $userId);
        $this->selectedUsers = array_values($this->selectedUsers);
        $this->updateSelectedUsersList();
    }

    protected function updateSelectedUsersList()
    {
        $this->selectedUsersList = User::whereIn('id', $this->selectedUsers)->get();
    }

    public function updatedTargetType()
    {
        $this->resetTargetCriteria();
        $this->showPreview = false;
    }

    public function updatedSendNow()
    {
        if ($this->sendNow) {
            $this->scheduledAt = '';
        }
    }

    public function resetTargetCriteria()
    {
        $this->selectedRoles = [];
        $this->selectedAcademicGroups = [];
        $this->selectedAcademicLevels = [];
        $this->selectedSubjects = [];
        $this->selectedUsers = [];
    }

    // Add the missing toggle methods
    public function toggleRole($role)
    {
        if (in_array($role, $this->selectedRoles)) {
            $this->removeRole($role);
        } else {
            $this->addRole($role);
        }
        $this->showPreview = false; // Reset preview when selection changes
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

    public function toggleAcademicLevel($levelId)
    {
        if (in_array($levelId, $this->selectedAcademicLevels)) {
            $this->removeAcademicLevel($levelId);
        } else {
            $this->addAcademicLevel($levelId);
        }
        $this->showPreview = false;
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

    public function addRole($role)
    {
        if (!in_array($role, $this->selectedRoles)) {
            $this->selectedRoles[] = $role;
        }
    }

    public function removeRole($role)
    {
        $this->selectedRoles = array_filter($this->selectedRoles, fn($r) => $r !== $role);
        $this->selectedRoles = array_values($this->selectedRoles); // Re-index array
    }

    public function addAcademicGroup($groupId)
    {
        if (!in_array($groupId, $this->selectedAcademicGroups)) {
            $this->selectedAcademicGroups[] = $groupId;
        }
    }

    public function removeAcademicGroup($groupId)
    {
        $this->selectedAcademicGroups = array_filter($this->selectedAcademicGroups, fn($id) => $id != $groupId);
        $this->selectedAcademicGroups = array_values($this->selectedAcademicGroups);
    }

    public function addAcademicLevel($levelId)
    {
        if (!in_array($levelId, $this->selectedAcademicLevels)) {
            $this->selectedAcademicLevels[] = $levelId;
        }
    }

    public function removeAcademicLevel($levelId)
    {
        $this->selectedAcademicLevels = array_filter($this->selectedAcademicLevels, fn($id) => $id != $levelId);
        $this->selectedAcademicLevels = array_values($this->selectedAcademicLevels);
    }

    public function addSubject($subjectId)
    {
        if (!in_array($subjectId, $this->selectedSubjects)) {
            $this->selectedSubjects[] = $subjectId;
        }
    }

    public function removeSubject($subjectId)
    {
        $this->selectedSubjects = array_filter($this->selectedSubjects, fn($id) => $id != $subjectId);
        $this->selectedSubjects = array_values($this->selectedSubjects);
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

    public function saveDraft()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => $this->targetType,
            'target_criteria' => $this->getTargetCriteria(),
            'is_urgent' => $this->isUrgent,
            'status' => Message::STATUS_DRAFT,
        ]);

        $this->saveAttachments($message);

        session()->flash('success', 'Message saved as draft successfully!');
        return redirect()->route('admin.messages.index');
    }

    public function send()
    {
        $this->validate();

        if (!$this->sendNow && empty($this->scheduledAt)) {
            $this->addError('scheduledAt', 'Please select a scheduled time or choose to send now.');
            return;
        }

        $messageService = app(MessageService::class);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => $this->targetType,
            'target_criteria' => $this->getTargetCriteria(),
            'is_urgent' => $this->isUrgent,
            'scheduled_at' => $this->sendNow ? null : $this->scheduledAt,
            'status' => $this->sendNow ? Message::STATUS_SENDING : Message::STATUS_SCHEDULED,
        ]);

        $this->saveAttachments($message);

        if ($this->sendNow) {
            try {
                $messageService->sendMessage($message);
                session()->flash('success', 'Message sent successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to send message. Please try again.');
            }
        } else {
            session()->flash('success', 'Message scheduled successfully!');
        }

        return redirect()->route('admin.messages.index');
    }

    protected function saveAttachments(Message $message)
    {
        foreach ($this->tempAttachments as $attachment) {
            // Check if we have a temporary file path
            if (!isset($attachment['temp_path']) || !Storage::disk('public')->exists($attachment['temp_path'])) {
                continue; // Skip if temporary file is not available
            }

            // Generate final filename
            $filename = (string) Str::uuid() . '.' . pathinfo($attachment['original_filename'], PATHINFO_EXTENSION);
            $finalPath = 'message-attachments/' . $filename;

            // Move from temporary location to final location
            Storage::disk('public')->move($attachment['temp_path'], $finalPath);

            $message->attachments()->create(
                [
                    'filename' => $filename,
                    'original_filename' => $attachment['original_filename'],
                    'path' => $finalPath,
                    'size' => $attachment['size'],
                    'mime_type' => $attachment['mime_type'],
                ]
            );
        }

        // Clear temporary attachments after saving
        $this->tempAttachments = [];
        $this->fileCache = [];
    }

    public function render()
    {
        $messageService = app(MessageService::class);

        return view('livewire.common.messages.compose-message', [
            'availableRoles' => $messageService->getAvailableRoles(),
            'academicGroups' => $messageService->getAcademicGroups(),
            'academicLevels' => $messageService->getAcademicLevels(),
            'academicSubjects' => $messageService->getAcademicSubjects(),
        ]);
    }
}
