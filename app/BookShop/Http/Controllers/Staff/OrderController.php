<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Enums\OrderStatus;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Order;
use App\BookShop\Models\Staff;
use App\BookShop\Services\OrderFulfillmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderFulfillmentService $fulfillment)
    {
    }

    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $orders = Order::query()
            ->with(['customer', 'branch'])
            ->visibleTo($staff)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('bookshop::staff.orders.index', compact('orders', 'staff'));
    }

    public function show(Order $order): View
    {
        $this->authorizeVisible($order);

        $order->load(['items', 'customer', 'branch']);

        return view('bookshop::staff.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeVisible($order);

        $data = $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $target = OrderStatus::tryFrom($data['status']);
        abort_if($target === null, 422, 'Invalid status.');

        try {
            $this->fulfillment->transition($order, $target, $data['reason'] ?? null);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', "Order {$order->order_number} moved to \"{$target->label()}\".");
    }

    private function authorizeVisible(Order $order): void
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        abort_unless(
            $staff->isSuperAdmin() || $order->branch_id === $staff->branch_id,
            403
        );
    }
}
