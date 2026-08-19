<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookProgressController;
use App\Http\Controllers\BookSubscriptionController;
use App\Livewire\Learning\BookQuizInterface;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Book Routes
|--------------------------------------------------------------------------
*/

// Public Book Routes
Route::get('shared/books/{book}', [BookController::class, 'publicShow'])->name('books.public');

// Authenticated Book Routes
Route::middleware(['auth'])->group(function () {

    // Book Browsing and Reading
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('kids-books', [BookController::class, 'kidsBooks'])->name('kids-books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show')->middleware('token.subscription');
    Route::post('/books/{book}/request-borrow', [BookController::class, 'requestBorrow'])->name('books.request-borrow');
    Route::post('/books/{book}/progress', [BookController::class, 'saveProgress'])->name('books.progress');
    Route::get('books/{book}/read', [BookController::class, 'read'])->name('books.read');
    Route::get('books/{book}/preview', [BookController::class, 'preview'])->name('books.preview');
    Route::get('books/{book}/file', [BookController::class, 'streamFile'])->name('books.file.stream');
    Route::get('books/{book}/download', [BookController::class, 'downloadFile'])->middleware('signed')->name('books.file.download');
    Route::get('books/{book}/paint', [BookController::class, 'paint'])->name('books.paint');
    Route::get('books/{book}/pdf-page-png', [BookController::class, 'pdfPageToPng'])->name('books.pdf-page-png');

    // Book Subscription Routes
    Route::get('books/{book}/payment-instructions', [BookSubscriptionController::class, 'create'])->name('books.payment-instructions');
    Route::post('books/{book}/subscribe', [BookSubscriptionController::class, 'store'])->name('books.subscribe.store');
    Route::get('subscriptions/{subscription}/payment', [BookSubscriptionController::class, 'showPayment'])->name('subscriptions.payment.show');
    Route::post('subscriptions/{subscription}/verify-payment', [BookSubscriptionController::class, 'verifyPayment'])->name('subscriptions.payment.verify');
    Route::delete('subscriptions/{subscription}/cancel', [BookSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // FIXED: Added missing controller action for reviews (Adjust class/method name if yours is different)
   // Route::post('/books/{book}/reviews', [\App\Http\Controllers\BookReviewController::class, 'store'])->name('books.reviews.store');

    // Book Reading Progress Routes
    Route::post('/books/update-progress', [BookProgressController::class, 'updateProgress'])->name('books.progress.update');
    Route::get('/books/{book}/progress', [BookProgressController::class, 'getProgress'])->name('books.progress.get');
    Route::get('/my-reading-progress', [BookProgressController::class, 'getUserProgress'])->name('books.progress.user');
    Route::post('/books/mark-completed', [BookProgressController::class, 'markCompleted'])->name('books.progress.complete');
    Route::delete('/books/{book}/progress', [BookProgressController::class, 'deleteProgress'])->name('books.progress.delete');

    // Book Annotations Routes
    Route::get('/books/{book}/annotations', [\App\Http\Controllers\BookAnnotationController::class, 'index'])->name('books.annotations.index');
    Route::post('/books/{book}/annotations', [\App\Http\Controllers\BookAnnotationController::class, 'store'])->name('books.annotations.store');
    Route::delete('/books/{book}/annotations/{annotation}', [\App\Http\Controllers\BookAnnotationController::class, 'destroy'])->name('books.annotations.destroy');

    // User Books Routes
    Route::get('/user-books/create', fn () => view('user-books.create'))->middleware('token.subscription')->name('user-books.create');
    Route::get('/user-books/shared', fn () => view('user-books.shared'))->middleware('token.subscription')->name('user-books.shared');
    Route::get('/user-books', \App\Livewire\UserBooks\UserBooksIndex::class)->middleware('token.subscription')->name('user-books.index');
    Route::get('/user-books/{userBook}', function (App\Models\UserBook $userBook) {
        if ($userBook->user_id !== auth()->id() &&
            ! $userBook->shares()->where('shared_to_user_id', auth()->id())->where('status', 'accepted')->exists()) {
            abort(403);
        }
        $userBook->load('user');
        return view('user-books.show', compact('userBook'));
    })->middleware('token.subscription')->name('user-books.show');
    Route::get('/user-books/{userBook}/edit', \App\Livewire\UserBooks\UserBookForm::class)->middleware('token.subscription')->name('user-books.edit');
    Route::get('/{userBook}/manage-shares', \App\Livewire\UserBooks\ManageShares::class)->middleware('token.subscription')->name('user-books.manage-shares');

    // Learning/Quiz Routes (Book-related)
    Route::get('/learning/quiz/{bookId?}', BookQuizInterface::class)->middleware('token.subscription')->name('learning.quiz');
});
