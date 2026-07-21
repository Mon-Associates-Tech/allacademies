<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Branch;
use App\BookShop\Models\Staff;
use App\BookShop\Services\BranchPaymentSetupService;
use App\BookShop\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Superadmin-only (route middleware) — bank/settlement details are a
 * fraud-sensitive surface, same reasoning as why branch admins can't
 * touch this even though they can manage almost everything else about
 * their own branch.
 */
class BranchPaymentController extends Controller
{
    public function __construct(
        private readonly BranchPaymentSetupService $setupService,
        private readonly PaystackService $paystack,
    ) {
    }

    public function edit(Branch $branch): View
    {
        $banks = $this->fetchBanks();

        return view('bookshop::staff.branches.payment', compact('branch', 'banks'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'bank_code' => ['required', 'string'],
            'settlement_bank_name' => ['nullable', 'string'],
            'account_number' => ['required', 'string', 'min:10', 'max:20'],
            'percentage_charge' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->setupService->createOrUpdateSubaccount($branch, $data, $staff);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['payment' => $e->getMessage()])->withInput();
        }

        return redirect()->route('bookshop.staff.branches.payment.edit', $branch)
            ->with('status', "Payment account for {$branch->name} saved.");
    }

    public function deactivate(Branch $branch): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $this->setupService->deactivate($branch, $staff);

        return back()->with('status', "Payments deactivated for {$branch->name}. Orders will settle to the platform account instead until reactivated.");
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    private function fetchBanks(): array
    {
        try {
            $response = $this->paystack->listBanks();
        } catch (\Throwable $e) {
            Log::warning('BookShop: could not fetch Paystack bank list', ['error' => $e->getMessage()]);

            return [];
        }

        if (empty($response['status']) || ! $response['status']) {
            return [];
        }

        return collect($response['data'] ?? [])
            ->map(fn ($bank) => ['code' => $bank['code'], 'name' => $bank['name']])
            ->sortBy('name')
            ->values()
            ->all();
    }
}
