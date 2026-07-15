<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureExamSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('exam_submission_id')) {
            return redirect()->route('examination-hub.take.join')
                ->withErrors(['error' => 'Please join the examination first.']);
        }

        return $next($request);
    }
}
