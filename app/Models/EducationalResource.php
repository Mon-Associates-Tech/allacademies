<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EducationalResource extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'tags',
        'file_path',
        'file_name',
        'file_type',
        'format',
        'file_size',
        'academic_subject_id',
        'school_id',
        'uploaded_by',
        'is_active',
        'download_count',
        'view_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
            'file_size' => 'integer',
            'download_count' => 'integer',
            'view_count' => 'integer',
        ];
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(AcademicTopic::class, 'educational_resource_topic')
            ->withTimestamps();
    }

    public function subtopics(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubtopic::class, 'educational_resource_subtopic')
            ->withTimestamps();
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Check if the resource is globally accessible (not school-scoped).
     */
    public function isGlobal(): bool
    {
        return $this->school_id === null;
    }

    /**
     * Check if a user can access this resource.
     */
    public function canBeAccessedBy(?User $user): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Global resources are accessible to all logged-in users
        if ($this->isGlobal()) {
            return $user !== null;
        }

        // School-scoped resources require user to be in the same school
        return $user && $user->school_id === $this->school_id;
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Scope to filter by format.
     */
    public function scopeByFormat($query, string $format)
    {
        return $query->where('format', $format);
    }

    /**
     * Scope to filter by academic subject.
     */
    public function scopeBySubject($query, int $subjectId)
    {
        return $query->where('academic_subject_id', $subjectId);
    }

    /**
     * Scope to filter by academic level (through subject).
     */
    public function scopeByAcademicLevel($query, int $levelId)
    {
        return $query->whereHas('academicSubject', function ($q) use ($levelId) {
            $q->where('academic_level_id', $levelId);
        });
    }

    /**
     * Scope to filter by academic group (through subject -> level).
     */
    public function scopeByAcademicGroup($query, int $groupId)
    {
        return $query->whereHas('academicSubject.academicLevel', function ($q) use ($groupId) {
            $q->where('academic_group_id', $groupId);
        });
    }

    /**
     * Scope to filter active resources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter resources accessible by a user.
     */
    public function scopeAccessibleBy($query, ?User $user)
    {
        return $query->where(function ($q) use ($user) {
            // Global resources
            $q->whereNull('school_id');

            // School-scoped resources for user's school
            if ($user && $user->school_id) {
                $q->orWhere('school_id', $user->school_id);
            }
        });
    }

    /**
     * Scope to search by tags.
     */
    public function scopeWithTags($query, array $tags)
    {
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    /**
     * Scope to search by title or description.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by topic.
     */
    public function scopeByTopic($query, int $topicId)
    {
        return $query->whereHas('topics', function ($q) use ($topicId) {
            $q->where('academic_topics.id', $topicId);
        });
    }

    /**
     * Scope to filter by subtopic.
     */
    public function scopeBySubtopic($query, int $subtopicId)
    {
        return $query->whereHas('subtopics', function ($q) use ($subtopicId) {
            $q->where('academic_subtopics.id', $subtopicId);
        });
    }
}
