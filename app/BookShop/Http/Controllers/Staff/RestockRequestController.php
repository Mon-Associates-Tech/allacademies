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

    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $requests = RestockRequest::query()
            ->with(['branch', 'book', 'requestedBy'])
            ->visibleTo($staff)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('bookshop::staff.restock-requests.index', compact('requests', 'staff'));
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

    public function approve(RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        try {
            $this->restockService->approve($staff, $restockRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Restock request approved — stock transferred to the branch.');
    }

    public function reject(Request $request, RestockRequest $restockRequest): RedirectResponse
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->restockService->reject($staff, $restockRequest, $data['reason']);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['request' => $e->getMessage()]);
        }

        return back()->with('status', 'Restock request rejected.');
    }
}
