<?php

namespace App\Livewire\Common\Messages;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class SendMessageToUser extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public $userId;

    public $userName;

    public $subject = '';

    public $body = '';

    public $isUrgent = false;

    // File uploads
    public $attachments = [];

    public $tempAttachments = [];

    private $fileCache = [];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'attachments.*' => 'file|max:10240', // 10MB max
    ];

    protected $messages = [
        'subject.required' => 'Please enter a subject for your message.',
        'body.required' => 'Please enter the message content.',
        'attachments.*.max' => 'Each attachment must be smaller than 10MB.',
    ];

    public function mount($userId, $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function updatedAttachments()
    {
        $this->handleNewAttachments();
    }

    protected function handleNewAttachments()
    {
        if (empty($this->attachments)) {
            return;
        }

        // Validate each new attachment
        $this->validate([
            'attachments.*' => 'file|max:10240',
        ]);

        foreach ($this->attachments as $attachment) {
            // Store file temporarily
            $tempId = (string) \Illuminate\Support\Str::uuid();
            $tempFilename = $tempId.'.'.$attachment->getClientOriginalExtension();

            $tempPath = $attachment->storeAs('temp-message-attachments', $tempFilename, 'public');

            // Store file metadata
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

        // Clear the attachments array
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
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($attachmentToRemove['temp_path'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachmentToRemove['temp_path']);
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

    public function sendMessage()
    {
        $this->validate();

        $messageService = app(MessageService::class);

        // Create message with individual target type
        $message = Message::create([
            'sender_id' => auth()->id(),
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => 'individual',
            'target_criteria' => [
                'user_ids' => [$this->userId],
                'include_students' => true,
                'include_teachers' => true,
            ],
            'is_urgent' => $this->isUrgent,
            'status' => Message::STATUS_SENDING,
        ]);

        // Save attachments
        $this->saveAttachments($message);

        try {
            $messageService->sendMessage($message);
            session()->flash('success', 'Message sent to '.$this->userName.' successfully!');
            $this->resetForm();
            $this->dispatch('messageSent');
            $this->dispatch('close-modal', name: 'send-message-to-user');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message. Please try again.');
        }
    }

    protected function saveAttachments(Message $message)
    {
        foreach ($this->tempAttachments as $attachment) {
            if (! isset($attachment['temp_path']) ||
                ! \Illuminate\Support\Facades\Storage::disk('public')->exists($attachment['temp_path'])) {
                continue;
            }

            $filename = (string) \Illuminate\Support\Str::uuid().'.'.
                       pathinfo($attachment['original_filename'], PATHINFO_EXTENSION);
            $finalPath = 'message-attachments/'.$filename;

            \Illuminate\Support\Facades\Storage::disk('public')->move($attachment['temp_path'], $finalPath);

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

    protected function resetForm()
    {
        $this->subject = '';
        $this->body = '';
        $this->isUrgent = false;
        $this->tempAttachments = [];
        $this->attachments = [];
    }

    public function render()
    {
        return view('livewire.common.messages.send-message-to-user');
    }
}
