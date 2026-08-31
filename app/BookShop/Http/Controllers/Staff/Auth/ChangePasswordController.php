<?php

namespace App\BookShop\Http\Controllers\Staff\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function show(): View
    {
        return view('bookshop::staff.auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $staff->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        return redirect()->route('bookshop.staff.dashboard')->with('status', 'Password updated.');
    }
}
