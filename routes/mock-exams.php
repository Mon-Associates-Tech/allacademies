<?php

use App\MockExam\Controllers\MockExamController;
use App\MockExam\Controllers\MockExamGradeScaleController;
use App\MockExam\Controllers\MockExamIdentityFieldController;
use App\MockExam\Controllers\MockExamMonitoringController;
use App\MockExam\Controllers\MockExamParticipantController;
use App\MockExam\Controllers\MockExamPdfController;
use App\MockExam\Controllers\MockExamProctoringController;
use App\MockExam\Controllers\MockExamResultController;
use App\MockExam\Controllers\MockExamSelfServeController;
use App\MockExam\Controllers\MockExamSubjectExamController;
use App\MockExam\Controllers\MockExamTakingController;
use App\MockExam\Controllers\MockExamTemplateController;
use App\MockExam\Controllers\MockExamUserIdentityController;
use App\MockExam\Models\MockExamSubscriptionPayment;
use App\MockExam\Services\MockExamSubscriptionService;
use Illuminate\Support\Facades\Route;

// ─── Instructor routes ────────────────────────────────────────────────────────

Route::middleware(['web', 'auth'])
    ->prefix('mock-exams')
    ->name('mock-exams.')
    ->group(function () {

        Route::get('/', [MockExamController::class, 'index'])->name('index');
        Route::get('/create', [MockExamController::class, 'create'])->name('create');
        Route::post('/', [MockExamController::class, 'store'])->name('store');


        Route::get('identity-fields', [MockExamIdentityFieldController::class, 'index'])
            ->name('identity-fields.index');

        Route::get('/my-identity', [MockExamUserIdentityController::class, 'edit'])
            ->name('identity.edit');

        Route::get('/{mockExam}', [MockExamController::class, 'show'])->name('show');
        Route::get('/{mockExam}/edit', [MockExamController::class, 'edit'])->name('edit');
        Route::put('/{mockExam}', [MockExamController::class, 'update'])->name('update');
        Route::delete('/{mockExam}', [MockExamController::class, 'destroy'])->name('destroy');

        // Subject exams
        Route::prefix('/{mockExam}/subject-exams')->name('subject-exams.')->group(function () {
            Route::get('/create', [MockExamSubjectExamController::class, 'create'])->name('create');
            Route::post('/', [MockExamSubjectExamController::class, 'store'])->name('store');
            Route::get('/{subjectExam}/edit', [MockExamSubjectExamController::class, 'edit'])->name('edit');
            Route::put('/{subjectExam}', [MockExamSubjectExamController::class, 'update'])->name('update');
            Route::delete('/{subjectExam}', [MockExamSubjectExamController::class, 'destroy'])->name('destroy');
        });

        // Configured participants
        Route::prefix('/{mockExam}/participants')->name('participants.')->group(function () {
            Route::post('/', [MockExamParticipantController::class, 'store'])->name('store');
            Route::post('/import', [MockExamParticipantController::class, 'import'])->name('import');
            Route::delete('/{participant}', [MockExamParticipantController::class, 'destroy'])->name('destroy');
        });

        // Results / grading
        Route::prefix('/{mockExam}/results')->name('results.')->group(function () {
            Route::get('/', [MockExamResultController::class, 'index'])->name('index');
            Route::post('/release', [MockExamResultController::class, 'release'])->name('release');
            Route::post('/hide', [MockExamResultController::class, 'hide'])->name('hide');
            Route::post('/unhide', [MockExamResultController::class, 'unhide'])->name('unhide');
            Route::get('/{submission}', [MockExamResultController::class, 'show'])->name('show');
            Route::post('/{submission}/grade', [MockExamResultController::class, 'grade'])->name('grade');
            Route::post('/{submission}/finalize', [MockExamResultController::class, 'finalize'])->name('finalize');
        });

        // Live monitoring
        Route::get('/{mockExam}/monitor', [MockExamMonitoringController::class, 'index'])->name('monitor');

        // PDF downloads
        Route::get('/{mockExam}/pdf', [MockExamPdfController::class, 'previewPage'])->name('pdf');
        Route::get('/{mockExam}/pdf/download', [MockExamPdfController::class, 'examPdf'])->name('pdf.exam');
        Route::get('/{mockExam}/pdf/preview', [MockExamPdfController::class, 'previewExamPdf'])->name('pdf.preview');
        Route::get('/{mockExam}/pdf/answer-key', [MockExamPdfController::class, 'answerKeyPdf'])->name('pdf.answer-key');

        // Subject exam PDF downloads
        Route::get('/{mockExam}/subject-exams/{subjectExam}/pdf', [MockExamPdfController::class, 'previewSubjectExamPdf'])->name('subject-exams.pdf.preview');
        Route::get('/{mockExam}/subject-exams/{subjectExam}/pdf/download', [MockExamPdfController::class, 'subjectExamPdf'])->name('subject-exams.pdf.download');
        Route::get('/{mockExam}/subject-exams/{subjectExam}/pdf/preview-page', [MockExamPdfController::class, 'previewSubjectExamPage'])->name('subject-exams.pdf.page');

        // Quick generate from template
        Route::post('/{mockExam}/quick-generate', [MockExamTemplateController::class, 'quickGenerate'])->name('quick-generate');
    });


