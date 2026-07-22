<?php

namespace App\BookShop\Services;

use App\BookShop\Enums\PaymentStatus;
use App\BookShop\Models\Order;
use App\BookShop\Models\OrderItem;
use App\BookShop\Models\Staff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * All figures are based on paid_at, not created_at - "sales" means
     * money that actually landed, matching how the rest of the payment
     * system already treats payment_status as the source of truth for
     * what counts as a real transaction.
     */
    public function summary(Staff $staff, Carbon $from, Carbon $to): array
    {
        $paidOrders = fn () => Order::query()
            ->visibleTo($staff)
            ->where('payment_status', PaymentStatus::PAID)
            ->whereBetween('paid_at', [$from, $to]);

        $totalRevenue = (float) $paidOrders()->sum('subtotal');
        $totalOrders = $paidOrders()->count();

        $topBooks = OrderItem::query()
            ->select('book_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(line_total) as revenue'))
            ->whereHas('order', fn ($q) => $q->visibleTo($staff)
                ->where('payment_status', PaymentStatus::PAID)
                ->whereBetween('paid_at', [$from, $to]))
            ->groupBy('book_id')
            ->orderByDesc('qty_sold')
            ->with('book')
            ->limit(10)
            ->get();

        // Raw query builder here, not Eloquent - same reasoning as
        // DashboardStatsService: grouping by a column and selecting raw
        // aggregates alongside a model whose payment_status/status are
        // cast to backed enums risks the same "enum as array key" trap
        // if this ever gets plucked. Sidestepped entirely by not
        // hydrating Order models for this query.
        $daily = DB::table('bookshop_orders')
            ->selectRaw('DATE(paid_at) as day, SUM(subtotal) as revenue, COUNT(*) as orders')
            ->where('payment_status', PaymentStatus::PAID->value)
            ->whereBetween('paid_at', [$from, $to])
            ->when(! $staff->isSuperAdmin(), fn ($q) => $q->where('branch_id', $staff->branch_id))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $byBranch = collect();
        if ($staff->isSuperAdmin()) {
            $byBranch = DB::table('bookshop_orders')
                ->join('bookshop_branches', 'bookshop_branches.id', '=', 'bookshop_orders.branch_id')
                ->selectRaw('bookshop_branches.id as branch_id, bookshop_branches.name as branch_name, SUM(bookshop_orders.subtotal) as revenue, COUNT(*) as orders')
                ->where('bookshop_orders.payment_status', PaymentStatus::PAID->value)
                ->whereBetween('bookshop_orders.paid_at', [$from, $to])
                ->groupBy('bookshop_branches.id', 'bookshop_branches.name')
                ->orderByDesc('revenue')
                ->get();
        }

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'top_books' => $topBooks,
            'daily' => $daily,
            'by_branch' => $byBranch,
        ];
    }
}
