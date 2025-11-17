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

class SyncSessionRecordingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public VirtualSession $session
    ) {}

    public function handle(BigBlueButtonService $bbbService): void
    {
        try {
            $count = $bbbService->syncRecordings($this->session);

            Log::info("Synced {$count} recordings for session {$this->session->id}");
        } catch (\Exception $e) {
            Log::error('Failed to sync recordings', [
                'session_id' => $this->session->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
