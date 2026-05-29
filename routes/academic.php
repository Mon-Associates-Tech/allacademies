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
                    
                });
                
                // Flat routes for topic-level imports to avoid nesting conflicts
                Route::get('academic-topics/{academic_topic}/import-questions', [QuestionImportController::class, 'showImportForm'])->name('questions.import.form');
                Route::post('academic-topics/{academic_topic}/preview-questions', [QuestionImportController::class, 'previewQuestions'])->name('questions.preview');
                Route::post('academic-topics/{academic_topic}/import-questions', [QuestionImportController::class, 'importQuestions'])->name('questions.import');
                Route::get('academic-topics/{academic_topic}/download-template', [QuestionImportController::class, 'downloadTemplate'])->name('questions.download.template');

                // Flat routes for subject-level imports to avoid nesting conflicts
                Route::get('academic-groups/{academic_group}/academic-levels/{academic_level}/academic-subjects/{academic_subject}/import-questions', [QuestionImportController::class, 'showSubjectImportForm'])->name('questions.subject.import.form');
                Route::post('academic-groups/{academic_group}/academic-levels/{academic_level}/academic-subjects/{academic_subject}/preview-questions', [QuestionImportController::class, 'previewSubjectQuestions'])->name('questions.subject.preview');
                Route::post('academic-groups/{academic_group}/academic-levels/{academic_level}/academic-subjects/{academic_subject}/import-questions', [QuestionImportController::class, 'importSubjectQuestions'])->name('questions.subject.import');
                Route::get('academic-groups/{academic_group}/academic-levels/{academic_level}/academic-subjects/{academic_subject}/download-template', [QuestionImportController::class, 'downloadSubjectTemplate'])->name('questions.subject.download.template');

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