<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCardTemplate extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_level_id',
        'name',
        'custom_columns',
        'header_config',
        'footer_config',
        'sections',
        'is_default',
    ];

    protected $casts = [
        'custom_columns' => 'array',
        'header_config' => 'array',
        'footer_config' => 'array',
        'sections' => 'array',
        'is_default' => 'boolean',
    ];

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    /**
     * The sensible starting point for a new template — every section
     * present and enabled with defaults, so the builder UI never has to
     * special-case a null section.
     */
    public static function defaultSections(): array
    {
        return [
            'header' => [
                'show_logo' => true,
                'show_school_name' => true,
                'show_tagline' => true,
                'show_contact' => true,
                'tagline' => null,
            ],
            'student_info' => [
                'fields' => ['name', 'student_id', 'class', 'academic_year', 'term', 'report_date'],
            ],
            'grades_table' => [
                'enabled' => true,
                // Weighting columns are resolved at render time from
                // ScoreWeighting for the template's academic_level_id —
                // not duplicated here, so a single edit to weightings
                // stays in sync with every template that uses that level.
            ],
            'attendance' => [
                'enabled' => true,
                'label' => 'Attendance',
            ],
            'remarks' => [
                'enabled' => true,
                'label' => "Class Teacher's Remarks",
            ],
            'signatures' => [
                'slots' => [
                    ['key' => 'class_teacher', 'label' => 'Class Teacher'],
                    ['key' => 'principal', 'label' => 'Head of School'],
                ],
            ],
            'footer' => [
                'enabled' => true,
                'text' => null, // null = falls back to "official document of {school}" default at render time
            ],
        ];
    }

    /**
     * Merge stored sections onto the defaults, so adding a new section key
     * in a future release doesn't break templates saved before it existed.
     */
    public function resolvedSections(): array
    {
        return array_replace_recursive(self::defaultSections(), $this->sections ?? []);
    }

    public function section(string $key, mixed $default = null): mixed
    {
        return $this->resolvedSections()[$key] ?? $default;
    }
}
