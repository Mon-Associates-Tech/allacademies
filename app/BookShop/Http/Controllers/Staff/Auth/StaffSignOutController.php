<?php

namespace App\BookShop\Http\Controllers\Staff\Auth;

use App\BookShop\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffSignOutController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Auth::guard('bookshop_staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('bookshop.staff.login');
    }
}
