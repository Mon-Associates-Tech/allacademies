<?php


use App\Livewire\Forums\ForumManagement;
use App\Livewire\Guests\Analytics;
use App\Livewire\Guests\Assessments;
use App\Livewire\Guests\Library;
use App\Livewire\Guests\Premium;
use App\Livewire\Guests\Progress;
use App\Livewire\Guests\Quizzes;
use App\Livewire\Guests\StudyGroups;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('')->group(function () {

// Subscriber Routes
    Route::prefix('guest')->name('guests.')->group(function () {
        Route::get('/library', Library::class)->name('library');
        Route::get('/assessments', Assessments::class)->name('assessments');
        Route::get('/quizzes', Quizzes::class)->name('quizzes');
        Route::get('/progress', Progress::class)->name('progress');
        Route::get('/forums', ForumManagement::class)->name('forums')->middleware('token.subscription');
        Route::get('/groups', StudyGroups::class)->name('groups');
        Route::get('/premium', Premium::class)->name('premium');
        Route::get('/analytics', Analytics::class)->name('analytics');
//        Route::get('courses', Courses::class)->name('courses');
    });
});
