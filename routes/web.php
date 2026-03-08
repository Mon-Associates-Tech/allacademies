<?php

use App\Http\Controllers\AcademicChatController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuditTeamController;
use App\Http\Controllers\Auth\RegisterAuthorController;
use App\Http\Controllers\Auth\RegisterGuestController;
use App\Http\Controllers\Auth\RegisterSchoolController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookProgressController;
use App\Http\Controllers\CalendarEventsController;
use App\Http\Controllers\Company\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\JoinTeamController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonNoteController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Livewire\Chats\ChatInterface;
use App\Livewire\Forums\ForumManagement;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/

// Branding & Static Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/privacy', 'branding.privacy')->name('branding.privacy');
Route::view('/terms', 'branding.terms')->name('branding.terms');
Route::view('/features', 'branding.features')->name('branding.features');
Route::view('/contact', 'branding.contact')->name('branding.contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Account Suspended
Route::view('/account/suspended', 'auth.suspended')->name('account.suspended');

// Newsletter
// Solution Pages
Route::view('/solutions/schools', 'branding.solutions.schools')->name('solutions.schools');
Route::view('/solutions/teachers', 'branding.solutions.teachers')->name('solutions.teachers');
Route::view('/solutions/students', 'branding.solutions.students')->name('solutions.students');

// Test Error Notification Route
Route::get('/test-error-notification', function () {
    throw new \Exception('This is a test error notification with stack trace');
})->name('test.error');

// Newsletter Routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');


// Public Payment Routes
Route::prefix('general/pay')->name('payments.public.')->group(function () {
    Route::get('/init', [App\Http\Controllers\PublicPaymentController::class, 'showLookupForm'])->name('lookup');
    Route::post('/lookup', [App\Http\Controllers\PublicPaymentController::class, 'lookupStudent'])->name('lookup.post');
    Route::post('/initialize', [App\Http\Controllers\PublicPaymentController::class, 'initializePayment'])->name('initialize');
    Route::get('/callback', [App\Http\Controllers\PublicPaymentController::class, 'paymentCallback'])->name('callback');
    Route::get('/success/{payment}', [App\Http\Controllers\PublicPaymentController::class, 'success'])->name('success');
});

// Public Book Routes
Route::get('shared/books/{book}', [BookController::class, 'publicShow'])->name('books.public');

// Public Financial Aid
Route::get('/financial-aid-programs', \App\Livewire\PublicFinancialAidList::class)->name('public.financial-aid');

// Calendar (Public)
Route::get('/calendar', function () {
    return view('calendar.index');
})->name('calendar.index');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Ping Route (Keep-alive)
    Route::post('/ping', static function () {
        return response()->noContent();
    });

    // Impersonation
    Route::impersonate();

    /*
    |--------------------------------------------------------------------------
    | Profile & Security
    |--------------------------------------------------------------------------
    */
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('security', SecurityController::class)->name('security');
    Route::get('/preferences', fn () => view('preferences'))->name('preferences');

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Team Management
    |--------------------------------------------------------------------------
    */
    Route::post('teams/{team}/activate', [TeamController::class, 'activate'])->name('teams.activate');
    Route::resource('teams', TeamController::class)->except('show');
    Route::post('teams/{team}/code', [JoinTeamController::class, 'generate'])->name('teams.code');
    Route::delete('teams/{team}/remove-code', [JoinTeamController::class, 'remove'])->name('teams.remove-code');
    Route::get('teams/joining', [JoinTeamController::class, 'joining'])->name('teams.joining');
    Route::post('teams/add-member', [JoinTeamController::class, 'join'])->name('teams.add-member');
    Route::resource('teams.members', MemberController::class)->except(['show', 'edit', 'update']);
    Route::get('teams/{team}/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::post('teams/{team}/members/{member}', [MemberController::class, 'update'])->name('members.update');

    /*
    |--------------------------------------------------------------------------
    | Settings & User Management
    |--------------------------------------------------------------------------
    */
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::match(['GET', 'POST'], 'settings/role', [SettingsController::class, 'role'])->name('settings.role');

    Route::resource('users', UserController::class)->only(['index', 'show', 'store']);
    Route::post('/users/change-role', [UserController::class, 'changeRole'])->name('users.change-role');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{type}/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{type}/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    /*
    |--------------------------------------------------------------------------
    | Audit Teams
    |--------------------------------------------------------------------------
    */
    Route::post('audit-teams/{audit_team}/approve', [AuditTeamController::class, 'approve'])->name('audit-teams.approve');
    Route::post('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'decline'])->name('audit-teams.decline');
    Route::get('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'reason'])->name('audit-teams.reason');
    Route::resource('audit-teams', AuditTeamController::class)->only(['index', 'show']);
    Route::post('audit-teams/bulk-approve', [AuditTeamController::class, 'bulkApprove'])->name('audit-teams.bulk-approve');

    /*
    |--------------------------------------------------------------------------
    | Export Routes
    |--------------------------------------------------------------------------
    */
    Route::post('export/pdf', fn () => exportToPdf())->name('export.pdf');
    Route::post('export/word', fn () => exportToWord())->name('export.word');

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */
    Route::resource('roles', RoleController::class);

    /*
    |--------------------------------------------------------------------------
    | Teacher Management
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Librarian Management
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Administrator Management
    |--------------------------------------------------------------------------
    */
    Route::resource('administrators', AdministratorController::class);
    Route::prefix('administrators/{administrator}')->group(function () {
        Route::get('/group-subscriptions/create', [AdministratorController::class, 'showGroupSubscriptionForm'])->name('administrators.group-subscriptions.form');
        Route::post('/group-subscriptions', [AdministratorController::class, 'subscribeGroupToBook'])->name('administrators.group-subscriptions.create');
        Route::get('/group-subscriptions', [AdministratorController::class, 'getGroupSubscriptions'])->name('administrators.group-subscriptions');
    });

    /*
    |--------------------------------------------------------------------------
    | Author Management
    |--------------------------------------------------------------------------
    */
    Route::resource('authors', AuthorController::class);
    Route::prefix('authors/{author}')->group(function () {
        Route::get('/books/create', [AuthorController::class, 'showCreateBookForm'])->name('authors.books.create.form');
        Route::post('/books', [AuthorController::class, 'createBook'])->name('authors.books.create');
        Route::get('/books/{book}/edit', [AuthorController::class, 'showUpdateBookForm'])->name('authors.books.edit.form');
        Route::put('/books/{book}', [AuthorController::class, 'updateBook'])->name('authors.books.update');
        Route::delete('/books/{book}', [AuthorController::class, 'deleteBook'])->name('authors.books.delete');
        Route::get('/books', [AuthorController::class, 'getBooks'])->name('authors.books');
    });

    /*
    |--------------------------------------------------------------------------
    | Book Categories
    |--------------------------------------------------------------------------
    */
    Route::resource('book-categories', BookCategoryController::class);
    Route::get('book-categories/{bookCategory}/books', [BookCategoryController::class, 'getBooks'])->name('book-categories.books');

    /*
    |--------------------------------------------------------------------------
    | Student Groups
    |--------------------------------------------------------------------------
    */
    Route::resource('student-groups', StudentGroupController::class);
    Route::prefix('student-groups/{studentGroup}')->group(function () {
        Route::get('/students/add', [StudentGroupController::class, 'showAddStudentForm'])->name('student-groups.students.add.form');
        Route::post('/students', [StudentGroupController::class, 'addStudent'])->name('student-groups.students.add');
        Route::get('/students/remove', [StudentGroupController::class, 'showRemoveStudentForm'])->name('student-groups.students.remove.form');
        Route::delete('/students', [StudentGroupController::class, 'removeStudent'])->name('student-groups.students.remove');
        Route::get('/students', [StudentGroupController::class, 'getStudents'])->name('student-groups.students');
    });

    /*
    |--------------------------------------------------------------------------
    | Subjects & Topics
    |--------------------------------------------------------------------------
    */
    Route::resource('subjects', SubjectController::class);
    Route::get('subjects/{subject}/topics', [SubjectController::class, 'getTopics'])->name('subjects.topics');

    Route::resource('topics', TopicController::class);
    Route::get('topics/{topic}/lesson-notes', [TopicController::class, 'getLessonNotes'])->name('topics.lesson-notes');

    /*
    |--------------------------------------------------------------------------
    | Lessons & Assessments
    |--------------------------------------------------------------------------
    */
    Route::resource('lessons', LessonController::class);
    Route::get('lessons/{lesson}/notes', [LessonController::class, 'getNotes'])->name('lessons.notes');
    Route::resource('lesson-notes', LessonNoteController::class);

    Route::resource('assessments', AssessmentController::class);

    // Book Routes
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show')->middleware('token.subscription');
    Route::post('/books/{book}/request-borrow', [BookController::class, 'requestBorrow'])->name('books.request-borrow');
    Route::post('/books/{book}/progress', [BookController::class, 'saveProgress'])->name('books.progress');
    Route::get('books/{book}/read', [BookController::class, 'read'])->name('books.read');
    Route::get('books/{book}/preview', [BookController::class, 'preview'])->name('books.preview');

    // Book Subscription Routes
    Route::get('books/{book}/payment-instructions', [BookSubscriptionController::class, 'create'])->name('books.payment-instructions');
    Route::post('books/{book}/subscribe', [BookSubscriptionController::class, 'store'])->name('books.subscribe.store');
    Route::get('subscriptions/{subscription}/payment', [BookSubscriptionController::class, 'showPayment'])->name('subscriptions.payment.show');
    Route::post('subscriptions/{subscription}/verify-payment', [BookSubscriptionController::class, 'verifyPayment'])->name('subscriptions.payment.verify');
    Route::delete('subscriptions/{subscription}/cancel', [BookSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/books/{book}/reviews/')->name('books.reviews.store');

    // Book Reading Progress Routes
    Route::post('/books/update-progress', [BookProgressController::class, 'updateProgress'])->name('books.progress.update');
    Route::get('/books/{book}/progress', [BookProgressController::class, 'getProgress'])->name('books.progress.get');
    Route::get('/my-reading-progress', [BookProgressController::class, 'getUserProgress'])->name('books.progress.user');
    Route::post('/books/mark-completed', [BookProgressController::class, 'markCompleted'])->name('books.progress.complete');
    Route::delete('/books/{book}/progress', [BookProgressController::class, 'deleteProgress'])->name('books.progress.delete');

    // Activity Tracker Routes
    Route::get('/activities', \App\Livewire\Activities\ActivityTracker::class)->name('activities.index');

    // Academic Content Routes
    Route::get('/course-outlines', fn () => view('course-outlines'))->name('course-outlines');
    Route::get('/academic-calendar', fn () => view('academic-calendar'))->name('academic-calendar');

    /*
    |--------------------------------------------------------------------------
    | Educational Resource Center
    |--------------------------------------------------------------------------
    */
    Route::prefix('resources')->name('educational-resources.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EducationalResourceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\EducationalResourceController::class, 'create'])->name('create');
        Route::get('/{educationalResource}/edit', [\App\Http\Controllers\EducationalResourceController::class, 'edit'])->name('edit');
        Route::get('/{educationalResource}', [\App\Http\Controllers\EducationalResourceController::class, 'show'])->name('show');
        Route::get('/{educationalResource}/download', [\App\Http\Controllers\EducationalResourceController::class, 'download'])->name('download');
        Route::get('/{educationalResource}/stream', [\App\Http\Controllers\EducationalResourceController::class, 'stream'])->name('stream');
    });

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */
    Route::get('/mediapage', [\App\Http\Controllers\Media\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/download', [\App\Http\Controllers\Media\MediaController::class, 'download'])->name('media.download');

    /*
    |--------------------------------------------------------------------------
    | Educational Chat & Academic Chat
    |--------------------------------------------------------------------------
    */
    Route::prefix('academic-chats')->middleware(['assignment.session', 'token.subscription'])->name('academic-chat.')->group(function () {
        Route::get('/', [AcademicChatController::class, 'index'])->name('index');
        Route::post('/chat', [AcademicChatController::class, 'chat'])->name('chat');
        Route::get('/subjects', [AcademicChatController::class, 'subjects'])->name('subjects');
        Route::post('/recommendations', [AcademicChatController::class, 'recommendations'])->name('recommendations');
        Route::post('/export', [AcademicChatController::class, 'exportChat'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Chat & Forums
    |--------------------------------------------------------------------------
    */
    Route::middleware(['verified', 'token.subscription'])->group(function () {
        Route::get('/chat', ChatInterface::class)->name('chat');
        Route::get('/chat/{group}', ChatInterface::class)->name('chat.group');
    });

    Route::get('/forums', ForumManagement::class)->middleware('token.subscription')->name('forums');

    /*
    |--------------------------------------------------------------------------
    | Quiz Performance
    |--------------------------------------------------------------------------
    */
    Route::get('/quiz-performance', \App\Livewire\Learning\QuizPerformanceDashboard::class)->name('quiz.performance');
    Route::get('/quiz-performance/{userId}', \App\Livewire\Learning\QuizPerformanceDashboard::class)->name('quiz.performance.user');

    /*
    |--------------------------------------------------------------------------
    | Notes & Calendar Events
    |--------------------------------------------------------------------------
    */
    Route::middleware('token.subscription')->group(function () {
        Route::resource('notes', NotesController::class);
        Route::get('/notes/{note}/download', [NotesController::class, 'download'])->name('notes.download');
        Route::post('/notes/{note}/share', [NotesController::class, 'share'])->name('notes.share');
        Route::delete('/notes/{note}/unshare/{user}', [NotesController::class, 'unshare'])->name('notes.unshare');
        Route::get('/notes/{note}/attachments/{attachment}/download', [NotesController::class, 'downloadAttachment'])->name('notes.attachments.download');
        Route::get('/notes/{note}/attachments/{attachment}/view', [NotesController::class, 'viewAttachment'])->name('notes.attachments.view');

        Route::resource('calendar-events', \App\Http\Controllers\CalendarEventsController::class);
        Route::post('/calendar-events/{event}/create-note', [CalendarEventsController::class, 'createNoteFromEvent'])
            ->name('calendar-events.create-note');
    });

    /*
    |--------------------------------------------------------------------------
    | Financial Aid
    |--------------------------------------------------------------------------
    */
    Route::get('financial-aid', \App\Livewire\FinancialAidManager::class)->name('financial-aid');
});

/*
|--------------------------------------------------------------------------
| Include Additional Route Files
|--------------------------------------------------------------------------
|
| Route files are organized by feature/domain for better maintainability.
| Each file contains related routes grouped together.
|
*/

// Authentication Routes (login, register, password, 2FA, email verification)
include_once 'auth.php';

// Payment Routes (public payments, fee payments, subscriptions, callbacks)
include_once 'payment.php';

// Book Routes (books, reading progress, user books, learning quiz)
include_once 'book.php';

// Token Routes (token subscriptions, allocations, messenger transactions)
include_once 'token.php';

// School Routes (school management, fee setup, settings, onboarding)
include_once 'school.php';

// Role-based Route Files
include_once 'student.php';
include_once 'teacher.php';
include_once 'author.php';
include_once 'librarian.php';
include_once 'parent.php';
include_once 'administrator.php';
include_once 'academic.php';
include_once 'guest.php';
include_once 'sponsorship.php';
include_once 'misc.php';
