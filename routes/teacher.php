<?php

use App\Livewire\Students\VirtualClassroom\MyVirtualSessions;
use App\Livewire\Students\VirtualClassroom\ViewSessionRecordings;
use App\Livewire\Teachers\Activities;
use App\Livewire\Teachers\Assignments;
use App\Livewire\Teachers\Attendance\AttendanceHistory;
use App\Livewire\Teachers\Attendance\AttendanceList;
use App\Livewire\Teachers\Attendance\TakeAttendance;
use App\Livewire\Teachers\CreateAssignment;
use App\Livewire\Teachers\Messages\ComposeMessage;
use App\Livewire\Teachers\Messages\MessageIndex;
use App\Livewire\Teachers\Messages\MessageShow;
use App\Livewire\Teachers\Messages\SendMessageToStudents;
use App\Livewire\Teachers\Schedules;
use App\Livewire\Teachers\StudentDetails;
use App\Livewire\Teachers\StudentPerformances;
use App\Livewire\Teachers\Students;
use App\Livewire\Teachers\Subjects;
use App\Livewire\Teachers\TeacherProfile;
use App\Livewire\Teachers\ViewAssignment;
use App\Livewire\Teachers\ViewAssignmentSubmission;
use App\Livewire\Teachers\VirtualClassroom;
use App\Livewire\Teachers\VirtualClassroom\CreateVirtualSession;
use App\Livewire\Teachers\VirtualClassroom\EditSession;
use App\Livewire\Teachers\VirtualClassroom\SessionDetails;
use App\Livewire\Teachers\VirtualClassroom\SessionManager;
use App\Livewire\Teachers\VirtualClassroom\StartSession;
use App\Livewire\Teachers\VirtualClassroom\VirtualSessionRecordings;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->name('teachers.')->prefix('dashboard/teachers')->group(function () {
    Route::get('assignments', Assignments::class)->name('assignments.index');
    Route::get('assignments/{assignment}', ViewAssignment::class)->name('assignments.show');
    Route::get('/create-assignment', CreateAssignment::class)->name('assignments.create');
    Route::get('/assignments/{assignment}/edit', App\Livewire\Teachers\EditAssignment::class)->name('assignments.edit');


    Route::get('students', Students::class)->name('students.index');
    Route::get('/students/{student}', StudentDetails::class)
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


    // Message index - view all sent messages
    Route::get('/messages', MessageIndex::class)->name('messages.index');

    // Compose new message
    Route::get('messages/compose', ComposeMessage::class)->name('messages.compose');

    // View specific message
    Route::get('messages/{message}', MessageShow::class)->name('messages.show');

    // Send message to students (simplified interface)
    Route::get('messages/students/send', SendMessageToStudents::class)->name('messages.students.send');

    // New route for book-based assignments
    Route::get('/book-assignments/create', \App\Livewire\Teachers\BookBasedAssignment::class)->name('book-assignments.create');

    Route::prefix('classroom')->name('classroom.')->group(function () {
        Route::get('/', SessionManager::class)->name('index');
        Route::get('/create', CreateVirtualSession::class)->name('create');
        Route::get('/sessions/{session}', SessionDetails::class)->name('show');
        Route::get('/sessions/{session}/start', StartSession::class)->name('start');
        Route::get('/sessions/{session}/edit', EditSession::class)->name('edit');
        Route::get('/sessions/{session}/recordings', VirtualSessionRecordings::class)->name('recordings');
        Route::get('/sessions/{session}/participants', App\Livewire\Teachers\VirtualClassroom\VirtualSessionParticipants::class)->name('participants');
    });

});
