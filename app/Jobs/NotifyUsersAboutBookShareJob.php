<?php

namespace App\Jobs;

use App\Models\UserBookShare;
use App\Notifications\UserBookSharedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyUsersAboutBookShareJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected UserBookShare $share;

    public function __construct(UserBookShare $share)
    {
        $this->share = $share;
    }

    public function handle(): void
    {
        try {
            // Get affected users based on share type
            $users = $this->share->getAffectedUsers();

            if ($users->isEmpty()) {
                Log::warning('No users found for book share', [
                    'share_id' => $this->share->id,
                    'share_type' => $this->share->share_type,
                ]);

                return;
            }

            // Send notifications in chunks to avoid memory issues
            $users->chunk(50)->each(function ($userChunk) {
                foreach ($userChunk as $user) {
                    if ($user && $user->id) {
                        try {
                            $user->notify(new UserBookSharedNotification($this->share));
                        } catch (\Exception $e) {
                            Log::error('Failed to notify user about book share', [
                                'user_id' => $user->id,
                                'share_id' => $this->share->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            });

            Log::info('Book share notifications sent', [
                'share_id' => $this->share->id,
                'user_count' => $users->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process book share notifications', [
                'share_id' => $this->share->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
