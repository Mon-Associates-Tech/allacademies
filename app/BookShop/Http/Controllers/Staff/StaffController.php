<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Enums\StaffRole;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Models\Staff;
use App\BookShop\Notifications\StaffAccountCreatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Superadmin-only (enforced by the 'bookshop.staff.superadmin-only'
 * middleware in routes/bookshop.php) — this is the page that was
 * missing from Phase 2: creating a Staff record and assigning it a
 * branch was tinker-only until now. Deliberately only two roles per
 * the project's scope: admin (branch-scoped) and superadmin.
 *
 * Route parameter is named {staffMember} rather than {staff} so it
 * doesn't collide, at a glance, with the $staff variable convention
 * used everywhere else in this module for "the currently logged-in
 * staff member" — $actingStaff below is that; $staffMember is the
 * record being acted on, which are very often different people here.
 */
class StaffController extends Controller
{
    public function index(): View
    {
        $staffMembers = Staff::query()->with('branch')->orderBy('name')->paginate(20);

        return view('bookshop::staff.team.index', compact('staffMembers'));
    }

    public function create(): View
    {
        $branches = Branch::active()->orderBy('name')->get();

        return view('bookshop::staff.team.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $plaintextPassword = $data['password'];
        $data['password'] = Hash::make($plaintextPassword);
        $data['must_change_password'] = true;

        $staffMember = Staff::create($data);
        $staffMember->notify(new StaffAccountCreatedNotification($staffMember, $plaintextPassword));

        return redirect()->route('bookshop.staff.team.index')
            ->with('status', "{$staffMember->name} added as {$staffMember->role->label()}. Login details emailed to them.");
    }

    public function edit(Staff $staffMember): View
    {
        $branches = Branch::active()->orderBy('name')->get();

        return view('bookshop::staff.team.edit', compact('staffMember', 'branches'));
    }

    public function update(Request $request, Staff $staffMember): RedirectResponse
    {
        $data = $this->validated($request, $staffMember);

        /** @var Staff $actingStaff */
        $actingStaff = Auth::guard('bookshop_staff')->user();

        if ($error = $this->lockoutError($staffMember, $actingStaff, StaffRole::from($data['role']), null)) {
            return back()->withErrors(['staff' => $error])->withInput();
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staffMember->update($data);

        return redirect()->route('bookshop.staff.team.index')
            ->with('status', "{$staffMember->name} updated.");
    }

    public function toggleActive(Staff $staffMember): RedirectResponse
    {
        /** @var Staff $actingStaff */
        $actingStaff = Auth::guard('bookshop_staff')->user();

        $goingInactive = $staffMember->is_active;

        if ($goingInactive && ($error = $this->lockoutError($staffMember, $actingStaff, null, false))) {
            return back()->withErrors(['staff' => $error]);
        }

        $staffMember->update(['is_active' => ! $staffMember->is_active]);

        return back()->with('status', "{$staffMember->name} ".($staffMember->is_active ? 'activated.' : 'deactivated.'));
    }

    private function validated(Request $request, ?Staff $staffMember = null): array
    {
        $emailRule = $staffMember
            ? 'unique:bookshop_staff,email,'.$staffMember->id
            : 'unique:bookshop_staff,email';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:'.StaffRole::ADMIN->value.','.StaffRole::SUPERADMIN->value],
            // Deliberately NOT required_if:role,admin — a branch admin can
            // be created without a branch and land on the branch-pending
            // holding page, same as the tinker-created ones from Phase 2.
            // The superadmin can assign the branch here or later via edit.
            'branch_id' => ['nullable', 'exists:bookshop_branches,id'],
            'password' => [$staffMember ? 'nullable' : 'required', 'string', 'min:8'],
        ]);

        // A superadmin isn't branch-scoped - clear any branch_id submitted
        // for one rather than leaving stale data if a role gets switched
        // from admin to superadmin.
        if ($data['role'] === StaffRole::SUPERADMIN->value) {
            $data['branch_id'] = null;
        }

        return $data;
    }

    /**
     * Guards against two lockout scenarios: a superadmin removing their
     * own super admin access or deactivating their own account, and the
     * last active superadmin being demoted or deactivated by someone
     * else, leaving nobody able to reach superadmin-only screens.
     */
    private function lockoutError(Staff $target, Staff $actingStaff, ?StaffRole $newRole, ?bool $newActive): ?string
    {
        if ($target->id === $actingStaff->id) {
            if ($newActive === false) {
                return "You can't deactivate your own account.";
            }
            if ($newRole !== null && $newRole !== StaffRole::SUPERADMIN && $target->role === StaffRole::SUPERADMIN) {
                return "You can't remove your own super admin access.";
            }
        }

        $losingSuperadminStatus = $target->role === StaffRole::SUPERADMIN
            && (($newRole !== null && $newRole !== StaffRole::SUPERADMIN) || $newActive === false);

        if ($losingSuperadminStatus) {
            $remaining = Staff::where('role', StaffRole::SUPERADMIN)
                ->where('is_active', true)
                ->where('id', '!=', $target->id)
                ->count();

            if ($remaining === 0) {
                return 'At least one active super admin must remain.';
            }
        }

        return null;
    }
}
