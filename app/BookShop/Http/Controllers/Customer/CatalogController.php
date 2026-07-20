<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Category;
use App\BookShop\Services\BranchResolutionService;
use App\BookShop\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    private const SORTS = [
        'title_asc' => ['title', 'asc'],
        'title_desc' => ['title', 'desc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'newest' => ['created_at', 'desc'],
    ];

    public function __construct(
        private readonly BranchResolutionService $branchResolver,
        private readonly CartService $cart,
    ) {
    }

    public function index(Request $request): View
    {
        /** @var \App\BookShop\Models\Customer|null $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveCurrentShoppingBranch($customer, $this->cart);

        $sortKey = $request->string('sort')->value();
        [$sortColumn, $sortDirection] = self::SORTS[$sortKey] ?? self::SORTS['title_asc'];

        $books = Book::query()
            ->active()
            ->with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($branch, function ($query) use ($branch) {
                // withSum here is scoped to the resolved branch only, so the
                // quantity shown is "what's available to *you*", not a
                // cross-branch total.
                $query->withSum(['stockLevels as branch_stock' => function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                }], 'quantity');
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('bookshop::customer.catalog', [
            'books' => $books,
            'branch' => $branch,
            'customer' => $customer,
            'categories' => $categories,
            'sortKey' => $sortKey ?: 'title_asc',
            'sortOptions' => [
                'title_asc' => 'Title (A-Z)',
                'title_desc' => 'Title (Z-A)',
                'price_asc' => 'Price (Low to High)',
                'price_desc' => 'Price (High to Low)',
                'newest' => 'Newest',
            ],
        ]);
    }
}
