<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerHomeController extends Controller
{
    public function index(): View
    {
        return view('bookshop::customer.home', [
            'customer' => Auth::guard('bookshop_customer')->user(),
        ]);
    }
}
