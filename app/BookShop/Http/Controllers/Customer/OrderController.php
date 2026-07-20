<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * store() was removed - order placement now goes exclusively through
 * CartController::checkout(), since orders can span multiple items and
 * the cart is the single source of truth for "what's in this order"
 * before it's placed. index()/show() (viewing order history) still live
 * here since they're not cart-related.
 */
class OrderController extends Controller
{
    public function index(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        $orders = Order::query()
            ->forCustomer($customer)
            ->with(['items', 'branch'])
            ->latest()
            ->paginate(10);

        return view('bookshop::customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        abort_unless($order->customer_id === $customer->id, 404);

        $order->load(['items', 'branch']);

        return view('bookshop::customer.orders.show', compact('order'));
    }
}
