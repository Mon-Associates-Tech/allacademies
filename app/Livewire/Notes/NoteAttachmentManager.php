<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class NoteAttachmentManager extends Component
{
    use WithFileUploads;

    public Note $note;
    public array $attachments = [];
    public array $tempAttachments = [];
    public bool $uploading = false;
    public int $uploadProgress = 0;

    protected $rules = [
        'attachments.*' => 'file|max:10240', // 10MB max
    ];

    public function mount(Note $note)
    {
        $this->note = $note;
    }

    public function updatedAttachments()
    {
        $this->validate();
        $this->handleNewAttachments();
    }

    protected function handleNewAttachments(): void
    {
        if (empty($this->attachments)) {
            return;
        }

        foreach ($this->attachments as $attachment) {
            // Validate file type
            $extension = strtolower($attachment->getClientOriginalExtension());
            $allowedExtensions = ['pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', '7z', 'jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($extension, $allowedExtensions)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "File type '.{$extension}' is not allowed."
                ]);
                continue;
            }

            $tempId = (string) Str::uuid();
            $tempFilename = $tempId . '.' . $extension;
            $tempPath = $attachment->storeAs('temp-note-attachments', $tempFilename, 'public');

            $this->tempAttachments[] = [
                'id' => $tempId,
                'original_filename' => $attachment->getClientOriginalName(),
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
                'human_size' => $this->formatFileSize($attachment->getSize()),
                'temp_path' => $tempPath,
                'extension' => $extension,
                'is_temp' => true,
            ];
        }

        $this->attachments = [];
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

        $this->tempAttachments = array_values(
            array_filter($this->tempAttachments, fn($att) => (string) $att['id'] !== $attachmentId)
        );
    }

    public function saveAttachments()
    {
        if (empty($this->tempAttachments)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'No attachments to save.'
            ]);
            return;
        }

        $this->uploading = true;
        $saved = 0;

        foreach ($this->tempAttachments as $attachment) {
            if (!isset($attachment['temp_path']) || !Storage::disk('public')->exists($attachment['temp_path'])) {
                continue;
            }

            $filename = (string) Str::uuid() . '.' . $attachment['extension'];
            $finalPath = 'note-attachments/' . $filename;

            Storage::disk('public')->move($attachment['temp_path'], $finalPath);

            NoteAttachment::create([
                'note_id' => $this->note->id,
                'user_id' => Auth::id(),
                'filename' => $filename,
                'original_filename' => $attachment['original_filename'],
                'path' => $finalPath,
                'size' => $attachment['size'],
                'mime_type' => $attachment['mime_type'],
            ]);

            $saved++;
        }

        $this->tempAttachments = [];
        $this->uploading = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$saved} " . Str::plural('attachment', $saved) . " uploaded successfully."
        ]);

        $this->dispatch('attachments-updated');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = NoteAttachment::find($attachmentId);

        if (!$attachment || $attachment->note_id !== $this->note->id) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Attachment not found.'
            ]);
            return;
        }

        // Check permissions
        if (!$this->note->canUserEdit(Auth::id())) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to delete this attachment.'
            ]);
            return;
        }

        $attachment->deleteFile();
        $attachment->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Attachment deleted successfully.'
        ]);

        $this->dispatch('attachments-updated');
    }

    protected function formatFileSize($bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
        }
    }

    public function render()
    {
        $existingAttachments = $this->note->attachments()
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.notes.note-attachment-manager', [
            'existingAttachments' => $existingAttachments,
        ]);
    }
}
