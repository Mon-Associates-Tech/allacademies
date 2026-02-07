<?php

namespace App\Models\Lms;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_courses';

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'slug',
        'description',
        'objectives',
        'thumbnail',
        'difficulty_level',
        'audience',
        'price',
        'is_free',
        'status',
        'estimated_duration_minutes',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'metadata' => 'array',
        'published_at' => 'datetime',
        'estimated_duration_minutes' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title).'-'.Str::random(6);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(CourseChapter::class, 'course_id')->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(IssuedCertificate::class, 'course_id');
    }

    public function sections(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseSection::class,
            CourseChapter::class,
            'course_id',
            'chapter_id'
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_free', true);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_free', false);
    }

    public function scopeForAudience(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->where('audience', 'public')
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('audience', 'school_only')
                        ->where('school_id', $user->school_id);
                });
        });
    }

    public function scopeByDifficulty(Builder $query, string $level): Builder
    {
        return $query->where('difficulty_level', $level);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'status' => 'unpublished',
        ]);
    }

    public function getTotalContentsCount(): int
    {
        return $this->chapters()
            ->with('sections.contents')
            ->get()
            ->flatMap->sections
            ->flatMap->contents
            ->count();
    }

    public function getRequiredContentsCount(): int
    {
        return $this->chapters()
            ->with('sections.contents')
            ->get()
            ->flatMap->sections
            ->flatMap->contents
            ->where('is_required', true)
            ->count();
    }

    public function isEnrolled(User $user): bool
    {
        return $this->enrollments()->where('user_id', $user->id)->exists();
    }

    public function getEnrollment(User $user): ?CourseEnrollment
    {
        return $this->enrollments()->where('user_id', $user->id)->first();
    }
}
