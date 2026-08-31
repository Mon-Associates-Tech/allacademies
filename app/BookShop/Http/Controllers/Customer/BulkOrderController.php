<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Exceptions\OrderPlacementException;
use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\BulkOrderRequest;
use App\BookShop\Models\Category;
use App\BookShop\Models\Customer;
use App\BookShop\Services\BranchResolutionService;
use App\BookShop\Services\BulkOrderCartService;
use App\BookShop\Services\BulkOrderService;
use App\BookShop\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Requires an account throughout (unlike the regular catalog/cart, which
 * are public) - a bulk request always needs to resolve to a real branch
 * and eventually a real customer to convert into an Order, and the whole
 * point is staff reviewing/quoting it, which doesn't make sense for an
 * anonymous session the way "browse then register at checkout" does for
 * a normal small order.
 */
class BulkOrderController extends Controller
{
    public function __construct(
        private readonly BulkOrderCartService $builder,
        private readonly BranchResolutionService $branchResolver,
        private readonly BulkOrderService $bulkOrderService,
    ) {
    }

    public function catalog(Request $request): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->builder);

        $books = Book::query()
            ->active()
            ->with('category')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($sub) => $sub
                ->where('title', 'like', '%'.$request->string('search').'%')
                ->orWhere('author', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();
        $builderItems = $this->builder->items();

        return view('bookshop::customer.bulk-orders.catalog', compact('books', 'categories', 'branch', 'builderItems'));
    }

    public function addItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:bookshop_books,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->builder);

        if (! $branch) {
            return back()->withErrors(['bulk' => 'Choose a branch first.']);
        }

        $this->builder->add((int) $data['book_id'], (int) ($data['quantity'] ?? 10));

        return back()->with('status', 'Added to your bulk request.');
    }

    public function review(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->builder);

        $items = $this->builder->items();
        $lines = collect();

        if (! empty($items)) {
            $books = Book::query()->whereIn('id', array_keys($items))->get()->keyBy('id');

            foreach ($items as $bookId => $quantity) {
                $book = $books->get($bookId);
                if (! $book) {
                    continue;
                }

                $lines->push(['book' => $book, 'quantity' => $quantity]);
            }
        }

        return view('bookshop::customer.bulk-orders.review', [
            'lines' => $lines,
            'branch' => $branch,
            'totalQuantity' => $lines->sum('quantity'),
            'minimumQuantity' => BulkOrderService::MINIMUM_TOTAL_QUANTITY,
        ]);
    }

    public function updateItems(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $this->builder->updateQuantities($data['quantities']);

        return redirect()->route('bookshop.shop.bulk-orders.review')->with('status', 'Updated.');
    }

    public function removeItem(Book $book): RedirectResponse
    {
        $this->builder->remove($book->id);

        return back()->with('status', 'Removed from your bulk request.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_type' => ['required', 'in:school,corporate,church,ngo,other'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'requested_delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->builder);

        if (! $branch) {
            return back()->withErrors(['bulk' => 'Choose a branch first.']);
        }

        try {
            $bulkRequest = $this->bulkOrderService->submit(
                $customer,
                $branch,
                $this->builder->items(),
                $data['institution_name'],
                $data['institution_type'],
                $data['contact_phone'] ?? null,
                $data['requested_delivery_date'] ?? null,
                $data['notes'] ?? null,
            );
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['bulk' => $e->getMessage()])->withInput();
        }

        $this->builder->clear();

        return redirect()->route('bookshop.shop.bulk-orders.show', $bulkRequest)
            ->with('status', "Request {$bulkRequest->request_number} submitted. We'll email you once it's been reviewed.");
    }

    public function index(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();

        $requests = BulkOrderRequest::query()
            ->forCustomer($customer)
            ->with(['items', 'branch'])
            ->latest()
            ->paginate(10);

        return view('bookshop::customer.bulk-orders.index', compact('requests'));
    }

    public function show(BulkOrderRequest $bulkOrderRequest): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        abort_unless($bulkOrderRequest->customer_id === $customer->id, 404);

        $bulkOrderRequest->load(['items', 'branch', 'order']);

        return view('bookshop::customer.bulk-orders.show', compact('bulkOrderRequest'));
    }

    public function acceptQuote(BulkOrderRequest $bulkOrderRequest): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        abort_unless($bulkOrderRequest->customer_id === $customer->id, 404);

        try {
            $order = $this->bulkOrderService->acceptQuote($customer, $bulkOrderRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['bulk' => $e->getMessage()]);
        }

        return redirect()->route('bookshop.shop.payments.initialize', $order);
    }

    public function cancel(BulkOrderRequest $bulkOrderRequest): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        abort_unless($bulkOrderRequest->customer_id === $customer->id, 404);

        try {
            $this->bulkOrderService->cancel($bulkOrderRequest);
        } catch (OrderPlacementException $e) {
            return back()->withErrors(['bulk' => $e->getMessage()]);
        }

        return redirect()->route('bookshop.shop.bulk-orders.index')->with('status', 'Request cancelled.');
    }
}
