<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;

class LibraryShowcaseController extends Controller
{
    public function index()
    {
        $categories = BookCategory::select('id', 'name')
            ->withCount('books')
            ->having('books_count', '>', 0)
            ->limit(8)
            ->get();

        $featuredBooks = Book::with(['bookCategory', 'author.user'])
            ->select('id', 'title', 'book_category_id', 'cover_image', 'author_id', 'description')
            ->limit(6)
            ->get()
            ->map(fn($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'cover_image' => $book->cover_image,
                'category' => $book->bookCategory?->name,
                'author' => $book->author?->user?->name ?? 'Unknown Author',
                'description' => $book->description ? substr($book->description, 0, 150) . '...' : null,
            ]);

        $totalBooks = Book::count();
        $totalCategories = BookCategory::count();

        return view('books-showcase', compact('categories', 'featuredBooks', 'totalBooks', 'totalCategories'));
    }
}
