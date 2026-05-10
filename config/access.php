<?php

return [
    'owner' => [
        'special_access_emails' => env('SPECIAL_ACCESS_EMAILS', ''),
    ],
    'general_exams' => [
        'access_roles' => array_values(array_filter(array_map(
            static fn (string $role): string => strtolower(trim($role)),
            explode(',', (string) env('GENERAL_EXAMS_ACCESS_ROLES', 'admin,owner,teacher'))
        ))),
    ],
];
