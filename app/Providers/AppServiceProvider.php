<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Contracts\SmsProvider;
use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Contracts\ExamCreationServiceInterface;
use App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface;
use App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Services\ExamCreationService;
use App\ExaminationHub\Services\ExamDashboardService;
use App\ExaminationHub\Services\ExamParticipantAccessService;
use App\ExaminationHub\Services\ExamSubmissionExportService;
use App\Livewire\Common\AppModal;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\CalendarEvent;
use App\Models\ChatGroup;
use App\Models\EssayQuestion;
use App\Models\Examination;
use App\Models\Forum\ForumCategory;
use App\Models\Forum\ForumTopic;
use App\Models\MultipleChoiceQuestion;
use App\Models\Note;
use App\Models\Payment;
use App\ExaminationHub\Models\GeneralExamParticipant;
use App\Models\Role;
use App\Models\AcademicFeeStructure;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use App\Models\UserBook;
use App\Observers\AcademicFeeStructureObserver;
use App\Observers\SchoolPaymentObserver;
use App\Observers\SchoolPaymentStructureObserver;
use App\Services\ErrorNotificationService;
use App\Services\Sms\NullSmsProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ErrorNotificationService::class, function ($app) {
            return new ErrorNotificationService;
        });

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

        $this->app->bind(ExamDashboardServiceInterface::class, ExamDashboardService::class);
        $this->app->bind(ExamCreationServiceInterface::class, ExamCreationService::class);
        $this->app->bind(ExamParticipantAccessServiceInterface::class, ExamParticipantAccessService::class);
        $this->app->bind(ExamSubmissionExportServiceInterface::class, ExamSubmissionExportService::class);
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

        $this->app->singleton(\App\Services\MediaService::class);
        // In AppServiceProvider boot method
        Blade::component('app-modal', AppModal::class);

        Relation::enforceMorphMap([
            'academic_group' => AcademicGroup::class,
            'academic_level' => AcademicLevel::class,
            'academic_subject' => AcademicSubject::class,
            'academic_topic' => AcademicTopic::class,
            'academic_subtopic' => AcademicSubtopic::class,
            'essay_question' => EssayQuestion::class,
            'examinations' => Examination::class,
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'payment' => Payment::class,
            'subscription' => Subscription::class,
            'team' => Team::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'user' => User::class,
            'book' => Book::class,
            'student' => Student::class,
            'role' => Role::class,
            'StudentGroup' => StudentGroup::class,
            'Teacher' => Teacher::class,
            'Author' => Author::class,
            'BookCategory' => BookCategory::class,
            'book_subscription' => \App\Models\BookSubscription::class,
            'assessment' => \App\Models\Assessment::class,
            'book_borrowing' => \App\Models\BookBorrowing::class,
            'assignment' => \App\Models\Assignment::class,
            'school_setting' => \App\Models\SchoolSetting::class,
            'notification' => DatabaseNotification::class,
            'book_reading_progress' => \App\Models\BookReadingProgress::class,
            'login_activity' => \App\Models\LoginActivity::class,
            'forum_category' => ForumCategory::class,
            'forum_topic' => ForumTopic::class,
            'forum_post' => \App\Models\Forum\ForumPost::class,
            'forum_reaction' => \App\Models\Forum\ForumReaction::class,
            'forum_mention' => \App\Models\Forum\ForumMention::class,
            'user_preference' => \App\Models\UserPreference::class,
            'assignment_submission' => \App\Models\AssignmentSubmission::class,
            'participant' => GeneralExamParticipant::class,
            'publicassignmentparticipant' => GeneralExamParticipant::class,
            'PublicAssignmentParticipant' => GeneralExamParticipant::class,
            'App\\Models\\PublicAssignmentParticipant' => GeneralExamParticipant::class,
            'media_file' => \App\Models\Media\MediaFile::class,
            'media_folder' => \App\Models\Media\MediaFolder::class,
            'media_attachment' => \App\Models\Media\MediaAttachment::class,
            'message' => \App\Models\Message::class,
            'chat_group' => ChatGroup::class,
            'school' => \App\Models\School::class,
            'academic_period' => \App\Models\AcademicPeriod::class,
            'quiz_session' => \App\Models\QuizSession::class,
            'attendance_record' => AttendanceRecord::class,
            'open_ai_token_package' => \App\Models\Chat\OpenAiTokenPackage::class,
            'user_book' => UserBook::class,
            'user_book_share' => \App\Models\UserBookShare::class,
            'academic_year' => \App\Models\AcademicYear::class,
            'studentIdCard' => \App\Models\StudentIdCard::class,
            'report_card' => \App\Models\ReportCard::class,
            'note' => Note::class,
            'calendar_event' => CalendarEvent::class,
            'librarian' => \App\Models\Librarian::class,
            'accountant' => \App\Models\Accountant::class,
            'parent' => \App\Models\StudentParent::class,
            'mock_exam' => \App\MockExam\Models\MockExam::class,
            'mock_exam_subject_exam' => \App\MockExam\Models\MockExamSubjectExam::class,
            'mock_exam_participant' => \App\MockExam\Models\MockExamParticipant::class,
            'mock_exam_submission' => \App\MockExam\Models\MockExamSubmission::class,
            'grade_scale' => \App\MockExam\Models\GradeScale::class,
        ]);
    }
}
