<?php

use App\Http\Controllers\PublicAssignmentController;
use App\Http\Controllers\Student\PublicAssignmentController as StudentPublicAssignmentController;
use App\Http\Controllers\Teachers\PublicAssignmentController as TeacherPublicAssignmentController;
use App\Services\PublicAssignment\ParticipantVerificationService;
use App\Services\PublicAssignment\PublicAssignmentService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Assignment Routes
|--------------------------------------------------------------------------
|
| Routes for the code-based public assignment system.
| Includes both teacher management routes and participant access routes.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Join assignment with access code
Route::get('/assignments/join', [PublicAssignmentController::class, 'join'])
    ->name('public-assignments.join');

Route::get('/assignments/join/{code}', [PublicAssignmentController::class, 'join'])
    ->name('public-assignments.join.code');

// Email verification for participants
Route::get('/assignments/verify-email', function () {
    $token = request('token');
    $accessCode = request('assignment');

    if (! $token || ! $accessCode) {
        return redirect()->route('public-assignments.join')
            ->with('error', 'Invalid verification link.');
    }

    $verificationService = app(ParticipantVerificationService::class);
    $assignmentService = app(\App\Services\PublicAssignment\PublicAssignmentService::class);
    $result = $verificationService->verifyEmail($token, $accessCode);

    if (! $result['success']) {
        return redirect()->route('public-assignments.join')
            ->with('error', $result['error']);
    }

    /** @var \App\Models\PublicAssignmentParticipant $participant */
    $participant = $result['participant'];
    /** @var \App\Models\PublicAssignment $assignment */
    $assignment = $result['assignment'];

    // Check eligibility
    $canTake = $verificationService->canParticipantTakeAssignment($participant, $assignment);
    if (! $canTake['can_take']) {
        return redirect()->route('public-assignments.join.code', $accessCode)
            ->with('error', $canTake['message']);
    }

    // Reuse existing in-progress submission if present
    if (! empty($canTake['has_existing_submission']) && $canTake['has_existing_submission'] === true) {
        $submission = $canTake['submission'];
    } else {
        $submission = $assignmentService->getOrCreateSubmission(
            $assignment,
            \App\Models\PublicAssignmentParticipant::class,
            $participant->id,
            [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }

    return redirect()->route('public-assignments.take', $submission)
        ->with('success', $result['message'] ?? 'Email verified. You can start the assignment now.');
})->name('public-assignments.verify-email')->middleware('signed');

// Take assignment (requires valid submission)
Route::get('/assignments/take/{submission}', [PublicAssignmentController::class, 'take'])
    ->name('public-assignments.take');

// View results with token (for guest participants)
Route::get('/assignments/results', [PublicAssignmentController::class, 'results'])
    ->name('public-assignments.results');

Route::get('/assignments/results/{token}', [PublicAssignmentController::class, 'results'])
    ->name('public-assignments.results.token');

/*
|--------------------------------------------------------------------------
| Teacher Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('teachers/public-assignments')->name('teachers.public-assignments.')->group(function () {
    // List all public assignments
    Route::get('/', [TeacherPublicAssignmentController::class, 'index']);
    Route::get('/index', [TeacherPublicAssignmentController::class, 'index'])->name('index');
    Route::get('/list', [TeacherPublicAssignmentController::class, 'index'])->name('list');

    // Create new public assignment
    Route::get('/create', [TeacherPublicAssignmentController::class, 'create'])
        ->name('create');

    // View/Edit assignment
    Route::get('/{assignment}', [TeacherPublicAssignmentController::class, 'show'])
        ->name('show');

    Route::get('/{assignment}/edit', [TeacherPublicAssignmentController::class, 'edit'])
        ->name('edit');

    // View results for an assignment
    Route::get('/{assignment}/results', [TeacherPublicAssignmentController::class, 'results'])
        ->name('results');

    // Grade a specific submission
    Route::get('/submissions/{submission}/grade', [TeacherPublicAssignmentController::class, 'gradeSubmission'])
        ->name('grade-submission');

    // API-style actions (for AJAX/Livewire)
    Route::post('/{assignment}/publish', function ($assignment) {
        $assignment = \App\Models\PublicAssignment::findOrFail($assignment);
        $assignment->update(['status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Assignment published']);
    })->name('publish');

    Route::post('/{assignment}/close', function ($assignment) {
        $assignment = \App\Models\PublicAssignment::findOrFail($assignment);
        $assignment->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Assignment closed']);
    })->name('close');

    Route::post('/{assignment}/release-results', function ($assignment) {
        $assignment = \App\Models\PublicAssignment::findOrFail($assignment);
        $assignment->releaseResults();

        return response()->json(['success' => true, 'message' => 'Results released']);
    })->name('release-results');

    Route::delete('/{assignment}', function ($assignment) {
        $assignment = \App\Models\PublicAssignment::findOrFail($assignment);
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

Route::middleware(['auth'])->prefix('student/public-assignments')->name('student.public-assignments.')->group(function () {
    // List student's public assignment submissions
    Route::get('/', [StudentPublicAssignmentController::class, 'index'])
        ->name('index');

    // View specific result
    Route::get('/results/{submission}', [StudentPublicAssignmentController::class, 'result'])
        ->name('result');
});
