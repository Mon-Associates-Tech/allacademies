<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageTemplate extends Model
{
    use BelongsToSchoolEnhanced, SoftDeletes;

    protected $fillable = [
        'school_id',
        'slug',
        'name',
        'category',
        'subject',
        'body',
        'sms_body',
        'available_variables',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_system'           => 'boolean',
        'is_active'           => 'boolean',
    ];

    // System templates have null school_id — exclude them from school scope deletion
    protected static bool $schoolRestricted = false;

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /** Replace {{variable}} placeholders with actual values. */
    public function renderSubject(array $variables): string
    {
        return $this->replacePlaceholders($this->subject, $variables);
    }

    public function renderBody(array $variables): string
    {
        return $this->replacePlaceholders($this->body, $variables);
    }

    public function renderSmsBody(array $variables): string
    {
        return $this->replacePlaceholders($this->sms_body ?? '', $variables);
    }

    protected function replacePlaceholders(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{'.$key.'}}', $value ?? '', $text);
        }

        return $text;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchoolOrSystem($query, int $schoolId)
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereNull('school_id');
        });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
