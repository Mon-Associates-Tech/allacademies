<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Branch;
use App\BookShop\Models\BranchStockLevel;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $stockLevels = BranchStockLevel::query()
            ->with(['branch', 'book'])
            ->visibleTo($staff)
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->integer('branch_id')))
            ->when($request->boolean('low_only'), fn ($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('book', fn ($bq) => $bq->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%"));
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        // Superadmin gets a branch filter dropdown; branch admins only ever
        // see their own branch, so the filter would be redundant for them.
        $branches = $staff->isSuperAdmin() ? Branch::active()->orderBy('name')->get() : collect();

        // Re-populates the book picker's search box after a failed
        // validation redisplay - otherwise the hidden book_id survives via
        // old() but the visible text box goes blank, which looks broken.
        $prefillBook = old('book_id') ? Book::find(old('book_id')) : null;

        return view('bookshop::staff.stock.index', compact('stockLevels', 'branches', 'staff', 'prefillBook'));
    }

    /**
     * Superadmin-only (route middleware) — sets/allocates initial stock for
     * a branch+book pair. The branch-initiated request/approve adjustment
     * flow lands in Phase 5; this is the "superadmin allocates directly"
     * path needed to seed stock at all before that exists.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:bookshop_branches,id'],
            'book_id' => ['required', 'exists:bookshop_books,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $stockLevel = BranchStockLevel::updateOrCreate(
            ['branch_id' => $data['branch_id'], 'book_id' => $data['book_id']],
            [
                'quantity' => $data['quantity'],
                'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
                'updated_by_staff_id' => $staff->id,
            ]
        );

        $book = Book::find($data['book_id']);

        return back()->with('status', "Stock for \"{$book?->title}\" set to {$stockLevel->quantity}.");
    }
}
