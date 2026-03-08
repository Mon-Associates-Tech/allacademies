<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Logging
    |--------------------------------------------------------------------------
    |
    | This configuration file is used to manage activity logging settings
    | throughout your application.
    |
    */

    /*
     * Enable or disable activity logging globally
     */
    'enabled' => env('ACTIVITY_LOG_ENABLED', true),

    /*
     * The database connection to use for activity logs
     */
    'connection' => env('DB_CONNECTION', 'mysql'),

    /*
     * The table name for storing user activities
     */
    'table' => env('ACTIVITY_LOG_TABLE', 'user_activities'),

    /*
     * Auto-log HTTP requests through middleware
     * Add LogUserActivity middleware to your HTTP kernel to enable
     */
    'log_requests' => env('ACTIVITY_LOG_REQUESTS', true),

    /*
     * Log page views (GET requests) - disable to reduce noise
     */
    'log_page_views' => env('ACTIVITY_LOG_PAGE_VIEWS', false),

    /*
     * Days to retain activity logs (0 = indefinite)
     */
    'retention_days' => env('ACTIVITY_LOG_RETENTION_DAYS', 365),

    /*
     * Categories used for activity organization
     * Extend this list based on your application's needs
     */
    'categories' => [
        'authentication' => 'User Authentication',
        'academic' => 'Academic Activities',
        'library' => 'Library & Books',
        'communication' => 'Communication & Messages',
        'payment' => 'Payments & Subscriptions',
        'system' => 'System & Settings',
        'document' => 'Document Management',
        'content' => 'Content Management',
        'settings' => 'User Settings',
        'assignment' => 'Assignments & Tasks',
        'forum' => 'Forum & Discussions',
        'notification' => 'Notifications',
    ],

    /*
     * Activity types commonly used
     */
    'activity_types' => [
        'view' => 'Viewed',
        'create' => 'Created',
        'update' => 'Updated',
        'delete' => 'Deleted',
        'download' => 'Downloaded',
        'upload' => 'Uploaded',
        'publish' => 'Published',
        'subscribe' => 'Subscribed',
        'unsubscribe' => 'Unsubscribed',
        'purchase' => 'Purchased',
        'login' => 'Logged In',
        'logout' => 'Logged Out',
        'read' => 'Read',
        'comment' => 'Commented',
        'reply' => 'Replied',
        'share' => 'Shared',
        'favorite' => 'Favorited',
        'unfavorite' => 'Unfavorited',
        'submit' => 'Submitted',
        'start' => 'Started',
        'complete' => 'Completed',
        'cancel' => 'Cancelled',
        'approve' => 'Approved',
        'reject' => 'Rejected',
        'export' => 'Exported',
    ],

    /*
     * Routes to exclude from automatic logging
     * Useful for health checks, monitoring endpoints, etc.
     */
    'exclude_routes' => [
        'api/*',
        'telescope',
        'debugbar',
        'sanctum/*',
        'health',
        'health-check',
    ],

    /*
     * Enable IP tracking
     */
    'track_ip' => env('ACTIVITY_LOG_TRACK_IP', true),

    /*
     * Enable user agent tracking
     */
    'track_user_agent' => env('ACTIVITY_LOG_TRACK_USER_AGENT', true),

    /*
     * Log system actions (not tied to a user)
     * Usually admin/system operations
     */
    'log_system_actions' => env('ACTIVITY_LOG_SYSTEM_ACTIONS', false),
];
