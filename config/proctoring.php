<?php
/**
 * Proctoring System Configuration
 *
 * Defines pluggable driver settings, violation thresholds, and polymorphic
 * model mappings. The model_mappings array allows the middleware to resolve
 * any route parameter (exam, quiz, assignment, mock, etc.) to its Eloquent class.
 */
return [
    'default_driver' => env('PROCTORING_DRIVER', 'basic'),
    'drivers' => [
        'basic' => App\Services\Proctoring\BasicProctoringDriver::class,
        // 'ai' => App\Services\Proctoring\AIProctoringDriver::class,
    ],
    'violations' => [
        'max_allowed' => 3,
        'auto_submit_on_exceed' => true,
        'warning_threshold' => 2,
    ],
    'features' => [
        'fullscreen' => true,
        'tab_switch' => true,
        'copy_paste' => true,
        'keyboard_shortcuts' => true,
    ],
    // Maps route parameter names to Eloquent model classes
    'model_mappings' => [
        'assessment' => \App\Models\Assessment::class,
        'quiz'       => \App\Models\Quiz::class,
        'assignment' => \App\Models\Assignment::class,
        'exam' => \App\ExaminationHub\Models\GeneralExam::class,
    ],
];
