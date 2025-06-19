<?php

namespace App\Http\Controllers;

use App\Models\BookCategory;
use Illuminate\Http\Request;
use App\Http\Resources\BookCategoryResource;
use App\Http\Resources\BookCategoryCollection;
use App\Http\Resources\BookResource;

class BookCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BookCategory::class, 'bookCategory');
    }

    public function index()
    {
        return new BookCategoryCollection(BookCategory::paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:book_categories',
            'description' => 'nullable|string',
        ]);

        $bookCategory = BookCategory::create($validated);

        return new BookCategoryResource($bookCategory);
    }

    public function show(BookCategory $bookCategory)
    {
        return new BookCategoryResource($bookCategory);
    }

    public function update(Request $request, BookCategory $bookCategory)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:book_categories,name,' . $bookCategory->id,
            'description' => 'nullable|string',
        ]);

        $bookCategory->update($validated);

        return new BookCategoryResource($bookCategory);
    }

    public function destroy(BookCategory $bookCategory)
    {
        // Check if category has books
        if ($bookCategory->books()->exists()) {
            return response()->json(['message' => 'Cannot delete category that has books assigned to it'], 422);
        }
        
        $bookCategory->delete();

        return response()->noContent();
    }
    
    public function getBooks(BookCategory $bookCategory)
    {
        $books = $bookCategory->books()->with('author')->paginate();
        return BookResource::collection($books);
    }
}