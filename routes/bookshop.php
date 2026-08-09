<?php

use App\BookShop\Http\Controllers\Customer\Auth\CustomerRegisterController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignInController;
use App\BookShop\Http\Controllers\Customer\Auth\CustomerSignOutController;
use App\BookShop\Http\Controllers\Customer\BookShowController;
use App\BookShop\Http\Controllers\Customer\BranchSwitchController;
use App\BookShop\Http\Controllers\Customer\BulkOrderController as CustomerBulkOrderController;
use App\BookShop\Http\Controllers\Customer\CartController;
use App\BookShop\Http\Controllers\Customer\CatalogController;
use App\BookShop\Http\Controllers\Customer\CustomerHomeController;
use App\BookShop\Http\Controllers\Customer\NotificationController as CustomerNotificationController;
use App\BookShop\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\BookShop\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\BookShop\Models\Notification as BookShopNotification;
use App\BookShop\Http\Controllers\Staff\Auth\ChangePasswordController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignInController;
use App\BookShop\Http\Controllers\Staff\Auth\StaffSignOutController;
use App\BookShop\Http\Controllers\Staff\BookController;
use App\BookShop\Http\Controllers\Staff\BranchController;
use App\BookShop\Http\Controllers\Staff\BranchPaymentController;
use App\BookShop\Http\Controllers\Staff\BranchPendingController;
use App\BookShop\Http\Controllers\Staff\BulkOrderController as StaffBulkOrderController;
use App\BookShop\Http\Controllers\Staff\CategoryController;
use App\BookShop\Http\Controllers\Staff\CustomerController as StaffCustomerController;
use App\BookShop\Http\Controllers\Staff\NotificationController as StaffNotificationController;
use App\BookShop\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\BookShop\Http\Controllers\Staff\ReportController;
use App\BookShop\Http\Controllers\Staff\RestockRequestController;
use App\BookShop\Http\Controllers\Staff\StaffController;
use App\BookShop\Http\Controllers\Staff\StaffDashboardController;
use App\BookShop\Http\Controllers\Staff\StockController;
use App\BookShop\Http\Controllers\Staff\WarehouseController;
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
| Wrapped in the 'web' middleware group explicitly: routes loaded via
| loadRoutesFrom() from a service provider do NOT automatically get the
| 'web' group the way routes/web.php does in bootstrap/app.php. Without
| this, there's no session, no CSRF verification, and no $errors sharing
| on any of these routes - guards that rely on sessions wouldn't work
| at all.
*/

