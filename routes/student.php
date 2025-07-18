<?php

use App\Livewire\Students\ActivityLogs;
use App\Livewire\Students\Books;
use App\Livewire\Students\Courses;
use App\Livewire\Students\Notifications;
use App\Livewire\Students\PerformanceOverview;
use App\Livewire\Students\StudentProfile;
use App\Livewire\Students\StudentSchedule;

Route::middleware(['auth'])->prefix('dashboard')->name('student.')->group(function () {

Route::get('assessments', \App\Livewire\Students\SelfAssessment::class )->name('assessments');
//Route::get('assessments', \App\Livewire\Assessment\SubjectSelectionComponent::class )->name('assessments');
//Route::get('assessments', \App\Livewire\Assessment\QuestionGeneratorComponent::class )->name('assessments');
//Route::get('assessments', \App\Livewire\Assessment\UnifiedAssessmentComponent::class )->name('assessments');
Route::get('performance', PerformanceOverview::class)->name('performance');
Route::get('account', StudentProfile::class)->name('account');
Route::get('activities', ActivityLogs::class)->name('activities');
Route::get('schedules', StudentSchedule::class)->name('schedules');
Route::get('courses', Courses::class)->name('courses');
Route::get('books', Books::class)->name('books');
Route::get('books/{book}', \App\Livewire\Authors\BookDetails::class)->name('books.show');
Route::get('lessons', Books::class)->name('lessons');
Route::get('self-assessments', Books::class)->name('self-assessment');
Route::get('quizzes', Books::class)->name('quizzes');
Route::get('library', Books::class)->name('library');
Route::get('materials', Books::class)->name('materials');
Route::get('schedule', Books::class)->name('schedule');
Route::get('assignments', \App\Livewire\Students\Assignments::class)->name('assignments');
Route::get('achievements', Books::class)->name('achievements');
Route::get('messages', Books::class)->name('messages');
Route::get('teachers', Books::class)->name('teachers');
Route::get('profile', Books::class)->name('profile');
Route::get('settings', Books::class)->name('settings');
Route::get('help', Books::class)->name('help');
Route::get('notifications', Notifications::class)->name('notifications.index');
});
