<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePayrollAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !in_array($user->role?->value ?? $user->role, ['admin', 'accountant'])) {
            abort(403, 'You do not have permission to access payroll features.');
        }
        
        return $next($request);
    }
}
