<?php

namespace App\BookShop\Http\Controllers\Customer\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
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

        $customer = Customer::create($data);

        // Branch resolution from region/city -> Branch happens properly in
        // Phase 4 (order placement). Nothing wired to preferred_branch_id yet.

        Auth::guard('bookshop_customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('bookshop.shop.home');
    }
}
