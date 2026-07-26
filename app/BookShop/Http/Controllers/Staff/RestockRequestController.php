<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\RestockRequest;
use App\BookShop\Models\Staff;
use App\BookShop\Services\RestockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestockRequestController extends Controller
{
    public function __construct(private readonly RestockService $restockService)
    {
    }

    /**
     * Grouped by batch (one submission = one row), not a flat list of
     * individual line items — a superadmin reviewing a 5-item request
     * needs to see it as one request with 5 items, not 5 unrelated rows
     * with no indication they were submitted together. Pagination
     * happens at the batch level: first paginate distinct batch_ids
     * ordered by most recent activity, then load every item belonging
     * to just those batches in a second query.
     */
    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        // Two-step query, deliberately: filtering item rows directly by
        // status before grouping would make item_count reflect only the
        // matching items (e.g. a 3-item batch where 2 are already
        // approved would show as "1 item" when filtering "pending") -
        // wrong. Instead: find which batches have at least one item
        // matching the filter, then compute counts against the FULL
        // batch regardless of filter, so "3-item batch, 1 still pending"
        // reads correctly rather than looking like a 1-item batch.
        $matchingBatchIds = RestockRequest::query()
            ->visibleTo($staff)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->distinct()
            ->pluck('batch_id');

        $batches = RestockRequest::query()
            ->whereIn('batch_id', $matchingBatchIds)
            ->selectRaw('batch_id, MAX(created_at) as latest_at, MIN(branch_id) as branch_id, MIN(requested_by_staff_id) as requested_by_staff_id, COUNT(*) as item_count')
            ->groupBy('batch_id')
            ->orderByDesc('latest_at')
            ->paginate(15)
            ->withQueryString();

        $batchIds = collect($batches->items())->pluck('batch_id');

        $itemsByBatch = RestockRequest::query()
            ->whereIn('batch_id', $batchIds)
            ->with(['book', 'branch', 'requestedBy'])
            ->get()
            ->groupBy('batch_id');

        return view('bookshop::staff.restock-requests.index', compact('batches', 'itemsByBatch', 'staff'));
    }

    /**
     * Branch admins only — a superadmin has no branch of their own to
     * request stock for; they allocate directly via WarehouseController
     * or StockController instead.
     */
    public function create(): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        abort_if($staff->isSuperAdmin(), 403, 'Super admins allocate stock directly rather than filing a request.');

        return view('bookshop::staff.restock-requests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        abort_if($staff->isSuperAdmin(), 403);

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.book_id' => ['nullable', 'exists:bookshop_books,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $requests = $this->restockService->createBatch($staff, $data['items'], $data['notes'] ?? null);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        $count = $requests->count();

        return redirect()->route('bookshop.staff.restock-requests.index')
            ->with('status', "Restock request".($count > 1 ? "s" : "")." for {$count} book(s) submitted.");
    }

    /**
     * The detail page this feature was missing entirely before: full
     * context on every item in a batch (book, quantity, per-item status,
     * who requested it, any notes) plus per-item and bulk actions,
     * rather than the superadmin only ever seeing bare approve/reject
     * buttons with no surrounding detail.
     */
    public function show(string $batchId): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $items = RestockRequest::query()
            ->inBatch($batchId)
            ->with(['book', 'branch', 'requestedBy', 'reviewedBy', 'dispatchedBy', 'deliveredBy', 'confirmedBy'])
            ->visibleTo($staff)
            ->orderBy('id')
            ->get();

        abort_if($items->isEmpty(), 404);

        return view('bookshop::staff.restock-requests.show', [
            'batchId' => $batchId,
            'items' => $items,
            'staff' => $staff,
        ]);
    }

    public function approve(RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->restockService->approve($staff, $restockRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Item approved — stock reserved from the warehouse. Dispatch it when it physically ships.');
    }

    public function reject(Request $request, RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $wasReserved = $restockRequest->status->hasReservedWarehouseStock();

        try {
            $this->restockService->reject($staff, $restockRequest, $data['reason']);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', $wasReserved
            ? 'Item rejected — its warehouse reservation was released.'
            : 'Item rejected.');
    }

    /**
     * "Accept ... all of the items in the request" — approves every
     * still-pending item in the batch in one action. Partial success is
     * a normal outcome, not an error: if one book's warehouse stock is
     * insufficient, the rest still go through, and the summary flash
     * message says exactly what happened to each.
     */
    public function approveAll(string $batchId): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $result = $this->restockService->approveBatch($staff, $batchId);
        $approvedCount = $result['approved']->count();
        $failedCount = $result['failed']->count();

        $status = "{$approvedCount} item(s) approved.";
        if ($failedCount > 0) {
            $status .= " {$failedCount} could not be approved and remain pending — see details below.";
        }

        return back()->with($failedCount > 0 ? 'warning' : 'status', $status);
    }

    /**
     * "Reject ... all of the items in the request" — rejects every
     * still-pending item in the batch with one shared reason.
     */
    public function rejectAll(Request $request, string $batchId): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $rejected = $this->restockService->rejectBatch($staff, $batchId, $data['reason']);

        return back()->with('status', "{$rejected->count()} item(s) rejected.");
    }

    /**
     * Superadmin-only (route middleware, same group as approve/reject) -
     * marks an approved item as physically sent from the warehouse.
     */
    public function dispatchRequest(RestockRequest $restockRequest)
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->restockService->dispatch($staff, $restockRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Marked as dispatched.');
    }

    /**
     * Not superadmin-gated — this is normally the receiving branch's own
     * action (their shipment showed up), so it's authorized against
     * "superadmin, or staff at the branch this request belongs to"
     * rather than restricted to superadmin like approve/reject/dispatch.
     */
    public function markDelivered(RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        $this->authorizeBranchAction($staff, $restockRequest);

        try {
            $this->restockService->markDelivered($staff, $restockRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Marked as delivered. Confirm the quantity received to add it to your stock.');
    }

    public function confirm(Request $request, RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        $this->authorizeBranchAction($staff, $restockRequest);

        $data = $request->validate([
            'confirmed_quantity' => ['nullable', 'integer', 'min:0', 'max:'.$restockRequest->requested_quantity],
        ]);

        try {
            $this->restockService->confirm($staff, $restockRequest, $data['confirmed_quantity'] ?? null);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Confirmed and added to your branch stock.');
    }

    private function authorizeBranchAction(Staff $staff, RestockRequest $restockRequest): void
    {
        abort_unless(
            $staff->isSuperAdmin() || $restockRequest->branch_id === $staff->branch_id,
            403
        );
    }
}
