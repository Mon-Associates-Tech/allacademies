<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BackupUsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $backupUsers = config('app.backup_users', env('BACKUP_USERS', ''));

        if (empty($backupUsers)) {
            abort(403, 'Backup access is not configured. Set BACKUP_USERS in your environment.');
        }

        $allowedEmails = array_map('trim', explode(',', $backupUsers));

        if (!$request->user() || !in_array($request->user()->email, $allowedEmails)) {
            abort(403, 'You do not have permission to access the backup feature.');
        }

        return $next($request);
    }
}
