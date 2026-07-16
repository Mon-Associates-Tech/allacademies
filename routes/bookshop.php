<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BookShop Module Routes
|--------------------------------------------------------------------------
|
| Two entirely separate identity spaces, matching the two guards:
|   - staff.*    -> bookshop_staff guard (superadmin / branch admin)
|   - shop.*     -> bookshop_customer guard (customers ordering books)
|
| Auth controllers (login/register) for both guards land in Phase 2 —
| this file just establishes the route-group/prefix/name skeleton and
| middleware boundaries so nothing here needs to change shape later.
|
*/

Route::prefix('bookshop/staff')->name('bookshop.staff.')->group(function () {
    Route::middleware('guest:bookshop_staff')->group(function () {
        // Route::get('login', [StaffSignInController::class, 'create'])->name('login');
        // Route::post('login', [StaffSignInController::class, 'store']);
    });

    Route::middleware('auth:bookshop_staff')->group(function () {
        // Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        // Route::resource('branches', BranchController::class); // superadmin only
        // Route::post('logout', [StaffSignOutController::class, 'store'])->name('logout');
    });
});

Route::prefix('bookshop/shop')->name('bookshop.shop.')->group(function () {
    Route::middleware('guest:bookshop_customer')->group(function () {
        // Route::get('register', [CustomerRegisterController::class, 'create'])->name('register');
        // Route::post('register', [CustomerRegisterController::class, 'store']);
        // Route::get('login', [CustomerSignInController::class, 'create'])->name('login');
        // Route::post('login', [CustomerSignInController::class, 'store']);
    });

    Route::middleware('auth:bookshop_customer')->group(function () {
        // Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        // Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        // Route::post('logout', [CustomerSignOutController::class, 'store'])->name('logout');
    });

    // Public catalog browsing — no auth required to view books
    // Route::get('/', [CatalogController::class, 'index'])->name('catalog');
});
