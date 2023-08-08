<?php

namespace App\Providers;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\EssayQuestion;
use App\Models\Examination;
use App\Models\MultipleChoiceQuestion;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\TrueOrFalseQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
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
            'essay_question' => EssayQuestion::class,
            'examinations' => Examination::class,
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'payment' => Payment::class,
            'subscription' => Subscription::class,
            'team' => Team::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'user' => User::class,
        ]);
    }
}
