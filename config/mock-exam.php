<?php

return [
    'monitoring' => [
        'idle_threshold_minutes' => env('MOCK_EXAM_IDLE_THRESHOLD', 3),
        'default_poll_interval' => env('MOCK_EXAM_POLL_INTERVAL', 15000),
    ],
];
