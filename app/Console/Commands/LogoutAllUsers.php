<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class LogoutAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:logout-all {--except-current : Keep current user logged in if running via web}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logout all users by invalidating their sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentUserId = auth()->id();
        $exceptCurrent = $this->option('except-current');
        $sessionDriver = Config::get('session.driver');

        $this->info("Session driver: {$sessionDriver}");

        $deletedSessions = 0;

        // Handle different session drivers
        switch ($sessionDriver) {
            case 'database':
                $deletedSessions = $this->clearDatabaseSessions($currentUserId, $exceptCurrent);
                break;

            case 'file':
                $deletedSessions = $this->clearFileSessions($currentUserId, $exceptCurrent);
                break;

            case 'redis':
                $deletedSessions = $this->clearRedisSessions($currentUserId, $exceptCurrent);
                break;

            default:
                $this->warn("Session driver '{$sessionDriver}' not fully supported. Clearing cache and remember tokens only.");
        }

        // Clear all cache that might contain session data
        Cache::flush();
        $this->info("Cache cleared");

        // Update all users' remember_token to invalidate "remember me" sessions
        $this->clearRememberTokens($currentUserId, $exceptCurrent);

        // Update online status
        $this->updateOnlineStatus($currentUserId, $exceptCurrent);

        $message = $exceptCurrent && $currentUserId
            ? "All users logged out except current user (ID: {$currentUserId})"
            : "All users have been logged out successfully";

        $this->info($message);
        $this->info("Processed {$deletedSessions} session records");

        return 0;
    }

    private function clearDatabaseSessions($currentUserId, $exceptCurrent)
    {
        try {
            if ($exceptCurrent && $currentUserId) {
                return DB::table('sessions')
                    ->where('user_id', '!=', $currentUserId)
                    ->delete();
            } else {
                return DB::table('sessions')->delete();
            }
        } catch (\Exception $e) {
            $this->error("Database sessions error: " . $e->getMessage());
            return 0;
        }
    }

    private function clearFileSessions($currentUserId, $exceptCurrent)
    {
        $sessionPath = storage_path('framework/sessions');

        if (!File::exists($sessionPath)) {
            $this->warn("Session directory not found: {$sessionPath}");
            return 0;
        }

        $files = File::files($sessionPath);
        $deletedCount = 0;
        $currentSessionId = session()->getId();

        foreach ($files as $file) {
            try {
                $sessionData = file_get_contents($file->getPathname());

                if ($sessionData === false) {
                    continue;
                }

                // Try to unserialize session data
                $data = $this->unserializeSession($sessionData);

                // Check if this session belongs to current user
                if ($exceptCurrent && $currentUserId) {
                    $sessionUserId = $data['_token'] ?? null;

                    // Skip current user's session if we want to keep it
                    if ($file->getFilename() === "sess_{$currentSessionId}") {
                        continue;
                    }

                    // Try to extract user ID from session data
                    if (isset($data['login_web_' . sha1('web')])) {
                        $sessionUserId = $data['login_web_' . sha1('web')];
                        if ($sessionUserId == $currentUserId) {
                            continue;
                        }
                    }
                }

                // Delete the session file
                if (File::delete($file->getPathname())) {
                    $deletedCount++;
                }

            } catch (\Exception $e) {
                $this->warn("Could not process session file {$file->getFilename()}: " . $e->getMessage());
                continue;
            }
        }

        return $deletedCount;
    }

    private function clearRedisSessions($currentUserId, $exceptCurrent)
    {
        try {
            $redis = app('redis');
            $prefix = Config::get('session.cookie') . ':*';
            $keys = $redis->keys($prefix);

            $deletedCount = 0;
            $currentSessionId = session()->getId();

            foreach ($keys as $key) {
                if ($exceptCurrent && $currentUserId) {
                    // Skip current user's session
                    if (str_contains($key, $currentSessionId)) {
                        continue;
                    }
                }

                $redis->del($key);
                $deletedCount++;
            }

            return $deletedCount;
        } catch (\Exception $e) {
            $this->error("Redis sessions error: " . $e->getMessage());
            return 0;
        }
    }

    private function clearRememberTokens($currentUserId, $exceptCurrent)
    {
        try {
            if ($exceptCurrent && $currentUserId) {
                $updated = DB::table('users')
                    ->where('id', '!=', $currentUserId)
                    ->update(['remember_token' => null]);
            } else {
                $updated = DB::table('users')->update(['remember_token' => null]);
            }

            $this->info("Cleared remember tokens for {$updated} users");
        } catch (\Exception $e) {
            $this->error("Error clearing remember tokens: " . $e->getMessage());
        }
    }

    private function updateOnlineStatus($currentUserId, $exceptCurrent)
    {
        try {
            if ($exceptCurrent && $currentUserId) {
                $updated = DB::table('users')
                    ->where('id', '!=', $currentUserId)
                    ->update([
                        'is_online' => false,
                        'last_seen_at' => now()
                    ]);
            } else {
                $updated = DB::table('users')->update([
                    'is_online' => false,
                    'last_seen_at' => now()
                ]);
            }

            $this->info("Updated online status for {$updated} users");
        } catch (\Exception $e) {
            $this->error("Error updating online status: " . $e->getMessage());
        }
    }

    private function unserializeSession($sessionData)
    {
        $data = [];
        $offset = 0;

        while ($offset < strlen($sessionData)) {
            if (!strstr(substr($sessionData, $offset), "|")) {
                break;
            }

            $pos = strpos($sessionData, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($sessionData, $offset, $num);
            $offset += $num + 1;
            $serializedData = substr($sessionData, $offset);

            try {
                $data[$varname] = unserialize($serializedData);
                break; // For simplicity, just get the first variable
            } catch (\Exception $e) {
                break;
            }
        }

        return $data;
    }
}
