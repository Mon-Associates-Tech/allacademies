<?php

namespace App\BookShop\Http\Controllers\Customer;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Customer;
use App\BookShop\Services\BranchResolutionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookShowController extends Controller
{
    public function __construct(private readonly BranchResolutionService $branchResolver)
    {
    }

    public function show(Book $book): View
    {
        abort_unless($book->is_active, 404);

        /** @var Customer $customer */
        $customer = Auth::guard('bookshop_customer')->user();
        $branch = $this->branchResolver->resolveForCustomer($customer);

        $book->load('category');

        $availableQuantity = $branch
            ? (int) $book->stockLevels()->where('branch_id', $branch->id)->value('quantity')
            : 0;

        // A handful of other active books in the same category, so the
        // page isn't a dead end - standard "you might also like" pattern,
        // cheap to compute and meaningfully improves browsing on mobile
        // where going back to re-search is more friction than on desktop.
        $relatedBooks = $book->category_id
            ? Book::query()->active()
                ->where('category_id', $book->category_id)
                ->where('id', '!=', $book->id)
                ->limit(4)
                ->get()
            : collect();

        return view('bookshop::customer.books.show', compact('book', 'branch', 'availableQuantity', 'relatedBooks'));
    }
}
