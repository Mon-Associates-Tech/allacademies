<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Models\Staff;
use App\BookShop\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $stats)
    {
    }

    public function index(): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        // scopeVisibleTo already encodes: superadmin sees all, admin sees own branch only.
        $branches = Branch::query()->visibleTo($staff)->get();

        $stats = $staff->isSuperAdmin()
            ? $this->stats->forSuperAdmin()
            : ($staff->branch ? $this->stats->forBranch($staff->branch) : null);

        return view('bookshop::staff.dashboard', [
            'staff' => $staff,
            'branches' => $branches,
            'stats' => $stats,
        ]);
    }
}
