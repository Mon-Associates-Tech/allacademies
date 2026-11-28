<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\Student\StudentManagementController;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\StudentFeeController;
use App\Livewire\Authors\BookDetails;
use App\Livewire\Common\UserPreferences;
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
use App\Livewire\Students\MyReportCards;
use App\Livewire\Students\PerformanceOverview;
use App\Livewire\Students\PublicProfile;
use App\Livewire\Students\StudentProfile;
use App\Livewire\Students\StudentSchedule;
use App\Livewire\Students\VirtualClassroom\MyVirtualSessions;
use App\Livewire\Students\VirtualClassroom\ViewSessionRecordings;
use App\Livewire\UnifiedNotifications;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard/students')->name('students.')->group(function () {

    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [StudentFeeController::class, 'index'])->name('index');
        Route::get('/payment', [StudentFeeController::class, 'payment'])->name('payment');
        Route::post('/initialize', [StudentFeeController::class, 'initializePayment'])->name('initialize');
        Route::get('/callback', [StudentFeeController::class, 'callback'])->name('callback');
        Route::get('/receipt/{payment}', [StudentFeeController::class, 'receipt'])->name('receipt');
        // Receipt for portal/one-off SchoolPayment entries
        Route::get('/receipt/payment/{payment}', [StudentFeeController::class, 'receiptPayment'])->name('receipt.payment');
    });

    // Alias route for convenience
    Route::get('payments', [StudentFeeController::class, 'index'])->name('payments.index');

    // Report Cards
    Route::get('/report-cards', MyReportCards::class)->name('report-cards');

    Route::get('assessments', BookQuizInterface::class)->name('assessments');
    Route::get('performance', PerformanceOverview::class)->name('performance');
    Route::get('account', StudentProfile::class)->name('account');
    Route::get('activities', ActivityLogs::class)->name('activities');
    Route::get('notifications', UnifiedNotifications::class)->name('notifications');
    Route::get('schedules', StudentSchedule::class)->name('schedules');
    Route::get('courses', Courses::class)->name('courses');
    Route::get('courses/{courseId}', CourseDetails::class)->name('course.details');
    Route::get('books', [BookController::class, 'index'])->name('books');
    Route::get('books/{book}', BookDetails::class)->name('books.show');
    Route::get('lessons', Courses::class)->name('lessons');
    Route::get('quizzes', BookQuizInterface::class)->name('quizzes');
    Route::get('library', Books::class)->name('library');
    Route::get('materials', Books::class)->name('materials');
    Route::get('schedule', StudentSchedule::class)->name('schedule');
    Route::get('assignments', Assignments::class)->name('assignments');
    Route::get('achievements', PerformanceOverview::class)->name('achievements');
    Route::get('teachers', [StudentPortalController::class, 'teachers'])->name('teachers');
    Route::get('profile', StudentProfile::class)->name('profile');
    Route::get('settings', UserPreferences::class)->name('settings');
    Route::view('help', 'students.help')->name('help');

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

    Route::prefix('classroom')->name('classroom.')->group(function () {
        Route::get('/sessions', MyVirtualSessions::class)->name('sessions');
        Route::get('/recordings', ViewSessionRecordings::class)->name('recordings');
    });
});

Route::get('students/{user}/profile', PublicProfile::class)->name('students.profile.public');
