<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        // scopeVisibleTo already encodes: superadmin sees all, admin sees own branch only.
        $branches = Branch::query()->visibleTo($staff)->get();

        return view('bookshop::staff.dashboard', [
            'staff' => $staff,
            'branches' => $branches,
        ]);
    }
}
