<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LogViewerController extends Controller
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs/laravel.log');
    }

    public function index(Request $request): View
    {
        $lines   = (int) $request->query('lines', 500);
        $lines   = min(max($lines, 50), 5000);
        $search  = $request->query('search', '');
        $level   = $request->query('level', '');

        $content = $this->readTail($lines);

        if ($search !== '') {
            $content = array_filter($content, fn($l) => stripos($l, $search) !== false);
        }

        if ($level !== '') {
            $content = array_filter($content, fn($l) => stripos($l, ".{$level}:") !== false || stripos($l, " {$level} ") !== false);
        }

        return view('log-viewer.index', [
            'lines'   => array_values($content),
            'count'   => count($content),
            'search'  => $search,
            'level'   => $level,
            'perPage' => $lines,
            'exists'  => file_exists($this->logPath),
            'size'    => file_exists($this->logPath) ? $this->humanSize(filesize($this->logPath)) : '0 B',
        ]);
    }

    public function clear(): \Illuminate\Http\RedirectResponse
    {
        if (file_exists($this->logPath)) {
            file_put_contents($this->logPath, '');
        }

        return back()->with('success', 'Log file cleared.');
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless(file_exists($this->logPath), 404);

        return response()->download($this->logPath, 'laravel-' . now()->format('Y-m-d') . '.log');
    }

    private function readTail(int $lines): array
    {
        if (! file_exists($this->logPath)) {
            return [];
        }

        $all = file($this->logPath, FILE_IGNORE_NEW_LINES);

        return array_slice($all, -$lines);
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) return "{$bytes} {$unit}";
            $bytes = (int) ($bytes / 1024);
        }
        return "{$bytes} TB";
    }
}
