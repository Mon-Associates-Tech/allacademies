<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Notification;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $notifications = $staff->notifications()->paginate(20);

        return view('bookshop::staff.notifications.index', compact('notifications'));
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
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $staff->unreadNotifications->markAsRead();

        return back();
    }

    private function authorizeOwnership(Notification $notification): void
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $expectedTypes = array_unique([
            Staff::class,
            (new Staff())->getMorphClass(),
        ]);

        abort_unless(
            $staff !== null
            && $notification->notifiable_id === $staff->id
            && in_array($notification->notifiable_type, $expectedTypes, true),
            404
        );
    }

    private function resolveTargetUrl(Notification $notification): string
    {
        $url = $notification->data['url'] ?? route('bookshop.staff.notifications.index');

        if (! is_string($url) || $url === '') {
            return route('bookshop.staff.notifications.index');
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
