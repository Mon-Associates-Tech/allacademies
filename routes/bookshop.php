<?php

use App\BookShop\Http\Controllers\Customer\Auth\CustomerRegisterController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignInController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignOutController;
use App\BookShop\Http\Controllers\Customer\CatalogController;
use App\BookShop\Http\Controllers\Customer\CustomerHomeController;
use App\BookShop\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignInController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignOutController;
use App\BookShop\Http\Controllers\Staff\BookController;
use App\BookShop\Http\Controllers\Staff\BranchController;
use App\BookShop\Http\Controllers\Staff\BranchPendingController;
use App\BookShop\Http\Controllers\Staff\CategoryController;
use App\BookShop\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\BookShop\Http\Controllers\Staff\StaffDashboardController;
use App\BookShop\Http\Controllers\Staff\StockController;
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

            // Visible to both roles: a branch admin needs to browse the
            // catalog, see their own stock, and manage their own orders,
            // just not edit the catalog or see other branches.
            Route::get('books', [BookController::class, 'index'])->name('books.index');
            Route::get('stock', [StockController::class, 'index'])->name('stock.index');

            Route::get('orders', [StaffOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.update-status');

            Route::middleware('bookshop.staff.superadmin-only')->group(function () {
                Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
                Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
                Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
                Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
                Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
                Route::patch('branches/{branch}/toggle-active', [BranchController::class, 'toggleActive'])->name('branches.toggle-active');

                Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                Route::patch('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
                Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

                Route::get('books/create', [BookController::class, 'create'])->name('books.create');
                Route::post('books', [BookController::class, 'store'])->name('books.store');
                Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
                Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
                Route::patch('books/{book}/toggle-active', [BookController::class, 'toggleActive'])->name('books.toggle-active');

                Route::post('stock', [StockController::class, 'store'])->name('stock.store');
            });
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

        Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');

        Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [CustomerOrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    });
});
