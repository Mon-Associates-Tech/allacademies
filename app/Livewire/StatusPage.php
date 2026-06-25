<?php

namespace App\Livewire;

use App\Services\Health\HealthCheckService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('System Status')]
class StatusPage extends Component
{
    public array  $checks           = [];
    public string $overallStatus    = 'operational';
    public string $lastUpdated      = '';
    public bool   $isRefreshing     = false;
    public int    $operationalCount = 0;
    public int    $totalCount       = 0;
    public float  $avgLatency       = 0;

    public function mount(): void
    {
        $this->runChecks();
    }

    public function refresh(): void
    {
        $this->isRefreshing = true;
        $this->runChecks();
        $this->isRefreshing = false;
    }

    protected function runChecks(): void
    {
        $this->checks      = app(HealthCheckService::class)->runAll();
        $this->lastUpdated = now()->format('M j, Y \a\t g:i:s A T');
        $this->computeSummary();
    }

    protected function computeSummary(): void
    {
        $statuses  = collect($this->checks)->pluck('status');
        $latencies = collect($this->checks)->pluck('latency')->filter()->values();

        $this->totalCount       = count($this->checks);
        $this->operationalCount = $statuses->filter(fn ($s) => $s === 'operational')->count();
        $this->avgLatency       = $latencies->count() ? round($latencies->avg(), 1) : 0;

        $this->overallStatus = match (true) {
            $statuses->contains('down')     => 'down',
            $statuses->contains('degraded') => 'degraded',
            default                         => 'operational',
        };
    }

    // ── View data helpers ──────────────────────────────────────────────────

    public function getHeroData(): array
    {
        return match ($this->overallStatus) {
            'down' => [
                'bg'     => 'bg-red-950/40',
                'border' => 'border-red-800/50',
                'dot'    => 'bg-red-500',
                'ring'   => 'bg-red-500/30',
                'text'   => 'text-red-400',
                'label'  => 'Service Disruption Detected',
                'sub'    => 'One or more critical services are experiencing issues.',
            ],
            'degraded' => [
                'bg'     => 'bg-amber-950/40',
                'border' => 'border-amber-800/50',
                'dot'    => 'bg-amber-400',
                'ring'   => 'bg-amber-400/30',
                'text'   => 'text-amber-400',
                'label'  => 'Partial System Degradation',
                'sub'    => 'Some services are operating below expected performance.',
            ],
            default => [
                'bg'     => 'bg-emerald-950/30',
                'border' => 'border-emerald-800/40',
                'dot'    => 'bg-emerald-400',
                'ring'   => 'bg-emerald-400/30',
                'text'   => 'text-emerald-400',
                'label'  => 'All Systems Operational',
                'sub'    => 'Every service is running smoothly.',
            ],
        };
    }

    public function getProcessedChecks(): array
    {
        return collect($this->checks)->map(function (array $check) {
            $status = $check['status'];
            $isOp   = $status === 'operational';
            $isDeg  = $status === 'degraded';
            $lat    = $check['latency'];

            return array_merge($check, [
                'isOp'   => $isOp,
                'isDeg'  => $isDeg,
                'isDown' => $status === 'down',

                // Badge / dot colours
                'dotClass'      => $isOp ? 'bg-emerald-400' : ($isDeg ? 'bg-amber-400' : 'bg-red-500'),
                'badgeBg'       => $isOp ? 'bg-emerald-400/10' : ($isDeg ? 'bg-amber-400/10' : 'bg-red-500/10'),
                'badgeText'     => $isOp ? 'text-emerald-400' : ($isDeg ? 'text-amber-400' : 'text-red-400'),
                'badgeRing'     => $isOp ? 'ring-emerald-400/20' : ($isDeg ? 'ring-amber-400/20' : 'ring-red-500/20'),
                'badgeLabel'    => $isOp ? 'Operational' : ($isDeg ? 'Degraded' : 'Down'),

                // Icon container colour
                'iconBg'        => $isOp ? 'bg-emerald-500/10 text-emerald-400' : ($isDeg ? 'bg-amber-500/10 text-amber-400' : 'bg-red-500/10 text-red-400'),

                // Top accent line
                'accentLine'    => $isOp ? 'bg-emerald-500/40' : ($isDeg ? 'bg-amber-500/40' : 'bg-red-500/40'),

                // Card hover class
                'cardStatus'    => $status,

                // Latency colour
                'latColor'      => $lat === null ? 'text-slate-600' : ($lat < 50 ? 'text-emerald-400' : ($lat < 200 ? 'text-amber-400' : 'text-red-400')),

                // Latency bar
                'barWidth'      => $lat !== null ? min(100, (int) round(($lat / 500) * 100)) : 0,
                'barColor'      => $isOp ? 'bg-emerald-500' : ($isDeg ? 'bg-amber-500' : 'bg-red-500'),

                // SVG icon path
                'iconPath'      => $this->iconPath($check['key']),

                // Formatted checked_at
                'checkedAtTime' => isset($check['checked_at'])
                    ? Carbon::parse($check['checked_at'])->format('H:i:s')
                    : '--:--:--',
            ]);
        })->all();
    }

    private function iconPath(string $key): string
    {
        return match ($key) {
            'cache'         => 'M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3m-13.5 0v-3.375c0-.621.504-1.125 1.125-1.125h11.25c.621 0 1.125.504 1.125 1.125V14.25m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3M3 8.625V7.5a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v1.125',
            'queue'         => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z',
            'scheduler'     => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            'mail'          => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
            'notifications' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0',
            'storage'       => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
            default         => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
        };
    }

    public function render()
    {
        return view('components.layouts.status', [
            'hero'             => $this->getHeroData(),
            'processedChecks'  => $this->getProcessedChecks(),
            'overallStatus'    => $this->overallStatus,
            'checks'           => $this->checks,
            'lastUpdated'      => $this->lastUpdated,
            'operationalCount' => $this->operationalCount,
            'totalCount'       => $this->totalCount,
            'avgLatency'       => $this->avgLatency,
        ]);
    }
}