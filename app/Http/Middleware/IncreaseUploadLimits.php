<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IncreaseUploadLimits
{
    /**
     * Increase PHP upload limits for Livewire file uploads.
     * This middleware runs as early as possible in the request lifecycle
     * to ensure upload limits are set before PHP processes the request body.
     */
    public function handle(Request $request, Closure $next)
    {
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '110M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');
        ini_set('memory_limit', '256M');

        return $next($request);
    }
}
