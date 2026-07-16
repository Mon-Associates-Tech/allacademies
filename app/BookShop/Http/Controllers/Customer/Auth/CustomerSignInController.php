<?php

namespace App\BookShop\Http\Controllers\Customer\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerSignInController extends Controller
{
    public function create(): View
    {
        return view('bookshop::customer.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('bookshop_customer')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        if (! $customer->is_active) {
            Auth::guard('bookshop_customer')->logout();
            $request->session()->invalidate();

            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated.',
            ]);
        }

        return redirect()->intended(route('bookshop.shop.home'));
    }
}
