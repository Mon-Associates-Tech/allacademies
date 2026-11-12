<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadBalancerStatus extends Command
{
    protected $signature = 'exam:lb:status';

    protected $description = 'Query the HAProxy stats socket and print a load balancer summary';

    public function handle(): int
    {
        $lbMode = config('center.lb_mode', 'off');
        $role = config('center.role', 'central');

        $this->info("Load Balancer Status");
        $this->line("Role: {$role}");
        $this->line("LB Mode: {$lbMode}");

        if ($lbMode === 'off' || $role !== 'center') {
            $this->info('Load balancer is not active on this server.');
            return self::SUCCESS;
        }

        // Try to read HAProxy stats via the Unix socket
        $socket = '/run/haproxy/admin.sock';
        if (!is_socket($socket)) {
            $this->warn("HAProxy stats socket not found at {$socket}");
            $this->info('HAProxy may not be running or the socket path is different.');
            return self::SUCCESS;
        }

        // Query backend stats
        $output = [];
        $sock = fsockopen('unix://' . $socket, -1, $errno, $errstr, 5);
        if (!$sock) {
            $this->error("Cannot connect to HAProxy socket: {$errstr}");
            return self::FAILURE;
        }

        fwrite($sock, "show stat\n");
        stream_set_timeout($sock, 5);

        $response = '';
        while (!feof($sock)) {
            $response .= fread($sock, 4096);
        }
        fclose($sock);

        // Parse CSV output
        $lines = array_filter(explode("\n", trim($response)));
        $header = null;
        $backends = [];

        foreach ($lines as $line) {
            $fields = str_getcsv($line);
            if ($header === null) {
                $header = $fields;
                continue;
            }

            $row = array_combine($header, $fields);
            $pxname = $row['pxname'] ?? '';
            $svname = $row['svname'] ?? '';

            if ($svname === 'BACKEND') {
                $backends[$pxname] = $row;
            }
        }

        $this->line('');
        $this->line('Backend Status:');

        foreach ($backends as $name => $data) {
            $status = $data['status'] ?? 'UNKNOWN';
            $weight = $data['weight'] ?? '-';
            $server_state = $data['check_status'] ?? '-';
            $check_result = $data['check_result'] ?? '-';
            $scur = $data['scur'] ?? '0';
            $qcur = $data['qcur'] ?? '0';
            $bin = $data['bin'] ?? '0';
            $bout = $data['bout'] ?? '0';
            $downtime = $data['downtime'] ?? '0';

            $this->line("  {$name}:");
            $this->line("    Status: {$status} ({$server_state})");
            $this->line("    Current sessions: {$scur}");
            $this->line("    Queue: {$qcur}");
            $this->line("    Bytes in: {$bin} / Bytes out: {$bout}");
            $this->line("    Downtime (seconds): {$downtime}");
            $this->line("    Check result: {$check_result}");
        }

        // Show active servers
        $this->line('');
        $this->line('Active Servers:');

        foreach ($lines as $line) {
            $fields = str_getcsv($line);
            if ($header === null) {
                $header = $fields;
                continue;
            }

            $row = array_combine($header, $fields);
            $svname = $row['svname'] ?? '';
            $pxname = $row['pxname'] ?? '';

            if ($svname === 'BACKEND' || $svname === 'FRONTEND' || $svname === 'STATS') {
                continue;
            }

            $status = $row['status'] ?? 'UNKNOWN';
            $scur = $row['scur'] ?? '0';
            $check_result = $row['check_result'] ?? '-';

            $this->line("  {$pxname}/{$svname}: {$status} (sessions: {$scur}, check: {$check_result})");
        }

        // Health check endpoints
        $this->line('');
        $this->line('Health Endpoints:');

        try {
            $upResponse = \Illuminate\Support\Facades\Http::timeout(5)->get('http://127.0.0.1:8081/up');
            $this->line("  /up: " . ($upResponse->successful() ? 'OK (200)' : "FAILED ({$upResponse->status()})"));
        } catch (\Throwable $e) {
            $this->line('  /up: UNREACHABLE');
        }

        return self::SUCCESS;
    }
}
