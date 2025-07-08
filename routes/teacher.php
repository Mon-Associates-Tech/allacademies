<?php

use App\Livewire\Teachers\Activities;
use App\Livewire\Teachers\Assignments;
use App\Livewire\Teachers\Schedules;
use App\Livewire\Teachers\StudentPerformances;
use App\Livewire\Teachers\Students;
use App\Livewire\Teachers\Subjects;
use App\Livewire\Teachers\TeacherNotifications;
use App\Livewire\Teachers\TeacherProfile;
use App\Livewire\Teachers\ViewAssignment;
use App\Livewire\Teachers\VirtualClassroom;

Route::middleware(['auth'])->prefix('dashboard/teacher')->name('teacher.')->group(function () {
    Route::get('assignments', Assignments::class)->name('assignments.index');
    Route::get('assignments/{assignment}', ViewAssignment::class)->name('assignments.show');
    Route::get('students', Students::class)->name('students.index');
    Route::get('subjects', Subjects::class)->name('subjects.index');
    Route::get('performance', StudentPerformances::class)->name('performance');
    Route::get('account', TeacherProfile::class)->name('account');
    Route::get('activities', Activities::class)->name('activities');
    Route::get('notifications', TeacherNotifications::class)->name('notifications.index');
    Route::get('schedules', Schedules::class)->name('schedules');
    Route::get('classroom', VirtualClassroom::class)->name('classroom');
//    Route::get('books', Books::class)->name('books');
});
