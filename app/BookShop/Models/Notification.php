<?php

namespace App\BookShop\Models;

use Illuminate\Notifications\DatabaseNotification;

/**
 * Own notifications table (bookshop_notifications) rather than assuming
 * the host app's default `notifications` table exists — keeps the module
 * extractable per the project's standalone-module goal. Staff and
 * Customer both override notifications()/readNotifications()/
 * unreadNotifications() to point here instead of the framework default.
 */
class Notification extends DatabaseNotification
{
    protected $table = 'bookshop_notifications';
}
