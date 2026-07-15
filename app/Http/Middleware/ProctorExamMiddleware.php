<?php

/**
 * Polymorphic Proctor Exam Middleware
 *
 * Attaches to any exam/quiz/assignment route. Dynamically resolves the
 * target model from route parameters, initializes a polymorphic proctoring
 * session, and intercepts frontend violation reports via AJAX.
 *
 * Usage: Route::middleware('proctor.exam:exam') or 'proctor.exam:quiz'
 * The parameter name after the colon must match a key in config('proctoring.model_mappings')
 */

namespace App\Http\Middleware;

use App\Models\Proctoring\ExamProctoringSession;
use App\Services\Proctoring\ProctoringManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProctorExamMiddleware
{
    protected ProctoringManager $manager;

    public function __construct(ProctoringManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  string  $modelKey  The config key for the model mapping (e.g., 'exam', 'quiz')
     */
    public function handle(Request $request, Closure $next, string $modelKey = 'assessment'): Response
    {
        // Allow unauthenticated participants if they have a valid exam session
        if (! Auth::check() && ! session()->has('exam_submission_id')) {
            return abort(403, 'Authentication required for proctored exams.');
        }

        $driver = $this->manager->driver();

        // Handle violation reporting via AJAX/POST (bypass session init)
        if ($request->has('proctoring_violation')) {
            return $this->handleViolationRequest($request, $driver);
        }

        // Resolve the examinable model dynamically using the modelKey
        $proctorable = $this->resolveProctorableModel($request, $modelKey);

        if (! $proctorable) {
            // Don't abort here - let Laravel's route model binding handle 404s
            // This prevents masking "route not found" with "model not found"
            return $next($request);
        }

        // Skip proctoring session initialization for unauthenticated participants
        if (! Auth::check()) {
            return $next($request);
        }

        // Initialize or retrieve existing session
        $session = $this->resolveSession($request, Auth::user(), $proctorable, $driver);

        if (! $session) {
            return abort(403, 'Invalid or expired proctoring session.');
        }

        if ($session->status === 'auto_submitted') {
            return abort(403, 'Exam auto-submitted due to excessive violations.');
        }

        // Inject session into request for downstream use
        $request->attributes->set('proctoring_session', $session);
        $request->merge(['proctoring_session_id' => $session->id]);

        return $next($request);
    }

    /**
     * Resolve the examinable model from the route using the configured mapping.
     */
    protected function resolveProctorableModel(Request $request, string $modelKey): ?\Illuminate\Database\Eloquent\Model
    {
        $mappings = config('proctoring.model_mappings', []);

        if (! isset($mappings[$modelKey])) {
            // Log error but don't break the request - let other middleware handle it
            \Log::warning("Proctoring: No model mapping configured for key: {$modelKey}");

            return null;
        }

        $modelClass = $mappings[$modelKey];

        // If route model binding already resolved the model, use it directly.
        $bound = $request->route($modelKey);
        if ($bound instanceof \Illuminate\Database\Eloquent\Model) {
            return $bound;
        }

        // Try any bound route parameter that is already a model of this class.
        $params = $request->route()?->parameters() ?? [];
        foreach ($params as $param) {
            if ($param instanceof $modelClass) {
                return $param;
            }
        }

        // Fallback: resolve by numeric id from named or first parameter.
        $id = $bound;
        if (! is_numeric($id)) {
            $firstParam = reset($params);
            $id = is_numeric($firstParam) ? $firstParam : null;
        }

        if (! $id) {
            return null;
        }

        // Use find() instead of findOrFail() to avoid throwing 404 prematurely
        return $modelClass::find($id);
    }

    protected function resolveSession(Request $request, $user, $proctorable, $driver): ?ExamProctoringSession
    {
        $sessionId = $request->input('proctoring_session_id');
        $proctorableClass = get_class($proctorable);

        if ($sessionId) {
            $session = ExamProctoringSession::find($sessionId);
            // Validate session belongs to this user and model
            if ($session
                && $session->user_id === $user->getAuthIdentifier()
                && ltrim($session->proctorable_type, '\\') === ltrim($proctorableClass, '\\')
                && $session->proctorable_id == $proctorable->getKey()
            ) {
                return $session;
            }
        }

        return $driver->initializeSession($user, $proctorable);
    }

    protected function handleViolationRequest(Request $request, $driver): Response
    {
        $session = ExamProctoringSession::find($request->input('proctoring_session_id'));

        if (! $session) {
            return response()->json(['error' => 'Invalid session'], 400);
        }

        $result = $driver->processViolation(
            $session,
            $request->input('proctoring_violation.type'),
            $request->input('proctoring_violation.metadata', [])
        );

        return response()->json($result);
    }

    public function terminate(Request $request): void
    {
        $session = $request->attributes->get('proctoring_session');
        if ($session) {
            $this->manager->driver()->terminateSession($session);
        }
    }
}
