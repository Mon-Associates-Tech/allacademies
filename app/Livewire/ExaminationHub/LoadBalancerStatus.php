<?php

namespace App\Livewire\ExaminationHub;

use Livewire\Component;

class LoadBalancerStatus extends Component
{
    public string $lbMode = 'off';
    public string $role = 'central';
    public bool $isCenter = false;

    public array $localBackend = [
        'status' => 'unknown',
        'sessions' => 0,
        'check' => '-',
    ];

    public array $centralBackend = [
        'status' => 'unknown',
        'sessions' => 0,
        'check' => '-',
    ];

    public string $healthUp = 'unknown';
    public string $healthLb = 'unknown';
    public string $lastChecked = '-';

    public function mount(): void
    {
        $this->lbMode = config('center.lb_mode', 'off');
        $this->role = config('center.role', 'central');
        $this->isCenter = $this->role === 'center';
        $this->refresh();
    }

    public function refresh(): void
    {
        if (!$this->isCenter || $this->lbMode === 'off') {
            return;
        }

        $this->readHaproxyStats();
        $this->checkHealthEndpoints();
        $this->lastChecked = now()->format('H:i:s');
    }

    public function rollback(): void
    {
        $this->dispatch('rollback-initiated');
    }

    protected function readHaproxyStats(): void
    {
        $socket = '/run/haproxy/admin.sock';
        if (!is_socket($socket)) {
            return;
        }

        $sock = @fsockopen('unix://' . $socket, -1, $errno, $errstr, 5);
        if (!$sock) {
            return;
        }

        fwrite($sock, "show stat\n");
        stream_set_timeout($sock, 5);

        $response = '';
        while (!feof($sock)) {
            $response .= fread($sock, 4096);
        }
        fclose($sock);

        $lines = array_filter(explode("\n", trim($response)));
        $header = null;

        foreach ($lines as $line) {
            $fields = str_getcsv($line);
            if ($header === null) {
                $header = $fields;
                continue;
            }

            $row = array_combine($header, $fields);
            $pxname = $row['pxname'] ?? '';
            $svname = $row['svname'] ?? '';

            if ($svname === 'BACKEND' && $pxname === 'local') {
                $this->localBackend = [
                    'status' => $row['status'] ?? 'unknown',
                    'sessions' => (int) ($row['scur'] ?? 0),
                    'check' => $row['check_result'] ?? '-',
                ];
            }

            if ($svname === 'BACKEND' && $pxname === 'central') {
                $this->centralBackend = [
                    'status' => $row['status'] ?? 'unknown',
                    'sessions' => (int) ($row['scur'] ?? 0),
                    'check' => $row['check_result'] ?? '-',
                ];
            }
        }
    }

    protected function checkHealthEndpoints(): void
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get('http://127.0.0.1:8081/up');
            $this->healthUp = $response->successful() ? 'ok' : 'error';
        } catch (\Throwable $e) {
            $this->healthUp = 'unreachable';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get('http://127.0.0.1:8081/lb-health');
            $this->healthLb = $response->successful() ? 'ok' : 'overloaded';
        } catch (\Throwable $e) {
            $this->healthLb = 'unreachable';
        }
    }

    public function render()
    {
        return view('livewire.examination-hub.load-balancer-status');
    }
}
