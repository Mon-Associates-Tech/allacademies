<?php

return [

    'default_driver' => env('PROCTORING_DRIVER', 'basic'),

    'drivers' => [
        'basic' => App\Services\Proctoring\BasicProctoringDriver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Violation Toggles
    |--------------------------------------------------------------------------
    | Each key maps to an event type. Set the env variable to false to disable
    | that violation entirely — it will be ignored on both the server (not
    | logged) and the client (event listener not bound).
    */
    'violations' => [
        'tab_switch'          => (bool) env('PROCTOR_TAB_SWITCH', true),
        'window_blur'         => (bool) env('PROCTOR_WINDOW_BLUR', true),
        'copy_attempt'        => (bool) env('PROCTOR_COPY_ATTEMPT', true),
        'paste_attempt'       => (bool) env('PROCTOR_PASTE_ATTEMPT', true),
        'right_click'         => (bool) env('PROCTOR_RIGHT_CLICK', true),
        'keyboard_shortcut'   => (bool) env('PROCTOR_KEYBOARD_SHORTCUT', true),
        'fullscreen_exit'     => (bool) env('PROCTOR_FULLSCREEN_EXIT', true),
        'exam_exit'           => (bool) env('PROCTOR_EXAM_EXIT', true),
        'multiple_faces'      => (bool) env('PROCTOR_MULTIPLE_FACES', true),
        'no_face'             => (bool) env('PROCTOR_NO_FACE', true),
        'face_mismatch'       => (bool) env('PROCTOR_FACE_MISMATCH', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Submit Thresholds
    |--------------------------------------------------------------------------
    */
    'auto_submit' => [
        'enabled'            => (bool) env('PROCTOR_AUTO_SUBMIT', true),
        'high_threshold'     => (int)  env('PROCTOR_HIGH_THRESHOLD', 2),
        'medium_threshold'   => (int)  env('PROCTOR_MEDIUM_THRESHOLD', 5),
    ],

    'model_mappings' => [
        'assessment' => \App\Models\Assessment::class,
        'quiz'       => \App\Models\Quiz::class,
        'assignment' => \App\Models\Assignment::class,
        'exam'       => \App\ExaminationHub\Models\GeneralExam::class,
    ],

];
