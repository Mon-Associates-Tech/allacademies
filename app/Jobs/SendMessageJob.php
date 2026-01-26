<?php

namespace App\Jobs;

use App\Mail\MessageNotificationMail;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    protected Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle(MessageService $messageService)
    {
        try {
            Log::info("Starting to send message: {$this->message->id}");

            // Update status to sending
            $this->message->update(['status' => Message::STATUS_SENDING]);

            // Get recipients
            $recipients = $messageService->getRecipientsForMessage($this->message);

            // Create recipient records if they don't exist
            foreach ($recipients as $recipient) {
                MessageRecipient::firstOrCreate([
                    'message_id' => $this->message->id,
                    'user_id' => $recipient->id,
                ]);
            }

            // Send email notifications
            $this->sendEmailNotifications();

            // Update message status
            $this->message->update([
                'status' => Message::STATUS_SENT,
                'sent_at' => now(),
            ]);

            Log::info("Successfully sent message: {$this->message->id}");

        } catch (\Exception $e) {
            Log::error("Failed to send message {$this->message->id}: ".$e->getMessage());

            $this->message->update(['status' => Message::STATUS_FAILED]);

            throw $e;
        }
    }

    protected function sendEmailNotifications()
    {
        $recipients = $this->message->recipients()->with('user')->get();

        foreach ($recipients as $recipient) {
            try {
                // Send email using your preferred mail class
                Mail::to($recipient->user->email)->send(new MessageNotificationMail($this->message, $recipient));

                $recipient->update([
                    'email_sent' => true,
                    'email_sent_at' => now(),
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to send email to {$recipient->email} for message {$this->message->id}: ".$e->getMessage());

                $recipient->update([
                    'email_failed_at' => now(),
                    'failure_reason' => $e->getMessage(),
                ]);
            }
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("Message job failed permanently: {$this->message->id}", [
            'exception' => $exception->getMessage(),
        ]);

        $this->message->update(['status' => Message::STATUS_FAILED]);
    }
}
