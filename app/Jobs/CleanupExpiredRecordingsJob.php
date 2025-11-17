<?php

namespace App\Jobs;

use App\Models\Classroom\SessionRecording;
use App\Services\BigBlueButtonService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredRecordingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BigBlueButtonService $bbbService): void
    {
        $expiredRecordings = SessionRecording::where('status', 'published')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredRecordings as $recording) {
            try {
                // Delete from BBB if it's a BBB recording
                if ($recording->type === 'bbb' && $recording->recording_id) {
                    $bbbService->deleteRecording($recording->recording_id);
                }

                // Delete local files if they exist
                if ($recording->storage_path) {
                    Storage::disk($recording->storage_disk ?? 'public')
                        ->delete($recording->storage_path);
                }

                if ($recording->thumbnail_path) {
                    Storage::disk($recording->storage_disk ?? 'public')
                        ->delete($recording->thumbnail_path);
                }

                // Mark as deleted
                $recording->update(['status' => 'deleted']);
                $recording->delete();

                Log::info("Deleted expired recording {$recording->id}");

            } catch (\Exception $e) {
                Log::error('Failed to delete expired recording', [
                    'recording_id' => $recording->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Also cleanup old recordings based on auto_delete_days
        $autoDeleteDays = config('bigbluebutton.recordings.auto_delete_days');

        if ($autoDeleteDays) {
            $oldRecordings = SessionRecording::where('status', 'published')
                ->where('recorded_at', '<', now()->subDays($autoDeleteDays))
                ->get();

            foreach ($oldRecordings as $recording) {
                try {
                    if ($recording->type === 'bbb' && $recording->recording_id) {
                        $bbbService->deleteRecording($recording->recording_id);
                    }

                    if ($recording->storage_path) {
                        Storage::disk($recording->storage_disk ?? 'public')
                            ->delete($recording->storage_path);
                    }

                    $recording->update(['status' => 'deleted']);
                    $recording->delete();

                    Log::info("Auto-deleted old recording {$recording->id}");

                } catch (\Exception $e) {
                    Log::error('Failed to auto-delete old recording', [
                        'recording_id' => $recording->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
