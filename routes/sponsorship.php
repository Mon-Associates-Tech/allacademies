<?php

use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\SponsorshipPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sponsorship Routes
|--------------------------------------------------------------------------
|
| Routes for the sponsorships system including projects, offers, bids,
| and contributions. Split between public and authenticated routes.
|
*/

// Public sponsorships routes (accessible to everyone)
Route::prefix('sponsorship/projects')->name('sponsorships.')->group(function () {
    // Public listing of active projects
    Route::get('/', \App\Livewire\Sponsorship\PublicSponsorshipList::class)
        ->name('projects.index');

    // Public listing of sponsor offers
    Route::get('/offers', \App\Livewire\Sponsorship\PublicSponsorOfferList::class)
        ->name('offers.index');

    // View single project (public)
    Route::get('/{project}', [SponsorshipController::class, 'show'])
        ->name('projects.show');

    // View single offer (public)
    Route::get('/offers/{offer}', [SponsorshipController::class, 'showOffer'])
        ->name('offers.show');

    // Contribute to a project (public - can be guest or authenticated)
    Route::get('/{project}/contribute', [SponsorshipPaymentController::class, 'showContributeForm'])
        ->name('projects.contribute');

    Route::post('/{project}/contribute', [SponsorshipPaymentController::class, 'initializeContribution'])
        ->name('projects.contribute.initialize');

    Route::get('sponsorships/payment/callback', [SponsorshipPaymentController::class, 'handleCallback'])
        ->name('payment.callback');

    Route::get('/payment/success/{contribution}', [SponsorshipPaymentController::class, 'success'])
        ->name('payment.success');
});

// Payment callback (must be accessible without auth middleware)


// Authenticated sponsorships routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Benefactor Routes (for users creating sponsorships projects)
    Route::prefix('dashboard/benefactors')->name('benefactors.')->group(function () {
        // Dashboard
        Route::get('/', \App\Livewire\Sponsorship\BenefactorDashboard::class)
            ->name('index');

        // Payment setup
        Route::get('/payment-setup', \App\Livewire\Sponsorship\BenefactorPaymentSetup::class)
            ->name('payment-setup');

        // Create project
        Route::get('/projects/create', \App\Livewire\Sponsorship\BenefactorProjectForm::class)
            ->name('projects.create');

        // Edit project
        Route::get('/projects/{project}/edit', \App\Livewire\Sponsorship\BenefactorProjectForm::class)
            ->name('projects.edit');

        // project actions
        Route::post('/projects/{project}/submit', [SponsorshipController::class, 'submitForVerification'])
            ->name('projects.submit');

        Route::delete('/projects/{project}', [SponsorshipController::class, 'deleteProject'])
            ->name('projects.delete');
    });

    // Sponsor Routes (for users offering sponsorships)
    Route::prefix('dashboard/sponsorships')->name('sponsorships.')->group(function () {
        // Dashboard
        Route::get('/', \App\Livewire\Sponsorship\SponsorDashboard::class)
            ->name('index');

        // Create offer
        Route::get('/offers/create', \App\Livewire\Sponsorship\SponsorOfferForm::class)
            ->name('offers.create');

        // Edit offer
        Route::get('/offers/{offer}/edit', \App\Livewire\Sponsorship\SponsorOfferForm::class)
            ->name('offers.edit');

        // Offer actions
        Route::post('/offers/{offer}/close', [SponsorshipController::class, 'closeOffer'])
            ->name('offers.close');

        Route::delete('/offers/{offer}', [SponsorshipController::class, 'deleteOffer'])
            ->name('offers.delete');

        // Bid actions
        Route::post('/bids/{bid}/accept', [SponsorshipController::class, 'acceptBid'])
            ->name('bids.accept');

        Route::post('/bids/{bid}/reject', [SponsorshipController::class, 'rejectBid'])
            ->name('bids.reject');
    });

    // Reviewer Routes (for verifying projects)
    Route::prefix('reviewer')->name('reviewer.')->middleware('role:owner,reviewer')->group(function () {
        // Verification queue
        Route::get('/verification-queue', \App\Livewire\Sponsorship\VerificationQueue::class)
            ->name('verification.queue');

        // Verification actions
        Route::post('/projects/{project}/verify', [SponsorshipController::class, 'verifyproject'])
            ->name('projects.verify');

        Route::post('/projects/{project}/reject', [SponsorshipController::class, 'rejectproject'])
            ->name('projects.reject');
    });

    // General authenticated actions
    Route::prefix('sponsorships')->name('sponsorships.')->group(function () {
        // Submit a bid (benefactor applying to sponsor offer)
        Route::post('/offers/{offer}/bid', [SponsorshipController::class, 'submitBid'])
            ->name('offers.bid');

        // View my contributions
        Route::get('/my-contributions', [SponsorshipController::class, 'myContributions'])
            ->name('contributions.mine');

        // View contribution receipt
        Route::get('/contributions/{contribution}/receipt', [SponsorshipPaymentController::class, 'viewReceipt'])
            ->name('contributions.receipt');

        // Retry pending payment
        Route::post('/contributions/{contribution}/retry', [SponsorshipPaymentController::class, 'retryPayment'])
            ->name('contributions.retry');
    });
});
