<?php

return [
    'logo' => env('EXAM_LOGO', '/img/og-logo.png'),
    'primary_color' => env('EXAM_PRIMARY_COLOR', '#007bff'),
    'secondary_color' => env('EXAM_SECONDARY_COLOR', '#6c757d'),
    'font_family' => env('EXAM_FONT_FAMILY', 'Arial, sans-serif'),
    'background_color' => env('EXAM_BACKGROUND_COLOR', '#ffffff'),
    'button_color' => env('EXAM_BUTTON_COLOR', '#007bff'),
    'brand_name' => env('EXAM_BRAND_NAME', 'AllAcademies Exams Center'),
    'brand_name_short' => env('EXAM_BRAND_NAME_SHORT', 'AllAcademies Exams Center'),
    'footer_text' => env('EXAM_FOOTER_TEXT', '© 2024 AllAcademies Exams Center. All rights reserved.'),
    'exam_rules' => [
        'No cheating or plagiarism.',
        'Complete the exam within the allotted time.',
        'Follow all instructions provided by the exam proctor.',
        'Do not use unauthorized materials or devices.',
        'Report any technical issues immediately.',
    ],
    'access_code_length' => env('EXAM_ACCESS_CODE_LENGTH', 6),
];
