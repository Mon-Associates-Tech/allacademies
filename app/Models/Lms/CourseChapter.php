<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CourseChapter extends Model
{
    use HasFactory;

    protected $table = 'lms_chapters';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'chapter_id')->orderBy('order');
    }

    public function rootSections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'chapter_id')
            ->whereNull('parent_section_id')
            ->orderBy('order');
    }

    public function contents(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseContent::class,
            CourseSection::class,
            'chapter_id',
            'section_id'
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function getTotalSectionsCount(): int
    {
        return $this->sections()->count();
    }

    public function getTotalContentsCount(): int
    {
        return $this->contents()->count();
    }

    public function getNextOrder(): int
    {
        return ($this->course->chapters()->max('order') ?? 0) + 1;
    }
}
