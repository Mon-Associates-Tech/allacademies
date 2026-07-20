<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Services\BranchResolutionService;
use App\BookShop\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BranchSwitchController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly BranchResolutionService $branchResolver,
    ) {
    }

    public function index(): View
    {
        /** @var \App\BookShop\Models\Customer|null $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $current = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->cart);
        $homeBranch = $customer ? $this->branchResolver->resolveForCustomer($customer) : null;

        $branches = Branch::active()->orderBy('region')->orderBy('name')->get();

        return view('bookshop::customer.branches.index', compact('branches', 'current', 'homeBranch'));
    }

    public function switch(Branch $branch): RedirectResponse
    {
        abort_unless($branch->is_active, 404);

        $hadItems = ! $this->cart->isEmpty();
        $switchingBranch = $this->cart->branchId() !== $branch->id;

        $this->cart->setBranch($branch->id);

        // Only logged-in customers have a preferred_branch_id to persist -
        // a guest's choice lives purely in the cart session until they
        // register, at which point it's on them to switch again if they
        // want it remembered going forward (registration doesn't currently
        // capture "which branch were you just browsing").
        Auth::guard('bookshop_customer')->user()?->update(['preferred_branch_id' => $branch->id]);

        $status = "Now shopping at {$branch->name}.";
        if ($hadItems && $switchingBranch) {
            $status .= ' Your cart was cleared since stock is branch-specific.';
        }

        return redirect()->route('bookshop.shop.catalog')->with('status', $status);
    }
}
