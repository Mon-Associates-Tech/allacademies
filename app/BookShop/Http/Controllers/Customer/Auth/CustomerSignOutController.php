<?php

namespace App\BookShop\Http\Controllers\Customer\Auth;

use App\BookShop\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSignOutController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Auth::guard('bookshop_customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('bookshop.shop.login');
    }
}
