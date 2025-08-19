<?php

use App\Http\Controllers\BookController;
use App\Livewire\Assessment\QuizTakingPage;
use App\Livewire\Authors\BookDetails;
use App\Livewire\Students\ActivityLogs;
use App\Livewire\Students\Assignments;
use App\Livewire\Students\AssignmentTakingComponent;
use App\Livewire\Students\Books;
use App\Livewire\Students\CourseDetails;
use App\Livewire\Students\Courses;
use App\Livewire\Students\Notifications;
use App\Livewire\Students\PerformanceOverview;
use App\Livewire\Students\StudentProfile;
use App\Livewire\Students\StudentSchedule;

Route::middleware(['auth'])->prefix('dashboard')->name('student.')->group(function () {

Route::get('assessments', QuizTakingPage::class )->name('assessments');
Route::get('performance', PerformanceOverview::class)->name('performance');
Route::get('account', StudentProfile::class)->name('account');
Route::get('activities', ActivityLogs::class)->name('activities');
Route::get('schedules', StudentSchedule::class)->name('schedules');
Route::get('courses', Courses::class)->name('courses');
Route::get('courses/{courseId}', CourseDetails::class)->name('course.details');
Route::get('books', [BookController::class, 'index'])->name('books');
Route::get('books/{book}', BookDetails::class)->name('books.show');
Route::get('lessons', Books::class)->name('lessons');
//Route::get('self-assessments', Books::class)->name('self-assessment');
Route::get('quizzes', Books::class)->name('quizzes');
Route::get('library', Books::class)->name('library');
Route::get('materials', Books::class)->name('materials');
Route::get('schedule', Books::class)->name('schedule');
Route::get('assignments', Assignments::class)->name('assignments');
Route::get('achievements', Books::class)->name('achievements');
Route::get('messages', Books::class)->name('messages');
Route::get('teachers', Books::class)->name('teachers');
Route::get('profile', Books::class)->name('profile');
Route::get('settings', Books::class)->name('settings');
Route::get('help', Books::class)->name('help');
//Route::get('notifications', Notifications::class)->name('notifications.index');
    Route::get('assignments/{assignment}/take', AssignmentTakingComponent::class)
        ->name('assignment.take')
        ->middleware(['auth', 'role:student']);
});
