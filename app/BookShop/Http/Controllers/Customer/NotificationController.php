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
    public function open(Notification $alerts): RedirectResponse
    {
        $this->authorizeOwnership($alerts);

        $alerts->markAsRead();

        return redirect($this->resolveTargetUrl($alerts));
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

        $expectedTypes = array_unique([
            Customer::class,
            (new Customer())->getMorphClass(),
        ]);

        abort_unless(
            $customer !== null
            && $notification->notifiable_id === $customer->id
            && in_array($notification->notifiable_type, $expectedTypes, true),
            404
        );
    }

    private function resolveTargetUrl(Notification $notification): string
    {
        $url = $notification->data['url'] ?? route('bookshop.shop.notifications.index');

        if (! is_string($url) || $url === '') {
            return route('bookshop.shop.notifications.index');
        }

        if (! str_contains($url, '://')) {
            return $url;
        }

        $parts = parse_url($url);

        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';
        $fragment = $parts['fragment'] ?? '';

        $target = $path;
        if ($query !== '') {
            $target .= '?'.$query;
        }
        if ($fragment !== '') {
            $target .= '#'.$fragment;
        }

        return $target;
    }
}