Route::middleware(['auth', 'verified', 'web'])
    ->prefix('dashboard/mock-exams/generate')
    ->name('mock-exams.generate.')
    ->group(function () {
        Route::get('/', [MockExamSelfServeController::class, 'index'])->name('index');
        Route::post('/{template}', [MockExamSelfServeController::class, 'generate'])->name('store');
    });

// ─── Template Management Routes ───────────────────────────────────────────────
//
// The template creation flow is a 2-step wizard:
//
//   Step 1 – Front Page Designer (Livewire)
//     GET  /mock-exam-templates/create                 → create()         new template
//     GET  /mock-exam-templates/{template}/front-page  → editFrontPage()  existing template
//
//   Step 2 – Template Details (Alpine.js form)
//     GET  /mock-exam-templates/configure              → configureCreate() new template
//     GET  /mock-exam-templates/{template}/edit        → configureEdit()   existing template
//
// Static segments (/create, /configure) are declared BEFORE the wildcard
// segments (/{template}/…) so Laravel never tries to resolve "create" or
// "configure" as a template model binding.

Route::middleware(['web', 'auth'])
    ->prefix('mock-exam-templates')
    ->name('mock-exams.templates.')
    ->group(function () {

        // List
        Route::get('/', [MockExamTemplateController::class, 'index'])->name('index');

        // ── Step 1 – Front Page Builder (Livewire) ────────────────────────
        Route::get('/create', [MockExamTemplateController::class, 'create'])->name('create');
        Route::get('/configure', [MockExamTemplateController::class, 'configureCreate'])->name('configure-create');

        // ── Step 2 – Template Details (Alpine.js form) ────────────────────
        Route::get('/{template}/front-page', [MockExamTemplateController::class, 'editFrontPage'])->name('front-page.edit');
        Route::get('/{template}/preview-front-page', [MockExamPdfController::class, 'previewTemplateFrontPage'])->name('preview-front-page');
        Route::get('/{template}/edit', [MockExamTemplateController::class, 'configureEdit'])->name('edit');

        // ── CRUD ──────────────────────────────────────────────────────────
        Route::post('/', [MockExamTemplateController::class, 'store'])->name('store');
        Route::put('/{template}', [MockExamTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [MockExamTemplateController::class, 'destroy'])->name('destroy');
    });

// ─── Grade scales — own top-level prefix, zero conflict with {mockExam} ───────
//
// Keeping grade-scales INSIDE /mock-exams/* means it always competes with the
// /{mockExam} wildcard at registration time. Moving it to its own prefix
// (/mock-exam-grading/*) removes the competition entirely — no constraints,
// no ordering tricks needed.

Route::middleware(['web', 'auth'])
    ->prefix('mock-exam-grading')
    ->name('mock-exams.grade-scales.')
    ->group(function () {
        Route::get('/', [MockExamGradeScaleController::class, 'index'])->name('index');
        Route::post('/', [MockExamGradeScaleController::class, 'store'])->name('store');
        Route::post('/initialize', [MockExamGradeScaleController::class, 'initialize'])->name('initialize');
        Route::put('/{gradeScale}', [MockExamGradeScaleController::class, 'update'])->name('update');
        Route::delete('/{gradeScale}', [MockExamGradeScaleController::class, 'destroy'])->name('destroy');
    });

// ─── Participant-facing routes (no auth required) ─────────────────────────────

Route::middleware('web')
    ->prefix('take/mock')
    ->name('mock-exams.take.')
    ->group(function () {
        Route::get('/join', [MockExamTakingController::class, 'join'])->name('join');
        Route::post('/authenticate', [MockExamTakingController::class, 'authenticate'])->name('authenticate');
        Route::get('/{mockExam}/start', [MockExamTakingController::class, 'start'])->name('start');
        Route::get('/{mockExam}/section/{sectionIndex}', [MockExamTakingController::class, 'section'])->name('section');
        Route::post('/{mockExam}/response', [MockExamTakingController::class, 'saveResponse'])->name('response');
        Route::post('/{mockExam}/submit', [MockExamTakingController::class, 'submit'])->name('submit');
        Route::get('/{mockExam}/completed', [MockExamTakingController::class, 'completed'])->name('completed');
        Route::get('/{mockExam}/results', [MockExamTakingController::class, 'viewResults'])->name('results');

        // Proctoring event recording (no auth, validated by session)
        Route::post('/{mockExam}/proctor-event', [MockExamProctoringController::class, 'recordEvent'])->name('proctor-event');
    });

// ─── PDF Rendering Routes (Secured via Signed URLs for Browsershot) ──────────
Route::prefix('pdf-render')->name('mock-exams.pdf.render.')->group(function () {
    // This route returns raw HTML for Browsershot to consume
    Route::get('/subject-exam/{mockExam}/{subjectExam}', [MockExamPdfController::class, 'renderSubjectExamHtml'])
        ->name('subject-exam')
        ->middleware('signed'); // Ensures only valid, signed requests can access this
});


/*
|--------------------------------------------------------------------------
| Subscription Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'web'])->prefix('dashboard/mock-exams/subscriptions')->name('mock-exams.subscription.')->group(function () {
    // Teacher subscription dashboard
    Route::get('/', function () {
        return view('mock-exams.subscriptions.dashboard');
    })->name('dashboard');

    // Paystack payment callback
    Route::get('/payment/callback', function (MockExamSubscriptionService $service) {
        $reference = request('reference');
        if (! $reference) {
            return redirect()->route('mock-exams.subscription.dashboard')->with('error', 'No payment reference found.');
        }

        $payment = MockExamSubscriptionPayment::where('paystack_reference', $reference)->first();
        if (! $payment) {
            return redirect()->route('general-exams.subscription.dashboard')->with('error', 'Payment record not found.');
        }

        if ($payment->payment_type === 'topup') {
            $result = $service->verifyTopUp($reference);
        } else {
            $result = $service->verifyAndActivate($reference);
        }

        if ($result['success']) {
            return redirect()->route('general-exams.subscription.dashboard')->with('success', 'Payment successful! Your subscription is now active.');
        }

        return redirect()->route('general-exams.subscription.dashboard')->with('error', 'Payment could not be verified. Please contact support.');
    })->name('payment.callback');
});

/*
|--------------------------------------------------------------------------
| Owner Subscription Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'web'])->prefix('admin/mock-exams')->name('admin.mock-exams.')->group(function () {
    Route::get('/subscriptions', function () {
        return view('mock-exams.subscriptions.index');
    })->name('subscriptions');

    Route::get('/pricing-tiers', function () {
        return view('mock-exams.pricing-tiers');
    })->name('pricing-tiers');
});
