<?php

namespace App\Jobs;

use App\Models\Classroom\VirtualSession;
use App\Services\BigBlueButtonService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckEndedSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BigBlueButtonService $bbbService): void
    {
        // Check sessions that should have ended but are still marked as live
        $liveSessions = VirtualSession::where('status', 'live')
            ->where('scheduled_end', '<', now()->subMinutes(30))
            ->get();

        foreach ($liveSessions as $session) {
            try {
                // Check if meeting is actually still running
                if (! $bbbService->isMeetingRunning($session->meeting_id)) {
                    $session->update([
                        'status' => 'ended',
                        'actual_end' => now(),
                    ]);

                    Log::info("Marked session {$session->id} as ended");

                    // Trigger recording sync if auto-record was enabled
                    if ($session->auto_record) {
                        SyncSessionRecordings::dispatch($session)
                            ->delay(now()->addMinutes(10)); // BBB needs time to process
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to check session status', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
