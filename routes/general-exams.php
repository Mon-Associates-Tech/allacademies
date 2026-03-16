<?php

use App\Http\Controllers\GeneralExamController;
use App\Http\Controllers\Student\GeneralExamController as StudentGeneralExamController;
use App\Http\Controllers\Teachers\GeneralExamController as TeacherGeneralExamController;
use App\Services\GeneralExam\GeneralExamParticipantVerificationService;
use App\Services\GeneralExam\GeneralExamService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| General Exam Routes
|--------------------------------------------------------------------------
|
| Routes for the code-based general exam system.
| Includes both teacher management routes and participant access routes.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Join assignment with access code
Route::get('/general-exams/join', [GeneralExamController::class, 'join'])
    ->name('general-exams.join');

Route::get('/general-exams/join/{code}', [GeneralExamController::class, 'join'])
    ->name('general-exams.join.code');

// Email verification for participants
Route::get('/general-exams/verify-email', function () {
    $token = request('token');
    $accessCode = request('assignment');

    if (! $token || ! $accessCode) {
        return redirect()->route('general-exams.join')
            ->with('error', 'Invalid verification link.');
    }

    $verificationService = app(GeneralExamParticipantVerificationService::class);
    $assignmentService = app(\App\Services\GeneralExam\GeneralExamService::class);
    $result = $verificationService->verifyEmail($token, $accessCode);

    if (! $result['success']) {
        return redirect()->route('general-exams.join')
            ->with('error', $result['error']);
    }

    /** @var \App\Models\GeneralExamParticipant $participant */
    $participant = $result['participant'];
    /** @var \App\Models\GeneralExam $assignment */
    $assignment = $result['assignment'];

    // Check eligibility
    $canTake = $verificationService->canParticipantTakeAssignment($participant, $assignment);
    if (! $canTake['can_take']) {
        return redirect()->route('general-exams.join.code', $accessCode)
            ->with('error', $canTake['message']);
    }

    // Reuse existing in-progress submission if present
    if (! empty($canTake['has_existing_submission']) && $canTake['has_existing_submission'] === true) {
        $submission = $canTake['submission'];
    } else {
        $submission = $assignmentService->getOrCreateSubmission(
            $assignment,
            \App\Models\GeneralExamParticipant::class,
            $participant->id,
            [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }

    return redirect()->route('general-exams.take', $submission)
        ->with('success', $result['message'] ?? 'Email verified. You can start the assignment now.');
})->name('general-exams.verify-email')->middleware('signed');

// Take assignment (requires valid submission)
Route::get('/general-exams/take/{submission}', [GeneralExamController::class, 'take'])
    ->name('general-exams.take');

// View results with token (for guest participants)
Route::get('/general-exams/results', [GeneralExamController::class, 'results'])
    ->name('general-exams.results');

Route::get('/general-exams/results/{token}', [GeneralExamController::class, 'results'])
    ->name('general-exams.results.token');

/*
|--------------------------------------------------------------------------
| Teacher Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('teachers/general-exams')->name('teachers.general-exams.')->group(function () {
    // List all public assignments
    Route::get('/', [TeacherGeneralExamController::class, 'index']);
    Route::get('/index', [TeacherGeneralExamController::class, 'index'])->name('index');
    Route::get('/list', [TeacherGeneralExamController::class, 'index'])->name('list');

    // Create new public assignment
    Route::get('/create', [TeacherGeneralExamController::class, 'create'])
        ->name('create');

    // View/Edit assignment
    Route::get('/{assignment}', [TeacherGeneralExamController::class, 'show'])
        ->name('show');

    Route::get('/{assignment}/edit', [TeacherGeneralExamController::class, 'edit'])
        ->name('edit');

    // View results for an assignment
    Route::get('/{assignment}/results', [TeacherGeneralExamController::class, 'results'])
        ->name('results');

    // Grade a specific submission
    Route::get('/submissions/{submission}/grade', [TeacherGeneralExamController::class, 'gradeSubmission'])
        ->name('grade-submission');

    // API-style actions (for AJAX/Livewire)
    Route::post('/{assignment}/publish', function ($assignment) {
        $assignment = \App\Models\GeneralExam::findOrFail($assignment);
        $assignment->update(['status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Assignment published']);
    })->name('publish');

    Route::post('/{assignment}/close', function ($assignment) {
        $assignment = \App\Models\GeneralExam::findOrFail($assignment);
        $assignment->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Assignment closed']);
    })->name('close');

    Route::post('/{assignment}/release-results', function ($assignment) {
        $assignment = \App\Models\GeneralExam::findOrFail($assignment);
        $assignment->releaseResults();

        return response()->json(['success' => true, 'message' => 'Results released']);
    })->name('release-results');

    Route::delete('/{assignment}', function ($assignment) {
        $assignment = \App\Models\GeneralExam::findOrFail($assignment);
        $assignment->delete();

        return response()->json(['success' => true, 'message' => 'Assignment deleted']);
    })->name('destroy');

    // Export results
    Route::get('/{assignment}/export', function ($assignment) {
        // TODO: Implement CSV/Excel export
        return response()->json(['message' => 'Export functionality coming soon']);
    })->name('export');
});

/*
|--------------------------------------------------------------------------
| Student Routes (For authenticated students viewing their results)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('student/general-exams')->name('student.general-exams.')->group(function () {
    // List student's public assignment submissions
    Route::get('/', [StudentGeneralExamController::class, 'index'])
        ->name('index');

    // View specific result
    Route::get('/results/{submission}', [StudentGeneralExamController::class, 'result'])
        ->name('result');
});
