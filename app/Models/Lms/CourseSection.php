<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory;

    protected $table = 'lms_sections';

    protected $fillable = [
        'chapter_id',
        'parent_section_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(CourseChapter::class, 'chapter_id');
    }

    public function parentSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'parent_section_id');
    }

    public function subsections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'parent_section_id')->orderBy('order');
    }

    public function allSubsections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'parent_section_id')
            ->with('allSubsections')
            ->orderBy('order');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CourseContent::class, 'section_id')->orderBy('order');
    }

    public function course(): ?Course
    {
        return $this->chapter?->course;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_section_id');
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_section_id);
    }

    public function hasSubsections(): bool
    {
        return $this->subsections()->exists();
    }

    public function getTotalContentsCount(): int
    {
        $count = $this->contents()->count();

        foreach ($this->subsections as $subsection) {
            $count += $subsection->getTotalContentsCount();
        }

        return $count;
    }

    public function getRequiredContentsCount(): int
    {
        $count = $this->contents()->where('is_required', true)->count();

        foreach ($this->subsections as $subsection) {
            $count += $subsection->getRequiredContentsCount();
        }

        return $count;
    }

    public function getDepth(): int
    {
        $depth = 0;
        $parent = $this->parentSection;

        while ($parent) {
            $depth++;
            $parent = $parent->parentSection;
        }

        return $depth;
    }

    public function getNextOrder(): int
    {
        if ($this->parent_section_id) {
            return ($this->parentSection->subsections()->max('order') ?? 0) + 1;
        }

        return ($this->chapter->sections()->whereNull('parent_section_id')->max('order') ?? 0) + 1;
    }
}
