<?php

namespace App\BookShop\Http\Controllers\Staff\Auth;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StaffSignInController extends Controller
{
    public function create(): View
    {
        return view('bookshop::staff.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('bookshop_staff')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        if (! $staff->is_active) {
            Auth::guard('bookshop_staff')->logout();
            $request->session()->invalidate();

            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated. Contact your super admin.',
            ]);
        }

        $staff->forceFill(['last_login_at' => now()])->save();

        if (! $staff->isSuperAdmin() && ! $staff->branch_id) {
            return redirect()->route('bookshop.staff.branch-pending');
        }

        return redirect()->intended(route('bookshop.staff.dashboard'));
    }
}
