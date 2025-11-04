<?php

use App\Livewire\Librarians\BookRequests;
use App\Livewire\Librarians\BorrowedBooks;
use App\Livewire\Librarians\LibraryBooks;
use App\Livewire\Librarians\LibraryDashboard;
use App\Livewire\Librarians\LibraryReports;
use App\Livewire\Librarians\BookReturns;
use App\Livewire\Librarians\OverdueBooks;
use App\Livewire\Librarians\BookInventory;
use App\Livewire\Librarians\StudentLibraryProfiles;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:librarian'])->prefix('librarian')->name('librarian.')->group(function () {
    Route::get('dashboard', LibraryDashboard::class)->name('dashboard');

    // Book Management
    Route::get('books', LibraryBooks::class)->name('books');
    Route::get('books/{book}', \App\Livewire\Authors\BookDetails::class)->name('books.show');
    Route::get('books/new', \App\Livewire\Authors\BookCreate::class)->name('books.create');
    Route::get('books/import', LibraryBooks::class)->name('books.import');
    Route::get('categories', LibraryBooks::class)->name('categories.index');
    Route::get('books/inventory/ee', BookInventory::class)->name('inventory');
    Route::get('cards', \App\Livewire\Librarians\LibraryCardManagement::class)->name('cards');

    // Borrowing Management
    Route::get('book-requests', BookRequests::class)->name('book-requests');
    Route::get('borrowed-books', BorrowedBooks::class)->name('borrowed-books');
    Route::get('book-returns', BookReturns::class)->name('book-returns');
    Route::get('overdue-books', OverdueBooks::class)->name('overdue-books');

    // Students & Reports
    Route::get('student-profiles', StudentLibraryProfiles::class)->name('student-profiles');
    Route::get('reports', LibraryReports::class)->name('reports');
});
