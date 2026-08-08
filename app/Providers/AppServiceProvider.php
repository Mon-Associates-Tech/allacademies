<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Contracts\SmsProvider;
use App\Models\AcademicFeeStructure;
use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use App\Models\UserBook;
use App\Observers\AcademicFeeStructureObserver;
use App\Observers\SchoolPaymentObserver;
use App\Observers\SchoolPaymentStructureObserver;
// ...existing code...
use App\Services\Sms\NullSmsProvider;
use App\Services\QuestionImportService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Observers\TrueOrFalseQuestionObserver;
use App\Observers\MultipleChoiceQuestionObserver;
use App\Observers\EssayQuestionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Previously registered an ErrorNotificationService for custom error emails.
        // This was removed to restore Laravel's default exception handling behavior.

        // Register SMS Provider - defaults to NullSmsProvider
        // Replace with actual provider (Twilio, Nexmo, etc.) when configured
        $this->app->singleton(SmsProvider::class, function ($app) {
            // Future: Check config to determine which provider to use
            // $provider = config('services.sms.provider');
            // return match($provider) {
            //     'twilio' => new TwilioSmsProvider(),
            //     'nexmo' => new NexmoSmsProvider(),
            //     default => new NullSmsProvider(),
            // };
            return new NullSmsProvider;
        });

        // Register SMS Channel with the provider
        $this->app->singleton(SmsChannel::class, function ($app) {
            return new SmsChannel($app->make(SmsProvider::class));
        });

        $this->app->bind(\App\ExaminationHub\Contracts\ExamDashboardServiceInterface::class, \App\ExaminationHub\Services\ExamDashboardService::class);
        $this->app->bind(\App\ExaminationHub\Contracts\ExamCreationServiceInterface::class, \App\ExaminationHub\Services\ExamCreationService::class);
        $this->app->bind(\App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface::class, \App\ExaminationHub\Services\ExamParticipantAccessService::class);
        $this->app->bind(\App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface::class, \App\ExaminationHub\Services\ExamSubmissionExportService::class);

        // Register QuestionImportService
        $this->app->singleton(QuestionImportService::class, function ($app) {
            return new QuestionImportService($app->make(\App\Services\ResearchAssistantService::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::resourceVerbs([
            'create' => 'new',
        ]);

        // Register observers
        AcademicFeeStructure::observe(AcademicFeeStructureObserver::class);
        SchoolPayment::observe(SchoolPaymentObserver::class);
        SchoolPaymentStructure::observe(SchoolPaymentStructureObserver::class);

        // Register question observers
        TrueOrFalseQuestion::observe(TrueOrFalseQuestionObserver::class);
        MultipleChoiceQuestion::observe(MultipleChoiceQuestionObserver::class);
        EssayQuestion::observe(EssayQuestionObserver::class);

        $this->app->singleton(\App\Services\MediaService::class);
        // In AppServiceProvider boot method
        Blade::component('app-modal', \App\Livewire\Common\AppModal::class);

        Relation::enforceMorphMap([
            'academic_group' => \App\Models\AcademicGroup::class,
            'academic_level' => \App\Models\AcademicLevel::class,
            'academic_subject' => \App\Models\AcademicSubject::class,
            'academic_topic' => \App\Models\AcademicTopic::class,
            'academic_subtopic' => \App\Models\AcademicSubtopic::class,
            'essay_question' => EssayQuestion::class,
            'examinations' => \App\Models\Examination::class,
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'payment' => \App\Models\Payment::class,
            'subscription' => \App\Models\Subscription::class,
            'team' => \App\Models\Team::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'user' => \App\Models\User::class,
            'book' => \App\Models\Book::class,
            'student' => \App\Models\Student::class,
            'role' => \App\Models\Role::class,
            'StudentGroup' => \App\Models\StudentGroup::class,
            'Teacher' => \App\Models\Teacher::class,
            'Author' => \App\Models\Author::class,
            'BookCategory' => \App\Models\BookCategory::class,
            'book_subscription' => \App\Models\BookSubscription::class,
            'assessment' => \App\Models\Assessment::class,
            'book_borrowing' => \App\Models\BookBorrowing::class,
            'assignment' => \App\Models\Assignment::class,
            'school_setting' => \App\Models\SchoolSetting::class,
            'notification' => \Illuminate\Notifications\DatabaseNotification::class,
            'book_reading_progress' => \App\Models\BookReadingProgress::class,
            'login_activity' => \App\Models\LoginActivity::class,
            'forum_category' => \App\Models\Forum\ForumCategory::class,
            'forum_topic' => \App\Models\Forum\ForumTopic::class,
            'forum_post' => \App\Models\Forum\ForumPost::class,
            'forum_reaction' => \App\Models\Forum\ForumReaction::class,
            'forum_mention' => \App\Models\Forum\ForumMention::class,
            'user_preference' => \App\Models\UserPreference::class,
            'assignment_submission' => \App\Models\AssignmentSubmission::class,
            'participant' => \App\ExaminationHub\Models\GeneralExamParticipant::class,
            'publicassignmentparticipant' => \App\ExaminationHub\Models\GeneralExamParticipant::class,
            'PublicAssignmentParticipant' => \App\ExaminationHub\Models\GeneralExamParticipant::class,
            'App\\Models\\PublicAssignmentParticipant' => \App\ExaminationHub\Models\GeneralExamParticipant::class,
            'media_file' => \App\Models\Media\MediaFile::class,
            'media_folder' => \App\Models\Media\MediaFolder::class,
            'media_attachment' => \App\Models\Media\MediaAttachment::class,
            'message' => \App\Models\Message::class,
            'chat_group' => \App\Models\ChatGroup::class,
            'school' => \App\Models\School::class,
            'academic_period' => \App\Models\AcademicPeriod::class,
            'quiz_session' => \App\Models\QuizSession::class,
            'attendance_record' => \App\Models\Attendance\AttendanceRecord::class,
            'open_ai_token_package' => \App\Models\Chat\OpenAiTokenPackage::class,
            'user_book' => \App\Models\UserBook::class,
            'user_book_share' => \App\Models\UserBookShare::class,
            'academic_year' => \App\Models\AcademicYear::class,
            'studentIdCard' => \App\Models\StudentIdCard::class,
            'report_card' => \App\Models\ReportCard::class,
            'note' => \App\Models\Note::class,
            'calendar_event' => \App\Models\CalendarEvent::class,
            'librarian' => \App\Models\Librarian::class,
            'accountant' => \App\Models\Accountant::class,
            'parent' => \App\Models\StudentParent::class,
            'mock_exam' => \App\MockExam\Models\MockExam::class,
            'mock_exam_subject_exam' => \App\MockExam\Models\MockExamSubjectExam::class,
            'mock_exam_participant' => \App\MockExam\Models\MockExamParticipant::class,
            'mock_exam_submission' => \App\MockExam\Models\MockExamSubmission::class,
            'grade_scale' => \App\MockExam\Models\GradeScale::class,
            'staff' => \App\BookShop\Models\Staff::class,
            'customer' => \App\BookShop\Models\Customer::class,
        ]);
    }
}
