<?php

use App\Http\Controllers\ActivityTrailController;
use App\Http\Controllers\Admin\SchoolPaymentController;
use App\Http\Controllers\BookController;
use App\Livewire\Administrators\AuthorManagement;
use App\Livewire\Administrators\BookApprovalManagement;
use App\Livewire\Administrators\BookManagement;
use App\Livewire\Administrators\LibrarianManagement;
use App\Livewire\Administrators\ParentManagement;
use App\Livewire\Administrators\SchoolSwitcherPage;
use App\Livewire\Administrators\StudentGroupManagement;
use App\Livewire\Administrators\StudentManagement;
use App\Livewire\Administrators\SubjectManagement;
use App\Livewire\Administrators\TeacherManagement;
use App\Livewire\Administrators\UserImpersonation;
use App\Livewire\Administrators\UserLoginLog;
use App\Livewire\Changelogs\ChangelogList;
use App\Livewire\Changelogs\CreateChangelog;
use App\Livewire\Common\ActivityLogManager;
use App\Livewire\Common\Messages\ComposeMessage;
use App\Livewire\Common\Messages\MessageEdit;
use App\Livewire\Common\Messages\MessageIndex;
use App\Livewire\Common\Messages\MessageShow;
use App\Livewire\School\SchoolDetails;
use App\Livewire\SchoolSettings\LetterheadSettings;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'school.scope'])->prefix('')->name('admin.')->group(function () {
    Route::get('/student-management', StudentManagement::class)->name('student-management');
    Route::get('/student-groups', StudentGroupManagement::class)->name('student-groups');
    Route::get('/teacher-management', TeacherManagement::class)->name('teacher-management');
    Route::get('/book-management', BookManagement::class)->name('book-management');
    Route::get('/book-management/create', [BookController::class, 'create'])->name('books.create');
    Route::get('/book-management/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::get('/book-approvals', BookApprovalManagement::class)->name('book-approvals');
    Route::get('/librarian-management', LibrarianManagement::class)->name('librarian-management');
    Route::get('/logins', UserLoginLog::class)->name('logins');
    Route::get('/author-management', AuthorManagement::class)->name('author-management');
    Route::get('/subject-management', SubjectManagement::class)->name('subject-management');
    Route::get('/parent-management', ParentManagement::class)->name('parent-management');
    Route::get('/impersonate', UserImpersonation::class)->name('users.impersonate');
    Route::get('datamanager', [\App\Http\Controllers\Student\StudentManagementController::class, 'index'])->name('data-manager');

    // Main activity trail page
    Route::get('/admin/activity-trail', [ActivityTrailController::class, 'index'])
        ->name('activity-trail.index');

    // Export activity data
    Route::post('/admin/activity-trail/export', [ActivityTrailController::class, 'export'])
        ->name('activity-trail.export');

    // Download exported file
    Route::get('/admin/activity-trail/download/{file}', [ActivityTrailController::class, 'download'])
        ->name('activity-trail.download');

    // Get activity statistics
    Route::get('/admin/activity-trail/stats', [ActivityTrailController::class, 'stats'])
        ->name('activity-trail.stats');

    // Get specific activity details
    Route::get('/admin/activity-trail/{activity}', [ActivityTrailController::class, 'show'])
        ->name('activity-trail.show');

    // Login Activity Details
    Route::get('/users/{user}/login-activities', [App\Http\Controllers\UserLoginActivityController::class, 'show'])
        ->name('login-activities.show');

    // Messages routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', MessageIndex::class)->name('index');
        Route::get('compose', ComposeMessage::class)->name('compose');
        Route::get('/{message}', MessageShow::class)->name('show');
        Route::get('/{message}/edit', MessageEdit::class)->name('edit');
    });

    Route::get('/academic-activities', ActivityLogManager::class)
        ->name('academic-activities')
        ->middleware('auth');

    Route::get('/admin/school-switcher', SchoolSwitcherPage::class)
        ->name('school-switcher');
    Route::get('/school/configuration', \App\Livewire\School\SchoolConfigurationManager::class)
        ->name('school.configuration');

    Route::get('/admin/schools/{schoolId}', SchoolDetails::class)
        ->name('school-details');

    Route::get('/change-log', CreateChangelog::class)->name('change-log');
    Route::get('/changelog', ChangelogList::class)->name('change-log.index');

    Route::get('students/{student}/documents', \App\Livewire\Students\DocumentGenerator::class)
        ->name('students.documents');

    Route::get('/school-settings/letterhead', LetterheadSettings::class)
        ->name('school-settings.letterhead');

    // School Payments/Transactions
    Route::get('/transactions', [SchoolPaymentController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{payment}', [SchoolPaymentController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/export', [SchoolPaymentController::class, 'export'])->name('transactions.export');
});
