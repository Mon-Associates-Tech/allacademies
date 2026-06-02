<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LicenseService
{
    protected string $binaryPath;
    protected string $hmacSecret;

    public function __construct()
    {
        $this->binaryPath = config('app.license_binary');
        $this->hmacSecret = config('app.license_secret');
    }

    public function check(): array
    {
        return Cache::remember('license_check', now()->addHours(6), fn() => $this->runBinary());
    }

    protected function runBinary(): array
    {
        if (!file_exists($this->binaryPath)) {
            return ['valid' => false, 'reason' => 'LICENSE_MISSING'];
        }

        exec(escapeshellcmd($this->binaryPath) . ' 2>/dev/null', $lines, $exitCode);
        $response = trim($lines[0] ?? '');

        if (!$this->verifySignature($response)) {
            return ['valid' => false, 'reason' => 'SIGNATURE_INVALID'];
        }

        $status = explode('.', $response)[0];

        return [
            'valid'  => $exitCode === 0,
            'reason' => $status,
        ];
    }

    protected function verifySignature(string $response): bool
    {
        $parts = explode('.', $response, 2);
        if (count($parts) !== 2) return false;

        [$status, $sig] = $parts;
        $expected = hash_hmac('sha256', $status, $this->hmacSecret);

        return hash_equals($expected, $sig);
    }
}