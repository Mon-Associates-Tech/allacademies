<?php

use App\Http\Controllers\SubtopicController;
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

use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonNoteController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BookBorrowingController;
use App\Http\Controllers\BookSubscriptionController;
use App\Http\Controllers\GroupBookSubscriptionController;
use App\Http\Controllers\BookApprovalController;


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

    Route::resource('academic-topics.subtopics', SubtopicController::class);


    Route::post('export/pdf', function(Request $request){
       return exportToPdf();

    })->name('export.pdf');
    Route::post('export/word', function(Request $request){
        return exportToWord();

    })->name('export.word');
});


Route::middleware(['auth'])->group(function () {

    // Role routes
    Route::resource('roles', RoleController::class);

    // Student routes
    Route::resource('students', StudentController::class);
    Route::prefix('students/{student}')->group(function () {
        Route::get('/borrow', [StudentController::class, 'showBorrowForm'])->name('students.borrow.form');
        Route::post('/borrow', [StudentController::class, 'borrowBook'])->name('students.borrow');
        Route::get('/return/{borrowing}', [StudentController::class, 'showReturnForm'])->name('students.return.form');
        Route::post('/return/{borrowing}', [StudentController::class, 'returnBook'])->name('students.return');
        Route::get('/subscribe', [StudentController::class, 'showSubscribeForm'])->name('students.subscribe.form');
        Route::post('/subscribe', [StudentController::class, 'subscribeToBook'])->name('students.subscribe');
        Route::get('/subscriptions/{subscription}/cancel', [StudentController::class, 'showCancelSubscriptionForm'])->name('students.subscription.cancel.form');
        Route::post('/subscriptions/{subscription}/cancel', [StudentController::class, 'cancelSubscription'])->name('students.subscription.cancel');
        Route::get('/assessments/create', [StudentController::class, 'showAssessmentForm'])->name('students.assessment.form');
        Route::post('/assessments', [StudentController::class, 'createAssessment'])->name('students.assessment.create');
        Route::get('/borrowed-books', [StudentController::class, 'getBorrowedBooks'])->name('students.borrowed-books');
        Route::get('/subscriptions', [StudentController::class, 'getSubscriptions'])->name('students.subscriptions');
        Route::get('/assessments', [StudentController::class, 'getAssessments'])->name('students.assessments');
    });

    // Teacher routes
    Route::resource('teachers', TeacherController::class);
    Route::prefix('teachers/{teacher}')->group(function () {
        Route::get('/student-groups/create', [TeacherController::class, 'showStudentGroupForm'])->name('teachers.student-groups.form');
        Route::post('/student-groups', [TeacherController::class, 'createStudentGroup'])->name('teachers.student-groups.create');
        Route::get('/student-groups', [TeacherController::class, 'getStudentGroups'])->name('teachers.student-groups');
        Route::get('/lessons/create', [TeacherController::class, 'showLessonForm'])->name('teachers.lessons.form');
        Route::post('/lessons', [TeacherController::class, 'createLesson'])->name('teachers.lessons.create');
        Route::get('/lesson-notes/create', [TeacherController::class, 'showLessonNoteForm'])->name('teachers.lesson-notes.form');
        Route::post('/lesson-notes', [TeacherController::class, 'uploadLessonNote'])->name('teachers.lesson-notes.create');
        Route::get('/group-subscriptions/create', [TeacherController::class, 'showGroupSubscriptionForm'])->name('teachers.group-subscriptions.form');
        Route::post('/group-subscriptions', [TeacherController::class, 'subscribeGroupToBook'])->name('teachers.group-subscriptions.create');
        Route::get('/lessons', [TeacherController::class, 'getLessons'])->name('teachers.lessons');
        Route::get('/lesson-notes', [TeacherController::class, 'getLessonNotes'])->name('teachers.lesson-notes');
        Route::get('/group-subscriptions', [TeacherController::class, 'getGroupSubscriptions'])->name('teachers.group-subscriptions');
    });

    // Librarian routes
    Route::resource('librarians', LibrarianController::class);
    Route::prefix('librarians/{librarian}')->group(function () {
        Route::get('/book-approvals/create', [LibrarianController::class, 'showBookApprovalForm'])->name('librarians.book-approvals.form');
        Route::post('/book-approvals', [LibrarianController::class, 'approveBook'])->name('librarians.book-approvals.create');
        Route::get('/book-lendings/create', [LibrarianController::class, 'showBookLendingForm'])->name('librarians.book-lendings.form');
        Route::post('/book-lendings', [LibrarianController::class, 'lendBook'])->name('librarians.book-lendings.create');
        Route::get('/book-returns/create', [LibrarianController::class, 'showBookReturnForm'])->name('librarians.book-returns.form');
        Route::post('/book-returns', [LibrarianController::class, 'processBookReturn'])->name('librarians.book-returns.create');
        Route::get('/group-subscriptions/create', [LibrarianController::class, 'showGroupSubscriptionForm'])->name('librarians.group-subscriptions.form');
        Route::post('/group-subscriptions', [LibrarianController::class, 'subscribeGroupToBook'])->name('librarians.group-subscriptions.create');
        Route::get('/book-approvals', [LibrarianController::class, 'getBookApprovals'])->name('librarians.book-approvals');
        Route::get('/book-lendings', [LibrarianController::class, 'getBookLendings'])->name('librarians.book-lendings');
        Route::get('/group-subscriptions', [LibrarianController::class, 'getGroupSubscriptions'])->name('librarians.group-subscriptions');
    });

    // Administrator routes
    Route::resource('administrators', AdministratorController::class);
    Route::prefix('administrators/{administrator}')->group(function () {
        Route::get('/group-subscriptions/create', [AdministratorController::class, 'showGroupSubscriptionForm'])->name('administrators.group-subscriptions.form');
        Route::post('/group-subscriptions', [AdministratorController::class, 'subscribeGroupToBook'])->name('administrators.group-subscriptions.create');
        Route::get('/group-subscriptions', [AdministratorController::class, 'getGroupSubscriptions'])->name('administrators.group-subscriptions');
    });

    // Author routes
    Route::resource('authors', AuthorController::class);
    Route::prefix('authors/{author}')->group(function () {
        Route::get('/books/create', [AuthorController::class, 'showCreateBookForm'])->name('authors.books.create.form');
        Route::post('/books', [AuthorController::class, 'createBook'])->name('authors.books.create');
        Route::get('/books/{book}/edit', [AuthorController::class, 'showUpdateBookForm'])->name('authors.books.edit.form');
        Route::put('/books/{book}', [AuthorController::class, 'updateBook'])->name('authors.books.update');
        Route::delete('/books/{book}', [AuthorController::class, 'deleteBook'])->name('authors.books.delete');
        Route::get('/books', [AuthorController::class, 'getBooks'])->name('authors.books');
    });

    // Book routes
    Route::resource('books', BookController::class);

    // Book Category routes
    Route::resource('book-categories', BookCategoryController::class);
    Route::get('book-categories/{bookCategory}/books', [BookCategoryController::class, 'getBooks'])->name('book-categories.books');

    // Student Group routes
    Route::resource('student-groups', StudentGroupController::class);
    Route::prefix('student-groups/{studentGroup}')->group(function () {
        Route::get('/students/add', [StudentGroupController::class, 'showAddStudentForm'])->name('student-groups.students.add.form');
        Route::post('/students', [StudentGroupController::class, 'addStudent'])->name('student-groups.students.add');
        Route::get('/students/remove', [StudentGroupController::class, 'showRemoveStudentForm'])->name('student-groups.students.remove.form');
        Route::delete('/students', [StudentGroupController::class, 'removeStudent'])->name('student-groups.students.remove');
        Route::get('/students', [StudentGroupController::class, 'getStudents'])->name('student-groups.students');
    });

    // Subject routes
    Route::resource('subjects', SubjectController::class);
    Route::get('subjects/{subject}/topics', [SubjectController::class, 'getTopics'])->name('subjects.topics');

    // Topic routes
    Route::resource('topics', TopicController::class);
    Route::get('topics/{topic}/lesson-notes', [TopicController::class, 'getLessonNotes'])->name('topics.lesson-notes');

    // Lesson routes
    Route::resource('lessons', LessonController::class);
    Route::get('lessons/{lesson}/notes', [LessonController::class, 'getNotes'])->name('lessons.notes');

    // Lesson Note routes
    Route::resource('lesson-notes', LessonNoteController::class);

    // Assessment routes
    Route::resource('assessments', AssessmentController::class);

    // Book Borrowing routes
    Route::resource('book-borrowings', BookBorrowingController::class)->except(['destroy']);

    // Book Subscription routes
    Route::resource('book-subscriptions', BookSubscriptionController::class)->except(['destroy']);

    // Group Book Subscription routes
    Route::resource('group-book-subscriptions', GroupBookSubscriptionController::class)->except(['destroy']);

    // Book Approval routes
    Route::resource('book-approvals', BookApprovalController::class)->except(['destroy']);
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', App\Livewire\Administrators\Dashboard::class)->name('admin.dashboard');
});


require __DIR__.'/demo.php';
