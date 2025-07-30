<?php

use App\Livewire\Administrators\AuthorManagement;
use App\Livewire\Administrators\BookApprovalManagement;
use App\Livewire\Administrators\BookManagement;
use App\Livewire\Administrators\GroupManagement;
use App\Livewire\Administrators\LibrarianManagement;
use App\Livewire\Administrators\ParentManagement;
use App\Livewire\Administrators\StudentManagement;
use App\Livewire\Administrators\SubjectManagement;
use App\Livewire\Administrators\TeacherManagement;
use App\Livewire\Administrators\UserImpersonation;
use App\Livewire\Administrators\UserLoginLog;

Route::middleware(['auth', 'verified'])->prefix('')->name('admin.')->group(function () {
    Route::get('/student-management', StudentManagement::class)->name('student-management');
    Route::get('/student-groups', GroupManagement::class)->name('student-groups');
    Route::get('/teacher-management', TeacherManagement::class)->name('teacher-management');
    Route::get('/book-management', BookManagement::class)->name('book-management');
    Route::get('/book-management/create', App\Livewire\Books\CreateBook::class)->name('create-book');
    Route::get('/book-approvals', BookApprovalManagement::class)->name('book-approvals');
    Route::get('/librarian-management', LibrarianManagement::class)->name('librarian-management');
    Route::get('/logins', UserLoginLog::class)->name('logins');
    Route::get('/author-management', AuthorManagement::class)->name('author-management');
    Route::get('/subject-management', SubjectManagement::class)->name('subject-management');
    Route::get('/parent-management', ParentManagement::class)->name('parent-management');
    Route::get('/impersonate', UserImpersonation::class)->name('users.impersonate');
});
