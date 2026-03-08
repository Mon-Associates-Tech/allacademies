<?php

use App\Http\Controllers\ImportTemplateController;
use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| School Routes
|--------------------------------------------------------------------------
|
| Routes for school management including school creation, fee setup,
| settings, comprehensive view, and admin payment management.
|
*/

Route::middleware(['auth'])->group(function () {
    // School Creation Routes
    Route::prefix('schools')->group(function () {
        Route::get('/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/store', [SchoolController::class, 'store'])->name('schools.store');
        Route::post('/{school}/collect-fees', [SchoolController::class, 'collectFees'])->name('schools.collectFees');
    });

    // School Fee Setup Routes
    Route::get('/school/fee-setup', [SchoolController::class, 'showFeeSetupForm'])->name('school.fee-setup');
    Route::post('/school/fee-setup', [SchoolController::class, 'storeFeeStructure'])->name('school.fee-setup.store');

    // Academic Term Switch
    Route::post('/academic/term/switch', function (\Illuminate\Http\Request $request) {
        $termId = $request->input('term_id');
        \Illuminate\Support\Facades\DB::table('academic_periods')->update(['is_current' => false]);
        \Illuminate\Support\Facades\DB::table('academic_periods')->where('id', $termId)->update(['is_current' => true]);

        return back()->with('success', 'Current term has been updated successfully.');
    })->name('academic.term.switch');

    // School Comprehensive View (Verified Users Only)
    Route::middleware('verified')->group(function () {
        Route::get('school/comprehensive-view', \App\Livewire\School\ComprehensiveSchoolDashboard::class)->name('school.comprehensive-view');
        Route::get('school/import-formats', [ImportTemplateController::class, 'viewFormats'])->name('school.import-formats');
        Route::get('school/download-template/{type}', [ImportTemplateController::class, 'download'])->name('school.download-template');
    });

    // School Settings
    Route::get('/school-settings', \App\Livewire\School\SchoolSettingsDashboard::class)->name('school-settings.index');
    Route::get('/school-settings/fee-structure/setup', \App\Livewire\SchoolSettings\FeeStructureSetup::class)->name('school-settings.fee-structure.setup');

    // School Onboarding
    Route::get('onboarding/school-setup', \App\Livewire\SchoolOnboarding::class)->name('onboarding.school-setup');

    // Admin Payment Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('payments', App\Http\Controllers\Admin\SchoolPaymentController::class);
        Route::get('payments/export', [App\Http\Controllers\Admin\SchoolPaymentController::class, 'export'])->name('payments.export');
        Route::resource('school-payment-structures', App\Http\Controllers\Admin\SchoolPaymentStructureController::class);
    });
});
