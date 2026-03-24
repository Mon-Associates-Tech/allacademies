<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class IssuedCertificate extends Model
{
    use HasFactory;

    protected $table = 'lms_issued_certificates';

    protected $fillable = [
        'template_id',
        'user_id',
        'course_id',
        'enrollment_id',
        'certificate_number',
        'recipient_name',
        'issue_date',
        'expiry_date',
        'custom_data',
        'verification_code',
        'pdf_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'custom_data' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (IssuedCertificate $certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = self::generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = (string) Str::uuid();
            }
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForCourse(Builder $query, Course $course): Builder
    {
        return $query->where('course_id', $course->id);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    public function isValid(): bool
    {
        if (is_null($this->expiry_date)) {
            return true;
        }

        return $this->expiry_date->isFuture() || $this->expiry_date->isToday();
    }

    public function isExpired(): bool
    {
        return ! $this->isValid();
    }

    public function getPdfUrl(): ?string
    {
        if ($this->pdf_path) {
            return asset('storage/'.$this->pdf_path);
        }

        return null;
    }

    public function getVerificationUrl(): string
    {
        return route('certificates.verify', ['code' => $this->verification_code]);
    }

    public function getCustomDataValue(string $key, mixed $default = null): mixed
    {
        return $this->custom_data[$key] ?? $default;
    }

    public static function generateCertificateNumber(): string
    {
        $prefix = 'CERT';
        $year = date('Y');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$year}-{$random}";
    }

    public static function findByVerificationCode(string $code): ?self
    {
        return static::where('verification_code', $code)->first();
    }
}
