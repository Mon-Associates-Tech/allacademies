<?php

namespace App\Services\Health;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HealthCheckService
{
    /**
     * Run all registered health checks and return results.
     */
    public function runAll(): array
    {
        return [
            $this->checkDatabase(),
            $this->checkCache(),
            $this->checkQueue(),
            $this->checkScheduler(),
            $this->checkMail(),
            $this->checkNotifications(),
            $this->checkStorage(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Database
    // ─────────────────────────────────────────────────────────────

    protected function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $latency = $this->ms($start);

            $driver = config('database.default');
            $extra  = [];

            // MySQL / MariaDB: grab thread count & slow query counter
            if (in_array($driver, ['mysql', 'mariadb'])) {
                try {
                    $threads     = DB::selectOne("SHOW STATUS LIKE 'Threads_connected'");
                    $slowQueries = DB::selectOne("SHOW STATUS LIKE 'Slow_queries'");
                    $extra       = [
                        'connections'  => $threads?->Value ?? 'N/A',
                        'slow_queries' => $slowQueries?->Value ?? 0,
                    ];
                } catch (Exception) {
                    // not critical
                }
            }

            $status = match (true) {
                $latency < 100  => 'operational',
                $latency < 500  => 'degraded',
                default         => 'down',
            };

            return $this->result('database', 'Database', $status,
                "Connected · {$driver}", $latency,
                array_merge(['driver' => $driver], $extra));

        } catch (Exception $e) {
            return $this->result('database', 'Database', 'down', $e->getMessage(), null);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Cache
    // ─────────────────────────────────────────────────────────────

    protected function checkCache(): array
    {
        $start = microtime(true);
        try {
            $key = 'health_check_cache_' . time();
            Cache::put($key, 'ping', 30);
            $value = Cache::get($key);
            Cache::forget($key);
            $latency = $this->ms($start);

            if ($value !== 'ping') {
                return $this->result('cache', 'Cache', 'degraded',
                    'Read/write mismatch detected', $latency,
                    ['driver' => config('cache.default')]);
            }

            return $this->result('cache', 'Cache',
                $latency < 50 ? 'operational' : 'degraded',
                'Read/write successful', $latency,
                ['driver' => config('cache.default')]);

        } catch (Exception $e) {
            return $this->result('cache', 'Cache', 'down', $e->getMessage(), null);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Queue
    // ─────────────────────────────────────────────────────────────

    protected function checkQueue(): array
    {
        $start = microtime(true);
        try {
            $failedCount  = 0;
            $pendingCount = 0;
            $stuckCount   = 0;

            if (Schema::hasTable('failed_jobs')) {
                $failedCount = DB::table('failed_jobs')
                    ->where('failed_at', '>=', now()->subHours(24))
                    ->count();
            }

            if (Schema::hasTable('jobs')) {
                $pendingCount = DB::table('jobs')->count();
                $stuckCount   = DB::table('jobs')
                    ->where('available_at', '<', now()->subMinutes(15)->timestamp)
                    ->count();
            }

            $latency = $this->ms($start);

            $status  = 'operational';
            $message = 'Queue is healthy';

            if ($failedCount > 10) {
                $status  = 'down';
                $message = "{$failedCount} failed jobs in last 24 h";
            } elseif ($failedCount > 0 || $stuckCount > 0) {
                $status  = 'degraded';
                $message = "{$failedCount} failed, {$stuckCount} stuck jobs";
            }

            return $this->result('queue', 'Queue', $status, $message, $latency, [
                'driver'    => config('queue.default'),
                'pending'   => $pendingCount,
                'failed_24h' => $failedCount,
                'stuck'     => $stuckCount,
            ]);

        } catch (Exception $e) {
            return $this->result('queue', 'Queue', 'degraded',
                'Queue tables not found', null, ['error' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Scheduler
    // ─────────────────────────────────────────────────────────────

    /**
     * Requires you to add the heartbeat command in your Kernel.php:
     *
     *   $schedule->call(fn() => Cache::put('scheduler:heartbeat', now(), 300))
     *            ->everyMinute()
     *            ->name('scheduler:heartbeat');
     */
    protected function checkScheduler(): array
    {
        $start = microtime(true);
        try {
            $heartbeat = Cache::get('scheduler:heartbeat');
            $latency   = $this->ms($start);

            if (! $heartbeat) {
                return $this->result('scheduler', 'Scheduler', 'degraded',
                    'No heartbeat — ensure cron is running', $latency,
                    ['last_run' => 'Unknown']);
            }

            $lastRun      = Carbon::parse($heartbeat);
            $minutesSince = $lastRun->diffInMinutes(now());

            $status = match (true) {
                $minutesSince <= 5  => 'operational',
                $minutesSince <= 15 => 'degraded',
                default             => 'down',
            };

            return $this->result('scheduler', 'Scheduler', $status,
                "Last run {$minutesSince}m ago", $latency,
                ['last_run' => $lastRun->diffForHumans()]);

        } catch (Exception $e) {
            return $this->result('scheduler', 'Scheduler', 'degraded', $e->getMessage(), null);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Mail
    // ─────────────────────────────────────────────────────────────

    protected function checkMail(): array
    {
        $start  = microtime(true);
        $mailer = config('mail.default', config('mail.driver', 'smtp'));

        try {
            $host = config("mail.mailers.{$mailer}.host", config('mail.host', ''));
            $port = config("mail.mailers.{$mailer}.port", config('mail.port', 587));

            if ($mailer === 'smtp' && $host) {
                $conn    = @fsockopen($host, (int) $port, $errno, $errstr, 5);
                $latency = $this->ms($start);

                if ($conn) {
                    fclose($conn);
                    return $this->result('mail', 'Mail', 'operational',
                        "SMTP reachable ({$host}:{$port})", $latency,
                        ['driver' => $mailer, 'host' => $host, 'port' => $port]);
                }

                return $this->result('mail', 'Mail', 'down',
                    "SMTP unreachable: {$errstr}", $latency,
                    ['driver' => $mailer, 'host' => $host, 'port' => $port]);
            }

            $latency = $this->ms($start);
            return $this->result('mail', 'Mail', 'operational',
                ucfirst($mailer) . ' driver configured', $latency,
                ['driver' => $mailer]);

        } catch (Exception $e) {
            return $this->result('mail', 'Mail', 'degraded', $e->getMessage(), null,
                ['driver' => $mailer]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Notifications
    // ─────────────────────────────────────────────────────────────

    protected function checkNotifications(): array
    {
        $start = microtime(true);
        try {
            $broadcast = config('broadcasting.default', 'log');
            $hasTable  = Schema::hasTable('notifications');
            $latency   = $this->ms($start);

            $details = [
                'broadcast_driver' => $broadcast,
                'database_channel' => $hasTable ? 'available' : 'table missing',
                'mail_channel'     => config('mail.default', 'N/A'),
            ];

            // Warn if notifications table is missing
            $status  = $hasTable ? 'operational' : 'degraded';
            $message = $hasTable
                ? 'Channels configured'
                : 'Notifications table not found — run migrations';

            return $this->result('notifications', 'Notifications', $status, $message, $latency, $details);

        } catch (Exception $e) {
            return $this->result('notifications', 'Notifications', 'degraded', $e->getMessage(), null);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Storage
    // ─────────────────────────────────────────────────────────────

    protected function checkStorage(): array
    {
        $start = microtime(true);
        try {
            $path     = storage_path('app');
            $tmpFile  = $path . '/health_' . time() . '.tmp';

            file_put_contents($tmpFile, 'ok');
            $read = file_get_contents($tmpFile);
            unlink($tmpFile);

            $latency  = $this->ms($start);
            $free     = disk_free_space($path);
            $total    = disk_total_space($path);
            $usedPct  = $total > 0 ? round((1 - $free / $total) * 100, 1) : 0;

            $status = match (true) {
                $usedPct < 80  => 'operational',
                $usedPct < 90  => 'degraded',
                default        => 'down',
            };

            return $this->result('storage', 'Storage',
                $read === 'ok' ? $status : 'degraded',
                "Read/write OK · {$usedPct}% used", $latency,
                [
                    'free'     => $this->bytes($free),
                    'total'    => $this->bytes($total),
                    'used'     => $usedPct . '%',
                    'writable' => is_writable($path) ? 'yes' : 'no',
                ]);

        } catch (Exception $e) {
            return $this->result('storage', 'Storage', 'down', $e->getMessage(), null);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    protected function result(
        string  $key,
        string  $name,
        string  $status,
        string  $message,
        ?float  $latency,
        array   $details = []
    ): array {
        return [
            'key'        => $key,
            'name'       => $name,
            'status'     => $status,       // operational | degraded | down
            'message'    => $message,
            'latency'    => $latency,      // milliseconds
            'details'    => $details,
            'checked_at' => now()->toDateTimeString(),
        ];
    }

    private function ms(float $start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

    private function bytes(float $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB', 'TB'] as $unit) {
            if ($bytes < 1024) return round($bytes, 1) . ' ' . $unit;
            $bytes /= 1024;
        }
        return round($bytes, 1) . ' PB';
    }
}