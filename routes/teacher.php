<?php

use App\Livewire\Teachers\Activities;
use App\Livewire\Teachers\Assignments;
use App\Livewire\Teachers\Attendance\AttendanceHistory;
use App\Livewire\Teachers\Attendance\AttendanceList;
use App\Livewire\Teachers\Attendance\TakeAttendance;
use App\Livewire\Teachers\Schedules;
use App\Livewire\Teachers\StudentPerformances;
use App\Livewire\Teachers\Students;
use App\Livewire\Teachers\Subjects;
use App\Livewire\Teachers\TeacherNotifications;
use App\Livewire\Teachers\TeacherProfile;
use App\Livewire\Teachers\ViewAssignment;
use App\Livewire\Teachers\ViewAssignmentSubmission;
use App\Livewire\Teachers\VirtualClassroom;

Route::middleware(['auth'])->name('teachers.')->group(function () {
    Route::get('assignments', Assignments::class)->name('assignments.index');
    Route::get('assignments/{assignment}', ViewAssignment::class)->name('assignments.show');
    Route::get('/create-assignment', \App\Livewire\Teachers\CreateAssignment::class)->name('assignments.create');
    Route::get('/assignments/{assignment}/edit', App\Livewire\Teachers\EditAssignment::class)->name('assignments.edit');



    Route::get('students', Students::class)->name('students.index');
    Route::get('/students/{student}', \App\Livewire\Teachers\StudentDetails::class)
        ->name('student.details');
    Route::get('subjects', Subjects::class)->name('subjects.index');
    Route::get('performance', StudentPerformances::class)->name('performance');
    Route::get('account', TeacherProfile::class)->name('account');
    Route::get('activities', Activities::class)->name('activities');
//    Route::get('notifications', TeacherNotifications::class)->name('notifications.index');
    Route::get('schedules', Schedules::class)->name('schedules');
    Route::get('classroom', VirtualClassroom::class)->name('classroom');
    Route::get('submissions/{submission}', ViewAssignmentSubmission::class)
        ->name('submissions.view');


        Route::get('/attendance', AttendanceList::class)->name('attendance.index');
        Route::get('/attendance/take', TakeAttendance::class)->name('attendance.take');
        Route::get('/attendance/{attendance}/edit', TakeAttendance::class)->name('attendance.edit');
    Route::get('/attendance/{student}/history', AttendanceHistory::class)
        ->name('attendance.history');
});
