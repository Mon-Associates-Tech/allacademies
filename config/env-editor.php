<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Editable Environment Variables
    |--------------------------------------------------------------------------
    |
    | Only variables listed here can be edited through the UI.
    | All other variables are read-only in the UI.
    | Database variables (DB_*) are NEVER editable, regardless of this list.
    |
    | You may also provide a label and description for each variable.
    |
    | Example:
    |   'APP_URL' => [
    |       'label' => 'App URL',
    |       'description' => 'The base URL of the application.',
    |   ],
    |
    | Or simply:
    |   'APP_URL',
    |
    */

    'editable' => [
        'APP_NAME',
        'APP_ENV',
        'APP_DEBUG',
        'APP_URL',

        'MAIL_MAILER',
        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_USERNAME',
        'MAIL_ENCRYPTION',
        'MAIL_FROM_ADDRESS',
        'MAIL_FROM_NAME',

        'EMAIL_SENDING_ENABLED',

        'BROADCAST_DRIVER',
        'CACHE_DRIVER',
        'QUEUE_CONNECTION',
        'SESSION_DRIVER',
        'SESSION_LIFETIME',

        'ENABLE_OTP',
        'DEBUGBAR_ENABLED',
        'ENABLE_IMPORTERS',

        'OPENAI_API_KEY' => [
            'label' => 'OpenAI API Key',
            'description' => 'API key for standard OpenAI access.',
        ],
        'OPENAI_PREMIUM_API_KEY' => [
            'label' => 'OpenAI Premium API Key',
            'description' => 'API key for premium OpenAI access.',
        ],
        'OPENAI_DEFAULT_MODEL',
        'OPENAI_PREMIUM_MODEL',
        'OPENAI_TTS_MODEL',

        'PAYSTACK_ENV',
        'PAYSTACK_PUBLIC_KEY',
        'PAYSTACK_SECRET_KEY',
        'PAYSTACK_LIVE_SECRET_KEY',
        'PAYSTACK_LIVE_PUBLIC_KEY',

        'SPECIAL_ACCESS_EMAILS',
        'GENERAL_EXAMS_ACCESS_ROLES',

        'BBB_SERVER_URL',
        'BBB_SECRET',

        'PROCTORING_DRIVER',
        'PROCTOR_TAB_SWITCH',
        'PROCTOR_WINDOW_BLUR',
        'PROCTOR_COPY_ATTEMPT',
        'PROCTOR_PASTE_ATTEMPT',
        'PROCTOR_RIGHT_CLICK',
        'PROCTOR_KEYBOARD_SHORTCUT',
        'PROCTOR_FULLSCREEN_EXIT',
        'PROCTOR_EXAM_EXIT',
        'PROCTOR_MULTIPLE_FACES',
        'PROCTOR_NO_FACE',
        'PROCTOR_FACE_MISMATCH',
        'PROCTOR_AUTO_SUBMIT',
        'PROCTOR_HIGH_THRESHOLD',
        'PROCTOR_MEDIUM_THRESHOLD',

        'FIREWALL_EMAIL_ENABLED',
        'FIREWALL_EMAIL_NAME',
        'FIREWALL_EMAIL_FROM',
        'FIREWALL_EMAIL_TO',
        'FIREWALL_EMAIL_QUEUE',

        'CLEANUP_ADMIN_EMAIL',
        'CLEANUP_ADMIN_ID',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Variables
    |--------------------------------------------------------------------------
    |
    | These variables will always show as masked (●●●●●●) in the UI,
    | even if they are editable. The user can still change them by
    | typing a new value, but the current value is never displayed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Restricted Variables
    |--------------------------------------------------------------------------
    |
    | These variables can only be edited by users whose email matches
    | the allowed list. Even if a user is an owner, they cannot edit
    | a restricted variable unless their email is listed here.
    |
    | Format:
    |   'VAR_KEY' => ['allowed@example.com', 'another@example.com'],
    |
    */

    'restricted' => [
        // Example:
        // 'OPENAI_API_KEY' => ['admin@example.com'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Variable Groups
    |--------------------------------------------------------------------------
    |
    | Organize variables into collapsible groups in the UI.
    | Variables not listed here will appear in an "Other" group.
    |
    */

    'groups' => [
        'Application' => [
            'APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL',
            'LOG_CHANNEL', 'LOG_LEVEL',
        ],
        'Database' => [
            'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
        ],
        'Cache & Session' => [
            'CACHE_DRIVER', 'SESSION_DRIVER', 'SESSION_LIFETIME', 'SESSION_DOMAIN',
            'QUEUE_CONNECTION', 'BROADCAST_DRIVER', 'FILESYSTEM_DISK',
        ],
        'Redis & Memcached' => [
            'REDIS_HOST', 'REDIS_PASSWORD', 'REDIS_PORT',
            'MEMCACHED_HOST',
        ],
        'Mail' => [
            'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD',
            'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
            'EMAIL_SENDING_ENABLED',
        ],
        'Pusher' => [
            'PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET',
            'PUSHER_HOST', 'PUSHER_PORT', 'PUSHER_SCHEME', 'PUSHER_APP_CLUSTER',
            'VITE_PUSHER_APP_KEY', 'VITE_PUSHER_HOST', 'VITE_PUSHER_PORT',
            'VITE_PUSHER_SCHEME', 'VITE_PUSHER_APP_CLUSTER',
        ],
        'OpenAI' => [
            'OPENAI_API_KEY', 'OPENAI_PREMIUM_API_KEY',
            'OPENAI_DEFAULT_MODEL', 'OPENAI_PREMIUM_MODEL', 'OPENAI_TTS_MODEL',
        ],
        'Paystack' => [
            'PAYSTACK_ENV', 'PAYSTACK_PUBLIC_KEY', 'PAYSTACK_SECRET_KEY',
            'PAYSTACK_LIVE_SECRET_KEY', 'PAYSTACK_LIVE_PUBLIC_KEY',
        ],
        'BigBlueButton' => [
            'BBB_SERVER_URL', 'BBB_SECRET',
        ],
        'Proctoring' => [
            'PROCTORING_DRIVER',
            'PROCTOR_TAB_SWITCH', 'PROCTOR_WINDOW_BLUR', 'PROCTOR_COPY_ATTEMPT',
            'PROCTOR_PASTE_ATTEMPT', 'PROCTOR_RIGHT_CLICK', 'PROCTOR_KEYBOARD_SHORTCUT',
            'PROCTOR_FULLSCREEN_EXIT', 'PROCTOR_EXAM_EXIT', 'PROCTOR_MULTIPLE_FACES',
            'PROCTOR_NO_FACE', 'PROCTOR_FACE_MISMATCH',
            'PROCTOR_AUTO_SUBMIT', 'PROCTOR_HIGH_THRESHOLD', 'PROCTOR_MEDIUM_THRESHOLD',
        ],
        'Firewall' => [
            'FIREWALL_EMAIL_ENABLED', 'FIREWALL_EMAIL_NAME',
            'FIREWALL_EMAIL_FROM', 'FIREWALL_EMAIL_TO', 'FIREWALL_EMAIL_QUEUE',
        ],
        'Features' => [
            'ENABLE_OTP', 'DEBUGBAR_ENABLED', 'ENABLE_IMPORTERS',
        ],
        'Access Control' => [
            'SPECIAL_ACCESS_EMAILS', 'GENERAL_EXAMS_ACCESS_ROLES',
        ],
        'Cleanup' => [
            'CLEANUP_ADMIN_EMAIL', 'CLEANUP_ADMIN_ID',
        ],
    ],

];
