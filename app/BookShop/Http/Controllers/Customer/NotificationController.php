<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        $notifications = $customer->notifications()->paginate(20);

        return view('bookshop::customer.notifications.index', compact('notifications'));
    }

    /**
     * GET-navigable and reachable via a plain link (not a PATCH-only form) -
     * matches how notification links work everywhere else (Slack, GitHub,
     * email): click it, it opens, it happens to also mark itself read.
     * Bookmarkable/shareable as a side effect of being a real GET route.
     */
    public function open(Notification $notification): RedirectResponse
    {
        $this->authorizeOwnership($notification);

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('bookshop.shop.notifications.index'));
    }

    public function markAllAsRead(): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        $customer->unreadNotifications->markAsRead();

        return back();
    }

    private function authorizeOwnership(Notification $notification): void
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        abort_unless(
            $notification->notifiable_type === Customer::class && $notification->notifiable_id === $customer->id,
            404
        );
    }
}
