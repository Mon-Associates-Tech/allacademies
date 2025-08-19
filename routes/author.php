<?php

use App\Http\Controllers\BookController;
use App\Livewire\Authors\AuthorProfile;
use App\Livewire\Authors\BookAnalytics;
use App\Livewire\Authors\BookBorrowings;
use App\Livewire\Authors\BookCategories;
use App\Livewire\Authors\BookDetails;
use App\Livewire\Authors\Books;
use App\Livewire\Authors\Community;
use App\Livewire\Authors\Help;
use App\Livewire\Authors\Notifications;
use App\Livewire\Authors\Profile;
use App\Livewire\Authors\Promotions;
use App\Livewire\Authors\Publishing;
use App\Livewire\Authors\Revenue;
use App\Livewire\Authors\Reviews;
use App\Livewire\Authors\Settings;
use App\Livewire\Authors\Subscriptions;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard/author')->name('author.')->group(function () {

    // Dashboard
//    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Book Management
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', Books::class)->name('index');
//        Route::get('/create', BookCreate::class)->name('create');
//        Route::get('/create', App\Livewire\Books\CreateBook::class)->name('create');
//        Route::get('/{book}/edit', BookCreate::class)->name('edit');
        Route::get('/{book}', BookDetails::class)->name('show');
        Route::get('/create', [BookController::class, 'create'])->name('create');
        Route::get('/{book}/edit', [BookController::class, 'edit'])->name('edit');

    });

    // Subscribers Management
    Route::prefix('subscribers')->name('subscribers.')->group(function () {
        Route::get('', Subscriptions::class)->name('index');
//        Route::get('/{subscription}', [Subscriptions::class, 'show'])->name('show');
//        Route::get('/book/{book}', [Subscriptions::class, 'byBook'])->name('by-book');
    });

    // Book Borrowings
    Route::prefix('borrowings')->name('borrowings.')->group(function () {
        Route::get('/', BookBorrowings::class)->name('index');
//        Route::get('/{borrowing}', [BookBorrowings::class, 'show'])->name('show');
//        Route::get('/book/{book}', [BookBorrowings::class, 'byBook'])->name('by-book');
//        Route::get('/overdue', [BookBorrowings::class, 'overdue'])->name('overdue');
    });

    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', BookAnalytics::class)->name('index');
        Route::get('/book/{book}', [BookAnalytics::class, 'bookAnalytics'])->name('book');
        Route::get('/downloads', [BookAnalytics::class, 'downloads'])->name('downloads');
        Route::get('/readership', [BookAnalytics::class, 'readership'])->name('readership');
        Route::get('/engagement', [BookAnalytics::class, 'engagement'])->name('engagement');
        Route::get('/export/{type}', [BookAnalytics::class, 'export'])->name('export');
    });

    // Revenue & Earnings
    Route::prefix('revenue')->name('revenue.')->group(function () {
        Route::get('/', Revenue::class)->name('index');
//        Route::get('/monthly', [Revenue::class, 'monthly'])->name('monthly');
//        Route::get('/yearly', [Revenue::class, 'yearly'])->name('yearly');
//        Route::get('/book/{book}', [Revenue::class, 'bookRevenue'])->name('book');
//        Route::get('/payouts', [Revenue::class, 'payouts'])->name('payouts');
//        Route::get('/statements', [Revenue::class, 'statements'])->name('statements');
//        Route::get('/export/{period}', [Revenue::class, 'export'])->name('export');
    });

    // Reviews & Feedback
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', Reviews::class)->name('index');
//        Route::get('/{review}', [Reviews::class, 'show'])->name('show');
//        Route::get('/book/{book}', [Reviews::class, 'byBook'])->name('by-book');
//        Route::post('/{review}/respond', [Reviews::class, 'respond'])->name('respond');
//        Route::get('/ratings/summary', [Reviews::class, 'ratingsSummary'])->name('ratings-summary');
    });

    // Book Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', BookCategories::class)->name('index');
