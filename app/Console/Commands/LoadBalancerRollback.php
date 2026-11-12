<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadBalancerRollback extends Command
{
    protected $signature = 'exam:lb:rollback {--mode=off : Mode to switch to (off, local-first, central-first)}';

    protected $description = 'Switch the load balancer to a different mode or disable it entirely';

    public function handle(): int
    {
        $newMode = $this->option('mode');
        $currentMode = config('center.lb_mode', 'off');
        $role = config('center.role', 'central');

        if ($role !== 'center') {
            $this->error('Load balancer is only available on center servers.');
            return self::FAILURE;
        }

        $validModes = ['off', 'local-first', 'central-first'];
        if (!in_array($newMode, $validModes)) {
            $this->error("Invalid mode: {$newMode}. Must be one of: " . implode(', ', $validModes));
            return self::FAILURE;
        }

        if ($newMode === $currentMode) {
            $this->info("Already in mode '{$currentMode}'. Nothing to do.");
            return self::SUCCESS;
        }

        $this->info("Switching load balancer from '{$currentMode}' to '{$newMode}'");

        // Update .env
        $envPath = app()->environmentFilePath();
        if (!file_exists($envPath)) {
            $this->error("Environment file not found at {$envPath}");
            return self::FAILURE;
        }

        $envContent = file_get_contents($envPath);

        // Update or add LB_MODE
        if (preg_match('/^LB_MODE=/m', $envContent)) {
            $envContent = preg_replace('/^LB_MODE=.*/m', "LB_MODE={$newMode}", $envContent);
        } else {
            $envContent .= "\nLB_MODE={$newMode}\n";
        }

        file_put_contents($envPath, $envContent);
        $this->info("Updated .env: LB_MODE={$newMode}");

        // If switching to off, stop HAProxy and restore Nginx
        if ($newMode === 'off') {
            $this->info('Stopping HAProxy...');
            $this->executeCommand('systemctl stop haproxy 2>/dev/null || true');
            $this->executeCommand('systemctl disable haproxy 2>/dev/null || true');

            $this->info('Reloading Nginx...');
            $this->executeCommand('nginx -t && systemctl reload nginx');

            $this->info('HAProxy stopped and disabled. Nginx restored to ports 80/443.');
        } else {
            // If switching to a mode, restart HAProxy to pick up new config
            $this->info('Restarting HAProxy...');
            $this->executeCommand('systemctl restart haproxy');
        }

        // Clear Laravel caches
        $this->call('config:clear');
        $this->call('route:clear');

        $this->info("Load balancer mode changed to '{$newMode}'");
        $this->line('');
        $this->line('Note: If switching between local-first and central-first,');
        $this->line('you may need to regenerate the HAProxy config using:');
        $this->line("  bash ahpc-multiserver-setup.sh --only lb --lb-mode {$newMode}");

        return self::SUCCESS;
    }

    protected function executeCommand(string $command): void
    {
        exec($command, $output, $returnCode);
        if ($returnCode !== 0) {
            $this->warn('Command failed: ' . implode("\n", $output));
        }
    }
}
