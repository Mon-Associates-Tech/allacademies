<?php

namespace App\BookShop\Http\Controllers\Customer\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CrossAuthController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    /**
     * Redirect to default auth login with return URL pointing to cross-auth completion.
     * This is used when user clicks "Sign in with Default Account" but is not logged in.
     */
    public function redirectToDefaultLogin(Request $request): RedirectResponse
    {
        // Store the intended destination in session
        session(['cross_auth_redirect' => $request->input('redirect', route('bookshop.shop.cart.show'))]);

        // Redirect to default login
        return redirect()->route('login');
    }

    /**
     * Handle the auto-login/registration from default auth.
     * Called after user successfully logs in with default auth.
     */
    public function handleDefaultAuthCompletion(Request $request): RedirectResponse
    {
        // Check if user is authenticated with default guard
        if (! Auth::check()) {
            return redirect()->route('bookshop.shop.login');
        }

        /** @var \App\Models\User $defaultUser */
        $defaultUser = Auth::user();

        // Check if a bookshop customer already exists with this email
        $customer = Customer::where('email', $defaultUser->email)->first();

        if ($customer) {
            // Customer exists, just log them in
            Auth::guard('bookshop_customer')->login($customer);
            $request->session()->regenerate();
        } else {
            // Create a new bookshop customer from the default auth user
            // Use a random password since this customer will only authenticate via default auth
            $customer = Customer::create([
                'name' => $defaultUser->name,
                'email' => $defaultUser->email,
                'phone' => $defaultUser->phone ?? null,
                'password' => Hash::make(Str::random(64)),
                'country' => $defaultUser->country ?? null,
                'country_code' => $defaultUser->country_code ?? null,
                'region' => $defaultUser->region ?? null,
                'city' => $defaultUser->city ?? null,
                'address' => null,
                'preferred_branch_id' => $this->cart->branchId(),
                'is_active' => true,
            ]);

            Auth::guard('bookshop_customer')->login($customer);
            $request->session()->regenerate();
        }

        // Get the intended redirect, default to cart
        $redirect = session()->pull('cross_auth_redirect', route('bookshop.shop.cart.show'));

        return redirect($redirect);
    }

    /**
     * Handle auto-registration directly from bookshop register page
     * when user is already logged in with default auth.
     */
    public function registerFromDefaultAuth(Request $request): RedirectResponse
    {
        // Check if user is authenticated with default guard
        if (! Auth::check()) {
            // User not logged in, redirect to login with return URL
            session(['cross_auth_redirect' => route('bookshop.shop.register')]);
            return redirect()->route('login');
        }

        /** @var \App\Models\User $defaultUser */
        $defaultUser = Auth::user();

        // Check if a bookshop customer already exists with this email
        $customer = Customer::where('email', $defaultUser->email)->first();

        if ($customer) {
            // Customer already exists, just log them in
            Auth::guard('bookshop_customer')->login($customer);
            $request->session()->regenerate();
        } else {
            // Create a new bookshop customer from the default auth user
            // Use a random password since this customer will only authenticate via default auth
            $customer = Customer::create([
                'name' => $defaultUser->name,
                'email' => $defaultUser->email,
                'phone' => $defaultUser->phone ?? null,
                'password' => Hash::make(Str::random(64)),
                'country' => $defaultUser->country ?? null,
                'country_code' => $defaultUser->country_code ?? null,
                'region' => $defaultUser->region ?? null,
                'city' => $defaultUser->city ?? null,
                'address' => null,
                'preferred_branch_id' => $this->cart->branchId(),
                'is_active' => true,
            ]);

            Auth::guard('bookshop_customer')->login($customer);
            $request->session()->regenerate();
        }

        return redirect()->route('bookshop.shop.cart.show');
    }
}
