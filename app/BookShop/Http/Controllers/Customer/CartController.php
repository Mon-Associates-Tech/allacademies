<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Enums\FulfillmentMethod;
use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Customer;
use App\BookShop\Services\BranchResolutionService;
use App\BookShop\Services\CartService;
use App\BookShop\Services\OrderPlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Fully public - a guest can browse, add to cart, and view the cart
 * without an account. Only checkout() actually requires one, checked
 * explicitly inside the method rather than via route middleware (a
 * middleware-driven auth redirect works cleanly for GET pages via
 * Laravel's "intended URL" mechanism, but not for a POST like this one -
 * redirecting back after login would just re-issue a GET to the
 * checkout URL and hit a 405, not resubmit the order). Redirecting to
 * registration with the cart intact, then having them press "Place
 * Order" once more from the now-authenticated cart page, sidesteps that
 * entirely and is standard e-commerce UX besides.
 */
class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly BranchResolutionService $branchResolver,
        private readonly OrderPlacementService $orderPlacer,
    ) {
    }

    public function show(): View
    {
        /** @var Customer|null $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->cart);

        $items = $this->cart->items();
        $lines = collect();

        if (! empty($items) && $branch) {
            $books = Book::query()->whereIn('id', array_keys($items))->get()->keyBy('id');

            $stockLevels = BranchStockLevel::query()
                ->where('branch_id', $branch->id)
                ->whereIn('book_id', array_keys($items))
                ->get()
                ->keyBy('book_id');

            foreach ($items as $bookId => $quantity) {
                $book = $books->get($bookId);
                if (! $book) {
                    continue; // book was deactivated/deleted since being added
                }

                $lines->push([
                    'book' => $book,
                    'quantity' => $quantity,
                    'available' => $stockLevels->get($bookId)?->quantity ?? 0,
                    'line_total' => round($book->price * $quantity, 2),
                ]);
            }
        }

        return view('bookshop::customer.cart.show', [
            'lines' => $lines,
            'branch' => $branch,
            'subtotal' => $lines->sum('line_total'),
            'isGuest' => ! $customer,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:bookshop_books,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        /** @var Customer|null $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->cart);

        if (! $branch) {
            return back()->withErrors(['cart' => 'Choose a branch before adding items to your cart.']);
        }

        $this->cart->add((int) $data['book_id'], (int) ($data['quantity'] ?? 1));

        return back()->with('status', 'Added to cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->updateQuantities($data['quantities']);

        return redirect()->route('bookshop.shop.cart.show')->with('status', 'Cart updated.');
    }

    public function remove(Book $book): RedirectResponse
    {
        $this->cart->remove($book->id);

        return back()->with('status', 'Removed from cart.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        /** @var Customer|null $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        if (! $customer) {
            if ($this->cart->isEmpty()) {
                return redirect()->route('bookshop.shop.register');
            }

            return redirect()->route('bookshop.shop.register')
                ->with('status', 'Create an account to complete your order - your cart is saved.');
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'fulfillment_method' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:fulfillment_method,delivery', 'nullable', 'string', 'max:500'],
        ]);

        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->cart);

        if (! $branch) {
            return back()->withErrors(['cart' => 'Choose a branch before placing an order.']);
        }

        try {
            $order = $this->orderPlacer->place(
                $customer,
                $branch,
                $this->cart->items(),
                $data['notes'] ?? null,
                FulfillmentMethod::from($data['fulfillment_method']),
                $data['delivery_address'] ?? null,
            );
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['cart' => $e->getMessage()])->withInput();
        }

        $this->cart->clear();

        // Stock is already decremented at this point (OrderPlacementService
        // reserves it immediately on order creation, same as before payment
        // existed) - the order now sits at payment_status: pending until
        // Paystack confirms. See PaymentController::initialize().
        return redirect()->route('bookshop.shop.payments.initialize', $order);
    }
}
