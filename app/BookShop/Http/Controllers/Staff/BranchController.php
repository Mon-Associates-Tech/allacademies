<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Superadmin-only (enforced by the 'bookshop.staff.superadmin-only'
 * middleware in routes/bookshop.php, not re-checked here) — a branch
 * admin never reaches these actions.
 */
class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::query()->withCount('staff')->latest()->paginate(15);

        return view('bookshop::staff.branches.index', compact('branches'));
    }

    public function create(): View
    {
        return view('bookshop::staff.branches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        $data['created_by_staff_id'] = $staff->id;

        $branch = Branch::create($data);

        return redirect()->route('bookshop.staff.branches.index')
            ->with('status', "Branch \"{$branch->name}\" created.");
    }

    public function edit(Branch $branch): View
    {
        return view('bookshop::staff.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $branch->update($this->validated($request, $branch));

        return redirect()->route('bookshop.staff.branches.index')
            ->with('status', "Branch \"{$branch->name}\" updated.");
    }

    public function toggleActive(Branch $branch): RedirectResponse
    {
        $branch->update(['is_active' => ! $branch->is_active]);

        return back()->with('status', $branch->is_active
            ? "Branch \"{$branch->name}\" reactivated."
            : "Branch \"{$branch->name}\" deactivated.");
    }

    private function validated(Request $request, ?Branch $branch = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:8'],
            'region' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
    }
}
