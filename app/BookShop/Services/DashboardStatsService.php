<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\OrderStatus;
use App\BookShop\Enums\RestockRequestStatus;
use App\BookShop\Models\Branch;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Order;
use App\BookShop\Models\RestockRequest;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Cross-branch totals for the superadmin dashboard, plus a per-branch
     * breakdown so "stats of all branches combined" includes both the
     * combined figure and where it's coming from.
     */
    public function forSuperAdmin(): array
    {
        $totalRevenue = Order::query()->where('status', OrderStatus::COMPLETED)->sum('subtotal');

        // Raw query builder (not the Eloquent model) here deliberately —
        // pluck()'ing a column the model casts to a backed enum would try
        // to use the enum object as an array key and throw a TypeError.
        // Plain query builder rows aren't cast, so 'status' stays a string.
        $ordersByStatus = DB::table('bookshop_orders')
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $perBranch = Branch::query()
            ->withCount([
                'orders',
                'orders as pending_orders_count' => fn ($q) => $q->where('status', OrderStatus::PENDING),
            ])
            ->withSum(['orders as revenue' => fn ($q) => $q->where('status', OrderStatus::COMPLETED)], 'subtotal')
            ->get()
            ->map(fn (Branch $branch) => [
                'branch' => $branch,
                'orders_count' => $branch->orders_count,
                'pending_orders_count' => $branch->pending_orders_count,
                'revenue' => $branch->revenue ?? 0,
                'low_stock_count' => BranchStockLevel::where('branch_id', $branch->id)
                    ->whereColumn('quantity', '<=', 'low_stock_threshold')
                    ->count(),
            ]);

        return [
            'total_branches' => Branch::query()->count(),
            'active_branches' => Branch::query()->active()->count(),
            'total_revenue' => $totalRevenue,
            'orders_by_status' => $ordersByStatus,
            'pending_restock_requests' => RestockRequest::query()->where('status', RestockRequestStatus::PENDING)->count(),
            'low_stock_count' => BranchStockLevel::query()->whereColumn('quantity', '<=', 'low_stock_threshold')->count(),
            'per_branch' => $perBranch,
        ];
    }

    public function forBranch(Branch $branch): array
    {
        $ordersByStatus = DB::table('bookshop_orders')
            ->where('branch_id', $branch->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'orders_by_status' => $ordersByStatus,
            'revenue' => Order::query()->where('branch_id', $branch->id)->where('status', OrderStatus::COMPLETED)->sum('subtotal'),
            'low_stock_count' => BranchStockLevel::query()
                ->where('branch_id', $branch->id)
                ->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->count(),
            'pending_restock_requests' => RestockRequest::query()
                ->where('branch_id', $branch->id)
                ->where('status', RestockRequestStatus::PENDING)
                ->count(),
        ];
    }
}
