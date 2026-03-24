<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IdCardTemplate extends Model
{
    use HasFactory;

    protected $table = 'id_card_templates';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'template_file',
        'default_fields',
        'required_fields',
        'preview_image',
        'orientation',
        'card_size',
        'is_active',
    ];

    protected $casts = [
        'default_fields' => 'array',
        'required_fields' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (IdCardTemplate $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getViewPath(): string
    {
        return 'components.id-cards.'.$this->template_file;
    }

    public function getPreviewUrl(): ?string
    {
        if ($this->preview_image) {
            return asset('storage/'.$this->preview_image);
        }

        return null;
    }

    public function getDefaultFieldValue(string $key, mixed $default = null): mixed
    {
        return $this->default_fields[$key] ?? $default;
    }

    public function isFieldRequired(string $field): bool
    {
        return in_array($field, $this->required_fields ?? []);
    }

    public static function getOrientations(): array
    {
        return [
            'portrait' => 'Portrait',
            'landscape' => 'Landscape',
        ];
    }

    public static function getCardSizes(): array
    {
        return [
            'standard' => 'Standard (85.6mm x 53.98mm)',
            'cr80' => 'CR80 (3.375" x 2.125")',
            'custom' => 'Custom',
        ];
    }

    public static function getRequiredFieldsList(): array
    {
        return [
            'school_name' => ['label' => 'School Name', 'source' => 'school.name'],
            'school_logo' => ['label' => 'School Logo', 'source' => 'school.logo'],
            'student_name' => ['label' => 'Student Name', 'source' => 'student.user.name'],
            'student_photo' => ['label' => 'Student Photo', 'source' => 'student.user.avatar'],
            'issue_date' => ['label' => 'Issue Date', 'source' => 'idCard.issue_date'],
            'expiry_date' => ['label' => 'Expiry Date', 'source' => 'idCard.expiry_date'],
            'student_id' => ['label' => 'Student ID', 'source' => 'student.student_id'],
            'card_number' => ['label' => 'Card Number', 'source' => 'idCard.card_number'],
        ];
    }

    public static function getOptionalFieldsList(): array
    {
        return [
            'academic_level' => ['label' => 'Class/Grade', 'source' => 'student.academicLevel.name'],
            'student_group' => ['label' => 'Section', 'source' => 'student.studentGroup.name'],
            'date_of_birth' => ['label' => 'Date of Birth', 'source' => 'student.date_of_birth'],
            'blood_group' => ['label' => 'Blood Group', 'source' => 'student.blood_group'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'source' => 'student.emergency_contact'],
            'address' => ['label' => 'Address', 'source' => 'student.address'],
            'barcode' => ['label' => 'Barcode', 'source' => 'idCard.barcode'],
            'qr_code' => ['label' => 'QR Code', 'generated' => true],
        ];
    }
}
