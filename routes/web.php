<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignOutController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\JoinTeamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\AuditTeamController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AcademicGroupController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicTopicController;
use App\Http\Controllers\EssayQuestionController;
use App\Http\Controllers\AcademicSubjectController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TrueOrFalseQuestionController;
use App\Http\Controllers\MultipleChoiceQuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'branding');

Route::middleware('guest')->group(function () {
    Route::get('sign-in', [SignInController::class, 'create'])->name('sign-in');
    Route::post('sign-in', [SignInController::class, 'store']);
    Route::get('sign-up', [SignUpController::class, 'create'])->name('sign-up');
    Route::post('sign-up', [SignUpController::class, 'store']);
});

Route::post('sign-out', [SignOutController::class, 'store'])->middleware('auth')->name('sign-out');

Route::middleware('auth')->prefix('verify/email')->name('verification.')->group(function () {
    Route::get('notice', [EmailVerificationController::class, 'notice'])->name('notice');
    Route::post('send', [EmailVerificationController::class, 'send'])->middleware('throttle:6,1')->name('send');
    Route::get('{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verify');
});

Route::prefix('password')->name('password.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('forgot', [PasswordController::class, 'forgotForm'])->name('request');
        Route::post('forgot', [PasswordController::class, 'forgot'])->name('email');
        Route::get('reset/{token}', [PasswordController::class, 'resetForm'])->name('reset');
        Route::post('reset', [PasswordController::class, 'reset'])->name('update');
    });
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('change', [PasswordController::class, 'changeForm'])->name('change');
        Route::post('change', [PasswordController::class, 'change']);
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('security', SecurityController::class)->name('security');

    Route::post('teams/{team}/activate', [TeamController::class, 'activate'])->name('teams.activate');
    Route::resource('teams', TeamController::class)->except('show');
    Route::post('teams/{team}/code', [JoinTeamController::class, 'generate'])->name('teams.code');
    Route::delete('teams/{team}/remove-code', [JoinTeamController::class, 'remove'])->name('teams.remove-code');
    Route::get('teams/joining', [JoinTeamController::class, 'joining'])->name('teams.joining');
    Route::post('teams/add-member', [JoinTeamController::class, 'join'])->name('teams.add-member');

    Route::resource('teams.members', MemberController::class)->except(['show', 'edit', 'update']);
    Route::get('teams/{team}/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::post('teams/{team}/members/{member}', [MemberController::class, 'update'])->name('members.update');

    Route::resource('subscriptions', SubscriptionController::class)->except(['show', 'edit', 'update']);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::match(['GET', 'POST'], 'settings/role', [SettingsController::class, 'role'])->name('settings.role');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('academic-groups', AcademicGroupController::class);
    Route::resource('academic-groups.academic-levels', AcademicLevelController::class)->shallow();
    Route::resource('academic-levels.academic-subjects', AcademicSubjectController::class)->shallow();
    Route::resource('academic-subjects.academic-topics', AcademicTopicController::class)->shallow();
    Route::resource('academic-topics.multiple-choice-questions', MultipleChoiceQuestionController::class)->shallow();
    Route::resource('academic-topics.essay-questions', EssayQuestionController::class)->shallow();
    Route::resource('academic-topics.true-or-false-questions', TrueOrFalseQuestionController::class)->shallow();

    Route::resource('users', UserController::class)->only(['index', 'show']);

    Route::get('examination/{examination}/answers', [ExaminationController::class, 'answers'])->name('examinations.answers');
    Route::resource('academic-subjects.examinations', ExaminationController::class)->shallow()->except(['edit', 'update', 'destroy']);
    Route::get('quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
    Route::match(['GET', 'POST'], 'quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
    Route::get('quizzes/{quiz}/stop', [QuizController::class, 'stop'])->name('quizzes.stop');
    Route::resource('academic-subjects.quizzes', QuizController::class)->shallow()->except(['edit', 'update', 'destroy']);
    Route::get('quizzes/{quiz}/scores', [QuizController::class, 'scores'])->name('quizzes.scores');

    Route::post('audit-teams/{audit_team}/approve', [AuditTeamController::class, 'approve'])->name('audit-teams.approve');
    Route::post('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'decline'])->name('audit-teams.decline');
    Route::get('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'reason'])->name('audit-teams.reason');
    Route::resource('audit-teams', AuditTeamController::class)->only(['index', 'show']);
});
