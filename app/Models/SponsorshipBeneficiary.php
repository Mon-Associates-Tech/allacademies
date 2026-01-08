<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorshipBeneficiary extends Model
{
    use HasFactory;

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_STUDENT = 'student';
    const TYPE_GROUP = 'group';
    const TYPE_ORGANIZATION = 'organization';
    protected $fillable = [
        'sponsorship_project_id',
        'beneficiary_type',
        'student_id',
        'beneficiary_name',
        'beneficiary_email',
        'beneficiary_phone',
        'beneficiary_description',
        'beneficiary_details',
    ];
    protected $casts = [
        'beneficiary_details' => 'array',
    ];

    /**
     * Get available types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_INDIVIDUAL => 'Individual',
            self::TYPE_STUDENT => 'Student',
            self::TYPE_GROUP => 'Group',
            self::TYPE_ORGANIZATION => 'Organization',
        ];
    }

    /**
     * Get the sponsorships project this beneficiary belongs to
     */
    public function sponsorshipProject(): BelongsTo
    {
        return $this->belongsTo(SponsorshipProject::class);
    }

    /**
     * Get the student if this beneficiary is linked to one
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Check if this beneficiary is an individual
     */
    public function isIndividual(): bool
    {
        return $this->beneficiary_type === self::TYPE_INDIVIDUAL;
    }

    /**
     * Check if this beneficiary is a group
     */
    public function isGroup(): bool
    {
        return $this->beneficiary_type === self::TYPE_GROUP;
    }

    /**
     * Check if this beneficiary is an organization
     */
    public function isOrganization(): bool
    {
        return $this->beneficiary_type === self::TYPE_ORGANIZATION;
    }

    /**
     * Get the display name for the beneficiary
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->user->name ?? $this->beneficiary_name;
        }

        return $this->beneficiary_name;
    }

    /**
     * Check if this beneficiary is a student
     */
    public function isStudent(): bool
    {
        return $this->beneficiary_type === self::TYPE_STUDENT && $this->student_id !== null;
    }

    /**
     * Get the display email for the beneficiary
     */
    public function getDisplayEmailAttribute(): ?string
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->user->email ?? $this->beneficiary_email;
        }

        return $this->beneficiary_email;
    }

    /**
     * Scope to get beneficiaries by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('beneficiary_type', $type);
    }

    /**
     * Scope to get student beneficiaries
     */
    public function scopeStudents($query)
    {
        return $query->where('beneficiary_type', self::TYPE_STUDENT);
    }

    /**
     * Scope to get individual beneficiaries
     */
    public function scopeIndividuals($query)
    {
        return $query->where('beneficiary_type', self::TYPE_INDIVIDUAL);
    }
}
