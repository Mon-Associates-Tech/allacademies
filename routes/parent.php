<?php

use App\Livewire\Parent\Dashboard;
use App\Livewire\Parent\ParentWardPerformance;
use App\Livewire\Parent\Wards;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'parent'])->prefix('parent')->name('parent.')->group(function () {

    // Dashboard
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    // Wards Management
    Route::get('wards', Wards::class)->name('wards');
    Route::get('wards/{student}', Wards::class)->name('wards.show');
    //    Route::get('wards/{student}', \App\Livewire\Parent\WardDetails::class)->name('wards.show');

    // Academic Performance
    Route::get('performance', ParentWardPerformance::class)->name('performance');
    Route::get('performance/{student}', ParentWardPerformance::class)->name('performance.student');
    //    Route::get('performance/{student}', \App\Livewire\Parent\StudentPerformance::class)->name('performance.student');

    // Reports & Analytics
    Route::get('reports', \App\Livewire\Parent\ParentReportsManager::class)->name('reports');
    Route::get('reports/generate', \App\Livewire\Parent\WardPerformanceReportGenerator::class)->name('reports.generate');
    Route::get('reports/{report}/download', \App\Http\Controllers\Parent\ReportController::class)->name('reports.download');

    // Terminal Reports
    Route::get('terminal-reports', \App\Livewire\Parent\ParentReportsManager::class)->name('terminal-reports');
    Route::get('terminal-reports/{student}', \App\Livewire\Parent\ParentReportsManager::class)->name('terminal-reports.student');
    Route::get('terminal-reports/{student}/print', \App\Http\Controllers\Parent\TerminalReportController::class)->name('terminal-reports.print');
    Route::get('terminal-reports/{student}/download', [\App\Http\Controllers\Parent\TerminalReportController::class, 'download'])->name('terminal-reports.download');

    // Notifications
    Route::get('notifications', \App\Livewire\Parent\ParentNotificationsHandler::class)->name('notifications');
    Route::post('notifications/{notification}/mark-read', [\App\Http\Controllers\Parent\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\Parent\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Book Subscriptions
    Route::get('books', \App\Livewire\Parent\ParentBooksManager::class)->name('books');
    //    Route::get('books/{book}', \App\Livewire\Parent\BookDetails::class)->name('books.show');
    Route::post('books/{book}/subscribe', [\App\Http\Controllers\Parent\BookSubscriptionController::class, 'subscribe'])->name('books.subscribe');
    Route::post('books/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Parent\BookSubscriptionController::class, 'cancel'])->name('books.cancel');
    Route::get('books/subscriptions/manage', \App\Livewire\Parent\ParentBooksManager::class)->name('books.manage');

    // Digital Library
    Route::get('library', \App\Livewire\Parent\ParentLibraryManager::class)->name('library');
    //    Route::get('library/{book}/read', \App\Livewire\Parent\BookReader::class)->name('library.read');
    //    Route::get('library/categories', \App\Livewire\Parent\LibraryCategories::class)->name('library.categories');
    //    Route::get('library/search', \App\Livewire\Parent\LibrarySearch::class)->name('library.search');

    // Additional routes for specific functionalities
    //    Route::get('calendar', \App\Livewire\Parent\Calendar::class)->name('calendar');
    //    Route::get('messages', \App\Livewire\Parent\Messages::class)->name('messages');
    //    Route::get('profile', \App\Livewire\Parent\Profile::class)->name('profile');
    //    Route::get('settings', \App\Livewire\Parent\Settings::class)->name('settings');

    // Fees & Payments
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Parent\ParentFeeController::class, 'index'])->name('index');
        Route::get('/payment/{student?}', [\App\Http\Controllers\Parent\ParentFeeController::class, 'payment'])->name('payment');
        Route::post('/initialize', [\App\Http\Controllers\Parent\ParentFeeController::class, 'initializePayment'])->name('initialize');
        Route::get('/callback', [\App\Http\Controllers\Parent\ParentFeeController::class, 'callback'])->name('callback');
        Route::get('/receipt/{payment}', [\App\Http\Controllers\Parent\ParentFeeController::class, 'receipt'])->name('receipt');
        //        Route::get('/transactions', [\App\Http\Controllers\Parent\ParentFeeController::class, 'transactions'])->name('transactions');
    });

    // Alias route for convenience
    Route::get('payments', [\App\Http\Controllers\Parent\ParentFeeController::class, 'index'])->name('payments.index');
    Route::get('transactions', [\App\Http\Controllers\Parent\ParentFeeController::class, 'transactions'])->name('payments.transactions');

});
