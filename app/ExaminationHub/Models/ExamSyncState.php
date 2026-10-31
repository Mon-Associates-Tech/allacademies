<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSyncState extends Model
{
    use HasFactory;

    protected $table = 'general_exam_sync_state';

    protected $fillable = [
        'exam_uuid',
        'applied_revision',
        'applied_state_version',
        'checksum',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_revision' => 'integer',
            'applied_state_version' => 'integer',
            'applied_at' => 'datetime',
        ];
    }

    /**
     * Get the sync state for an exam, creating if it doesn't exist.
     */
    public static function getOrCreate(string $exam_uuid): self
    {
        return static::firstOrCreate(
            ['exam_uuid' => $exam_uuid],
            ['applied_revision' => 0, 'applied_state_version' => 0]
        );
    }

    /**
     * Check if a new revision needs to be applied.
     */
    public function needsRevision(int $revision): bool
    {
        return $revision > $this->applied_revision;
    }

    /**
     * Check if the state version needs to be updated.
     */
    public function needsStateUpdate(int $stateVersion): bool
    {
        return $stateVersion > $this->applied_state_version;
    }

    /**
     * Mark a revision as applied.
     */
    public function markRevisionApplied(int $revision, string $checksum): void
    {
        $this->update([
            'applied_revision' => $revision,
            'checksum' => $checksum,
            'applied_at' => now(),
        ]);
    }

    /**
     * Update the state version.
     */
    public function updateStateVersion(int $stateVersion): void
    {
        $this->update([
            'applied_state_version' => $stateVersion,
        ]);
    }
}
