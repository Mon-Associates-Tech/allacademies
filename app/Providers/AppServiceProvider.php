<?php

namespace App\Providers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\EssayQuestion;
use App\Models\Examination;
use App\Models\MultipleChoiceQuestion;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;
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
        //
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
        ]);
    }
}
