<?php

namespace App\BookShop\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

abstract class BookShopNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Applies a standalone "from" identity for BookShop's own emails if
     * BOOKSHOP_MAIL_FROM_ADDRESS is set in .env, otherwise falls through
     * to the host app's default mail "from" config untouched. Optional -
     * BookShop works fine sharing the host app's mail identity too.
     */
    protected function applyFrom(MailMessage $message): MailMessage
    {
        $address = config('bookshop.mail_from_address') ?: env('BOOKSHOP_MAIL_FROM_ADDRESS');

        if ($address) {
            return $message->from($address, env('BOOKSHOP_MAIL_FROM_NAME', 'BookShop'));
        }

        return $message;
    }
}
