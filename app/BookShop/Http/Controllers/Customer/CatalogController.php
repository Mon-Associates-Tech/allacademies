<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Customer;
use App\BookShop\Services\BranchResolutionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private readonly BranchResolutionService $branchResolver)
    {
    }

    public function index(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveForCustomer($customer);

        $books = Book::query()
            ->active()
            ->with('category')
            ->when($branch, function ($query) use ($branch) {
                // withSum here is scoped to the resolved branch only, so the
                // quantity shown is "what's available to *you*", not a
                // cross-branch total.
                $query->withSum(['stockLevels as branch_stock' => function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                }], 'quantity');
            })
            ->orderBy('title')
            ->paginate(12);

        return view('bookshop::customer.catalog', compact('books', 'branch', 'customer'));
    }
}
