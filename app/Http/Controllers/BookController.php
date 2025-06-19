<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(Request $request)
    {
        $query = Book::with('author', 'category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('book_category_id', $request->category_id);
        }

        // Filter by format
        if ($request->has('format')) {
            if ($request->format === 'hardcopy') {
                $query->where('has_hardcopy', true);
            } elseif ($request->format === 'softcopy') {
                $query->where('has_softcopy', true);
            } elseif ($request->format === 'both') {
                $query->where('has_hardcopy', true)->where('has_softcopy', true);
            }
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return view('books.index', ['books' => $query->paginate(10)]);
        return new BookCollection($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'book_category_id' => 'required|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'has_hardcopy' => 'boolean',
            'has_softcopy' => 'boolean',
            'additional_info' => 'nullable|string',
        ]);

        if (!$validated['has_hardcopy'] && !$validated['has_softcopy']) {
            return response()->json(['message' => 'Book must have at least one format (hardcopy or softcopy)'], 422);
        }

        $book = Book::create($validated);

        return new BookResource($book->load('author', 'category'));
    }

    public function show(Book $book)
    {
        return new BookResource($book->load('author', 'category', 'borrowings', 'subscriptions', 'groupSubscriptions'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|exists:authors,id',
            'book_category_id' => 'sometimes|required|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'has_hardcopy' => 'boolean',
            'has_softcopy' => 'boolean',
            'additional_info' => 'nullable|string',
        ]);

        if (isset($validated['has_hardcopy']) && isset($validated['has_softcopy']) &&
            !$validated['has_hardcopy'] && !$validated['has_softcopy']) {
            return response()->json(['message' => 'Book must have at least one format (hardcopy or softcopy)'], 422);
        }

        $book->update($validated);

        return new BookResource($book->load('author', 'category'));
    }

    public function destroy(Book $book)
    {
        // Check if book has active borrowings or subscriptions
        $hasBorrowings = $book->borrowings()->where('status', 'borrowed')->exists();
        $hasSubscriptions = $book->subscriptions()->where('status', 'active')->exists();
        $hasGroupSubscriptions = $book->groupSubscriptions()->where('status', 'active')->exists();

        if ($hasBorrowings || $hasSubscriptions || $hasGroupSubscriptions) {
            return response()->json([
                'message' => 'Cannot delete book with active borrowings or subscriptions'
            ], 422);
        }

        $book->delete();

        return response()->noContent();
    }
}
