<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\Student\StudentManagementController;
use App\Livewire\Authors\BookDetails;
use App\Livewire\Learning\BookQuizInterface;
use App\Livewire\Students\ActivityLogs;
use App\Livewire\Students\Assignments;
use App\Livewire\Students\AssignmentTakingComponent;
use App\Livewire\Students\Books;
use App\Livewire\Students\CourseDetails;
use App\Livewire\Students\Courses;
use App\Livewire\Students\Messages\ComposeMessage;
use App\Livewire\Students\Messages\MessageIndex;
use App\Livewire\Students\Messages\MessageShow;
use App\Livewire\Students\PerformanceOverview;
use App\Livewire\Students\StudentProfile;
use App\Livewire\Students\StudentSchedule;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard/students')->name('students.')->group(function () {


    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StudentFeeController::class, 'index'])->name('index');
        Route::get('/payment', [\App\Http\Controllers\StudentFeeController::class, 'payment'])->name('payment');
        Route::post('/initialize', [\App\Http\Controllers\StudentFeeController::class, 'initializePayment'])->name('initialize');
        Route::get('/callback', [\App\Http\Controllers\StudentFeeController::class, 'callback'])->name('callback');
        Route::get('/receipt/{payment}', [\App\Http\Controllers\StudentFeeController::class, 'receipt'])->name('receipt');
    });

    // Alias route for convenience
    Route::get('payments', [\App\Http\Controllers\StudentFeeController::class, 'index'])->name('payments.index');

    Route::get('assessments', BookQuizInterface::class)->name('assessments');
    Route::get('performance', PerformanceOverview::class)->name('performance');
    Route::get('account', StudentProfile::class)->name('account');
    Route::get('activities', ActivityLogs::class)->name('activities');
    Route::get('schedules', StudentSchedule::class)->name('schedules');
    Route::get('courses', Courses::class)->name('courses');
    Route::get('courses/{courseId}', CourseDetails::class)->name('course.details');
    Route::get('books', [BookController::class, 'index'])->name('books');
    Route::get('books/{book}', BookDetails::class)->name('books.show');
    Route::get('lessons', Books::class)->name('lessons');
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

    Route::get('assignments/{assignment}/take', AssignmentTakingComponent::class)
        ->name('assignment.take')
        ->middleware(['auth', 'role:student']);

    // Messages
    Route::get('/messages/compose', ComposeMessage::class)->name('messages.compose');
    Route::get('/messages', MessageIndex::class)->name('messages.index');
    Route::get('/messages/{message}', MessageShow::class)->name('messages.show');


    Route::get('/students', [StudentManagementController::class, 'index'])->name('index');
    Route::get('{student}', [StudentManagementController::class, 'show'])->name('show');
    Route::post('/{student}/promote', [StudentManagementController::class, 'promote'])->name('promote');
    Route::post('{student}/generate-report-card', [StudentManagementController::class, 'generateReportCard'])->name('generate-report-card');
    Route::post('/{student}/generate-id-card', [StudentManagementController::class, 'generateIdCard'])->name('generate-id-card');
    Route::get('/{student}/print-id-card', [StudentManagementController::class, 'printIdCard'])->name('print-id-card');
    Route::get('/report-cards/{reportCard}/print', [StudentManagementController::class, 'printReportCard'])->name('print-report-card');
    Route::post('/import', [StudentManagementController::class, 'import'])->name('import');
});