Route::middleware('web')->group(function () {
   Route::bind('alerts', function ($value) {
       return BookShopNotification::query()->findOrFail($value);
   });
 
Route::prefix('bookshop/staff')->name('bookshop.staff.')->group(function () {
    Route::middleware('bookshop.guest:bookshop_staff')->group(function () {
        Route::get('login', [StaffSignInController::class, 'create'])->name('login');
        Route::post('login', [StaffSignInController::class, 'store']);
    });

    Route::middleware('bookshop.auth:bookshop_staff')->group(function () {
        Route::post('logout', [StaffSignOutController::class, 'store'])->name('logout');

        // Reachable even with a forced password change pending - it's the
        // escape valve the password-check middleware redirects to, so it
        // must sit outside that middleware's group, same reasoning as
        // branch-pending below.
        Route::get('password/change', [ChangePasswordController::class, 'show'])->name('password.change');
        Route::post('password/change', [ChangePasswordController::class, 'update'])->name('password.update');

        Route::middleware('bookshop.staff.password-check')->group(function () {
            // Reachable even without a branch assigned - it's the escape valve
            // the branch-check middleware redirects to, so it must sit outside
            // that middleware's group.
            Route::get('branch-pending', [BranchPendingController::class, 'show'])->name('branch-pending');

            Route::middleware('bookshop.staff.branch-check')->group(function () {
            Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

            // Visible to both roles: a branch admin needs to browse the
            // catalog, see their own stock, and manage their own orders,
            // just not edit the catalog or see other branches.
            Route::get('books/search', [BookController::class, 'search'])->name('books.search');
            Route::get('books', [BookController::class, 'index'])->name('books.index');
            Route::get('stock', [StockController::class, 'index'])->name('stock.index');

            Route::get('orders', [StaffOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::get('orders/{order}/packing-slip', [StaffOrderController::class, 'packingSlip'])->name('orders.packing-slip');

            Route::get('notifications', [StaffNotificationController::class, 'index'])->name('notifications.index');
            Route::patch('notifications/mark-all-read', [StaffNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
            Route::get('notifications/{alerts}/open', [StaffNotificationController::class, 'open'])->name('notifications.open');

            // Index: both roles. Create/store: branch admins only (enforced
            // in the controller, since a superadmin has no branch of their
            // own to request stock for). Approve/reject: superadmin-only
            // middleware below.
            Route::get('restock-requests', [RestockRequestController::class, 'index'])->name('restock-requests.index');
            Route::get('restock-requests/create', [RestockRequestController::class, 'create'])->name('restock-requests.create');
            Route::post('restock-requests', [RestockRequestController::class, 'store'])->name('restock-requests.store');
            Route::get('restock-requests/{batchId}', [RestockRequestController::class, 'show'])->name('restock-requests.show');
            Route::patch('restock-requests/{restockRequest}/deliver', [RestockRequestController::class, 'markDelivered'])->name('restock-requests.deliver');
            Route::patch('restock-requests/{restockRequest}/confirm', [RestockRequestController::class, 'confirm'])->name('restock-requests.confirm');

            Route::get('customers', [StaffCustomerController::class, 'index'])->name('customers.index');
            Route::post('customers/send-email', [StaffCustomerController::class, 'sendEmail'])->name('customers.send-email');

            // Visible to both roles, branch-scoped like Orders (not
            // superadmin-only like restock approval - quoting is a
            // per-branch operational call, not a shared-warehouse one).
            Route::get('bulk-orders', [StaffBulkOrderController::class, 'index'])->name('bulk-orders.index');
            Route::get('bulk-orders/{bulkOrderRequest}', [StaffBulkOrderController::class, 'show'])->name('bulk-orders.show');
            Route::patch('bulk-orders/{bulkOrderRequest}/quote', [StaffBulkOrderController::class, 'quote'])->name('bulk-orders.quote');
            Route::patch('bulk-orders/{bulkOrderRequest}/reject', [StaffBulkOrderController::class, 'reject'])->name('bulk-orders.reject');

            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

            Route::middleware('bookshop.staff.superadmin-only')->group(function () {
                Route::get('team', [StaffController::class, 'index'])->name('team.index');
                Route::get('team/create', [StaffController::class, 'create'])->name('team.create');
                Route::post('team', [StaffController::class, 'store'])->name('team.store');
                Route::get('team/{staffMember}/edit', [StaffController::class, 'edit'])->name('team.edit');
                Route::put('team/{staffMember}', [StaffController::class, 'update'])->name('team.update');
                Route::patch('team/{staffMember}/toggle-active', [StaffController::class, 'toggleActive'])->name('team.toggle-active');

                Route::patch('restock-requests/{restockRequest}/approve', [RestockRequestController::class, 'approve'])->name('restock-requests.approve');
                Route::patch('restock-requests/{restockRequest}/reject', [RestockRequestController::class, 'reject'])->name('restock-requests.reject');
                Route::patch('restock-requests/{batchId}/approve-all', [RestockRequestController::class, 'approveAll'])->name('restock-requests.approve-all');
                Route::patch('restock-requests/{batchId}/reject-all', [RestockRequestController::class, 'rejectAll'])->name('restock-requests.reject-all');
                Route::patch('restock-requests/{restockRequest}/dispatch', [RestockRequestController::class, 'dispatchRequest'])->name('restock-requests.dispatch');

                Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
                Route::post('warehouse', [WarehouseController::class, 'store'])->name('warehouse.store');

                Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
                Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
                Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
                Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
                Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
                Route::patch('branches/{branch}/toggle-active', [BranchController::class, 'toggleActive'])->name('branches.toggle-active');

                Route::get('branches/{branch}/payment', [BranchPaymentController::class, 'edit'])->name('branches.payment.edit');
                Route::put('branches/{branch}/payment', [BranchPaymentController::class, 'update'])->name('branches.payment.update');
                Route::patch('branches/{branch}/payment/deactivate', [BranchPaymentController::class, 'deactivate'])->name('branches.payment.deactivate');

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
        }); // end bookshop.staff.password-check
    });
});

Route::prefix('bookshop/shop')->name('bookshop.shop.')->group(function () {
    Route::middleware('bookshop.guest:bookshop_customer')->group(function () {
        Route::get('register', [CustomerRegisterController::class, 'create'])->name('register');
        Route::post('register', [CustomerRegisterController::class, 'store']);
        Route::get('login', [CustomerSignInController::class, 'create'])->name('login');
        Route::post('login', [CustomerSignInController::class, 'store']);
    });

    // Public - no account required. A guest can browse the catalog, view
    // book detail pages, pick a branch, and build up a cart entirely
    // anonymously. Auth::guard('bookshop_customer')->user() returns null
    // for guests throughout these controllers, which is handled
    // explicitly rather than assumed away.
    Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');
    Route::get('books/{book}', [BookShowController::class, 'show'])->name('books.show');

    Route::get('branches', [BranchSwitchController::class, 'index'])->name('branches.index');
    Route::post('branches/{branch}/switch', [BranchSwitchController::class, 'switch'])->name('branches.switch');

    Route::get('cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('cart', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{book}', [CartController::class, 'remove'])->name('cart.remove');
    // Also public - checkout() checks auth internally and redirects a
    // guest to registration rather than relying on route middleware (see
    // the docblock on CartController for why: POST + intended-URL
    // redirects after login don't resubmit the original POST).
    Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    Route::middleware('bookshop.auth:bookshop_customer')->group(function () {
        Route::get('home', [CustomerHomeController::class, 'index'])->name('home');
        Route::post('logout', [CustomerSignOutController::class, 'store'])->name('logout');

        Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/receipt', [CustomerOrderController::class, 'receipt'])->name('orders.receipt');

        Route::get('payments/callback', [CustomerPaymentController::class, 'callback'])->name('payments.callback');
        Route::get('payments/{order}/initialize', [CustomerPaymentController::class, 'initialize'])->name('payments.initialize');

        // All bulk-order routes require an account throughout (unlike the
        // regular catalog/cart above, which stay public) - see the
        // docblock on Customer\BulkOrderController for why. Literal
        // segments (catalog/add/review/submit/index) declared before the
        // {bulkOrderRequest} wildcard, or e.g. "catalog" would be
        // swallowed as an attempted bulkOrderRequest ID.
        Route::get('bulk-orders/catalog', [CustomerBulkOrderController::class, 'catalog'])->name('bulk-orders.catalog');
        Route::post('bulk-orders/add', [CustomerBulkOrderController::class, 'addItem'])->name('bulk-orders.add');
        Route::get('bulk-orders/review', [CustomerBulkOrderController::class, 'review'])->name('bulk-orders.review');
        Route::put('bulk-orders/review', [CustomerBulkOrderController::class, 'updateItems'])->name('bulk-orders.update');
        Route::delete('bulk-orders/remove/{book}', [CustomerBulkOrderController::class, 'removeItem'])->name('bulk-orders.remove');
        Route::post('bulk-orders/submit', [CustomerBulkOrderController::class, 'submit'])->name('bulk-orders.submit');
        Route::get('bulk-orders', [CustomerBulkOrderController::class, 'index'])->name('bulk-orders.index');
        Route::get('bulk-orders/{bulkOrderRequest}', [CustomerBulkOrderController::class, 'show'])->name('bulk-orders.show');
        Route::post('bulk-orders/{bulkOrderRequest}/accept', [CustomerBulkOrderController::class, 'acceptQuote'])->name('bulk-orders.accept');
        Route::post('bulk-orders/{bulkOrderRequest}/cancel', [CustomerBulkOrderController::class, 'cancel'])->name('bulk-orders.cancel');

        Route::get('notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/mark-all-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('notifications/{alerts}/open', [CustomerNotificationController::class, 'open'])->name('notifications.open');
    });
});

}); // end Route::middleware('web')
