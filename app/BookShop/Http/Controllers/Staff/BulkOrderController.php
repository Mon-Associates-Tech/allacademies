<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\BulkOrderRequest;
use App\BookShop\Models\Staff;
use App\BookShop\Services\BulkOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Visible to both roles, scoped by branch (same visibleTo() pattern as
 * Order/RestockRequest) rather than superadmin-only - quoting/rejecting
 * a bulk request is a per-branch operational decision (the branch admin
 * knows their own stock and customers), matching how order status
 * updates work rather than how restock approval works (which touches
 * the shared warehouse resource and IS superadmin-gated).
 */
class BulkOrderController extends Controller
{
    public function __construct(private readonly BulkOrderService $bulkOrderService)
    {
    }

    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $requests = BulkOrderRequest::query()
            ->with(['customer', 'branch', 'items'])
            ->visibleTo($staff)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('bookshop::staff.bulk-orders.index', compact('requests', 'staff'));
    }

    public function show(BulkOrderRequest $bulkOrderRequest): View
    {
        $this->authorizeVisible($bulkOrderRequest);

        $bulkOrderRequest->load(['items.book', 'customer', 'branch', 'reviewedBy', 'order']);

        return view('bookshop::staff.bulk-orders.show', compact('bulkOrderRequest'));
    }

    public function quote(Request $request, BulkOrderRequest $bulkOrderRequest): RedirectResponse
    {
        $this->authorizeVisible($bulkOrderRequest);

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->bulkOrderService->quote($staff, $bulkOrderRequest, $data['items'], $data['staff_notes'] ?? null);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['quote' => $e->getMessage()])->withInput();
        }

        return redirect()->route('bookshop.staff.bulk-orders.show', $bulkOrderRequest)
            ->with('status', 'Quote sent to the customer.');
    }

    public function reject(Request $request, BulkOrderRequest $bulkOrderRequest): RedirectResponse
    {
        $this->authorizeVisible($bulkOrderRequest);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->bulkOrderService->reject($staff, $bulkOrderRequest, $data['reason']);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['quote' => $e->getMessage()]);
        }

        return redirect()->route('bookshop.staff.bulk-orders.index')->with('status', 'Request rejected.');
    }

    private function authorizeVisible(BulkOrderRequest $bulkOrderRequest): void
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        abort_unless(
            $staff->isSuperAdmin() || $bulkOrderRequest->branch_id === $staff->branch_id,
            403
        );
    }
}
