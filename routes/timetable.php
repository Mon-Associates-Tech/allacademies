<?php
// routes/timetable.php

use App\Timetable\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('timetable')->name('timetable.')->group(function () {
    Route::get('/', [TimetableController::class, 'index'])->name('index');
    Route::get('/rooms', [TimetableController::class, 'rooms'])->name('rooms');
    Route::get('/time-slots', [TimetableController::class, 'timeSlots'])->name('time-slots');
});
