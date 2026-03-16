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
        'is_default',
    ];

    protected $casts = [
        'custom_columns' => 'array',
        'header_config' => 'array',
        'footer_config' => 'array',
        'is_default' => 'boolean',
    ];

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function getColumns(): array
    {
        $default = [
            ['name' => 'subject', 'label' => 'Subject', 'type' => 'text', 'required' => true],
            ['name' => 'class_score', 'label' => 'Class Score', 'type' => 'number', 'required' => true],
            ['name' => 'test_score', 'label' => 'Test Score', 'type' => 'number', 'required' => true],
            ['name' => 'exam_score', 'label' => 'Exam Score', 'type' => 'number', 'required' => true],
            ['name' => 'total_score', 'label' => 'Total Score', 'type' => 'number', 'required' => true],
            ['name' => 'grade', 'label' => 'Grade', 'type' => 'text', 'required' => true],
            ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => true],
        ];

        return array_merge($default, $this->custom_columns ?? []);
    }
}
