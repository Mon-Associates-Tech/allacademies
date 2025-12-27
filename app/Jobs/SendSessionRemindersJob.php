<?php

namespace App\Jobs;

use App\Models\Classroom\VirtualSession;
use App\Notifications\VirtualSessionReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSessionRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $minutesBefore = 15
    ) {}

    public function handle(): void
    {
        $startTime = now()->addMinutes($this->minutesBefore);

        $sessions = VirtualSession::where('status', 'scheduled')
            ->whereBetween('scheduled_start', [
                $startTime->copy()->subMinutes(5),
                $startTime->copy()->addMinutes(5),
            ])
            ->with('participants.user')
            ->get();

        foreach ($sessions as $session) {
            foreach ($session->participants as $participant) {
                if ($participant->user && in_array($participant->status, ['invited', 'joined'])) {
                    $participant->user->notify(
                        new VirtualSessionReminder($session, $this->minutesBefore)
                    );
                }
            }
        }
    }
}
