<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;

class PreventDisabledMailSending
{
    public function handle(MessageSending $event): bool
    {
        $emailSendingEnabled = config('mail.enabled', true);

        if (! $emailSendingEnabled) {
            $to = $event->message->getTo();
            $recipients = is_array($to) ? implode(', ', array_keys($to)) : (string) $to;
            Log::info('Email sending is disabled. Prevented email to: '.$recipients);

            return false;
        }

        return true;
    }
}
