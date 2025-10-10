<?php

namespace App\Traits;

use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait MessageAttachments
{
    use WithFileUploads;

    public array $attachments = [];
    public array $tempAttachments = [];
    private array $fileCache = [];

    protected function handleNewAttachments(): void
    {
        if (empty($this->attachments)) {
            return;
        }

        $this->validate([
            'attachments.*' => 'file|max:10240'
        ]);

        foreach ($this->attachments as $attachment) {
            $tempId = (string) Str::uuid();
            $tempFilename = $tempId . '.' . $attachment->getClientOriginalExtension();
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
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
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
            fn($att) => (string) $att['id'] !== $attachmentId
        );

        if (isset($this->fileCache[$attachmentId])) {
            unset($this->fileCache[$attachmentId]);
        }

        $this->tempAttachments = array_values($this->tempAttachments);
    }

    protected function saveAttachments($message)
    {
        foreach ($this->tempAttachments as $attachment) {
            if (!isset($attachment['temp_path']) ||
                !Storage::disk('public')->exists($attachment['temp_path'])) {
                continue;
            }

            $filename = (string) Str::uuid() . '.' .
                       pathinfo($attachment['original_filename'], PATHINFO_EXTENSION);
            $finalPath = 'message-attachments/' . $filename;

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
}
