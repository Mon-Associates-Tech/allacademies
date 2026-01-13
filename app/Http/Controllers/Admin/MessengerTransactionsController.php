<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat\SubscriptionCycle;
use Illuminate\Http\Request;

class MessengerTransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin() && !auth()->user()->isOwner()) {
                abort(403, 'Unauthorized access');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = SubscriptionCycle::with(['user', 'pricingTier'])
            ->where('allocated_by_admin', false)
            ->where('current_price', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(50);

        $stats = [
            'total_subscriptions' => SubscriptionCycle::where('allocated_by_admin', false)
                ->where('current_price', '>', 0)
                ->count(),
            'active_subscriptions' => SubscriptionCycle::where('allocated_by_admin', false)
                ->where('current_price', '>', 0)
                ->where('status', 'active')
                ->count(),
            'total_revenue' => SubscriptionCycle::where('allocated_by_admin', false)
                ->where('current_price', '>', 0)
                ->sum('current_price'),
            'total_tokens_allocated' => SubscriptionCycle::where('allocated_by_admin', false)
                ->where('current_price', '>', 0)
                ->sum('tokens_allocated'),
            'total_tokens_used' => SubscriptionCycle::where('allocated_by_admin', false)
                ->where('current_price', '>', 0)
                ->sum('tokens_used'),
        ];

        return view('token-subscriptions.messenger-transactions', compact('subscriptions', 'stats'));
    }
}
