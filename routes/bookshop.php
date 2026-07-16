<?php

use App\BookShop\Http\Controllers\Customer\Auth\CustomerRegisterController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignInController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignOutController;
use App\BookShop\Http\Controllers\Customer\CustomerHomeController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignInController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignOutController;
use App\BookShop\Http\Controllers\Staff\BranchPendingController;
use App\BookShop\Http\Controllers\Staff\StaffDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BookShop Module Routes
|--------------------------------------------------------------------------
|
| Two entirely separate identity spaces, matching the two guards:
|   - bookshop.staff.*  -> bookshop_staff guard (superadmin / branch admin)
|   - bookshop.shop.*   -> bookshop_customer guard (customers)
|
*/

Route::prefix('bookshop/staff')->name('bookshop.staff.')->group(function () {
    Route::middleware('bookshop.guest:bookshop_staff')->group(function () {
        Route::get('login', [StaffSignInController::class, 'create'])->name('login');
        Route::post('login', [StaffSignInController::class, 'store']);
    });

    Route::middleware('bookshop.auth:bookshop_staff')->group(function () {
        Route::post('logout', [StaffSignOutController::class, 'store'])->name('logout');

        // Reachable even without a branch assigned - it's the escape valve
        // the branch-check middleware redirects to, so it must sit outside
        // that middleware's group.
        Route::get('branch-pending', [BranchPendingController::class, 'show'])->name('branch-pending');

        Route::middleware('bookshop.staff.branch-check')->group(function () {
            Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
            // Route::resource('branches', BranchController::class); // superadmin only - Phase 3+
        });
    });
});

Route::prefix('bookshop/shop')->name('bookshop.shop.')->group(function () {
    Route::middleware('bookshop.guest:bookshop_customer')->group(function () {
        Route::get('register', [CustomerRegisterController::class, 'create'])->name('register');
        Route::post('register', [CustomerRegisterController::class, 'store']);
        Route::get('login', [CustomerSignInController::class, 'create'])->name('login');
        Route::post('login', [CustomerSignInController::class, 'store']);
    });

    Route::middleware('bookshop.auth:bookshop_customer')->group(function () {
        Route::get('home', [CustomerHomeController::class, 'index'])->name('home');
        Route::post('logout', [CustomerSignOutController::class, 'store'])->name('logout');
        // Route::get('orders', [OrderController::class, 'index'])->name('orders.index'); // Phase 4
    });

    // Public catalog browsing - no auth required to view books (Phase 3)
    // Route::get('/', [CatalogController::class, 'index'])->name('catalog');
});
