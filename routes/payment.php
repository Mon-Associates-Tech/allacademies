<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\TokenPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Routes for handling various payment operations including public payments,
| fee payments, payment callbacks, and token payments.
|
*/

// Public Payment Routes (No Authentication Required)
Route::prefix('general/pay')->name('payments.public.')->group(function () {
    Route::get('/init', [App\Http\Controllers\PublicPaymentController::class, 'showLookupForm'])->name('lookup');
    Route::post('/lookup', [App\Http\Controllers\PublicPaymentController::class, 'lookupStudent'])->name('lookup.post');
    Route::post('/initialize', [App\Http\Controllers\PublicPaymentController::class, 'initializePayment'])->name('initialize');
    Route::get('/callback', [App\Http\Controllers\PublicPaymentController::class, 'paymentCallback'])->name('callback');
    Route::get('/success/{payment}', [App\Http\Controllers\PublicPaymentController::class, 'success'])->name('success');
});

// Payment Form and Callback Routes (Public)
Route::get('/payment', [PaymentController::class, 'showForm'])->name('payment.form');
Route::get('/pay', [PaymentController::class, 'initialize'])->name('payment.initialize');
Route::get('/book-pay/{subscription}', [PaymentController::class, 'initializeBook'])->name('payment.book.initialize');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/book-callback', [PaymentController::class, 'bookCallback'])->name('payment.book.callback');
Route::get('/createSubAccount', [PaymentController::class, 'createSubAccount'])->name('payment.subAccount');
Route::get('/payment/callback/school-fees', [SchoolController::class, 'schoolFeesCallback'])->name('schoolfees.callback');

// Fee Payment Routes (Public)
Route::get('/feepayment/{student}', [PaymentController::class, 'showPaymentForm'])->name('feepayment.form');
Route::post('/feepayment', [PaymentController::class, 'processPayment'])->name('feepayment.process');
Route::get('/feepayment/callback', [PaymentController::class, 'paymentCallback'])->name('feepayment.callback');
Route::get('/feepayment/{student}/thank-you', [PaymentController::class, 'thankYou'])->name('feepayment.thankyou');
Route::get('/feepayment/callback/{student}', [PaymentController::class, 'paymentCallback'])->name('feepayment.student.callback');

// Authenticated Payment Routes
Route::middleware(['auth'])->group(function () {
    // Subscription & Payment Management
    Route::resource('subscriptions', \App\Http\Controllers\SubscriptionController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
    Route::post('/subscriptions/toggle-test-mode', [\App\Http\Controllers\SubscriptionController::class, 'toggleTestMode'])->name('subscriptions.toggle-test-mode');

    // Admin payment actions: manual creation and status updates
    Route::post('/admin/payments/manual', [\App\Http\Controllers\AdminPaymentController::class, 'store'])->name('admin.payments.manual');
    Route::post('/admin/payments/{id}/status', [\App\Http\Controllers\AdminPaymentController::class, 'updateStatus'])->name('admin.payments.update-status');

    // Token Payment Routes
    Route::prefix('token-payments')->name('token-payments.')->group(function () {
        Route::get('/token/initialize', [TokenPaymentController::class, 'initialize'])->name('initialize');
        Route::get('/callback', [TokenPaymentController::class, 'callback'])->name('callback');
        Route::get('/cancel', [TokenPaymentController::class, 'cancel'])->name('cancel');
    });
});
