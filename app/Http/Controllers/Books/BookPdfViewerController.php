<?php

namespace App\Http\Controllers\Books;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BookController;

class BookPdfViewerController extends Controller
{
    /**
     * Show the PDF.js reader.
     */
 public function read(Book $book): View
{
    $user = Auth::user();

    if (! $this->canReadBook($user, $book)) {
        return redirect()
            ->route('books.show', $book)
            ->with('error', 'Subscription required to read this book');
    }

    $streamUrl = URL::temporarySignedRoute(
        'books.pdf.stream',
        now()->addMinutes(60),
        ['book' => $book->id]
    );

    return view('books.book-document-viewer', [
        'book' => $book,
        'streamUrl' => $streamUrl,
        'progressUrl' => route('books.progress.update'),
        'pdfJsViewerUrl' => asset('pdfjs/web/viewer.html'),

        // ← 5b-2: annotation endpoints for the bridge
        'annotationsUrl' => route('books.pdfjs-annotations.index', $book),
        'annotationsStoreUrl' => route('books.annotations.store', $book),
        'annotationsSyncUrl' => route('books.pdfjs-annotations.sync', $book),
    ]);
}

    /**
     * Signed PDF stream for the PDF.js viewer.
     *
     * This delegates to the existing BookController@streamFile so we do not
     * duplicate or replace the existing streaming logic.
     */
    public function streamFile(Book $book)
    {
        return app(BookController::class)->streamFile($book);
    }

    /**
     * Mirrors the existing read permission logic without modifying BookController.
     */
    private function canReadBook($user, Book $book): bool
    {
        if (! $user) {
            return false;
        }

        if (! $book->has_softcopy) {
            return false;
        }

        if ($user->hasAnyRole(['owner', 'admin'])) {
            return true;
        }

        if ((float) $book->annual_subscription_fee <= 0) {
            return true;
        }

        $isBookAuthor = (int) ($book->author?->user?->id ?? 0) === (int) $user->id;

        if ($isBookAuthor) {
            return true;
        }

        return $user->bookSubscriptions()
            ->where('book_id', $book->id)
            ->where('status', 'paid')
            ->exists();
    }
}