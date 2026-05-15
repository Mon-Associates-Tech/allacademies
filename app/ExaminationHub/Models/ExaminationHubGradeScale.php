<?php

namespace App\ExaminationHub\Models;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Grading scale specific to the Examinations Hub.
 * Deliberately separate from the academic GradeScale model.
 * Uses percentage ranges rather than raw scores.
 */
class ExaminationHubGradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'grade_label',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'is_passing',
        'color_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_percentage' => 'integer',
            'max_percentage' => 'integer',
            'grade_point'    => 'float',
            'is_passing'     => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /** Active grades ordered from highest threshold to lowest. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderByDesc('min_percentage');
    }

    /** Grades created by a specific user. */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Grades scoped to a school; pass null to match records where school_id IS NULL
     * (user-global defaults).
     */
    public function scopeForSchool($query, ?int $schoolId)
    {
        return $schoolId
            ? $query->where('school_id', $schoolId)
            : $query->whereNull('school_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Does the given percentage fall within this grade's range? */
    public function matches(float $percentage): bool
    {
        return $percentage >= $this->min_percentage
            && $percentage <= $this->max_percentage;
    }

    /** Default A+–F scale seeded when a user initialises the system. */
    public static function defaults(): array
    {
        return [
            ['grade_label' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'grade_point' => 4.00, 'is_passing' => true,  'color_code' => '#16A34A'],
            ['grade_label' => 'A',  'min_percentage' => 80, 'max_percentage' => 89,  'grade_point' => 4.00, 'is_passing' => true,  'color_code' => '#22C55E'],
            ['grade_label' => 'B+', 'min_percentage' => 75, 'max_percentage' => 79,  'grade_point' => 3.50, 'is_passing' => true,  'color_code' => '#84CC16'],
            ['grade_label' => 'B',  'min_percentage' => 70, 'max_percentage' => 74,  'grade_point' => 3.00, 'is_passing' => true,  'color_code' => '#A3E635'],
            ['grade_label' => 'C+', 'min_percentage' => 65, 'max_percentage' => 69,  'grade_point' => 2.50, 'is_passing' => true,  'color_code' => '#FACC15'],
            ['grade_label' => 'C',  'min_percentage' => 60, 'max_percentage' => 64,  'grade_point' => 2.00, 'is_passing' => true,  'color_code' => '#FB923C'],
            ['grade_label' => 'D',  'min_percentage' => 50, 'max_percentage' => 59,  'grade_point' => 1.00, 'is_passing' => true,  'color_code' => '#F97316'],
            ['grade_label' => 'F',  'min_percentage' => 0,  'max_percentage' => 49,  'grade_point' => 0.00, 'is_passing' => false, 'color_code' => '#EF4444'],
        ];
    }
}
