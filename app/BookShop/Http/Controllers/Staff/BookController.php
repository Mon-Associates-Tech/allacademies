<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Category;
use App\BookShop\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Visible to both roles — a branch admin needs to see the catalog to
     * know what they can request stock for, even though they can't edit it.
     */
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with('category')
            ->withSum('stockLevels as total_stock', 'quantity')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('bookshop::staff.books.index', compact('books', 'categories'));
    }

    // Everything below is superadmin-only (enforced via route middleware).

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('bookshop::staff.books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();
        $data['created_by_staff_id'] = $staff->id;

        $book = Book::create($data);

        return redirect()->route('bookshop.staff.books.index')
            ->with('status', "\"{$book->title}\" added to the catalog.");
    }

    public function edit(Book $book): View
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('bookshop::staff.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $book->update($this->validated($request));

        return redirect()->route('bookshop.staff.books.index')
            ->with('status', "\"{$book->title}\" updated.");
    }

    public function toggleActive(Book $book): RedirectResponse
    {
        $book->update(['is_active' => ! $book->is_active]);

        return back()->with('status', "\"{$book->title}\" ".($book->is_active ? 'activated.' : 'deactivated.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:bookshop_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
