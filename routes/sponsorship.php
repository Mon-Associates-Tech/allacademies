<?php

use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\SponsorshipPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sponsorship Routes
|--------------------------------------------------------------------------
|
| Routes for the sponsorship system including programs, offers, bids,
| and contributions. Split between public and authenticated routes.
|
*/

// Public sponsorship routes (accessible to everyone)
Route::prefix('sponsorship')->name('sponsorship.')->group(function () {
    // Public listing of active programs
    Route::get('/programs', \App\Livewire\Sponsorship\PublicSponsorshipList::class)
        ->name('programs.index');

    // Public listing of sponsor offers
    Route::get('/offers', \App\Livewire\Sponsorship\PublicSponsorOfferList::class)
        ->name('offers.index');

    // View single program (public)
    Route::get('/programs/{program}', [SponsorshipController::class, 'show'])
        ->name('programs.show');

    // View single offer (public)
    Route::get('/offers/{offer}', [SponsorshipController::class, 'showOffer'])
        ->name('offers.show');

    // Contribute to a program (public - can be guest or authenticated)
    Route::get('/programs/{program}/contribute', [SponsorshipPaymentController::class, 'showContributeForm'])
        ->name('programs.contribute');

    Route::post('/programs/{program}/contribute', [SponsorshipPaymentController::class, 'initializeContribution'])
        ->name('programs.contribute.initialize');
});

// Payment callback (must be accessible without auth middleware)
Route::get('/sponsorship/payment/callback', [SponsorshipPaymentController::class, 'handleCallback'])
    ->name('payment.callback');

Route::get('/sponsorship/payment/success/{contribution}', [SponsorshipPaymentController::class, 'success'])
    ->name('payment.success');

// Authenticated sponsorship routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Benefactor Routes (for users creating sponsorship programs)
    Route::prefix('benefactor')->name('benefactor.')->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Sponsorship\BenefactorDashboard::class)
            ->name('dashboard');

        // Payment setup
        Route::get('/payment-setup', \App\Livewire\Sponsorship\BenefactorPaymentSetup::class)
            ->name('payment-setup');

        // Create program
        Route::get('/programs/create', \App\Livewire\Sponsorship\BenefactorProgramForm::class)
            ->name('programs.create');

        // Edit program
        Route::get('/programs/{program}/edit', \App\Livewire\Sponsorship\BenefactorProgramForm::class)
            ->name('programs.edit');

        // Program actions
        Route::post('/programs/{program}/submit', [SponsorshipController::class, 'submitForVerification'])
            ->name('programs.submit');

        Route::delete('/programs/{program}', [SponsorshipController::class, 'deleteProgram'])
            ->name('programs.delete');
    });

    // Sponsor Routes (for users offering sponsorships)
    Route::prefix('sponsor')->name('sponsor.')->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Sponsorship\SponsorDashboard::class)
            ->name('dashboard');

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

    // Reviewer Routes (for verifying programs)
    Route::prefix('reviewer')->name('reviewer.')->middleware('role:owner,reviewer')->group(function () {
        // Verification queue
        Route::get('/verification-queue', \App\Livewire\Sponsorship\VerificationQueue::class)
            ->name('verification.queue');

        // Verification actions
        Route::post('/programs/{program}/verify', [SponsorshipController::class, 'verifyProgram'])
            ->name('programs.verify');

        Route::post('/programs/{program}/reject', [SponsorshipController::class, 'rejectProgram'])
            ->name('programs.reject');
    });

    // General authenticated actions
    Route::prefix('sponsorship')->name('sponsorship.')->group(function () {
        // Submit a bid (benefactor applying to sponsor offer)
        Route::post('/offers/{offer}/bid', [SponsorshipController::class, 'submitBid'])
            ->name('offers.bid');

        // View my contributions
        Route::get('/my-contributions', [SponsorshipController::class, 'myContributions'])
            ->name('contributions.mine');

        // View contribution receipt
        Route::get('/contributions/{contribution}/receipt', [SponsorshipPaymentController::class, 'viewReceipt'])
            ->name('contributions.receipt');
    });
});
