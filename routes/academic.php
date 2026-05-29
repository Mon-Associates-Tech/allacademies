<?php

use App\Http\Controllers\AcademicGroupController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicSubjectController;
use App\Http\Controllers\AcademicTopicController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\Questions\EssayQuestionController;
use App\Http\Controllers\Questions\MultipleChoiceQuestionController;
use App\Http\Controllers\Questions\TrueOrFalseQuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SubtopicController;
use App\Http\Controllers\QuestionImportController;
use App\Livewire\AcademicManagement\AcademicHierarchy;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

    // Hierarchical academic routes with full nesting
    Route::resource('academic-groups', AcademicGroupController::class);

    Route::prefix('academic-groups/{academic_group}')->group(function () {
        Route::resource('academic-levels', AcademicLevelController::class);

        Route::prefix('academic-levels/{academic_level}')->group(function () {
            Route::resource('academic-subjects', AcademicSubjectController::class);

            Route::prefix('academic-subjects/{academic_subject}')->group(function () {
                Route::resource('academic-topics', AcademicTopicController::class);

                Route::prefix('academic-topics/{academic_topic}')->group(function () {
                    Route::resource('essay-questions', EssayQuestionController::class);
                    Route::resource('true-or-false-questions', TrueOrFalseQuestionController::class);
                    Route::resource('multiple-choice-questions', MultipleChoiceQuestionController::class);

                    // Keep the subtopic route within the hierarchy
                    Route::resource('subtopics', SubtopicController::class);
                    
                    // Add import route for questions - properly nested within the hierarchy
                    Route::get('/import-questions', [QuestionImportController::class, 'showImportForm'])
                        ->name('questions.import.form');
                    Route::post('/preview-questions', [QuestionImportController::class, 'previewQuestions'])
                        ->name('questions.preview');
                    Route::post('/import-questions', [QuestionImportController::class, 'importQuestions'])
                        ->name('questions.import');
                    Route::get('/download-template', [QuestionImportController::class, 'downloadTemplate'])
                        ->name('questions.template.download');
                });

                // Keep examinations and quizzes routes within the subject hierarchy
                Route::get('/examinations/preview', [ExaminationController::class, 'preview'])
                    ->name('examinations.preview');
                Route::post('/examinations/generate-preview', [ExaminationController::class, 'generatePreview'])
                    ->name('examinations.generate-preview');
                Route::resource('examinations', ExaminationController::class)
                    ->except(['edit', 'update', 'destroy']);
                Route::get('examination/{examination}/answers', [ExaminationController::class, 'answers'])->name('examinations.answers');
                //                Route::get('/quizzes/start', [QuizController::class, 'start'])->name('quizzes.start');
                Route::match(['GET', 'POST'], '/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
                Route::get('/quizzes/{quiz}/stop', [QuizController::class, 'stop'])->name('quizzes.stop');
                Route::resource('quizzes', QuizController::class)
                    ->except(['edit', 'update', 'destroy']);
                Route::get('/quizzes/{quiz}/scores', [QuizController::class, 'scores'])->name('quizzes.scores');
                Route::get('quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
            });
        });
    });

    Route::get('/academic-structure', AcademicHierarchy::class)->name('academic.structure');

});