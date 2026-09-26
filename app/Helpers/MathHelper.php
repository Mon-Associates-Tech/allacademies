<?php

namespace App\Helpers;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class MathHelper
{
    public static function renderMath(string $text): string
    {
        // ✅ FIXED: Use str_contains() instead of preg_match()
        // In single quotes, '\\[' is exactly the literal characters \ and [
        if (!str_contains($text, '$') && !str_contains($text, '\\[') && !str_contains($text, '\\(')) {
            return $text;
        }

        $scriptPath = resource_path('js/content/math-jax.js');

        if (!file_exists($scriptPath)) {
            Log::error("MathHelper: math-jax.js not found at {$scriptPath}");
            return $text;
        }

        $process = new Process(['node', $scriptPath]);
        $process->setInput($text);
        $process->setTimeout(15);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('MathJax Node Error: ' . $process->getErrorOutput());
            return $text;
        }

        return $process->getOutput();
    }
}
