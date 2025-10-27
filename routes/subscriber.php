<?php


use App\Livewire\Forums\ForumManagement;
use App\Livewire\Subscribers\Analytics;
use App\Livewire\Subscribers\Assessments;
use App\Livewire\Subscribers\Library;
use App\Livewire\Subscribers\Premium;
use App\Livewire\Subscribers\Progress;
use App\Livewire\Subscribers\Quizzes;
use App\Livewire\Subscribers\StudyGroups;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('')->group(function () {

// Subscriber Routes
    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/library', Library::class)->name('library');
        Route::get('/assessments', Assessments::class)->name('assessments');
        Route::get('/quizzes', Quizzes::class)->name('quizzes');
        Route::get('/progress', Progress::class)->name('progress');
        Route::get('/forums', ForumManagement::class)->name('forums');
        Route::get('/groups', StudyGroups::class)->name('groups');
        Route::get('/premium', Premium::class)->name('premium');
        Route::get('/analytics', Analytics::class)->name('analytics');
//        Route::get('courses', Courses::class)->name('courses');
    });
});
