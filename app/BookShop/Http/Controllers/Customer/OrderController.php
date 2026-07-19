<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use App\BookShop\Services\OrderPlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderPlacementService $orderPlacer)
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
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        abort_unless($order->customer_id === $customer->id, 404);

        $order->load(['items', 'branch']);

        return view('bookshop::customer.orders.show', compact('order'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:99'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        try {
            $order = $this->orderPlacer->place($customer, $data['quantities'], $data['notes'] ?? null);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['order' => $e->getMessage()])->withInput();
        }

        return redirect()->route('bookshop.shop.orders.show', $order)
            ->with('status', "Order {$order->order_number} placed successfully.");
    }
}
