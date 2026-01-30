<?php

namespace App\Livewire\Students\Messages;

use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ComposeMessage extends Component
{
    use WithFileUploads;

    public $subject = '';

    public $body = '';

    public $targetType = 'teacher'; // Default to teachers

    public $isUrgent = false;

    public $scheduledAt = '';

    public $sendNow = true;

    // Target criteria specific to students
    public $selectedTeachers = []; // Only assigned teachers

    public $selectedParents = [];  // Parents/guardians

    public $selectedUsers = [];    // Individual selection

    // File uploads
    public $attachments = [];

    public $tempAttachments = [];

    public $showPreview = false;

    // Preview
    public $previewRecipients = [];

    public $recipientCount = 0;

    public $userSearch = '';

    public $searchedUsers = [];

    public $selectedUsersList = [];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'targetType' => 'required|in:teacher,parent,individual',
        'scheduledAt' => 'nullable|date|after:now',
        'attachments.*' => 'file|max:10240',
    ];

    protected $messages = [
        'subject.required' => 'Please enter a subject for your message.',
        'body.required' => 'Please enter the message content.',
        'attachments.*.max' => 'Each attachment must be smaller than 10MB.',
    ];

    private $fileCache = [];

    public function mount()
    {
        $this->scheduledAt = now()->addMinutes(5)->format('Y-m-d\TH:i');
    }

    public function updatedAttachments()
    {
        $this->handleNewAttachments();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'subject' || $propertyName === 'body') {
            $this->validateOnly($propertyName);
        }
    }

    public function send()
    {
        $this->validateBaseFields();

        if (! $this->sendNow && empty($this->scheduledAt)) {
            $this->addError('scheduledAt', 'Please select a scheduled time or choose to send now.');

            return;
        }

        $this->validateTargetCriteria();

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

        return redirect()->route('students.messages.index');
    }

    protected function validateBaseFields()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'targetType' => 'required|in:teacher,parent,individual',
        ]);
    }

    protected function validateTargetCriteria()
    {
        $rules = [];

        switch ($this->targetType) {
            case 'teacher':
                $rules['selectedTeachers'] = 'required|array|min:1';
                $rules['selectedTeachers.*'] = 'integer';
                break;
            case 'parent':
                $rules['selectedParents'] = 'required|array|min:1';
                $rules['selectedParents.*'] = 'integer';
                break;
            case 'individual':
                $rules['selectedUsers'] = 'required|array|min:1';
                $rules['selectedUsers.*'] = 'integer';
                break;
        }

        if (! empty($rules)) {
            $this->validate($rules);
        }
    }

    protected function handleNewAttachments()
    {
        if (empty($this->attachments)) {
            return;
        }

        $this->validate([
            'attachments.*' => 'file|max:10240',
        ]);

        foreach ($this->attachments as $attachment) {
            $tempId = (string) Str::uuid();
            $tempFilename = $tempId.'.'.$attachment->getClientOriginalExtension();
            $tempPath = $attachment->storeAs('temp-message-attachments', $tempFilename, 'public');

            $this->tempAttachments[] = [
                'id' => $tempId,
                'original_filename' => $attachment->getClientOriginalName(),
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
                'human_size' => $this->formatFileSize($attachment->getSize()),
                'temp_path' => $tempPath,
                'is_temp' => true,
            ];
        }

        $this->attachments = [];
    }

    protected function formatFileSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        } else {
            return round($bytes / 1048576, 1).' MB';
        }
    }

    public function removeAttachment($attachmentId)
    {
        $attachmentId = (string) $attachmentId;
        $attachmentToRemove = collect($this->tempAttachments)->firstWhere('id', $attachmentId);

        if ($attachmentToRemove && isset($attachmentToRemove['temp_path'])) {
            if (Storage::disk('public')->exists($attachmentToRemove['temp_path'])) {
                Storage::disk('public')->delete($attachmentToRemove['temp_path']);
            }
        }

        $this->tempAttachments = array_filter(
            $this->tempAttachments,
            fn ($att) => (string) $att['id'] !== $attachmentId
        );

        if (isset($this->fileCache[$attachmentId])) {
            unset($this->fileCache[$attachmentId]);
        }

        $this->tempAttachments = array_values($this->tempAttachments);
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            // Students can only search among their assigned teachers and parents
            $this->searchedUsers = User::where('is_active', true)
                ->whereIn('id', $this->getAllowedRecipients()?->pluck('id'))
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

    protected function updateSelectedUsersList()
    {
        $this->selectedUsersList = User::whereIn('id', $this->selectedUsers)->get();
    }

    public function addUser($userId)
    {
        if (! in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
            $this->updateSelectedUsersList();
        }
    }

    public function updatedTargetType()
    {
        $this->resetTargetCriteria();
        $this->showPreview = false;
    }

    public function resetTargetCriteria()
    {
        $this->selectedTeachers = [];
        $this->selectedParents = [];
        $this->selectedUsers = [];
    }

    public function updatedSendNow()
    {
        if ($this->sendNow) {
            $this->scheduledAt = '';
        }
    }

    public function toggleTeacher($teacherId)
    {
        if (in_array($teacherId, $this->selectedTeachers)) {
            $this->removeTeacher($teacherId);
        } else {
            $this->addTeacher($teacherId);
        }
        $this->showPreview = false;
    }

    public function removeTeacher($teacherId)
    {
        $this->selectedTeachers = array_filter($this->selectedTeachers, fn ($id) => $id != $teacherId);
        $this->selectedTeachers = array_values($this->selectedTeachers);
    }

    public function addTeacher($teacherId)
    {
        if (! in_array($teacherId, $this->selectedTeachers)) {
            $this->selectedTeachers[] = $teacherId;
        }
    }

    public function toggleParent($parentId)
    {
        if (in_array($parentId, $this->selectedParents)) {
            $this->removeParent($parentId);
        } else {
            $this->addParent($parentId);
        }
        $this->showPreview = false;
    }

    public function removeParent($parentId)
    {
        $this->selectedParents = array_filter($this->selectedParents, fn ($id) => $id != $parentId);
        $this->selectedParents = array_values($this->selectedParents);
    }

    public function addParent($parentId)
    {
        if (! in_array($parentId, $this->selectedParents)) {
            $this->selectedParents[] = $parentId;
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

    protected function getAllowedRecipients()
    {
        // Get the student's assigned teachers and parents
        $student = auth()->user();

        // Get assigned teachers (primary and level teachers)
        $teachers = $student->academicGroups?->flatMap(function ($group) {
            return $group->teachers;
        })->unique('id');

        // Get parents/guardians
        $parents = $student->parents;

        return $teachers?->merge($parents);
    }

    protected function getTargetCriteria(): array
    {
        $criteria = [];

        switch ($this->targetType) {
            case 'teacher':
                $criteria['teacher_ids'] = $this->selectedTeachers;
                break;
            case 'parent':
                $criteria['parent_ids'] = $this->selectedParents;
                break;
            case 'individual':
                $criteria['user_ids'] = $this->selectedUsers;
                break;
        }

        return $criteria;
    }

    protected function saveAttachments(Message $message)
    {
        foreach ($this->tempAttachments as $attachment) {
            if (! isset($attachment['temp_path']) || ! Storage::disk('public')->exists($attachment['temp_path'])) {
                continue;
            }

            $filename = (string) Str::uuid().'.'.pathinfo($attachment['original_filename'], PATHINFO_EXTENSION);
            $finalPath = 'message-attachments/'.$filename;

            Storage::disk('public')->move($attachment['temp_path'], $finalPath);

            $message->attachments()->create([
                'filename' => $filename,
                'original_filename' => $attachment['original_filename'],
                'path' => $finalPath,
                'size' => $attachment['size'],
                'mime_type' => $attachment['mime_type'],
            ]);
        }

        $this->tempAttachments = [];
        $this->fileCache = [];
    }

    public function render()
    {
        $allowedRecipients = $this->getAllowedRecipients();

        $teachers = $allowedRecipients?->filter(function ($user) {
            return $user->hasRole('teacher');
        });

        $parents = $allowedRecipients?->filter(function ($user) {
            return $user->hasRole('parent');
        });

        return view('livewire.students.messages.compose-message', [
            'teachers' => $teachers,
            'parents' => $parents,
        ]);
    }
}
