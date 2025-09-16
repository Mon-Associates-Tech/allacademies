<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledMessages extends Command
{
    protected $signature = 'messages:send-scheduled';
    protected $description = 'Send all scheduled messages that are due';

    public function handle()
    {
        $this->info('Checking for scheduled messages...');

        $scheduledMessages = Message::where('status', Message::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($scheduledMessages->isEmpty()) {
            $this->info('No scheduled messages found.');
            return 0;
        }

        $this->info("Found {$scheduledMessages->count()} scheduled messages to send.");

        foreach ($scheduledMessages as $message) {
            try {
                $this->line("Dispatching message: {$message->id} - {$message->subject}");

                // Dispatch job to send the message
                SendMessageJob::dispatch($message);

                Log::info("Dispatched scheduled message job: {$message->id}");

            } catch (\Exception $e) {
                $this->error("Failed to dispatch message {$message->id}: " . $e->getMessage());

                $message->update(['status' => Message::STATUS_FAILED]);

                Log::error("Failed to dispatch scheduled message: {$message->id}", [
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Finished processing scheduled messages.');
        return 0;
    }
}
