<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Book;
use App\BookShop\Models\Category;
use App\BookShop\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Backs the <x-bookshop::book-picker> component used on the stock,
     * warehouse, and restock-request forms — lets both roles search by
     * title/author/ISBN instead of needing to know a book's numeric ID.
     * Not superadmin-gated: branch admins need this for restock requests.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        $books = Book::query()
            ->active()
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'like', "%{$query}%")
                        ->orWhere('author', 'like', "%{$query}%")
                        ->orWhere('isbn', 'like', "%{$query}%");
                });
            })
            ->orderBy('title')
            ->limit(15)
            ->get(['id', 'title', 'author']);

        return response()->json($books);
    }

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

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('bookshop/covers', 'public');
        }

        if ($request->hasFile('preview_pdf')) {
            $data['preview_pdf_path'] = $request->file('preview_pdf')->store('bookshop/previews', 'public');
        }

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
        $data = $this->validated($request);

        if ($request->boolean('remove_cover_image')) {
            $this->deleteFile($book->cover_image_path);
            $data['cover_image_path'] = null;
        } elseif ($request->hasFile('cover_image')) {
            $this->deleteFile($book->cover_image_path);
            $data['cover_image_path'] = $request->file('cover_image')->store('bookshop/covers', 'public');
        }

        if ($request->boolean('remove_preview_pdf')) {
            $this->deleteFile($book->preview_pdf_path);
            $data['preview_pdf_path'] = null;
        } elseif ($request->hasFile('preview_pdf')) {
            $this->deleteFile($book->preview_pdf_path);
            $data['preview_pdf_path'] = $request->file('preview_pdf')->store('bookshop/previews', 'public');
        }

        $book->update($data);

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
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:bookshop_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            // 4MB cover, common web image formats only.
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            // 20MB preview - generous enough for a sample chapter scan
            // without leaving the door open to arbitrarily large uploads.
            'preview_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        // The raw UploadedFile objects aren't mass-assignable (only
        // cover_image_path/preview_pdf_path are in $fillable) - drop them
        // here rather than relying on Eloquent to silently ignore them.
        unset($data['cover_image'], $data['preview_pdf']);

        return $data;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
