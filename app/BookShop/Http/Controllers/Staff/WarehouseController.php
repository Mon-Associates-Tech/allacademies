<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Staff;
use App\BookShop\Models\WarehouseStock;
use App\BookShop\Services\RestockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct(private readonly RestockService $restockService)
    {
    }

    public function index(Request $request): View
    {
        $stock = WarehouseStock::query()
            ->with('book')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('book', fn ($bq) => $bq->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%"));
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $prefillBook = old('book_id') ? Book::find(old('book_id')) : null;

        return view('bookshop::staff.warehouse.index', compact('stock', 'prefillBook'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:bookshop_books,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $stock = WarehouseStock::updateOrCreate(
            ['book_id' => $data['book_id']],
            ['quantity' => $data['quantity'], 'updated_by_staff_id' => $staff->id]
        );

        $book = Book::find($data['book_id']);

        $status = "Warehouse stock for \"{$book?->title}\" set to {$stock->quantity}.";

        // "Their fulfillment is matched when items are in stock" - every
        // time warehouse quantity is set, sweep for pending requests on
        // this specific book and auto-approve as many as the new
        // quantity supports, oldest first. Harmless to run even when
        // quantity went down or nothing changed: with nothing available,
        // the first attempt just fails immediately and the loop stops.
        if ($book) {
            $result = $this->restockService->autoFulfillPendingForBook($book, $staff);
            $approvedCount = $result['approved']->count();

            if ($approvedCount > 0) {
                $status .= " Also auto-approved {$approvedCount} pending request(s) for this book that were waiting on stock.";
            }
        }

        return back()->with('status', $status);
    }
}
