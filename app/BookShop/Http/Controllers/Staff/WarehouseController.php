<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Staff;
use App\BookShop\Models\WarehouseStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WarehouseController extends Controller
{
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

        return back()->with('status', "Warehouse stock for \"{$book?->title}\" set to {$stock->quantity}.");
    }
}
