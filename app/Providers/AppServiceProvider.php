<?php

namespace App\Providers;

use App\BookShop\Models\Customer;
use App\BookShop\Models\Staff;
use App\Channels\SmsChannel;
use App\Contracts\SmsProvider;
use App\ExaminationHub\Contracts\ExamCreationServiceInterface;
use App\ExaminationHub\Contracts\ExamDashboardServiceInterface;
use App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface;
use App\ExaminationHub\Contracts\ExamSubmissionExportServiceInterface;
use App\ExaminationHub\Models\GeneralExamParticipant;
use App\ExaminationHub\Services\ExamCreationService;
use App\ExaminationHub\Services\ExamDashboardService;
use App\ExaminationHub\Services\ExamParticipantAccessService;
use App\ExaminationHub\Services\ExamSubmissionExportService;
use App\Livewire\Common\AppModal;
use App\Livewire\Support\OverseerReadOnlyHook;
use App\MockExam\Models\GradeScale;
// ...existing code...
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamParticipant;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamSubmission;
use App\Models\AcademicFeeStructure;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\AcademicYear;
use App\Models\Accountant;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance\AttendanceRecord;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookReadingProgress;
use App\Models\BookSubscription;
use App\Models\CalendarEvent;
use App\Models\Chat\OpenAiTokenPackage;
use App\Models\ChatGroup;
use App\Models\Classroom\SessionRecording;
use App\Models\Classroom\VirtualSession;
use App\Models\EssayQuestion;
use App\Models\Examination;
use App\Models\Forum\ForumCategory;
use App\Models\Forum\ForumMention;
use App\Models\Forum\ForumPost;
use App\Models\Forum\ForumReaction;
use App\Models\Forum\ForumTopic;
use App\Models\Librarian;
use App\Models\LoginActivity;
use App\Models\Media\MediaAttachment;
use App\Models\Media\MediaFile;
use App\Models\Media\MediaFolder;
use App\Models\Message;
use App\Models\MultipleChoiceQuestion;
use App\Models\Note;
use App\Models\Payment;
use App\Models\QuizSession;
use App\Models\ReportCard;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\StudentIdCard;
use App\Models\StudentParent;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use App\Models\UserBook;
use App\Models\UserBookShare;
use App\Models\UserPreference;
use App\Observers\AcademicFeeStructureObserver;
use App\Observers\EssayQuestionObserver;
use App\Observers\MockExamObserver;
use App\Observers\MultipleChoiceQuestionObserver;
use App\Observers\SchoolPaymentObserver;
use App\Observers\SchoolPaymentStructureObserver;
use App\Observers\TrueOrFalseQuestionObserver;
use App\Services\DocumentContentExtractionService;
use App\Services\MediaService;
use App\Services\QuestionImportService;
use App\Services\ResearchAssistantService;
use App\Services\Sms\NullSmsProvider;
use App\Timetable\Models\Room;
use App\Timetable\Models\Timeslot;
use App\Timetable\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Livewire::componentHook(OverseerReadOnlyHook::class);

        // Increase PHP upload limits EARLY in the lifecycle for Livewire file uploads (PDFs up to 100MB)
        // Must be in register() so it runs before any request processing, including the upload-file endpoint
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '110M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');
        ini_set('memory_limit', '256M');

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

        $this->app->bind(ExamDashboardServiceInterface::class, ExamDashboardService::class);
        $this->app->bind(ExamCreationServiceInterface::class, ExamCreationService::class);
        $this->app->bind(ExamParticipantAccessServiceInterface::class, ExamParticipantAccessService::class);
        $this->app->bind(ExamSubmissionExportServiceInterface::class, ExamSubmissionExportService::class);

        // Register QuestionImportService
        $this->app->singleton(QuestionImportService::class, function ($app) {
            return new QuestionImportService(
                $app->make(ResearchAssistantService::class),
                $app->make(DocumentContentExtractionService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (request()->isSecure() || parse_url(config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }

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

        // Register MockExam observer
        MockExam::observe(MockExamObserver::class);

        $this->app->singleton(MediaService::class);
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
            'book_subscription' => BookSubscription::class,
            'assessment' => Assessment::class,
            'book_borrowing' => BookBorrowing::class,
            'assignment' => Assignment::class,
            'school_setting' => SchoolSetting::class,
            'notification' => DatabaseNotification::class,
            'book_reading_progress' => BookReadingProgress::class,
            'login_activity' => LoginActivity::class,
            'forum_category' => ForumCategory::class,
            'forum_topic' => ForumTopic::class,
            'forum_post' => ForumPost::class,
            'forum_reaction' => ForumReaction::class,
            'forum_mention' => ForumMention::class,
            'user_preference' => UserPreference::class,
            'assignment_submission' => AssignmentSubmission::class,
            'participant' => GeneralExamParticipant::class,
            'publicassignmentparticipant' => GeneralExamParticipant::class,
            'PublicAssignmentParticipant' => GeneralExamParticipant::class,
            'App\\Models\\PublicAssignmentParticipant' => GeneralExamParticipant::class,
            'media_file' => MediaFile::class,
            'media_folder' => MediaFolder::class,
            'media_attachment' => MediaAttachment::class,
            'message' => Message::class,
            'chat_group' => ChatGroup::class,
            'school' => School::class,
            'academic_period' => AcademicPeriod::class,
            'quiz_session' => QuizSession::class,
            'attendance_record' => AttendanceRecord::class,
            'open_ai_token_package' => OpenAiTokenPackage::class,
            'user_book' => UserBook::class,
            'user_book_share' => UserBookShare::class,
            'academic_year' => AcademicYear::class,
            'studentIdCard' => StudentIdCard::class,
            'report_card' => ReportCard::class,
            'note' => Note::class,
            'calendar_event' => CalendarEvent::class,
            'librarian' => Librarian::class,
            'accountant' => Accountant::class,
            'parent' => StudentParent::class,
            'mock_exam' => MockExam::class,
            'mock_exam_subject_exam' => MockExamSubjectExam::class,
            'mock_exam_participant' => MockExamParticipant::class,
            'mock_exam_submission' => MockExamSubmission::class,
            'grade_scale' => GradeScale::class,
            'staff' => Staff::class,
            'customer' => Customer::class,
            'timeslot' => Timeslot::class,
            'room' => Room::class,
            'timetable_entry' => TimetableEntry::class,
            'virtual_classroom' => VirtualSession::class,
            'session_recording' => SessionRecording::class,
        ]);
    }
}
