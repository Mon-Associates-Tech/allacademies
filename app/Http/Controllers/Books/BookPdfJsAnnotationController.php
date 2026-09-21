<?php

namespace App\Http\Controllers\Books;

use App\Models\Book;
use App\Models\BookAnnotation;
use App\Models\BookAnnotationComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class BookPdfJsAnnotationController extends Controller
{
    public function index(Book $book)
    {
        abort_unless($this->canReadBook(Auth::user(), $book), 403);

        return response()->json([
            'annotations' => $this->serializeAnnotations($book),
        ]);
    }

    /**
     * Bulk upsert PDF.js native editors for the current user.
     * Rows with external_id = NULL (rect annotations) are never touched.
     */
    public function sync(Request $request, Book $book)
    {
        $user = Auth::user();
        abort_unless($this->canReadBook($user, $book), 403);

        $validated = $request->validate([
            'editors' => 'array',
            'editors.*.external_id' => 'required|string|max:191',
            'editors.*.page' => 'required|integer|min:1',
            'editors.*.x_pct' => 'required|numeric|min:0|max:100',
            'editors.*.y_pct' => 'required|numeric|min:0|max:100',
            'editors.*.width_pct' => 'required|numeric|min:0|max:100',
            'editors.*.height_pct' => 'required|numeric|min:0|max:100',
            'editors.*.color' => 'nullable|string|max:20',
            'editors.*.source' => 'nullable|string|max:30',
        ]);

        $incoming = collect($validated['editors'] ?? []);

        // Delete user's native rows that no longer exist in the viewer.
        BookAnnotation::query()
            ->where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->whereNotNull('external_id')
            ->whereNotIn('external_id', $incoming->pluck('external_id')->all())
            ->get()
            ->each(fn (BookAnnotation $stale) => $stale->delete());

        foreach ($incoming as $data) {
            $annotation = BookAnnotation::query()
                ->where('book_id', $book->id)
                ->where('user_id', $user->id)
                ->where('external_id', $data['external_id'])
                ->first();

            if (! $annotation) {
                $annotation = new BookAnnotation();
                $annotation->book_id = $book->id;
                $annotation->user_id = $user->id;
                $annotation->external_id = $data['external_id'];
            }

            $annotation->page_number = (int) $data['page'];
            $annotation->x_pct = $data['x_pct'];
            $annotation->y_pct = $data['y_pct'];
            $annotation->width_pct = $data['width_pct'];
            $annotation->height_pct = $data['height_pct'];
            $annotation->color = $data['color'] ?? '#f59e0b';
            $annotation->source = $data['source'] ?? 'highlight';
            $annotation->save();
        }

        return response()->json([
            'annotations' => $this->serializeAnnotations($book),
        ]);
    }

    public function comments(Book $book, BookAnnotation $annotation)
    {
        abort_unless($annotation->book_id === $book->id, 404);
        abort_unless($this->canReadBook(Auth::user(), $book), 403);

        return response()->json(['comments' => $this->serializeComments($annotation)]);
    }

    public function storeComment(Request $request, Book $book, BookAnnotation $annotation)
    {
        $user = Auth::user();
        abort_unless($annotation->book_id === $book->id, 404);
        abort_unless($this->canReadBook($user, $book), 403);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:book_annotation_comments,id',
        ]);

        $comment = new BookAnnotationComment();
        $comment->book_annotation_id = $annotation->id;
        $comment->user_id = $user->id;
        $comment->parent_id = $validated['parent_id'] ?? null;
        $comment->message = $validated['message'];
        $comment->save();

        return response()->json(['comment' => $comment->load('user')]);
    }

    public function destroyComment(Book $book, BookAnnotation $annotation, BookAnnotationComment $comment)
    {
        abort_unless($annotation->book_id === $book->id, 404);
        abort_unless($comment->book_annotation_id === $annotation->id, 404);
        abort_unless($comment->user_id === Auth::id(), 403);

        $comment->delete();

        return response()->json(['success' => true]);
    }

    public function resolve(Request $request, Book $book, BookAnnotation $annotation)
    {
        $user = Auth::user();
        abort_unless($annotation->book_id === $book->id, 404);
        abort_unless($this->canReadBook($user, $book), 403);

        $resolved = (bool) $request->boolean('resolved');
        $annotation->resolved_at = $resolved ? now() : null;
        $annotation->resolved_by = $resolved ? $user->id : null;
        $annotation->save();

        return response()->json(['success' => true, 'resolved_at' => $annotation->resolved_at?->toISOString()]);
    }

    private function serializeAnnotations(Book $book)
    {
        return BookAnnotation::query()
            ->where('book_id', $book->id)
            ->withCount('comments')
            ->with(['user:id,name', 'comments' => fn ($q) => $q->with('user:id,name')->orderBy('created_at')])
            ->orderBy('page_number')
            ->get()
            ->map(fn (BookAnnotation $a) => [
                'id' => $a->id,
                'user_id' => $a->user_id,
                'user_name' => $a->user?->name,
                'page_number' => (int) $a->page_number,
                'x_pct' => (float) $a->x_pct,
                'y_pct' => (float) $a->y_pct,
                'width_pct' => (float) $a->width_pct,
                'height_pct' => (float) $a->height_pct,
                'color' => $a->color,
                'external_id' => $a->external_id,
                'source' => $a->source,
                'resolved_at' => $a->resolved_at?->toISOString(),
                'comments_count' => $a->comments_count,
                'comments' => $a->comments->map(fn ($c) => [
                    'id' => $c->id,
                    'user_id' => $c->user_id,
                    'user_name' => $c->user?->name,
                    'parent_id' => $c->parent_id,
                    'message' => $c->message,
                    'created_at' => $c->created_at?->toISOString(),
                ]),
            ]);
    }

    private function serializeComments(BookAnnotation $annotation)
    {
        return $annotation->comments()
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'user_name' => $c->user?->name,
                'parent_id' => $c->parent_id,
                'message' => $c->message,
                'created_at' => $c->created_at?->toISOString(),
            ]);
    }

    private function canReadBook($user, Book $book): bool
    {
        if (! $user || ! $book->has_softcopy) {
            return false;
        }
        if ($user->hasAnyRole(['owner', 'admin'])) {
            return true;
        }
        if ((float) $book->annual_subscription_fee <= 0) {
            return true;
        }
        if ((int) ($book->author?->user?->id ?? 0) === (int) $user->id) {
            return true;
        }

        return $user->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', 'paid')
            ->exists();
    }
}