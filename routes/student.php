<?php

use App\Livewire\Students\ActivityLogs;
use App\Livewire\Students\Books;
use App\Livewire\Students\Courses;
use App\Livewire\Students\PerformanceOverview;
use App\Livewire\Students\SelfAssessment;
use App\Livewire\Students\StudentProfile;
use App\Livewire\Students\StudentSchedule;

Route::middleware(['auth'])->name('student.')->group(function () {

Route::get('assessments', SelfAssessment::class )->name('assessments');
Route::get('performance', PerformanceOverview::class)->name('performance');
Route::get('account', StudentProfile::class)->name('account');
Route::get('activities', ActivityLogs::class)->name('activities');
Route::get('schedules', StudentSchedule::class)->name('schedules');
Route::get('courses', Courses::class)->name('courses');
Route::get('books', Books::class)->name('books');
});
