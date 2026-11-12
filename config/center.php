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
    | 1 and 60 minutes. Default is 15 minutes to avoid excessive sync runs.
    |
    */
    'pull_interval_minutes' => (int) env('CENTER_PULL_INTERVAL', 15),

    /*
    |--------------------------------------------------------------------------
    | Push Interval
    |--------------------------------------------------------------------------
    |
    | How often the center pushes to central, in minutes. Default is 15 minutes
    | to avoid excessive sync runs. Reduce if you need faster result delivery.
    |
    */
    'push_interval_minutes' => (int) env('CENTER_PUSH_INTERVAL', 15),

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

    /*
    |--------------------------------------------------------------------------
    | Load Balancer Mode
    |--------------------------------------------------------------------------
    |
    | Controls the HAProxy routing mode on center servers.
    | - "off": No load balancer; Nginx serves directly on ports 80/443.
    | - "local-first": Local server is primary; overflow to Central when saturated.
    | - "central-first": Central is primary; fall back to local when Central is busy.
    |
    */
    'lb_mode' => env('LB_MODE', 'off'),

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxy IPs
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of IP addresses that act as trusted proxies
    | (HAProxy on center servers). Laravel will use X-Forwarded-For headers
    | from these IPs to determine the real client IP.
    |
    */
    'trusted_proxy_ips' => array_filter(
        array_map('trim', explode(',', env('TRUSTED_PROXY_IPS', '')))
    ),

    /*
    |--------------------------------------------------------------------------
    | LB Health Check Threshold
    |--------------------------------------------------------------------------
    |
    | PHP-FPM worker utilization percentage threshold. When the percentage of
    | busy workers reaches this value, /lb-health returns 503 so HAProxy can
    | route new candidates to a different backend.
    |
    */
    'lb_health_threshold' => (int) env('LB_HEALTH_THRESHOLD', 85),

    /*
    |--------------------------------------------------------------------------
    | LB Health DB Connection Threshold
    |--------------------------------------------------------------------------
    |
    | Fallback threshold when PHP-FPM status is unavailable. If the number of
    | active database connections reaches this value, /lb-health returns 503.
    |
    */
    'lb_health_db_threshold' => (int) env('LB_HEALTH_DB_THRESHOLD', 50),

    /*
    |--------------------------------------------------------------------------
    | PHP-FPM Status Path
    |--------------------------------------------------------------------------
    |
    | Nginx location path that exposes the PHP-FPM status page.
    | Used by /lb-health to read worker utilization.
    |
    */
    'fpm_status_path' => env('FPM_STATUS_PATH', '/fpm-status'),

];
