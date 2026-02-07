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
use App\Http\Controllers\BookSubscriptionController;
use App\Http\Controllers\CalendarEventsController;
use App\Http\Controllers\Company\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ImportTemplateController;
use App\Http\Controllers\JoinTeamController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonNoteController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignOutController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TokenPaymentController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Livewire\Chats\ChatInterface;
use App\Livewire\Forums\ForumManagement;
use App\Livewire\Learning\BookQuizInterface;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Unauthenticated Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::view('/', 'branding')->name('home');
Route::view('/privacy', 'branding.privacy')->name('branding.privacy');
Route::view('/terms', 'branding.terms')->name('branding.terms');
Route::view('/features', 'branding.features')->name('branding.features');
Route::view('/contact', 'branding.contact')->name('branding.contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Test Error Notification Route
Route::get('/test-error-notification', function () {
    throw new \Exception('This is a test error notification with stack trace');
})->name('test.error');

// Newsletter Routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [SignInController::class, 'create'])->name('login');
    Route::post('login', [SignInController::class, 'store']);

    // Registration Type Selection
    Route::get('register', function () {
        return view('auth.register-type-selection');
    })->name('register');

    // Guest Registration Flow
    Route::get('register/guest', [RegisterGuestController::class, 'create'])->name('register.guest');
    Route::post('register/guest', [RegisterGuestController::class, 'store'])->name('register.store-guest');

    // Author Registration Flow
    Route::get('register/author', [RegisterAuthorController::class, 'create'])->name('register.author');
    Route::post('register/author', [RegisterAuthorController::class, 'store'])->name('register.store-author');

    // School Registration Flow
    Route::get('register/school', [RegisterSchoolController::class, 'create'])->name('register.school');
    Route::post('register/school', [RegisterSchoolController::class, 'store'])->name('register.store-school');

    // Legacy registration endpoint (redirect to type selection)
    Route::post('register', [SignUpController::class, 'store']);

    Route::prefix('password')->name('password.')->group(function () {
        Route::get('forgot', [PasswordController::class, 'forgotForm'])->name('request');
        Route::post('forgot', [PasswordController::class, 'forgot'])->name('email');
        Route::get('reset/{token}', [PasswordController::class, 'resetForm'])->name('reset');
        Route::post('reset', [PasswordController::class, 'reset'])->name('update');
    });
});

