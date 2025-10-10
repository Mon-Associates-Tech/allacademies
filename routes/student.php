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

Route::middleware(['auth'])->prefix('dashboard/students')->name('students.')->group(function () {

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



        Route::get('/students', [StudentManagementController::class, 'index'])->name('index');
        Route::get('{student}', [StudentManagementController::class, 'show'])->name('show');
        Route::post('/{student}/promote', [StudentManagementController::class, 'promote'])->name('promote');
        Route::post('{student}/generate-report-card', [StudentManagementController::class, 'generateReportCard'])->name('generate-report-card');
        Route::post('/{student}/generate-id-card', [StudentManagementController::class, 'generateIdCard'])->name('generate-id-card');
        Route::get('/{student}/print-id-card', [StudentManagementController::class, 'printIdCard'])->name('print-id-card');
        Route::get('/report-cards/{reportCard}/print', [StudentManagementController::class, 'printReportCard'])->name('print-report-card');
        Route::post('/import', [StudentManagementController::class, 'import'])->name('import');
//        Route::post('students/', [StudentManagementController::class, 'import'])->name('import');

    Route::get('/messages/compose', ComposeMessage::class)->name('messages.compose');
    Route::get('/messages', MessageIndex::class)->name('messages.index');
    Route::get('/messages/{message}', MessageShow::class)->name('messages.show');

});

// entities should be given our academic structure to correctly map the students, teachers, librarians, etc to their respective roles and permissions within the system.
// For example, a student should be linked to their academic level, group, and school to ensure they have access to the appropriate resources and functionalities.
// Similarly, teachers should be associated with the subjects they teach and the academic levels they are responsible for.
// This structure helps maintain a clear hierarchy and ensures that users can only access information and perform actions relevant to their roles.
// By implementing this structure, we can create a more organized and efficient system that caters to the specific needs of each user type.
