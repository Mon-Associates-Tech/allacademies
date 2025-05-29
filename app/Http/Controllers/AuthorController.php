<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\BookResource;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Author::class, 'author');
    }

    public function index()
    {
        return new AuthorCollection(Author::with('user')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $author = Author::create($validated);

        return new AuthorResource($author->load('user'));
    }

    public function show(Author $author)
    {
        return new AuthorResource($author->load('user', 'books'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $author->update($validated);

        return new AuthorResource($author->load('user'));
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return response()->noContent();
    }

    // Additional methods specific to authors

    public function createBook(Request $request, Author $author)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
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

        $book = Book::create([
            'title' => $validated['title'],
            'author_id' => $author->id,
            'book_category_id' => $validated['book_category_id'],
            'edition' => $validated['edition'] ?? null,
            'publisher' => $validated['publisher'] ?? null,
            'pages' => $validated['pages'] ?? null,
            'has_hardcopy' => $validated['has_hardcopy'] ?? false,
            'has_softcopy' => $validated['has_softcopy'] ?? false,
            'additional_info' => $validated['additional_info'] ?? null,
        ]);

        return new BookResource($book->load('category'));
    }

    public function updateBook(Request $request, Author $author, Book $book)
    {
        if ($book->author_id !== $author->id) {
            return response()->json(['message' => 'This book does not belong to this author'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
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

        return new BookResource($book->load('category'));
    }

    public function deleteBook(Author $author, Book $book)
    {
        if ($book->author_id !== $author->id) {
            return response()->json(['message' => 'This book does not belong to this author'], 403);
        }

        $this->authorize('delete', $book);

        $book->delete();

        return response()->noContent();
    }

    public function getBooks(Author $author)
    {
        $books = $author->books()->with('category')->paginate();
        return BookResource::collection($books);
    }
}
