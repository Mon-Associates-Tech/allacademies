<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BranchPendingController extends Controller
{
    public function show(): View|RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        // If they've since been assigned a branch (or are a superadmin who
        // landed here by mistake), bounce them straight to the dashboard.
        if ($staff->isSuperAdmin() || $staff->branch_id) {
            return redirect()->route('bookshop.staff.dashboard');
        }

        return view('bookshop::staff.branch-pending', ['staff' => $staff]);
    }
}
