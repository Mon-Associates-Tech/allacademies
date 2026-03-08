<?php

namespace App\Http\Controllers;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->isSuperAdmin() && ! auth()->user()->isOwner()) {
                abort(403, 'Unauthorized access');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $tierId = $request->get('tier_id', '');

        $pricingTiers = PricingTier::orderBy('name')->get();
        $allocations = SubscriptionCycle::with(['user', 'pricingTier'])
            ->when($filter === 'admin', fn ($q) => $q->allocatedByAdmin())
            ->when($filter === 'user', fn ($q) => $q->where('allocated_by_admin', false))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($tierId, fn ($q) => $q->where('pricing_tier_id', $tierId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('token-allocations.index', compact('pricingTiers', 'allocations', 'filter', 'search', 'status', 'tierId'));
    }

    public function createTier()
    {
        return view('token-allocations.create-tier');
    }

    public function storeTier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'initial_price' => 'required|numeric|min:0',
            'subsequent_price' => 'required|numeric|min:0',
            'monthly_token_limit' => 'required|integer|min:1',
            'initial_period_months' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        PricingTier::create($validated);

        return redirect()->route('token-allocations.index')
            ->with('success', 'Pricing tier created successfully');
    }

    public function editTier(PricingTier $tier)
    {
        return view('token-allocations.edit-tier', compact('tier'));
    }

    public function updateTier(Request $request, PricingTier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'initial_price' => 'required|numeric|min:0',
            'subsequent_price' => 'required|numeric|min:0',
            'monthly_token_limit' => 'required|integer|min:1',
            'initial_period_months' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $tier->update($validated);

        return redirect()->route('token-allocations.index')
            ->with('success', 'Pricing tier updated successfully');
    }

    public function assignTokens()
    {
        $pricingTiers = PricingTier::active()->get();

        return view('token-allocations.assign-tokens', compact('pricingTiers'));
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'assignment_type' => 'required|in:new_cycle,topup',
            'pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'tokens' => 'required|integer|min:1',
            'cycle_start_date' => 'required|date',
            'cycle_end_date' => 'required|date|after:cycle_start_date',
            'is_trial' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($validated) {
            $pricingTier = PricingTier::find($validated['pricing_tier_id']);

            foreach ($validated['user_ids'] as $userId) {
                $user = User::find($userId);

                if ($validated['assignment_type'] === 'topup') {
                    $activeCycle = $user->getCurrentActiveCycle();
                    if ($activeCycle) {
                        $activeCycle->addTopupTokens($validated['tokens']);

                        continue;
                    }
                }

                SubscriptionCycle::create([
                    'user_id' => $userId,
                    'pricing_tier_id' => $validated['pricing_tier_id'],
                    'cycle_number' => 1,
                    'cycle_start_date' => $validated['cycle_start_date'],
                    'cycle_end_date' => $validated['cycle_end_date'],
                    'tokens_allocated' => $validated['tokens'],
                    'topup_tokens_allocated' => 0,
                    'tokens_used' => 0,
                    'current_price' => 0,
                    'status' => $validated['status'],
                    'is_trial' => $validated['is_trial'] ?? false,
                    'is_topup' => false,
                    'allocated_by_admin' => true,
                ]);
            }
        });

        return redirect()->route('token-allocations.index')
            ->with('success', 'Tokens assigned to '.count($validated['user_ids']).' user(s) successfully');
    }

    public function deactivateCycle(Request $request, $cycleId)
    {
        $cycle = SubscriptionCycle::findOrFail($cycleId);

        $cycle->update(['status' => 'inactive']);

        return redirect()->back()
            ->with('success', 'Cycle deactivated successfully');
    }

    public function revokeTokens(Request $request, $cycleId)
    {
        $cycle = SubscriptionCycle::findOrFail($cycleId);

        $cycle->update([
            'status' => 'cancelled',
            'tokens_allocated' => 0,
            'tokens_used' => 0,
            'topup_tokens_allocated' => 0,
        ]);

        return redirect()->back()
            ->with('success', 'Tokens revoked successfully');
    }

    public function viewUserTokens($userId)
    {
        $user = User::with(['subscriptionCycles' => function ($query) {
            $query->latest();
        }])->findOrFail($userId);

        $cycles = $user->subscriptionCycles;
        $activeCycle = $user->getCurrentActiveCycle();

        return view('token-allocations.user-tokens', compact('user', 'cycles', 'activeCycle'));
    }

    public function getUsersJson(Request $request)
    {
        $search = $request->get('search', '');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email')
            ->limit(50)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name.' ('.$user->email.')',
                ];
            });

        return response()->json($users);
    }
}
