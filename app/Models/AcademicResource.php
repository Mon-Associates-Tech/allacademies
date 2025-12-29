<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AcademicResource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'user_id',
        'resourceable_type',
        'resourceable_id',
        'is_public',
        'download_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Supported file types with their extensions and mime types
     */
    public const SUPPORTED_TYPES = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'txt' => ['text/plain'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    ];

    /**
     * Maximum file size in bytes (100 MB)
     */
    public const MAX_FILE_SIZE = 104857600;

    /**
     * Get the user who uploaded the resource
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent resourceable model (AcademicGroup, AcademicLevel, etc.)
     */
    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file type icon class
     */
    public function getFileIconAttribute(): string
    {
        return match ($this->file_type) {
            'pdf' => 'file-pdf',
            'doc', 'docx' => 'file-word',
            'xls', 'xlsx' => 'file-excel',
            'ppt', 'pptx' => 'file-powerpoint',
            'txt' => 'file-text',
            'image' => 'file-image',
            default => 'file',
        };
    }

    /**
     * Check if the file is previewable
     */
    public function getIsPreviewableAttribute(): bool
    {
        return in_array($this->file_type, ['pdf', 'image', 'txt']);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Check if user can access this resource
     */
    public function canUserAccess(User $user): bool
    {
        // Owner can always access
        if ($this->user_id === $user->id) {
            return true;
        }

        // Public resources are accessible to all
        if ($this->is_public) {
            return true;
        }

        // Check role-based access
        if ($user->hasRole(['admin', 'owner'])) {
            return true;
        }

        // Teachers can access resources in their assigned areas
        if ($user->teacher) {
            return $this->canTeacherAccess($user->teacher);
        }

        // Students can access resources in their assigned group/level
        if ($user->student) {
            return $this->canStudentAccess($user->student);
        }

        return false;
    }

    /**
     * Check if teacher can access this resource
     */
    protected function canTeacherAccess(Teacher $teacher): bool
    {
        $resourceable = $this->resourceable;

        if ($resourceable instanceof AcademicGroup) {
            return $teacher->academicGroups()->where('academic_groups.id', $resourceable->id)->exists();
        }

        if ($resourceable instanceof AcademicLevel) {
            return $teacher->academicLevels()->where('academic_levels.id', $resourceable->id)->exists();
        }

        if ($resourceable instanceof AcademicSubject) {
            return $teacher->subjects()->where('academic_subjects.id', $resourceable->id)->exists();
        }

        if ($resourceable instanceof AcademicTopic) {
            return $teacher->subjects()->where('academic_subjects.id', $resourceable->academicSubject->id)->exists();
        }

        if ($resourceable instanceof AcademicSubtopic) {
            return $teacher->subjects()->where('academic_subjects.id', $resourceable->academicTopic->academicSubject->id)->exists();
        }

        return false;
    }

    /**
     * Check if student can access this resource
     */
    protected function canStudentAccess(Student $student): bool
    {
        $resourceable = $this->resourceable;

        if ($resourceable instanceof AcademicGroup) {
            return $student->academic_group_id === $resourceable->id;
        }

        if ($resourceable instanceof AcademicLevel) {
            return $student->academic_level_id === $resourceable->id;
        }

        if ($resourceable instanceof AcademicSubject) {
            return $student->academic_level_id === $resourceable->academic_level_id;
        }

        if ($resourceable instanceof AcademicTopic) {
            return $student->academic_level_id === $resourceable->academicSubject->academic_level_id;
        }

        if ($resourceable instanceof AcademicSubtopic) {
            return $student->academic_level_id === $resourceable->academicTopic->academicSubject->academic_level_id;
        }

        return false;
    }

    /**
     * Scope to filter by resourceable type
     */
    public function scopeForResourceable($query, string $type, int $id)
    {
        return $query->where('resourceable_type', $type)
                     ->where('resourceable_id', $id);
    }

    /**
     * Scope to filter by file type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope to get public resources
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get the hierarchy path for breadcrumb
     */
    public function getHierarchyPathAttribute(): array
    {
        $path = [];
        $resourceable = $this->resourceable;

        if ($resourceable instanceof AcademicSubtopic) {
            $topic = $resourceable->academicTopic;
            $subject = $topic->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $topic->id, 'name' => $topic->name],
                ['type' => 'subtopic', 'id' => $resourceable->id, 'name' => $resourceable->name],
            ];
        } elseif ($resourceable instanceof AcademicTopic) {
            $subject = $resourceable->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $resourceable->id, 'name' => $resourceable->name],
            ];
        } elseif ($resourceable instanceof AcademicSubject) {
            $level = $resourceable->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $resourceable->id, 'name' => $resourceable->name],
            ];
        } elseif ($resourceable instanceof AcademicLevel) {
            $group = $resourceable->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $resourceable->id, 'name' => $resourceable->name],
            ];
        } elseif ($resourceable instanceof AcademicGroup) {
            $path = [
                ['type' => 'group', 'id' => $resourceable->id, 'name' => $resourceable->name],
            ];
        }

        return $path;
    }
}
