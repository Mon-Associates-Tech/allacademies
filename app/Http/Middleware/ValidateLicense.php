<?php
// app/Http/Middleware/ValidateLicense.php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;

class ValidateLicense
{
    // Paths that must never be intercepted
    protected array $excluded = [
        'license-expired',
        'license-expired/*',
        'livewire/*',          // Livewire AJAX endpoints
        '_debugbar/*',         // Laravel Debugbar if installed
        'vendor/*',            // Published vendor assets
    ];

    public function __construct(protected LicenseService $license) {}

    public function handle(Request $request, Closure $next)
    {
        // Use path matching — more reliable than routeIs() at middleware stage
        if ($request->is($this->excluded)) {
            return $next($request);
        }

        $result = $this->license->check();

        if (!$result['valid']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error'   => 'Service unavailable',
                    'message' => 'Trial period has ended. Please contact PAPS Tech Studio.',
                ], 403);
            }

            // Use a direct URL instead of named route to avoid resolution issues
            if ($request->path() !== 'license-expired') {
                return redirect(url('license-expired'));
            }
        }

        return $next($request);
    }
}