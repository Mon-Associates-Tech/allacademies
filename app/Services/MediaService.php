<?php

namespace App\Services;

use App\Models\Media\MediaFile;
use App\Models\Media\MediaFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Image;

class MediaService
{
    protected $disk;

    protected $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'video/mp4', 'video/avi', 'video/quicktime', 'video/webm',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv',
    ];

    protected $maxFileSize = 10485760; // 10MB in bytes

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'public');
    }

    public function uploadFile(UploadedFile $file, ?int $folderId = null, ?int $userId = null): MediaFile
    {
        \Log::info('Starting file upload process', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'folder_id' => $folderId,
            'user_id' => $userId,
        ]);

        $this->validateFile($file);

        $folder = $folderId ? MediaFolder::find($folderId) : null;
        $path = $folder ? 'media/'.$folder->path : 'media';

        \Log::info('Upload path determined', ['path' => $path]);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        $filePath = $path.'/'.$filename;

        \Log::info('Generated filename', ['filename' => $filename, 'full_path' => $filePath]);

        // Store file
        $storedPath = Storage::disk($this->disk)->putFileAs($path, $file, $filename);

        \Log::info('File stored', ['stored_path' => $storedPath]);

        // Create media file record
        $mediaFile = MediaFile::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'disk' => $this->disk,
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'folder_id' => $folderId,
            'uploaded_by' => $userId ?? auth()->id(),
            'metadata' => $this->extractMetadata($file, $storedPath),
        ]);

        \Log::info('Media file record created', ['media_file_id' => $mediaFile->id]);

        // Process image dimensions and create thumbnails if it's an image
        if ($mediaFile->isImage()) {
            $this->processImage($mediaFile, $storedPath);
        }

        return $mediaFile;
    }

    public function createFolder(string $name, ?int $parentId = null, ?string $description = null): MediaFolder
    {
        return MediaFolder::create([
            'name' => $name,
            'description' => $description,
            'parent_id' => $parentId,
        ]);
    }

    public function moveFile(MediaFile $file, ?int $newFolderId = null): MediaFile
    {
        $oldPath = $file->file_path;
        $newFolder = $newFolderId ? MediaFolder::find($newFolderId) : null;
        $newPath = $newFolder ? 'media/'.$newFolder->path : 'media';

        $filename = basename($oldPath);
        $newFilePath = $newPath.'/'.$filename;

        // Move file in storage
        Storage::disk($this->disk)->move($oldPath, $newFilePath);

        // Update file record
        $file->update([
            'file_path' => $newFilePath,
            'folder_id' => $newFolderId,
        ]);

        return $file;
    }

    public function deleteFile(MediaFile $file): bool
    {
        // Delete file from storage
        Storage::disk($this->disk)->delete($file->file_path);

        // Delete thumbnails if they exist
        $this->deleteThumbnails($file);

        // Delete record
        return $file->delete();
    }

    public function deleteFolder(MediaFolder $folder, bool $forceDelete = false): bool
    {
        if (! $forceDelete && ($folder->children()->count() > 0 || $folder->files()->count() > 0)) {
            throw new \Exception('Folder is not empty. Use forceDelete to delete non-empty folders.');
        }

        if ($forceDelete) {
            // Delete all files in folder
            foreach ($folder->files as $file) {
                $this->deleteFile($file);
            }

            // Recursively delete subfolders
            foreach ($folder->children as $child) {
                $this->deleteFolder($child, true);
            }
        }

        return $folder->delete();
    }

    public function getFolderContents(?int $folderId = null, array $filters = [])
    {
        $folders = MediaFolder::where('parent_id', $folderId)
            ->orderBy('name')
            ->get();

        $filesQuery = MediaFile::with(['folder', 'uploadedBy'])
            ->where('folder_id', $folderId);

        // Apply filters
        if (! empty($filters['mime_type'])) {
            $filesQuery->where('mime_type', 'like', $filters['mime_type'].'%');
        }

        if (! empty($filters['search'])) {
            $filesQuery->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('original_name', 'like', '%'.$filters['search'].'%');
            });
        }

        $files = $filesQuery->orderBy('created_at', 'desc')->get();

        return [
            'folders' => $folders,
            'files' => $files,
        ];
    }

    public function getBreadcrumb(?int $folderId = null): array
    {
        if (! $folderId) {
            return [['name' => 'Media Library', 'id' => null]];
        }

        $folder = MediaFolder::find($folderId);
        $breadcrumb = [];

        while ($folder) {
            array_unshift($breadcrumb, [
                'name' => $folder->name,
                'id' => $folder->id,
            ]);
            $folder = $folder->parent;
        }

        array_unshift($breadcrumb, ['name' => 'Media Library', 'id' => null]);

        return $breadcrumb;
    }

    protected function validateFile(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('File type not allowed: '.$file->getMimeType());
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('File size exceeds maximum allowed size.');
        }
    }

    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $uniqueId = uniqid();

        return $filename.'-'.$uniqueId.'.'.$extension;
    }

    protected function extractMetadata(UploadedFile $file, string $storedPath): array
    {
        $metadata = [];

        if (str_starts_with($file->getMimeType(), 'image/')) {
            try {
                $image = Image::make(Storage::disk($this->disk)->path($storedPath));
                $metadata['width'] = $image->width();
                $metadata['height'] = $image->height();
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $metadata;
    }

    protected function processImage(MediaFile $mediaFile, string $storedPath): void
    {
        try {
            $image = Image::make(Storage::disk($this->disk)->path($storedPath));

            // Update dimensions
            $mediaFile->update([
                'width' => $image->width(),
                'height' => $image->height(),
            ]);

            // Create thumbnails
            $this->createThumbnails($mediaFile, $image);
        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::error('Failed to process image: '.$e->getMessage());
        }
    }

    protected function createThumbnails(MediaFile $mediaFile, $image): void
    {
        $thumbnailSizes = [
            'thumb' => [150, 150],
            'medium' => [300, 300],
            'large' => [800, 600],
        ];

        foreach ($thumbnailSizes as $size => $dimensions) {
            $thumbnail = clone $image;
            $thumbnail->fit($dimensions[0], $dimensions[1]);

            $thumbPath = 'media/thumbnails/'.$size.'/'.basename($mediaFile->file_path);

            Storage::disk($this->disk)->put($thumbPath, $thumbnail->encode());
        }
    }

    protected function deleteThumbnails(MediaFile $file): void
    {
        $thumbnailSizes = ['thumb', 'medium', 'large'];

        foreach ($thumbnailSizes as $size) {
            $thumbPath = 'media/thumbnails/'.$size.'/'.basename($file->file_path);
            Storage::disk($this->disk)->delete($thumbPath);
        }
    }
}
