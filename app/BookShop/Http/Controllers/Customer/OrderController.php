<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use App\BookShop\Services\OrderPdfService;
use Illuminate\Http\Response;
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
    public function __construct(private readonly OrderPdfService $pdfService)
    {
    }

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
        $this->authorizeOwnership($order);

        $order->load(['items', 'branch']);

        return view('bookshop::customer.orders.show', compact('order'));
    }

    public function receipt(Order $order): Response
    {
        $this->authorizeOwnership($order);

        abort_unless($order->isPaid(), 403, 'Receipts are only available once an order is paid.');

        return $this->pdfService->receipt($order)->stream("receipt-{$order->order_number}.pdf");
    }

    private function authorizeOwnership(Order $order): void
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        abort_unless($order->customer_id === $customer->id, 404);
    }
}
