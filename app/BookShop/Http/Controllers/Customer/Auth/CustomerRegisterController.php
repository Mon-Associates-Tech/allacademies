<?php

namespace App\BookShop\Http\Controllers\Customer\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function create(): View
    {
        return view('bookshop::customer.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:bookshop_customers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:8'],
            'region' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $data['password'] = Hash::make($data['password']);

        // If they were browsing as a guest and already picked a branch,
        // carry that choice onto the new account rather than dropping it
        // and making them pick again right after registering.
        $data['preferred_branch_id'] = $this->cart->branchId();

        $customer = Customer::create($data);

        Auth::guard('bookshop_customer')->login($customer);
        $request->session()->regenerate();

        // The cart itself (session-based) survives session regenerate()
        // untouched - regenerate() cycles the session ID to prevent
        // fixation, it doesn't clear session data. So a guest's cart is
        // still right there once they land back on it.
        return redirect()->route('bookshop.shop.cart.show');
    }
}
