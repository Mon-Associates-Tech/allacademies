<?php

namespace App\Http\Middleware;

use App\Services\UserActivityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): Response $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log activities for authenticated users
        if (auth()->check()) {
            $this->logActivity($request);
        }

        return $response;
    }

    /**
     * Log the activity based on the request
     */
    protected function logActivity(Request $request): void
    {
        // Skip logging for certain paths
        if ($this->shouldSkip($request)) {
            return;
        }

        $path = $request->path();
        $method = $request->method();
        $routeName = $request->route()?->getName() ?? '';

        // Check if this is a custom route with special handling
        $customRoute = $this->getCustomRouteConfig($routeName);
        if ($customRoute) {
            $activityType = $customRoute['type'];
            $activityName = $this->parseActivityTemplate($customRoute['template'], $request);
            $category = $customRoute['category'] ?? $this->getActivityCategory($request);
        } else {
            // Determine activity type based on HTTP method
            $activityType = $this->getActivityTypeForRoute($routeName, $method);

            // Skip page views if disabled in config
            if ($activityType === 'view' && !config('activity_log.log_page_views')) {
                return;
            }

            // Get activity name and category from route
            $activityName = $this->getActivityName($request);
            $category = $this->getActivityCategory($request);
        }

        // Get additional metadata
        $metadata = $this->getMetadata($request);

        // Log the activity
        UserActivityService::log(
            $activityType,
            $activityName,
            $category,
            null,
            $metadata
        );
    }

    /**
     * Get custom route configuration if it exists
     */
    protected function getCustomRouteConfig(string $routeName): ?array
    {
        $customRoutes = config('activity_routes.custom_routes', []);
        return $customRoutes[$routeName] ?? null;
    }

    /**
     * Parse activity template with placeholders
     */
    protected function parseActivityTemplate(string $template, Request $request): string
    {
        $input = $request->all();
        $result = $template;

        // First, replace {input.key|fallback.key} placeholders (fallback syntax)
        // This must be done before simple {input.key} to avoid partial replacements
        preg_match_all('/\{input\.([^|]+)\|input\.([^}]+)\}/', $result, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $i => $placeholder) {
                $key1 = $matches[1][$i];
                $key2 = $matches[2][$i];
                $value = $input[$key1] ?? $input[$key2] ?? '';
                if ($value) {
                    $result = str_replace($placeholder, $value, $result);
                } else {
                    // Remove placeholder if no value found
                    $result = str_replace($placeholder, '', $result);
                }
            }
        }

        // Replace remaining {input.key} placeholders
        preg_match_all('/\{input\.([^}|]+)\}/', $result, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $i => $placeholder) {
                $key = $matches[1][$i];
                $value = $input[$key] ?? '';
                if ($value) {
                    $result = str_replace($placeholder, $value, $result);
                } else {
                    // Remove placeholder if no value found
                    $result = str_replace($placeholder, '', $result);
                }
            }
        }

        // Replace {resource} placeholder
        $routeName = $request->route()?->getName() ?? '';
        $resource = explode('.', $routeName)[0] ?? 'resource';
        $result = str_replace('{resource}', $this->formatResourceName($resource), $result);

        return trim(preg_replace('/\s+/', ' ', $result)); // Clean up extra spaces
    }

    /**
     * Get activity type, checking overrides first
     */
    protected function getActivityTypeForRoute(string $routeName, string $method): string
    {
        // Check for overrides
        $override = config("activity_routes.activity_type_overrides.{$routeName}");
        if ($override) {
            return $override;
        }

        // Default based on HTTP method
        return match ($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'view',
        };
    }

    /**
     * Check if we should skip logging this request
     */
    protected function shouldSkip(Request $request): bool
    {
        $skipPatterns = [
            'api/',
            'telescope',
            'debugbar',
            'sanctum',
            'logout',
            'login', // We log login separately
            'register',
            'password/reset',
            'health',
            'health-check',
            '404',
            '500',
            'ping',
            'livewire/', // Skip Livewire component updates to reduce noise
        ];

        $path = $request->path();

        foreach ($skipPatterns as $pattern) {
            if (str_starts_with($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a human-readable activity name from the request
     */
    protected function getActivityName(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';
        $method = $request->method();
        $input = $request->all();

        // Try to extract a meaningful name from the route
        $segments = explode('.', $routeName);
        $resource = $segments[0] ?? 'resource';
        $action = end($segments) ?? 'page';

        // Build descriptive names with actual data
        return match ($action) {
            'store' => $this->getCreateActivityName($resource, $input),
            'update' => $this->getUpdateActivityName($resource, $input, $request),
            'destroy' => $this->getDeleteActivityName($resource, $input, $request),
            'index' => "Viewed {$this->formatResourceName($resource)} List",
            'show' => "Viewed {$this->formatResourceName($resource)} Details",
            'create' => "Opened {$this->formatResourceName($resource)} Creation Form",
            'edit' => "Opened {$this->formatResourceName($resource)} Edit Form",
            default => match ($method) {
                'GET' => "Viewed {$this->formatRouteName($routeName)}",
                'POST' => "Created {$this->formatRouteName($routeName)}",
                'PUT', 'PATCH' => "Updated {$this->formatRouteName($routeName)}",
                'DELETE' => "Deleted {$this->formatRouteName($routeName)}",
                default => "Accessed {$this->formatRouteName($routeName)}",
            }
        };
    }

    /**
     * Generate descriptive name for role change actions
     */
    protected function getChangeRoleActivityName(array $input): string
    {
        $userName = $input['name'] ?? $input['email'] ?? 'Unknown User';
        $newRole = $input['role'] ?? 'Unknown';
        
        return "Changed role for user {$userName} to {$newRole}";
    }

    /**
     * Generate descriptive name for create actions
     */
    protected function getCreateActivityName(string $resource, array $input): string
    {
        $resourceName = $this->formatResourceName($resource);

        // Extract identifiable information based on resource type
        $identifier = match ($resource) {
            'users' => $input['name'] ?? $input['email'] ?? 'Unknown',
            'students' => $input['name'] ?? $input['enrollment_id'] ?? 'Unknown',
            'teachers' => $input['name'] ?? $input['email'] ?? 'Unknown',
            'quizzes', 'quiz' => $input['title'] ?? 'Untitled Quiz',
            'assignments' => $input['title'] ?? 'Untitled Assignment',
            'books' => $input['title'] ?? 'Untitled Book',
            'documents' => $input['file_name'] ?? $input['title'] ?? 'Unknown File',
            'groups', 'group' => $input['name'] ?? 'Unnamed Group',
            default => null,
        };

        if ($identifier) {
            return "Created {$resourceName}: {$identifier}";
        }

        return "Created {$resourceName}";
    }

    /**
     * Generate descriptive name for update actions
     */
    protected function getUpdateActivityName(string $resource, array $input, Request $request): string
    {
        $resourceName = $this->formatResourceName($resource);
        
        // Get the resource identifier
        $identifier = match ($resource) {
            'users' => $input['name'] ?? $input['email'] ?? 'Unknown',
            'students' => $input['name'] ?? $input['enrollment_id'] ?? 'Unknown',
            'teachers' => $input['name'] ?? $input['email'] ?? 'Unknown',
            'quizzes', 'quiz' => $input['title'] ?? 'Untitled Quiz',
            'assignments' => $input['title'] ?? 'Untitled Assignment',
            'books' => $input['title'] ?? 'Untitled Book',
            'groups', 'group' => $input['name'] ?? 'Unnamed Group',
            default => null,
        };

        // Check for role changes in user updates
        if ($resource === 'users' && isset($input['role'])) {
            $from = 'Unknown';
            $to = $input['role'];
            
            // Try to get the old role from session or context
            $identifier = $input['name'] ?? $input['email'] ?? 'Unknown';
            return "Updated {$resourceName} {$identifier} (role changed to: {$to})";
        }

        if ($identifier) {
            return "Updated {$resourceName}: {$identifier}";
        }

        return "Updated {$resourceName}";
    }

    /**
     * Generate descriptive name for delete actions
     */
    protected function getDeleteActivityName(string $resource, array $input, Request $request): string
    {
        $resourceName = $this->formatResourceName($resource);

        // Try to get identifier from route parameters
        $routeParams = $request->route()?->parameters() ?? [];
        $identifier = null;

        if (!empty($routeParams)) {
            $model = reset($routeParams);
            if (is_object($model) && method_exists($model, '__toString')) {
                $identifier = (string)$model;
            } elseif (is_object($model) && isset($model->name)) {
                $identifier = $model->name;
            } elseif (is_object($model) && isset($model->email)) {
                $identifier = $model->email;
            } elseif (is_object($model) && isset($model->title)) {
                $identifier = $model->title;
            }
        }

        if ($identifier) {
            return "Deleted {$resourceName}: {$identifier}";
        }

        return "Deleted {$resourceName}";
    }

    /**
     * Format resource name for display
     */
    protected function formatResourceName(string $resource): string
    {
        // Remove trailing 's' and capitalize
        $singular = rtrim($resource, 's');
        return ucfirst($singular);
    }

    /**
     * Get the activity category based on the route
     */
    protected function getActivityCategory(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';

        // Extract category from route name (e.g., 'students.index' -> 'academic')
        if (str_contains($routeName, 'quiz') || str_contains($routeName, 'assessment') || str_contains($routeName, 'assignment')) {
            return 'academic';
        }

        if (str_contains($routeName, 'book') || str_contains($routeName, 'library')) {
            return 'library';
        }

        if (str_contains($routeName, 'message') || str_contains($routeName, 'chat')) {
            return 'communication';
        }

        if (str_contains($routeName, 'payment') || str_contains($routeName, 'subscription')) {
            return 'payment';
        }

        if (str_contains($routeName, 'admin') || str_contains($routeName, 'setting')) {
            return 'settings';
        }

        return 'content';
    }

    /**
     * Format route name for display
     */
    protected function formatRouteName(string $routeName): string
    {
        return ucfirst(str_replace(['.', '_'], ' ', explode('.', $routeName)[0] ?? 'page'));
    }

    /**
     * Get additional metadata about the request
     */
    protected function getMetadata(Request $request): array
    {
        $metadata = [
            'path' => $request->path(),
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
        ];

        // Capture relevant form data based on route
        $routeName = $request->route()?->getName() ?? '';
        $input = $request->all();

        // Log user-specific data
        if (str_contains($routeName, 'users')) {
            $metadata['user_data'] = [
                'name' => $input['name'] ?? null,
                'email' => $input['email'] ?? null,
                'role' => $input['role'] ?? null,
            ];
        }

        // Log student-specific data
        if (str_contains($routeName, 'student')) {
            $metadata['student_data'] = [
                'name' => $input['name'] ?? null,
                'email' => $input['email'] ?? null,
                'enrollment_id' => $input['enrollment_id'] ?? null,
            ];
        }

        // Log teacher-specific data
        if (str_contains($routeName, 'teacher')) {
            $metadata['teacher_data'] = [
                'name' => $input['name'] ?? null,
                'email' => $input['email'] ?? null,
                'department' => $input['department'] ?? null,
            ];
        }

        // Log quiz/assessment data
        if (str_contains($routeName, 'quiz') || str_contains($routeName, 'assessment')) {
            $metadata['quiz_data'] = [
                'title' => $input['title'] ?? null,
                'subject' => $input['subject'] ?? null,
                'score' => $input['score'] ?? null,
            ];
        }

        // Log document/file uploads
        if (str_contains($routeName, 'document') || str_contains($routeName, 'upload')) {
            $metadata['file_data'] = [
                'file_name' => $input['file_name'] ?? null,
                'file_size' => $input['file_size'] ?? null,
                'mime_type' => $input['mime_type'] ?? null,
            ];
        }

        // Log payment data
        if (str_contains($routeName, 'payment') || str_contains($routeName, 'subscription')) {
            $metadata['payment_data'] = [
                'amount' => $input['amount'] ?? null,
                'currency' => $input['currency'] ?? null,
                'type' => $input['type'] ?? null,
            ];
        }

        // Log resource IDs that are being modified
        if (in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            // Try to get ID from route parameters
            $routeParams = $request->route()?->parameters() ?? [];
            if (!empty($routeParams)) {
                $metadata['resource_id'] = reset($routeParams)?->id ?? current($routeParams);
            }
        }

        return $metadata;
    }
}
