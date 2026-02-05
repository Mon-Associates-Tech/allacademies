<?php

/**
 * Activity Logging Route Configuration
 *
 * This file defines custom activity logging patterns for routes that don't follow
 * standard CRUD naming conventions. Add your custom routes here.
 *
 * Patterns:
 * - 'route.name' => ['type' => 'activity_type', 'template' => 'Descriptive message with {placeholders}']
 * - Use {resource} for resource name, {id} for resource ID, {input.key} for input values
 */

return [
    'custom_routes' => [
        // User-related custom routes
        'users.change-role' => [
            'type' => 'update',
            'template' => 'Changed role for user {input.name|input.email} to {input.role}',
            'category' => 'system',
        ],

        // Add more custom routes below
        // Example:
        // 'quizzes.submit' => [
        //     'type' => 'submit',
        //     'template' => 'Submitted quiz: {input.title}',
        //     'category' => 'academic',
        // ],
        //
        // 'books.add-to-reading-list' => [
        //     'type' => 'create',
        //     'template' => 'Added book to reading list: {input.title}',
        //     'category' => 'library',
        // ],
        //
        // 'documents.upload' => [
        //     'type' => 'upload',
        //     'template' => 'Uploaded document: {input.file_name}',
        //     'category' => 'document',
        // ],
    ],

    /**
     * Route name patterns for automatic categorization
     * Patterns are checked in order, first match wins
     */
    'category_patterns' => [
        'quiz' => 'academic',
        'assessment' => 'academic',
        'assignment' => 'academic',
        'exam' => 'academic',
        'lesson' => 'academic',
        'book' => 'library',
        'library' => 'library',
        'reading' => 'library',
        'message' => 'communication',
        'chat' => 'communication',
        'comment' => 'communication',
        'payment' => 'payment',
        'subscription' => 'payment',
        'invoice' => 'payment',
        'document' => 'document',
        'upload' => 'document',
        'file' => 'document',
        'admin' => 'system',
        'setting' => 'system',
        'user' => 'system',
    ],

    /**
     * Activity type overrides for specific routes
     */
    'activity_type_overrides' => [
        'users.change-role' => 'update',
        'quizzes.submit' => 'submit',
        'assignments.submit' => 'submit',
        'documents.download' => 'download',
        'books.subscribe' => 'subscribe',
        'messages.read' => 'read',
    ],
];
