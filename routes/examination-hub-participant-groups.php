<?php

/**
 * Participant Groups Routes for Examination Hub
 * Add these routes to your routes/web.php file or examination hub routes file
 */

use App\ExaminationHub\Controllers\ParticipantGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('examinations')->name('examination-hub.')->group(function () {

    Route::prefix('participant-groups')->name('participant-groups.')->group(function () {
        // List all groups
        Route::get('/', [ParticipantGroupController::class, 'index'])->name('index');

        // Create group
        Route::get('/create', [ParticipantGroupController::class, 'create'])->name('create');
        Route::post('/', [ParticipantGroupController::class, 'store'])->name('store');

        // Import from CSV
        Route::get('/import', [ParticipantGroupController::class, 'importCsv'])->name('import');
        Route::post('/import', [ParticipantGroupController::class, 'processImport'])->name('import.process');

        // View, edit, delete group
        Route::get('/{group}', [ParticipantGroupController::class, 'show'])->name('show');
        Route::get('/{group}/edit', [ParticipantGroupController::class, 'edit'])->name('edit');
        Route::put('/{group}', [ParticipantGroupController::class, 'update'])->name('update');
        Route::delete('/{group}', [ParticipantGroupController::class, 'destroy'])->name('destroy');

        // Member management
        Route::post('/{group}/members', [ParticipantGroupController::class, 'storeMember'])->name('members.store');
        Route::put('/{group}/members/{member}', [ParticipantGroupController::class, 'updateMember'])->name('members.update');
        Route::delete('/{group}/members/{member}', [ParticipantGroupController::class, 'destroyMember'])->name('members.destroy');
    });

});
