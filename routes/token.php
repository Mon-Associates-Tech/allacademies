<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Token Routes
|--------------------------------------------------------------------------
|
| Routes for token subscription management, token allocations,
| and messenger transactions (Admin revenue tracking).
|
*/

Route::middleware(['auth'])->group(function () {
    // Token Subscription Management Routes (Admin Only - Revenue Tracking)
    Route::prefix('dashboard/messenger-transactions')->name('admin.messenger-transactions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MessengerTransactionsController::class, 'index'])->name('index');
    });

    // Token Subscription Routes (User Purchases)
    Route::prefix('token-subscriptions')->name('token-subscriptions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TokenSubscriptionController::class, 'index'])->name('index');
        Route::get('/history', [\App\Http\Controllers\TokenSubscriptionController::class, 'history'])->name('history');
        Route::get('/create', [\App\Http\Controllers\TokenSubscriptionController::class, 'create'])->name('create');
        Route::match(['get', 'post'], '/checkout', [\App\Http\Controllers\TokenSubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/process-payment', [\App\Http\Controllers\TokenSubscriptionController::class, 'processPayment'])->name('process-payment');
        Route::post('/', [\App\Http\Controllers\TokenSubscriptionController::class, 'store'])->name('store');
        Route::get('/{subscription}', [\App\Http\Controllers\TokenSubscriptionController::class, 'show'])->name('show');
        Route::get('/{cycle}/topup', [\App\Http\Controllers\TokenSubscriptionController::class, 'topup'])->name('topup');
        Route::post('/topup/process', [\App\Http\Controllers\TokenSubscriptionController::class, 'processTopup'])->name('process-topup');
    });

    // Token Allocation Management Routes (Admin Only)
    Route::prefix('token-allocations')->name('token-allocations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TokenAllocationController::class, 'index'])->name('index');
        Route::get('/create-tier', [\App\Http\Controllers\TokenAllocationController::class, 'createTier'])->name('create-tier');
        Route::post('/store-tier', [\App\Http\Controllers\TokenAllocationController::class, 'storeTier'])->name('store-tier');
        Route::get('/tiers/{tier}/edit', [\App\Http\Controllers\TokenAllocationController::class, 'editTier'])->name('edit-tier');
        Route::put('/tiers/{tier}', [\App\Http\Controllers\TokenAllocationController::class, 'updateTier'])->name('update-tier');
        Route::get('/assign-tokens', [\App\Http\Controllers\TokenAllocationController::class, 'assignTokens'])->name('assign-tokens');
        Route::post('/store-assignment', [\App\Http\Controllers\TokenAllocationController::class, 'storeAssignment'])->name('store-assignment');
        Route::get('/users-json', [\App\Http\Controllers\TokenAllocationController::class, 'getUsersJson'])->name('users-json');
        Route::patch('/cycles/{cycle}/deactivate', [\App\Http\Controllers\TokenAllocationController::class, 'deactivateCycle'])->name('deactivate-cycle');
        Route::delete('/cycles/{cycle}/revoke', [\App\Http\Controllers\TokenAllocationController::class, 'revokeTokens'])->name('revoke-tokens');
        Route::get('/users/{user}/tokens', [\App\Http\Controllers\TokenAllocationController::class, 'viewUserTokens'])->name('view-user-tokens');
    });
});
