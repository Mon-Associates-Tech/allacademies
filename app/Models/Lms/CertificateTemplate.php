<?php

namespace App\Models\Lms;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $table = 'lms_certificate_templates';

    public const TYPE_COURSE = 'course';

    public const TYPE_ACHIEVEMENT = 'achievement';

    public const TYPE_PARTICIPATION = 'participation';

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'type',
        'description',
        'template_file',
        'default_fields',
        'background_image',
        'orientation',
        'paper_size',
        'is_active',
    ];

    protected $casts = [
        'default_fields' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CertificateTemplate $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name).'-'.Str::random(6);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function issuedCertificates(): HasMany
    {
        return $this->hasMany(IssuedCertificate::class, 'template_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForSchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->whereNull('school_id')
                ->orWhere('school_id', $schoolId);
        });
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('school_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isGlobal(): bool
    {
        return is_null($this->school_id);
    }

    public function isCourseTemplate(): bool
    {
        return $this->type === self::TYPE_COURSE;
    }

    public function isAchievementTemplate(): bool
    {
        return $this->type === self::TYPE_ACHIEVEMENT;
    }

    public function isParticipationTemplate(): bool
    {
        return $this->type === self::TYPE_PARTICIPATION;
    }

    public function getViewPath(): string
    {
        return 'components.certificates.'.$this->template_file;
    }

    public function getBackgroundUrl(): ?string
    {
        if ($this->background_image) {
            return asset('storage/'.$this->background_image);
        }

        return null;
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_COURSE => 'Course Completion',
            self::TYPE_ACHIEVEMENT => 'Achievement',
            self::TYPE_PARTICIPATION => 'Participation',
        ];
    }

    public static function getOrientations(): array
    {
        return [
            'landscape' => 'Landscape',
            'portrait' => 'Portrait',
        ];
    }

    public static function getPaperSizes(): array
    {
        return [
            'a4' => 'A4',
            'letter' => 'Letter',
            'legal' => 'Legal',
        ];
    }
}
