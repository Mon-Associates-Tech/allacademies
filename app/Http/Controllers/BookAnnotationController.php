<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookAnnotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookAnnotationController extends Controller
{
    public function index(Book $book)
    {
        $annotations = BookAnnotation::query()
            ->where('book_id', $book->id)
            ->withCount('comments')
            ->get()
            ->map(fn($annotation) => [
                'id' => $annotation->id,
                'book_id' => $annotation->book_id,
                'user_id' => $annotation->user_id,
                'page_number' => $annotation->page_number,
                'x_pct' => (float) $annotation->x_pct,
                'y_pct' => (float) $annotation->y_pct,
                'width_pct' => (float) $annotation->width_pct,
                'height_pct' => (float) $annotation->height_pct,
                'color' => $annotation->color,
                'resolved_at' => $annotation->resolved_at?->toISOString(),
                'comments_count' => $annotation->comments_count,
            ]);

        return response()->json(['annotations' => $annotations]);
    }

    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
            'x_pct' => 'required|numeric|min:0|max:100',
            'y_pct' => 'required|numeric|min:0|max:100',
            'width_pct' => 'required|numeric|min:0|max:100',
            'height_pct' => 'required|numeric|min:0|max:100',
            'color' => 'nullable|string|max:20',
        ]);

        $annotation = BookAnnotation::create([
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'page_number' => $validated['page_number'],
            'x_pct' => $validated['x_pct'],
            'y_pct' => $validated['y_pct'],
            'width_pct' => $validated['width_pct'],
            'height_pct' => $validated['height_pct'],
            'color' => $validated['color'] ?? '#f59e0b',
        ]);

        return response()->json([
            'annotation' => [
                'id' => $annotation->id,
                'book_id' => $annotation->book_id,
                'user_id' => $annotation->user_id,
                'page_number' => $annotation->page_number,
                'x_pct' => (float) $annotation->x_pct,
                'y_pct' => (float) $annotation->y_pct,
                'width_pct' => (float) $annotation->width_pct,
                'height_pct' => (float) $annotation->height_pct,
                'color' => $annotation->color,
                'resolved_at' => null,
                'comments_count' => 0,
            ]
        ], 201);
    }

    public function destroy(Book $book, BookAnnotation $annotation)
    {
        if ($annotation->book_id !== $book->id) {
            abort(404);
        }

        if ($annotation->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['owner', 'admin'])) {
            abort(403);
        }

        $annotation->delete();

        return response()->json(['message' => 'Annotation deleted'], 200);
    }
}
