<?php

namespace App\Models;

use App\Contracts\CalendarEventable;
use App\Traits\HasCalendarEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model implements CalendarEventable
{
    use HasFactory, HasCalendarEvents;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'book_id',
        'academic_subject_id',
        'noteable_type',
        'noteable_id',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | CalendarEventable Implementation
    |--------------------------------------------------------------------------
    | These methods customize the calendar integration for Notes.
    | The base functionality is provided by the HasCalendarEvents trait.
    */

    /**
     * Get the title to display on the calendar.
     */
    public function getCalendarTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the description to display on the calendar.
     */
    public function getCalendarDescription(): ?string
    {
        return $this->content;
    }

    /**
     * Get the default color for notes on the calendar.
     */
    public function getCalendarColor(): ?string
    {
        return '#3B82F6'; // Blue color for notes
    }

    /**
     * Get the URL to view this note's details.
     */
    public function getCalendarEventUrl(): ?string
    {
        return route('notes.show', $this);
    }

    /**
     * Get additional metadata for the calendar event.
     */
    public function getCalendarMetadata(): array
    {
        return [
            'model_type' => static::class,
            'model_id' => $this->id,
            'event_type' => $this->getCalendarEventType(),
            'is_public' => $this->is_public,
            'book_id' => $this->book_id,
            'academic_subject_id' => $this->academic_subject_id,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'note_shares', 'note_id', 'shared_with_user_id')
            ->withPivot('can_edit', 'share_type')
            ->withTimestamps();
    }

    public function shares(): HasMany
    {
        return $this->hasMany(NoteShare::class);
    }

    public function isSharedWith($userId): bool
    {
        // Check direct individual shares
        if ($this->shares()->where('shared_with_user_id', $userId)->exists()) {
            return true;
        }

        // Check group-based shares
        $user = User::find($userId);
        if (!$user || !$user->student) {
            return false;
        }

        return $this->shares()
            ->where(function ($query) use ($user) {
                // Academic Groups - check if user's student belongs to shared academic group
                $query->where(function ($q) use ($user) {
                    $q->where('share_type', 'academic_group')
                        ->where('shareable_type', AcademicGroup::class)
                        ->where('shareable_id', $user->student->academic_group_id);
                })
                    // Academic Levels - check if user's student belongs to shared academic level
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'academic_level')
                            ->where('shareable_type', AcademicLevel::class)
                            ->where('shareable_id', $user->student->academic_level_id);
                    })
                    // Student Groups - check if user's student belongs to shared student group
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'student_group')
                            ->where('shareable_type', StudentGroup::class)
                            ->where('shareable_id', $user->student->student_group_id);
                    })
                    // School-wide - check if same school
                    ->orWhere(function ($q) use ($user) {
                        $q->where('share_type', 'school_wide')
                            ->whereHas('note.user', function ($noteUserQuery) use ($user) {
                                $noteUserQuery->where('school_id', $user->school_id);
                            });
                    });
            })
            ->exists();
    }

    public function canUserEdit($userId): bool
    {
        if ($this->user_id === $userId) {
            return true;
        }

        $user = User::find($userId);
        if (!$user || !$user->student) {
            return false;
        }

        return $this->shares()
            ->where('can_edit', true)
            ->where(function ($query) use ($userId, $user) {
                // Check individual shares
                $query->where('shared_with_user_id', $userId)
                    // Or check group-based shares with the same logic as isSharedWith
                    ->orWhere(function ($q) use ($user) {
                        $q->where(function ($subQ) use ($user) {
                            $subQ->where('share_type', 'academic_group')
                                ->where('shareable_type', AcademicGroup::class)
                                ->where('shareable_id', $user->student->academic_group_id);
                        })
                            ->orWhere(function ($subQ) use ($user) {
                                $subQ->where('share_type', 'academic_level')
                                    ->where('shareable_type', AcademicLevel::class)
                                    ->where('shareable_id', $user->student->academic_level_id);
                            })
                            ->orWhere(function ($subQ) use ($user) {
                                $subQ->where('share_type', 'student_group')
                                    ->where('shareable_type', StudentGroup::class)
                                    ->where('shareable_id', $user->student->student_group_id);
                            });
                    });
            })
            ->exists();
    }
    public function canUserView($userId): bool
    {
        return $this->user_id === $userId ||
            $this->is_public ||
            $this->isSharedWith($userId);
    }

    private function checkGroupEditPermissions($query, $user): void
    {
        $query->whereHasMorph('shareable', [AcademicGroup::class], function ($q) use ($user) {
            $q->whereHas('users', function ($userQuery) use ($user) {
                $userQuery->where('users.id', $user->id);
            });
        })
            ->orWhereHasMorph('shareable', [AcademicLevel::class], function ($q) use ($user) {
                $q->where('id', $user->academic_level_id);
            })
            ->orWhereHasMorph('shareable', [StudentGroup::class], function ($q) use ($user) {
                $q->whereHas('students', function ($studentQuery) use ($user) {
                    $studentQuery->where('user_id', $user->id);
                });
            });
    }

    public function shareable()
    {
        return $this->morphTo();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(NoteAttachment::class);
    }

    /**
     * Get the parent noteable model (AcademicGroup, AcademicLevel, AcademicSubject, AcademicTopic, AcademicSubtopic)
     */
    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the hierarchy path for breadcrumb
     */
    public function getHierarchyPathAttribute(): array
    {
        $path = [];
        $noteable = $this->noteable;

        if (!$noteable) {
            return $path;
        }

        if ($noteable instanceof AcademicSubtopic) {
            $topic = $noteable->academicTopic;
            $subject = $topic->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $topic->id, 'name' => $topic->name],
                ['type' => 'subtopic', 'id' => $noteable->id, 'name' => $noteable->name],
            ];
        } elseif ($noteable instanceof AcademicTopic) {
            $subject = $noteable->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $subject->id, 'name' => $subject->name],
                ['type' => 'topic', 'id' => $noteable->id, 'name' => $noteable->name],
            ];
        } elseif ($noteable instanceof AcademicSubject) {
            $level = $noteable->academicLevel;
            $group = $level->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $level->id, 'name' => $level->name],
                ['type' => 'subject', 'id' => $noteable->id, 'name' => $noteable->name],
            ];
        } elseif ($noteable instanceof AcademicLevel) {
            $group = $noteable->academicGroup;

            $path = [
                ['type' => 'group', 'id' => $group->id, 'name' => $group->name],
                ['type' => 'level', 'id' => $noteable->id, 'name' => $noteable->name],
            ];
        } elseif ($noteable instanceof AcademicGroup) {
            $path = [
                ['type' => 'group', 'id' => $noteable->id, 'name' => $noteable->name],
            ];
        }

        return $path;
    }

    /**
     * Export note as PDF (HTML format for browser printing)
     */
    public function toHtmlForPdf(): string
    {
        $content = "<html><head><title>{$this->title}</title>";
        $content .= "<style>body{font-family:Arial,sans-serif;margin:40px;line-height:1.6;}h1{color:#333;}";
        $content .= ".meta{color:#666;font-size:14px;margin-bottom:20px;}.content{margin-top:20px;}</style></head><body>";
        $content .= "<h1>{$this->title}</h1>";
        $content .= "<div class='meta'>By " . ($this->user->name ?? 'Unknown') . " on {$this->created_at->format('F d, Y')}</div>";
        $content .= "<div class='content'>{$this->content}</div>";
        $content .= "</body></html>";

        return $content;
    }

    /**
     * Export note as Markdown
     */
    public function toMarkdown(): string
    {
        $content = "# {$this->title}\n\n";
        $content .= "*By " . ($this->user->name ?? 'Unknown') . " on {$this->created_at->format('F d, Y')}*\n\n";
        $content .= "---\n\n";
        $content .= strip_tags($this->content) . "\n";

        return $content;
    }

    /**
     * Export note as Plain Text
     */
    public function toPlainText(): string
    {
        $content = strtoupper($this->title) . "\n";
        $content .= str_repeat("=", strlen($this->title)) . "\n\n";
        $content .= "By " . ($this->user->name ?? 'Unknown') . " on {$this->created_at->format('F d, Y')}\n\n";
        $content .= str_repeat("-", 50) . "\n\n";
        $content .= strip_tags($this->content) . "\n";

        return $content;
    }

    /**
     * Export multiple notes as PDF (HTML format)
     */
    public static function collectionToHtmlForPdf($notes): string
    {
        $content = "<html><head><title>Notes Export</title>";
        $content .= "<style>body{font-family:Arial,sans-serif;margin:40px;line-height:1.6;}h1,h2{color:#333;}";
        $content .= ".meta{color:#666;font-size:14px;margin-bottom:20px;}.note{margin-bottom:40px;padding-bottom:20px;border-bottom:1px solid #ccc;}</style></head><body>";
        $content .= "<h1>Notes Export</h1>";

        foreach ($notes as $note) {
            $content .= "<div class='note'>";
            $content .= "<h2>{$note->title}</h2>";
            $content .= "<div class='meta'>By " . ($note->user->name ?? 'Unknown') . " on {$note->created_at->format('F d, Y')}</div>";
            $content .= "<div class='content'>{$note->content}</div>";
            $content .= "</div>";
        }

        $content .= "</body></html>";

        return $content;
    }

    /**
     * Export multiple notes as Markdown
     */
    public static function collectionToMarkdown($notes): string
    {
        $content = "# Notes Export\n\n";
        $content .= "Exported on " . now()->format('F d, Y') . "\n\n";
        $content .= "---\n\n";

        foreach ($notes as $note) {
            $content .= "## {$note->title}\n\n";
            $content .= "*By " . ($note->user->name ?? 'Unknown') . " on {$note->created_at->format('F d, Y')}*\n\n";
            $content .= strip_tags($note->content) . "\n\n";
            $content .= "---\n\n";
        }

        return $content;
    }

    /**
     * Export multiple notes as Plain Text
     */
    public static function collectionToPlainText($notes): string
    {
        $content = "NOTES EXPORT\n";
        $content .= str_repeat("=", 50) . "\n";
        $content .= "Exported on " . now()->format('F d, Y') . "\n\n";

        foreach ($notes as $note) {
            $content .= strtoupper($note->title) . "\n";
            $content .= "By " . ($note->user->name ?? 'Unknown') . " on {$note->created_at->format('F d, Y')}\n\n";
            $content .= strip_tags($note->content) . "\n\n";
            $content .= str_repeat("-", 50) . "\n\n";
        }

        return $content;
    }
}
