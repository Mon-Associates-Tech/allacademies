<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BigBlueButton Server Configuration
    |--------------------------------------------------------------------------
    */
    'server_url' => env('BBB_SERVER_URL', 'https://bbb.yourdomain.com/bigbluebutton/'),
    'secret' => env('BBB_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Meeting Settings
    |--------------------------------------------------------------------------
    */
    'default_settings' => [
        'duration' => 0, // 0 = unlimited
        'maxParticipants' => 100,
        'autoStartRecording' => false,
        'allowStartStopRecording' => true,
        'muteOnStart' => false,
        'webcamsOnlyForModerator' => false,
        'lockSettingsDisableCam' => false,
        'lockSettingsDisableMic' => false,
        'allowModsToUnmuteUsers' => true,
        'guestPolicy' => 'ASK_MODERATOR', // ALWAYS_ACCEPT, ALWAYS_DENY, ASK_MODERATOR
    ],

    /*
    |--------------------------------------------------------------------------
    | Recording Settings
    |--------------------------------------------------------------------------
    */
    'recordings' => [
        'enabled' => true,
        'auto_delete_days' => 90, // Auto-delete recordings after X days
        'storage_disk' => 's3', // local, s3, etc.
        'storage_path' => 'virtual-sessions/recordings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Limits
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_session_duration_minutes' => 180, // 3 hours
        'max_participants_per_session' => 100,
        'max_simultaneous_sessions_per_teacher' => 3,
    ],
];
