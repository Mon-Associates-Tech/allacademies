<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncRun extends Model
{
    use HasFactory;

    public const DIRECTION_PULL = 'pull';
    public const DIRECTION_PUSH = 'push';

    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected $table = 'general_exam_sync_runs';

    protected $fillable = [
        'direction',
        'started_at',
        'finished_at',
        'status',
        'items_total',
        'items_ok',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'items_total' => 'integer',
            'items_ok' => 'integer',
        ];
    }

    /**
     * Start a new sync run.
     */
    public static function start(string $direction): self
    {
        return static::create([
            'direction' => $direction,
            'started_at' => now(),
            'status' => self::STATUS_RUNNING,
        ]);
    }

    /**
     * Mark the run as successful.
     */
    public function markSuccess(int $itemsTotal, int $itemsOk): void
    {
        $this->update([
            'finished_at' => now(),
            'status' => self::STATUS_SUCCESS,
            'items_total' => $itemsTotal,
            'items_ok' => $itemsOk,
        ]);
    }

    /**
     * Mark the run as failed.
     */
    public function markFailed(string $error, int $itemsTotal = 0, int $itemsOk = 0): void
    {
        $this->update([
            'finished_at' => now(),
            'status' => self::STATUS_FAILED,
            'items_total' => $itemsTotal,
            'items_ok' => $itemsOk,
            'error' => $error,
        ]);
    }

    /**
     * Scope for pull runs.
     */
    public function scopePull($query)
    {
        return $query->where('direction', self::DIRECTION_PULL);
    }

    /**
     * Scope for push runs.
     */
    public function scopePush($query)
    {
        return $query->where('direction', self::DIRECTION_PUSH);
    }

    /**
     * Scope for failed runs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Get the most recent run for a direction.
     */
    public static function latestForDirection(string $direction): ?self
    {
        return static::where('direction', $direction)
            ->orderByDesc('started_at')
            ->first();
    }

    /**
     * Clean up stale "running" sync runs that exceeded their timeout.
     *
     * If a job crashes without completing, the SyncRun stays in "running"
     * state. This method marks any run older than $timeoutMinutes as failed
     * so that future syncs are not blocked.
     *
     * @param int $timeoutMinutes Maximum allowed runtime in minutes
     * @return int Number of stale runs cleaned up
     */
    public static function cleanupStaleRunning(int $timeoutMinutes = 30): int
    {
        $stale = static::where('status', 'running')
            ->where('started_at', '<', now()->subMinutes($timeoutMinutes))
            ->update([
                'status' => self::STATUS_FAILED,
                'finished_at' => now(),
                'error' => 'Timed out — job did not complete within ' . $timeoutMinutes . ' minutes',
            ]);

        if ($stale > 0) {
            \Illuminate\Support\Facades\Log::warning("SyncRun: Cleaned up {$stale} stale running sync run(s).");
        }

        return $stale;
    }
}