//        Route::get('/{category}', [BookCategories::class, 'show'])->name('show');
//        Route::get('/{category}/books', [BookCategories::class, 'books'])->name('books');
    });

    // Publishing Status & Management
    Route::prefix('publishing')->name('publishing.')->group(function () {
        Route::get('/', Publishing::class)->name('index');
//        Route::get('/drafts', [Publishing::class, 'drafts'])->name('drafts');
//        Route::get('/pending', [Publishing::class, 'pending'])->name('pending');
//        Route::get('/published', [Publishing::class, 'published'])->name('published');
//        Route::get('/rejected', [Publishing::class, 'rejected'])->name('rejected');
//        Route::post('/book/{book}/submit', [Publishing::class, 'submit'])->name('submit');
//        Route::post('/book/{book}/withdraw', [Publishing::class, 'withdraw'])->name('withdraw');
//        Route::post('/book/{book}/republish', [Publishing::class, 'republish'])->name('republish');
    });

    // Promotions & Marketing
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', Promotions::class)->name('index');
//        Route::get('/create', [Promotions::class, 'create'])->name('create');
//        Route::get('/{promotion}', [Promotions::class, 'show'])->name('show');
//        Route::get('/{promotion}/edit', [Promotions::class, 'edit'])->name('edit');
//        Route::get('/campaigns/active', [Promotions::class, 'active'])->name('active');
//        Route::get('/campaigns/completed', [Promotions::class, 'completed'])->name('completed');
//        Route::get('/discounts', [Promotions::class, 'discounts'])->name('discounts');
//        Route::post('/{promotion}/activate', [Promotions::class, 'activate'])->name('activate');
//        Route::post('/{promotion}/deactivate', [Promotions::class, 'deactivate'])->name('deactivate');
    });

    // Author Community
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', Community::class)->name('index');
//        Route::get('/forums', [Community::class, 'forums'])->name('forums');
//        Route::get('/forums/{forum}', [Community::class, 'forum'])->name('forum');
//        Route::get('/discussions', [Community::class, 'discussions'])->name('discussions');
//        Route::get('/discussions/{discussion}', [Community::class, 'discussion'])->name('discussion');
//        Route::get('/events', [Community::class, 'events'])->name('events');
//        Route::get('/events/{event}', [Community::class, 'event'])->name('event');
//        Route::get('/authors', [Community::class, 'authors'])->name('authors');
//        Route::get('/authors/{author}', [Community::class, 'author'])->name('author');
//        Route::post('/follow/{author}', [Community::class, 'follow'])->name('follow');
//        Route::post('/unfollow/{author}', [Community::class, 'unfollow'])->name('unfollow');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', Notifications::class)->name('index');
//        Route::get('/{notification}', [Notifications::class, 'show'])->name('show');
//        Route::post('/{notification}/mark-read', [Notifications::class, 'markRead'])->name('mark-read');
//        Route::post('/mark-all-read', [Notifications::class, 'markAllRead'])->name('mark-all-read');
//        Route::get('/settings', [Notifications::class, 'settings'])->name('settings');
//        Route::post('/settings', [Notifications::class, 'updateSettings'])->name('update-settings');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', Settings::class)->name('index');
//        Route::get('/account', [Settings::class, 'account'])->name('account');
//        Route::get('/privacy', [Settings::class, 'privacy'])->name('privacy');
//        Route::get('/notifications', [Settings::class, 'notifications'])->name('notifications');
//        Route::get('/publishing', [Settings::class, 'publishing'])->name('publishing');
//        Route::get('/payment', [Settings::class, 'payment'])->name('payment');
//        Route::get('/security', [Settings::class, 'security'])->name('security');
//        Route::post('/update', [Settings::class, 'update'])->name('update');
    });

    // Profile Management
    Route::prefix('author-profile')->name('profile.')->group(function () {
        Route::get('/', AuthorProfile::class)->name('show');
//        Route::get('/edit', [Profile::class, 'edit'])->name('edit');
//        Route::get('/public', [Profile::class, 'public'])->name('public');
//        Route::post('/update', [Profile::class, 'update'])->name('update');
//        Route::post('/upload-photo', [Profile::class, 'uploadPhoto'])->name('upload-photo');
//        Route::delete('/remove-photo', [Profile::class, 'removePhoto'])->name('remove-photo');
    });

    // Help & Support
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', Help::class)->name('index');
//        Route::get('/getting-started', [Help::class, 'gettingStarted'])->name('getting-started');
//        Route::get('/publishing-guide', [Help::class, 'publishingGuide'])->name('publishing-guide');
//        Route::get('/marketing-tips', [Help::class, 'marketingTips'])->name('marketing-tips');
//        Route::get('/faq', [Help::class, 'faq'])->name('faq');
//        Route::get('/contact', [Help::class, 'contact'])->name('contact');
//        Route::post('/contact', [Help::class, 'submitContact'])->name('submit-contact');
//        Route::get('/tutorials', [Help::class, 'tutorials'])->name('tutorials');
//        Route::get('/tutorials/{tutorial}', [Help::class, 'tutorial'])->name('tutorial');
//        Route::get('/api-docs', [Help::class, 'apiDocs'])->name('api-docs');
    });
});
