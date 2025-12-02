<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Educational Chat Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the educational chat service
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4'),
        'premium_model' => env('OPENAI_PREMIUM_MODEL', 'gpt-4-turbo'),
        'timeout' => env('OPENAI_TIMEOUT', 30),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    ],

    'models' => [
        'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4'),
        'premium_model' => env('OPENAI_PREMIUM_MODEL', 'gpt-4-turbo'),
        'tts_model' => env('OPENAI_TTS_MODEL', 'gpt-4o-mini-tts'),
        'default_image_model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-1-mini'),
        'premium_image_model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-1'),

    ],

    'chat' => [
        'max_conversation_history' => env('CHAT_MAX_HISTORY', 20),
        'session_timeout' => env('CHAT_SESSION_TIMEOUT', 3600), // 1 hour
        'rate_limit' => [
            'max_requests' => env('CHAT_RATE_LIMIT_REQUESTS', 50),
            'per_minutes' => env('CHAT_RATE_LIMIT_MINUTES', 60),
        ],
    ],

    'educational' => [
        'age_groups' => [
            'elementary' => ['min' => 5, 'max' => 11],
            'middle_school' => ['min' => 12, 'max' => 14],
            'high_school' => ['min' => 15, 'max' => 18],
            'college' => ['min' => 18, 'max' => 25],
            'graduate' => ['min' => 22, 'max' => 100],
        ],

        'subjects' => [
            'mathematics' => [
                'elementary' => ['basic_arithmetic', 'shapes', 'counting', 'time_money'],
                'middle_school' => ['pre_algebra', 'geometry_basics', 'fractions', 'percentages'],
                'high_school' => ['algebra', 'geometry', 'trigonometry', 'pre_calculus'],
                'college' => ['calculus', 'linear_algebra', 'statistics', 'discrete_math'],
            ],
            'science' => [
                'elementary' => ['plants_animals', 'weather', 'simple_machines', 'states_of_matter'],
                'middle_school' => ['earth_science', 'basic_chemistry', 'basic_physics', 'life_science'],
                'high_school' => ['biology', 'chemistry', 'physics', 'environmental_science'],
                'college' => ['organic_chemistry', 'molecular_biology', 'quantum_physics', 'biochemistry'],
            ],
            'language_arts' => [
                'elementary' => ['phonics', 'basic_reading', 'simple_writing', 'storytelling'],
                'middle_school' => ['grammar', 'reading_comprehension', 'creative_writing', 'poetry_basics'],
                'high_school' => ['literature_analysis', 'essay_writing', 'rhetoric', 'advanced_grammar'],
                'college' => ['literary_theory', 'academic_writing', 'research_methods', 'critical_analysis'],
            ],
        ],

        'learning_styles' => [
            'visual' => [
                'description' => 'Learns best through seeing and visualizing',
                'strategies' => ['diagrams', 'charts', 'color_coding', 'mind_maps'],
            ],
            'auditory' => [
                'description' => 'Learns best through hearing and discussing',
                'strategies' => ['verbal_explanations', 'discussions', 'audio_content', 'repetition'],
            ],
            'kinesthetic' => [
                'description' => 'Learns best through hands-on activities',
                'strategies' => ['experiments', 'role_playing', 'physical_activities', 'real_world_applications'],
            ],
            'reading' => [
                'description' => 'Learns best through reading and writing',
                'strategies' => ['detailed_notes', 'written_exercises', 'research_projects', 'textbooks'],
            ],
        ],

        'accommodations' => [
            'simplified_language' => [
                'description' => 'Use simpler vocabulary and shorter sentences',
                'applicable_ages' => [5, 16],
            ],
            'step_by_step' => [
                'description' => 'Break complex concepts into smaller steps',
                'applicable_ages' => [5, 100],
            ],
            'examples_heavy' => [
                'description' => 'Provide multiple examples for each concept',
                'applicable_ages' => [5, 100],
            ],
            'visual_aids' => [
                'description' => 'Include descriptions of visual elements',
                'applicable_ages' => [5, 100],
            ],
            'repetition' => [
                'description' => 'Repeat key concepts in different ways',
                'applicable_ages' => [5, 18],
            ],
        ],

        'difficulty_levels' => [
            'beginner' => [
                'description' => 'Basic concepts and fundamental understanding',
                'complexity_score' => 1,
            ],
            'intermediate' => [
                'description' => 'Moderate complexity with some advanced concepts',
                'complexity_score' => 5,
            ],
            'advanced' => [
                'description' => 'Complex concepts and critical thinking',
                'complexity_score' => 9,
            ],
        ],

        'response_formats' => [
            'detailed' => [
                'description' => 'Comprehensive explanations with examples',
                'avg_length' => 800,
            ],
            'concise' => [
                'description' => 'Brief, to-the-point responses',
                'avg_length' => 300,
            ],
            'interactive' => [
                'description' => 'Engaging responses with follow-up questions',
                'avg_length' => 600,
            ],
        ],
    ],

    'security' => [
        'content_filtering' => env('CHAT_CONTENT_FILTERING', true),
        'inappropriate_content_response' => 'I understand you\'re curious, but let\'s focus on educational topics that will help with your learning goals. What would you like to learn about today?',
        'max_message_length' => env('CHAT_MAX_MESSAGE_LENGTH', 2000),
        'blocked_keywords' => [
            // Add any keywords you want to filter out
        ],
    ],

    'analytics' => [
        'track_usage' => env('CHAT_TRACK_USAGE', true),
        'track_learning_progress' => env('CHAT_TRACK_PROGRESS', true),
        'anonymous_analytics' => env('CHAT_ANONYMOUS_ANALYTICS', true),
    ],
];
