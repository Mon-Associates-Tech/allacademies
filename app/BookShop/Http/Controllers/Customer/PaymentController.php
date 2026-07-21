<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Order;
use App\BookShop\Services\OrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private readonly OrderPaymentService $payments)
    {
    }

    public function initialize(Order $order): RedirectResponse
    {
        $this->authorizeOwnership($order);

        try {
            $authorizationUrl = $this->payments->initialize($order, route('bookshop.shop.payments.callback'));
        } catch (OrderPlacementException $e) {
            return redirect()->route('bookshop.shop.orders.show', $order)->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('bookshop.shop.orders.index')->withErrors(['payment' => 'Invalid payment callback.']);
        }

        try {
            $order = $this->payments->verify($reference);
        } catch (OrderPlacementException $e) {
            return redirect()->route('bookshop.shop.orders.index')->withErrors(['payment' => $e->getMessage()]);
        }

        return $order->isPaid()
            ? redirect()->route('bookshop.shop.orders.show', $order)->with('status', 'Payment successful! Your order is confirmed.')
            : redirect()->route('bookshop.shop.orders.show', $order)->withErrors(['payment' => 'Payment was not completed. You can retry from this page.']);
    }

    private function authorizeOwnership(Order $order): void
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        abort_unless($order->customer_id === $customer->id, 404);
    }
}