// Email Verification Routes
Route::get('verify/email/notice', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::post('verify/email/send', [EmailVerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
Route::get('verify/email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

// 2FA Routes
Route::get('2fa/verify', [SignInController::class, 'show2faForm'])->name('2fa.verify');
Route::post('2fa/verify', [SignInController::class, 'verify2fa']);
Route::post('/2fa/resend', [SignInController::class, 'resend2fa'])->name('2fa.resend');

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

// Payment Callback Routes
Route::get('/payment', [PaymentController::class, 'showForm'])->name('payment.form');
Route::get('/pay', [PaymentController::class, 'initialize'])->name('payment.initialize');
Route::get('/book-pay/{subscription}', [PaymentController::class, 'initializeBook'])->name('payment.book.initialize');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/book-callback', [PaymentController::class, 'bookCallback'])->name('payment.book.callback');
Route::get('/createSubAccount', [PaymentController::class, 'createSubAccount'])->name('payment.subAccount');
Route::get('/payment/callback/school-fees', [SchoolController::class, 'schoolFeesCallback'])->name('schoolfees.callback');

// Fee Payment Routes
Route::get('/feepayment/{student}', [PaymentController::class, 'showPaymentForm'])->name('feepayment.form');
Route::post('/feepayment', [PaymentController::class, 'processPayment'])->name('feepayment.process');
Route::get('/feepayment/callback', [PaymentController::class, 'paymentCallback'])->name('feepayment.callback');
Route::get('/feepayment/{student}/thank-you', [PaymentController::class, 'thankYou'])->name('feepayment.thankyou');
Route::get('/feepayment/callback/{student}', [PaymentController::class, 'paymentCallback'])->name('feepayment.student.callback');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Sign Out
    Route::post('sign-out', [SignOutController::class, 'store'])->name('logout');

    // Ping Route
    Route::post('/ping', static function () {
        return response()->noContent();
    });

    // Impersonation
    Route::impersonate();

    // Profile Routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('security', SecurityController::class)->name('security');
    Route::get('/preferences', fn () => view('preferences'))->name('preferences');

    // Password Change Routes
    Route::middleware('verified')->group(function () {
        Route::get('password/change', [PasswordController::class, 'changeForm'])->name('password.change');
        Route::post('password/change', [PasswordController::class, 'change']);
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Team Routes
    Route::post('teams/{team}/activate', [TeamController::class, 'activate'])->name('teams.activate');
    Route::resource('teams', TeamController::class)->except('show');
    Route::post('teams/{team}/code', [JoinTeamController::class, 'generate'])->name('teams.code');
    Route::delete('teams/{team}/remove-code', [JoinTeamController::class, 'remove'])->name('teams.remove-code');
    Route::get('teams/joining', [JoinTeamController::class, 'joining'])->name('teams.joining');
    Route::post('teams/add-member', [JoinTeamController::class, 'join'])->name('teams.add-member');
    Route::resource('teams.members', MemberController::class)->except(['show', 'edit', 'update']);
    Route::get('teams/{team}/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    Route::post('teams/{team}/members/{member}', [MemberController::class, 'update'])->name('members.update');

    // Subscription & Payment Routes
    Route::resource('subscriptions', SubscriptionController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
    Route::post('/subscriptions/toggle-test-mode', [SubscriptionController::class, 'toggleTestMode'])->name('subscriptions.toggle-test-mode');

    // Settings Routes
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::match(['GET', 'POST'], 'settings/role', [SettingsController::class, 'role'])->name('settings.role');

    // User Routes
    Route::resource('users', UserController::class)->only(['index', 'show', 'store']);
    Route::post('/users/change-role', [UserController::class, 'changeRole'])->name('users.change-role');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{type}/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{type}/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Audit Team Routes
    Route::post('audit-teams/{audit_team}/approve', [AuditTeamController::class, 'approve'])->name('audit-teams.approve');
    Route::post('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'decline'])->name('audit-teams.decline');
    Route::get('audit-teams/{audit_team}/decline', [AuditTeamController::class, 'reason'])->name('audit-teams.reason');
    Route::resource('audit-teams', AuditTeamController::class)->only(['index', 'show']);
    Route::post('audit-teams/bulk-approve', [AuditTeamController::class, 'bulkApprove'])->name('audit-teams.bulk-approve');

    // Export Routes
    Route::post('export/pdf', fn () => exportToPdf())->name('export.pdf');
    Route::post('export/word', fn () => exportToWord())->name('export.word');

    // Role Routes
    Route::resource('roles', RoleController::class);

    // Teacher Routes
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

    // Librarian Routes
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

    // Administrator Routes
    Route::resource('administrators', AdministratorController::class);
    Route::prefix('administrators/{administrator}')->group(function () {
        Route::get('/group-subscriptions/create', [AdministratorController::class, 'showGroupSubscriptionForm'])->name('administrators.group-subscriptions.form');
        Route::post('/group-subscriptions', [AdministratorController::class, 'subscribeGroupToBook'])->name('administrators.group-subscriptions.create');
        Route::get('/group-subscriptions', [AdministratorController::class, 'getGroupSubscriptions'])->name('administrators.group-subscriptions');
    });

    // Author Routes
    Route::resource('authors', AuthorController::class);
    Route::prefix('authors/{author}')->group(function () {
        Route::get('/books/create', [AuthorController::class, 'showCreateBookForm'])->name('authors.books.create.form');
        Route::post('/books', [AuthorController::class, 'createBook'])->name('authors.books.create');
        Route::get('/books/{book}/edit', [AuthorController::class, 'showUpdateBookForm'])->name('authors.books.edit.form');
        Route::put('/books/{book}', [AuthorController::class, 'updateBook'])->name('authors.books.update');
        Route::delete('/books/{book}', [AuthorController::class, 'deleteBook'])->name('authors.books.delete');
        Route::get('/books', [AuthorController::class, 'getBooks'])->name('authors.books');
    });

    // Book Category Routes
    Route::resource('book-categories', BookCategoryController::class);
    Route::get('book-categories/{bookCategory}/books', [BookCategoryController::class, 'getBooks'])->name('book-categories.books');

    // Student Group Routes
    Route::resource('student-groups', StudentGroupController::class);
    Route::prefix('student-groups/{studentGroup}')->group(function () {
        Route::get('/students/add', [StudentGroupController::class, 'showAddStudentForm'])->name('student-groups.students.add.form');
        Route::post('/students', [StudentGroupController::class, 'addStudent'])->name('student-groups.students.add');
        Route::get('/students/remove', [StudentGroupController::class, 'showRemoveStudentForm'])->name('student-groups.students.remove.form');
        Route::delete('/students', [StudentGroupController::class, 'removeStudent'])->name('student-groups.students.remove');
        Route::get('/students', [StudentGroupController::class, 'getStudents'])->name('student-groups.students');
    });

    // Subject Routes
    Route::resource('subjects', SubjectController::class);
    Route::get('subjects/{subject}/topics', [SubjectController::class, 'getTopics'])->name('subjects.topics');

    // Topic Routes
    Route::resource('topics', TopicController::class);
    Route::get('topics/{topic}/lesson-notes', [TopicController::class, 'getLessonNotes'])->name('topics.lesson-notes');

    // Lesson Routes
    Route::resource('lessons', LessonController::class);
    Route::get('lessons/{lesson}/notes', [LessonController::class, 'getNotes'])->name('lessons.notes');
    Route::resource('lesson-notes', LessonNoteController::class);

    // Assessment Routes
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

    // Media Routes
    Route::get('/mediapage', [\App\Http\Controllers\Media\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/download', [\App\Http\Controllers\Media\MediaController::class, 'download'])->name('media.download');

    // Onboarding Routes
    Route::get('onboarding/school-setup', \App\Livewire\SchoolOnboarding::class)->name('onboarding.school-setup');

    // Educational Chat Routes
    Route::prefix('academic-chats')->middleware(['assignment.session', 'token.subscription'])->name('academic-chat.')->group(function () {
        Route::get('/', [AcademicChatController::class, 'index'])->name('index');
        Route::post('/chat', [AcademicChatController::class, 'chat'])->name('chat');
        Route::get('/subjects', [AcademicChatController::class, 'subjects'])->name('subjects');
        Route::post('/recommendations', [AcademicChatController::class, 'recommendations'])->name('recommendations');
        Route::post('/export', [AcademicChatController::class, 'exportChat'])->name('export');
    });

    // Chat Routes
    Route::middleware(['verified', 'token.subscription'])->group(function () {
        Route::get('/chat', ChatInterface::class)->name('chat');
        Route::get('/chat/{group}', ChatInterface::class)->name('chat.group');
    });

    // Forum Routes
    Route::get('/forums', ForumManagement::class)->middleware('token.subscription')->name('forums');

    // Learning Routes
    Route::get('/learning/quiz/{bookId?}', BookQuizInterface::class)->middleware('token.subscription')->name('learning.quiz');

    // School Settings
    Route::get('/school-settings', \App\Livewire\School\SchoolSettingsDashboard::class)->name('school-settings.index');
    Route::get('/school-settings/fee-structure/setup', \App\Livewire\SchoolSettings\FeeStructureSetup::class)->name('school-settings.fee-structure.setup');

    // Token Subscription Management Routes (Admin Only - Revenue Tracking)
    Route::prefix('dashboard/messenger-transactions')->name('admin.messenger-transactions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MessengerTransactionsController::class, 'index'])->name('index');
    });

    // Token Subscription Routes (User Purchases)
    Route::prefix('token-subscriptions')->name('token-subscriptions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TokenSubscriptionController::class, 'index'])->name('index');
        Route::get('/history', [\App\Http\Controllers\TokenSubscriptionController::class, 'history'])->name('history');
        Route::get('/create', [\App\Http\Controllers\TokenSubscriptionController::class, 'create'])->name('create');
        Route::post('/checkout', [\App\Http\Controllers\TokenSubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/process-payment', [\App\Http\Controllers\TokenSubscriptionController::class, 'processPayment'])->name('process-payment');
        Route::post('/', [\App\Http\Controllers\TokenSubscriptionController::class, 'store'])->name('store');
        Route::get('/{subscription}', [\App\Http\Controllers\TokenSubscriptionController::class, 'show'])->name('show');
        Route::get('/{cycle}/topup', [\App\Http\Controllers\TokenSubscriptionController::class, 'topup'])->name('topup');
        Route::post('/topup/process', [\App\Http\Controllers\TokenSubscriptionController::class, 'processTopup'])->name('process-topup');
    });

    // Token Allocation Management Routes (Admin Only)
    Route::prefix('token-allocations')->name('token-allocations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TokenAllocationController::class, 'index'])->name('index');
        Route::get('/create-tier', [\App\Http\Controllers\TokenAllocationController::class, 'createTier'])->name('create-tier');
        Route::post('/store-tier', [\App\Http\Controllers\TokenAllocationController::class, 'storeTier'])->name('store-tier');
        Route::get('/tiers/{tier}/edit', [\App\Http\Controllers\TokenAllocationController::class, 'editTier'])->name('edit-tier');
        Route::put('/tiers/{tier}', [\App\Http\Controllers\TokenAllocationController::class, 'updateTier'])->name('update-tier');
        Route::get('/assign-tokens', [\App\Http\Controllers\TokenAllocationController::class, 'assignTokens'])->name('assign-tokens');
        Route::post('/store-assignment', [\App\Http\Controllers\TokenAllocationController::class, 'storeAssignment'])->name('store-assignment');
        Route::get('/users-json', [\App\Http\Controllers\TokenAllocationController::class, 'getUsersJson'])->name('users-json');
        Route::patch('/cycles/{cycle}/deactivate', [\App\Http\Controllers\TokenAllocationController::class, 'deactivateCycle'])->name('deactivate-cycle');
        Route::delete('/cycles/{cycle}/revoke', [\App\Http\Controllers\TokenAllocationController::class, 'revokeTokens'])->name('revoke-tokens');
        Route::get('/users/{user}/tokens', [\App\Http\Controllers\TokenAllocationController::class, 'viewUserTokens'])->name('view-user-tokens');
    });

    // Quiz Performance Routes
    Route::get('/quiz-performance', \App\Livewire\Learning\QuizPerformanceDashboard::class)->name('quiz.performance');
    Route::get('/quiz-performance/{userId}', \App\Livewire\Learning\QuizPerformanceDashboard::class)->name('quiz.performance.user');

    // Token Payment Routes
    Route::prefix('token-payments')->name('token-payments.')->group(function () {
        Route::get('/token/initialize', [TokenPaymentController::class, 'initialize'])->name('initialize');
        Route::get('/callback', [TokenPaymentController::class, 'callback'])->name('callback');
    });

    // User Books Routes
    Route::get('/user-books/create', fn () => view('user-books/create'))->middleware('token.subscription')->name('user-books.create');
    Route::get('/user-books/shared', fn () => view('user-books.shared'))->middleware('token.subscription')->name('user-books.shared');
    Route::get('/user-books', \App\Livewire\UserBooks\UserBooksIndex::class)->middleware('token.subscription')->name('user-books.index');
    Route::get('/user-books/{userBook}', function (App\Models\UserBook $userBook) {
        if ($userBook->user_id !== auth()->id() &&
            ! $userBook->shares()->where('shared_to_user_id', auth()->id())->where('status', 'accepted')->exists()) {
            abort(403);
        }
        $userBook->load('user');

        return view('user-books.show', compact('userBook'));
    })->middleware('token.subscription')->name('user-books.show');
    Route::get('/user-books/{userBook}/edit', \App\Livewire\UserBooks\UserBookForm::class)->middleware('token.subscription')->name('user-books.edit');
    Route::get('/{userBook}/manage-shares', \App\Livewire\UserBooks\ManageShares::class)->middleware('token.subscription')->name('user-books.manage-shares');

    // School Routes
    Route::prefix('schools')->group(function () {
        Route::get('/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/store', [SchoolController::class, 'store'])->name('schools.store');
        Route::post('/{school}/collect-fees', [SchoolController::class, 'collectFees'])->name('schools.collectFees');
    });

    // School Fee Setup Routes
    Route::get('/school/fee-setup', [SchoolController::class, 'showFeeSetupForm'])->name('school.fee-setup');
    Route::post('/school/fee-setup', [SchoolController::class, 'storeFeeStructure'])->name('school.fee-setup.store');

    // Academic Term Switch
    Route::post('/academic/term/switch', function (\Illuminate\Http\Request $request) {
        $termId = $request->input('term_id');
        \Illuminate\Support\Facades\DB::table('academic_periods')->update(['is_current' => false]);
        \Illuminate\Support\Facades\DB::table('academic_periods')->where('id', $termId)->update(['is_current' => true]);

        return back()->with('success', 'Current term has been updated successfully.');
    })->name('academic.term.switch');

    // School Comprehensive View
    Route::middleware('verified')->group(function () {
        Route::get('school/comprehensive-view', \App\Livewire\School\ComprehensiveSchoolDashboard::class)->name('school.comprehensive-view');
        Route::get('school/import-formats', [ImportTemplateController::class, 'viewFormats'])->name('school.import-formats');
        Route::get('school/download-template/{type}', [ImportTemplateController::class, 'download'])->name('school.download-template');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('payments', App\Http\Controllers\Admin\SchoolPaymentController::class);
        Route::get('payments/export', [App\Http\Controllers\Admin\SchoolPaymentController::class, 'export'])->name('payments.export');
        Route::resource('school-payment-structures', App\Http\Controllers\Admin\SchoolPaymentStructureController::class);
    });

    // Notes Routes
    Route::middleware('token.subscription')->group(function () {
        Route::resource('notes', NotesController::class);
        Route::get('/notes/{note}/download', [NotesController::class, 'download'])->name('notes.download');
        Route::post('/notes/{note}/share', [NotesController::class, 'share'])->name('notes.share');
        Route::delete('/notes/{note}/unshare/{user}', [NotesController::class, 'unshare'])->name('notes.unshare');
        Route::get('/notes/{note}/attachments/{attachment}/download', [NotesController::class, 'downloadAttachment'])->name('notes.attachments.download');
        Route::get('/notes/{note}/attachments/{attachment}/view', [NotesController::class, 'viewAttachment'])->name('notes.attachments.view');

        // Calendar Events Routes
        Route::resource('calendar-events', \App\Http\Controllers\CalendarEventsController::class);
        Route::post('/calendar-events/{event}/create-note', [CalendarEventsController::class, 'createNoteFromEvent'])
            ->name('calendar-events.create-note');
    });

    // Financial Aid
    Route::get('financial-aid', \App\Livewire\FinancialAidManager::class)->name('financial-aid');
});

// Calendar Route
Route::get('/calendar', function () {
    return view('calendar.index');
})->name('calendar.index');

// Include additional route files
include_once 'student.php';
include_once 'teacher.php';
include_once 'author.php';
include_once 'librarian.php';
include_once 'parent.php';
include_once 'administrator.php';
include_once 'academic.php';
include_once 'guest.php';
include_once 'misc.php';
