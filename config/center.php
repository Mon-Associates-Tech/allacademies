<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Center Role
    |--------------------------------------------------------------------------
    |
    | Determines whether this instance runs as the central server or an
    | examination center. Use "central" for the source of truth and
    | "center" for downstream examination centers.
    |
    */
    'role' => env('APP_ROLE', 'central'),

    /*
    |--------------------------------------------------------------------------
    | Center ID
    |--------------------------------------------------------------------------
    |
    | The unique identifier for this center. Required when APP_ROLE=center.
    | Matches the uuid in the centers table on the central server.
    |
    */
    'center_id' => env('CENTER_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Central Server URL
    |--------------------------------------------------------------------------
    |
    | Base URL of the central server for sync operations. Required when
    | APP_ROLE=center. Used by the pull and push agents.
    |
    */
    'central_url' => env('CENTRAL_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Sync Token
    |--------------------------------------------------------------------------
    |
    | Bearer token for authenticating with the central server's sync API.
    | Generated on central and copied to the center's configuration.
    |
    */
    'sync_token' => env('CENTER_SYNC_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | Central Public Key
    |--------------------------------------------------------------------------
    |
    | Ed25519 public key of the central server, used to verify exam package
    | signatures. Store the base64-encoded public key here.
    |
    */
    'central_public_key' => env('CENTRAL_PUBLIC_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Central Private Key
    |--------------------------------------------------------------------------
    |
    | Ed25519 private key of the central server, used to sign exam packages.
    | Only used when APP_ROLE=central. Store the base64-encoded private key.
    |
    */
    'central_private_key' => env('CENTRAL_PRIVATE_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Pull Interval
    |--------------------------------------------------------------------------
    |
    | How often the center pulls from central, in minutes. Adjustable between
    | 1 and 60 minutes. Default is 5 minutes.
    |
    */
    'pull_interval_minutes' => (int) env('CENTER_PULL_INTERVAL', 5),

    /*
    |--------------------------------------------------------------------------
    | Push Interval
    |--------------------------------------------------------------------------
    |
    | How often the center pushes to central, in minutes. Default is 5 minutes.
    |
    */
    'push_interval_minutes' => (int) env('CENTER_PUSH_INTERVAL', 5),

    /*
    |--------------------------------------------------------------------------
    | Clock Skew Threshold
    |--------------------------------------------------------------------------
    |
    | Maximum allowed clock skew in seconds before blocking exam joins.
    | Default is 120 seconds (2 minutes).
    |
    */
    'clock_skew_threshold_seconds' => (int) env('CENTER_CLOCK_SKEW_THRESHOLD', 120),

    /*
    |--------------------------------------------------------------------------
    | App Version
    |--------------------------------------------------------------------------
    |
    | Application version string for version handshake with central.
    | Central will reject sync requests if versions are incompatible.
    |
    */
    'app_version' => env('APP_VERSION', '1.0.0'),

];
