<?php

// Admin Routes
use App\Livewire\Teachers\EssayGrader;
use App\Models\Assessment;
use App\Models\Student;
use App\Services\SchoolContextService;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', App\Livewire\Administrators\Dashboard::class)->name('admin.dashboard');

    // Add this route for logging out all users
    Route::get('/logout-all-users', function () {
        $currentUserId = auth()->id();

        // Clear all sessions except current user
        $deletedSessions = DB::table('sessions')
            ->where('user_id', '!=', $currentUserId)
            ->delete();

        // Clear cache
        Cache::flush();

        // Update remember tokens except current user
        DB::table('users')
            ->where('id', '!=', $currentUserId)
            ->update(['remember_token' => null]);

        return response()->json([
            'success' => true,
            'message' => "All users logged out successfully. Cleared {$deletedSessions} sessions.",
        ]);
    })->name('admin.logout-all-users');
});
// Dashboard v2
Route::get('v2', function () {
    $contextInfo = SchoolContextService::getContextInfo();
    $stats = SchoolContextService::getStats();

    // Get students based on current context
    $students = SchoolContextService::applySchoolContext(Student::query())
        ->with(['user', 'academicLevel', 'academicGroup'])
        ->latest()
        ->paginate(20);

    return view('dashboard-v2', compact('contextInfo', 'stats', 'students'));
});
// Teacher Routes
Route::middleware(['auth', 'teacher'])->prefix('teacher')->group(function () {
    Route::get('/essays', function () {
        $assessments = Assessment::whereHas('responses', fn ($q) => $q->whereJsonContains('data->needs_grading', true))
            ->with('student.user')
            ->get();

        return view('livewire.teachers.essay-dashboard', compact('assessments'));
    })->name('teacher.essays.index');

    Route::get('/essays/{id}', EssayGrader::class)->name('teacher.essay.grade');
});
