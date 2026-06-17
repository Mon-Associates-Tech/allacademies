use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Finder\Finder;

Route::get('/secure-cleanup', function () {
    // 1. Strict Authentication Check
    if (!Auth::check()) {
        abort(403, 'Unauthorized: You must be logged in.');
    }

    $user = Auth::user();
    $allowedEmail = env('CLEANUP_ADMIN_EMAIL');
    $allowedId = (int) env('CLEANUP_ADMIN_ID');

    // Abort if the authenticated user does not match the specific criteria
    if ($user->email !== $allowedEmail || $user->id !== $allowedId) {
        abort(403, 'Unauthorized: Insufficient privileges for this action.');
    }

    // 2. Safety First: Set to false ONLY after verifying the dry-run logs
    $dryRun = true; 
    
    $baseDirectory = base_path(); // Starts at the root of the Laravel app
    $suspiciousFiles = [];
    $deletedFiles = [];

    try {
        // 3. Use Symfony Finder (built into Laravel) to safely search
        $finder = new Finder();
        $finder->in($baseDirectory)
               ->ignoreDotFiles(false) // Ensure .htaccess is not ignored
               ->ignoreVCS(true)       // Ignore .git directories
               ->name('*.htaccess')
               ->name('*.php')
               ->filter(function (\Symfony\Component\Finder\SplFileInfo $file) {
                   // Check if the file size is exactly 127 bytes
                   return $file->getSize() === 127;
               });

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            $suspiciousFiles[] = $filePath;

            if (!$dryRun) {
                // WARNING: Ensure you have backups before enabling deletion
                if (File::delete($filePath)) {
                    $deletedFiles[] = $filePath;
                    Log::warning("Security Cleanup: Deleted suspicious 127-byte file", ['path' => $filePath]);
                } else {
                    Log::error("Security Cleanup: Failed to delete file", ['path' => $filePath]);
                }
            }
        }

        // 4. Return a safe response
        if ($dryRun) {
            return response()->json([
                'message' => 'Dry Run Complete. No files were deleted. Review the paths below and in your Laravel logs before setting $dryRun = false.',
                'suspicious_files_found' => $suspiciousFiles,
                'dry_run' => true
            ]);
        }

        return response()->json([
            'message' => 'Cleanup executed.',
            'deleted_files' => $deletedFiles,
            'dry_run' => false
        ]);

    } catch (\Exception $e) {
        Log::error('Security Cleanup Error: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred during the cleanup process. Check logs.'], 500);
    }
})->middleware('web'); // Ensure web middleware is applied for session/auth
