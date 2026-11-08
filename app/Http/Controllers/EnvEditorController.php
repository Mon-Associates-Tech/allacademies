<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnvEditorController extends Controller
{
    /**
     * Variables that are NEVER editable from the UI, regardless of config.
     */
    private array $lockedPrefixes = [
        'DB_',
    ];

    /**
     * Display the environment variable editor.
     */
    public function index(): View
    {
        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);
        $lines = explode("\n", $envContent);

        $config = config('env-editor');
        $editableKeys = $this->getEditableKeys($config);
        $sensitiveKeys = $config['sensitive'] ?? [];
        $restrictedKeys = $config['restricted'] ?? [];
        $groups = $config['groups'] ?? [];

        $currentUserEmail = Auth::user()->email ?? '';

        $variables = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Parse KEY=VALUE
            if (!str_contains($line, '=')) {
                continue;
            }

            $key = trim(explode('=', $line, 2)[0]);
            $rawValue = trim(explode('=', $line, 2)[1] ?? '');

            // Remove surrounding quotes if present
            $value = $this->unquote($rawValue);

            $isRestricted = array_key_exists($key, $restrictedKeys);
            $allowedEmails = $isRestricted ? ($restrictedKeys[$key] ?? []) : [];
            $isAuthorized = $isRestricted ? in_array($currentUserEmail, $allowedEmails) : true;

            $isEditable = in_array($key, $editableKeys) && !$this->isLocked($key) && $isAuthorized;
            $isSensitive = in_array($key, $sensitiveKeys);
            $isSet = $value !== '' && $rawValue !== 'null' && $rawValue !== '';
            $isLocked = $this->isLocked($key);

            $variables[] = [
                'key' => $key,
                'value' => $value,
                'raw_value' => $rawValue,
                'is_editable' => $isEditable,
                'is_sensitive' => $isSensitive,
                'is_set' => $isSet,
                'is_locked' => $isLocked,
                'is_restricted' => $isRestricted,
                'allowed_emails' => $allowedEmails,
            ];
        }

        // Organize into groups
        $grouped = $this->groupVariables($variables, $groups);

        return view('env-editor.index', [
            'variables' => $variables,
            'grouped' => $grouped,
            'editableCount' => collect($variables)->filter(fn ($v) => $v['is_editable'])->count(),
            'lockedCount' => collect($variables)->filter(fn ($v) => $v['is_locked'])->count(),
        ]);
    }

    /**
     * Update environment variables.
     */
    public function update(Request $request): RedirectResponse
    {
        $config = config('env-editor');
        $editableKeys = $this->getEditableKeys($config);
        $restrictedKeys = $config['restricted'] ?? [];

        $validated = $request->validate([
            'variables' => 'array',
            'variables.*.key' => 'required|string',
            'variables.*.value' => 'nullable|string',
        ]);

        $currentUserEmail = Auth::user()->email ?? '';

        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);
        $lines = explode("\n", $envContent);

        // Build updates map: key => value
        $updates = [];
        $skipped = [];
        foreach ($validated['variables'] as $var) {
            $key = $var['key'];

            // Skip locked keys (DB_*)
            if ($this->isLocked($key)) {
                continue;
            }

            // Skip keys not in editable whitelist
            if (!in_array($key, $editableKeys)) {
                continue;
            }

            // Check restricted keys — only authorized emails can update
            if (array_key_exists($key, $restrictedKeys)) {
                $allowedEmails = $restrictedKeys[$key] ?? [];
                if (!in_array($currentUserEmail, $allowedEmails)) {
                    $skipped[] = $key;
                    continue;
                }
            }

            $updates[$key] = $var['value'] ?? '';
        }

        if (empty($updates)) {
            if (!empty($skipped)) {
                return back()->with('error', 'Cannot update restricted variable(s): ' . implode(', ', $skipped) . '. Your email is not authorized.');
            }
            return back()->with('success', 'No changes to save.');
        }

        $newLines = [];
        $updatedKeys = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, '#') || $trimmed === '') {
                $newLines[] = $line;
                continue;
            }

            if (!str_contains($trimmed, '=')) {
                $newLines[] = $line;
                continue;
            }

            $key = trim(explode('=', $trimmed, 2)[0]);

            if (array_key_exists($key, $updates)) {
                $newLines[] = $key . '=' . $this->quote($updates[$key]);
                $updatedKeys[] = $key;
            } else {
                $newLines[] = $line;
            }
        }

        file_put_contents($envPath, implode("\n", $newLines));

        // Clear config cache so new values take effect
        \Artisan::call('config:clear');

        $successMsg = "Updated " . count($updatedKeys) . " variable(s): " . implode(', ', $updatedKeys) . '. Config cache cleared.';
        if (!empty($skipped)) {
            $successMsg .= " Skipped restricted variable(s): " . implode(', ', $skipped) . ' (not authorized).';
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Generate a copy-friendly export of selected variables for terminal use.
     */
    public function export(Request $request): \Illuminate\Http\JsonResponse
    {
        $keys = $request->input('keys', []);

        if (empty($keys)) {
            return response()->json(['error' => 'No keys provided'], 400);
        }

        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);
        $lines = explode("\n", $envContent);

        $output = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
                continue;
            }

            $key = trim(explode('=', $trimmed, 2)[0]);

            if (in_array($key, $keys)) {
                $output[] = $line;
            }
        }

        return response()->json([
            'text' => implode("\n", $output),
            'count' => count($output),
        ]);
    }

    /**
     * Get the list of editable keys from config.
     */
    private function getEditableKeys(array $config): array
    {
        $editable = $config['editable'] ?? [];
        $keys = [];

        foreach ($editable as $key => $value) {
            if (is_int($key)) {
                $keys[] = $value;
            } else {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Check if a variable is locked (never editable).
     */
    private function isLocked(string $key): bool
    {
        foreach ($this->lockedPrefixes as $prefix) {
            if (str_starts_with($key, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove surrounding quotes from a value.
     */
    private function unquote(string $value): string
    {
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];

            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                return substr($value, 1, -1);
            }
        }

        return $value;
    }

    /**
     * Quote a value if necessary.
     */
    private function quote(string $value): string
    {
        if ($value === '' || $value === 'null' || $value === 'true' || $value === 'false') {
            return $value;
        }

        // Quote if it contains spaces or special characters
        if (str_contains($value, ' ') || str_contains($value, '#') || str_contains($value, '$')) {
            return '"' . addcslashes($value, '"\\') . '"';
        }

        return $value;
    }

    /**
     * Group variables by the config groups.
     */
    private function groupVariables(array $variables, array $groups): array
    {
        $grouped = [];
        $assignedKeys = [];

        foreach ($groups as $groupName => $groupKeys) {
            $groupVars = collect($variables)->filter(function ($v) use ($groupKeys) {
                return in_array($v['key'], $groupKeys);
            })->values()->all();

            if (!empty($groupVars)) {
                $grouped[$groupName] = $groupVars;
                $assignedKeys = array_merge($assignedKeys, $groupKeys);
            }
        }

        // Remaining unassigned variables
        $unassigned = collect($variables)->filter(function ($v) use ($assignedKeys) {
            return !in_array($v['key'], $assignedKeys);
        })->values()->all();

        if (!empty($unassigned)) {
            $grouped['Other'] = $unassigned;
        }

        return $grouped;
    }
}
