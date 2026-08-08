{{--
|──────────────────────────────────────────────────────────────────────────
| Status Page  ·  Livewire component view
| Polls every 30 seconds. Manual refresh available via button.
|──────────────────────────────────────────────────────────────────────────
--}}
<div
    wire:poll.30000ms="refresh"
    x-data="{
        countdown: 30,
        timer: null,
        start() {
            clearInterval(this.timer);
            this.countdown = 30;
            this.timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) this.countdown = 30;
            }, 1000);
        }
    }"
    x-init="start()"
    x-on:livewire:update.window="start()"
    class="min-h-screen pb-20"
>
    {{-- ══════════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════════ --}}
    <header class="sticky top-0 z-50 border-b border-slate-800/60 bg-slate-950/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/10 ring-1 ring-amber-500/30">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-slate-500">{{ config('app.name') }}</p>
                    <h1 class="text-sm font-semibold text-slate-100 leading-none">System Status</h1>
                </div>
            </div>

            {{-- Right: env badge + refresh --}}
            <div class="flex items-center gap-3">
                <span class="hidden rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-medium uppercase tracking-wider text-slate-400 sm:inline-flex">
                    {{ app()->environment() }}
                </span>

                <button
                    wire:click="refresh"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-medium text-slate-300 transition hover:border-amber-500/40 hover:bg-slate-700 hover:text-amber-400 disabled:opacity-50"
                >
                    <svg wire:loading.class="animate-spin" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span wire:loading.class="opacity-0">Refresh</span>
                    <span wire:loading class="absolute">…</span>
                    <span class="font-mono text-slate-500" x-text="`${countdown}s`"></span>
                </button>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ══════════════════════════════════════════════════════════
             OVERALL STATUS HERO BANNER
        ══════════════════════════════════════════════════════════ --}}
        <div class="mt-8">
            @php
                $hero = match($overallStatus) {
                    'down'     => ['bg' => 'bg-red-950/40',    'border' => 'border-red-800/50',    'dot' => 'bg-red-500',    'ring' => 'bg-red-500/30',    'text' => 'text-red-400',    'label' => 'Service Disruption Detected',   'sub' => 'One or more critical services are experiencing issues.'],
                    'degraded' => ['bg' => 'bg-amber-950/40',  'border' => 'border-amber-800/50',  'dot' => 'bg-amber-400',  'ring' => 'bg-amber-400/30',  'text' => 'text-amber-400',  'label' => 'Partial System Degradation',    'sub' => 'Some services are operating below expected performance.'],
                    default    => ['bg' => 'bg-emerald-950/30', 'border' => 'border-emerald-800/40', 'dot' => 'bg-emerald-400', 'ring' => 'bg-emerald-400/30', 'text' => 'text-emerald-400', 'label' => 'All Systems Operational',     'sub' => 'Every service is running smoothly.'],
                };
            @endphp

            <div class="relative overflow-hidden rounded-2xl border {{ $hero['border'] }} {{ $hero['bg'] }} px-6 py-8">
                {{-- ambient glow --}}
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full {{ $hero['ring'] }} blur-3xl opacity-40"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Status indicator --}}
                    <div class="flex items-center gap-4">
                        <div class="relative flex h-14 w-14 shrink-0 items-center justify-center">
                            <span class="ping-ring absolute inline-flex h-full w-full rounded-full {{ $hero['ring'] }}"></span>
                            <span class="relative inline-flex h-7 w-7 rounded-full {{ $hero['dot'] }} shadow-lg"></span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight {{ $hero['text'] }}">{{ $hero['label'] }}</h2>
                            <p class="mt-0.5 text-sm text-slate-400">{{ $hero['sub'] }}</p>
                        </div>
                    </div>

                    {{-- Quick stats --}}
                    <div class="flex items-center gap-6 sm:shrink-0">
                        <div class="text-center">
                            <p class="font-mono text-2xl font-semibold text-slate-100">{{ $operationalCount }}<span class="text-slate-500">/{{ $totalCount }}</span></p>
                            <p class="mt-0.5 text-xs uppercase tracking-wider text-slate-500">Operational</p>
                        </div>
                        <div class="h-10 w-px bg-slate-700"></div>
                        <div class="text-center">
                            <p class="font-mono text-2xl font-semibold text-slate-100">{{ $avgLatency }}<span class="text-sm text-slate-500"> ms</span></p>
                            <p class="mt-0.5 text-xs uppercase tracking-wider text-slate-500">Avg Latency</p>
                        </div>
                    </div>
                </div>

                {{-- Last updated --}}
                <p class="relative mt-5 text-xs text-slate-600 border-t border-slate-800/60 pt-4">
                    Last checked: <span class="text-slate-500 font-mono">{{ $lastUpdated }}</span>
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION LABEL
        ══════════════════════════════════════════════════════════ --}}
        <div class="mt-10 mb-5 flex items-center gap-3">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-500">Service Health</h3>
            <div class="h-px flex-1 bg-slate-800"></div>
            <span class="text-xs text-slate-600 font-mono">{{ count($checks) }} checks</span>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SERVICE CARDS GRID
        ══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-60">

            @foreach($checks as $check)
            @php
                $isOp  = $check['status'] === 'operational';
                $isDeg = $check['status'] === 'degraded';
                $isDown= $check['status'] === 'down';

                $statusColor = $isOp ? 'emerald' : ($isDeg ? 'amber' : 'red');

                $statusMeta = match($check['status']) {
                    'down'     => ['dot' => 'bg-red-500',    'badge_bg' => 'bg-red-500/10',    'badge_text' => 'text-red-400',    'badge_ring' => 'ring-red-500/20',    'label' => 'Down'],
                    'degraded' => ['dot' => 'bg-amber-400',  'badge_bg' => 'bg-amber-400/10',  'badge_text' => 'text-amber-400',  'badge_ring' => 'ring-amber-400/20',  'label' => 'Degraded'],
                    default    => ['dot' => 'bg-emerald-400','badge_bg' => 'bg-emerald-400/10','badge_text' => 'text-emerald-400','badge_ring' => 'ring-emerald-400/20','label' => 'Operational'],
                };

                // Icons (inline heroicons)
                $icons = [
                    'database'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>',
                    'cache'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3m-13.5 0v-3.375c0-.621.504-1.125 1.125-1.125h11.25c.621 0 1.125.504 1.125 1.125V14.25m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3M3 8.625V7.5a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v1.125"/>',
                    'queue'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>',
                    'scheduler'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
                    'mail'          => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>',
                    'notifications' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>',
                    'storage'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25 4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.25c0 2.278-3.694 4.125-8.25 4.125S3.75 10.902 3.75 8.625"/>',
                ];

                $iconPaths = [
                    'database'      => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
                    'cache'         => 'M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3m-13.5 0v-3.375c0-.621.504-1.125 1.125-1.125h11.25c.621 0 1.125.504 1.125 1.125V14.25m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3M3 8.625V7.5a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v1.125',
                    'queue'         => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z',
                    'scheduler'     => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'mail'          => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
                    'notifications' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0',
                    'storage'       => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                ];
                $iconPath = $iconPaths[$check['key']] ?? $iconPaths['database'];

                // Latency colour-coding
                $lat = $check['latency'];
                $latColor = match(true) {
                    $lat === null => 'text-slate-600',
                    $lat < 50    => 'text-emerald-400',
                    $lat < 200   => 'text-amber-400',
                    default      => 'text-red-400',
                };

                // Latency bar width (capped at 500 ms = 100%)
                $barWidth  = $lat !== null ? min(100, round(($lat / 500) * 100)) : 0;
                $barColor  = $isOp ? 'bg-emerald-500' : ($isDeg ? 'bg-amber-500' : 'bg-red-500');
            @endphp

            <div class="service-card {{ $check['status'] }} group relative overflow-hidden rounded-xl border border-slate-800 bg-slate-900/70 p-5 backdrop-blur-sm">

                {{-- Subtle top accent line --}}
                <div class="absolute inset-x-0 top-0 h-px {{ $isOp ? 'bg-emerald-500/40' : ($isDeg ? 'bg-amber-500/40' : 'bg-red-500/40') }}"></div>

                {{-- Header row: icon + status badge --}}
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2.5">
                        {{-- Icon --}}
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                            {{ $isOp ? 'bg-emerald-500/10 text-emerald-400' : ($isDeg ? 'bg-amber-500/10 text-amber-400' : 'bg-red-500/10 text-red-400') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-100">{{ $check['name'] }}</h4>
                            <p class="text-xs text-slate-500">{{ $check['key'] }}</p>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1
                        {{ $statusMeta['badge_bg'] }} {{ $statusMeta['badge_text'] }} {{ $statusMeta['badge_ring'] }}">
                        <span class="relative flex h-1.5 w-1.5">
                            @if(!$isDown)
                            <span class="ping-ring absolute inline-flex h-full w-full rounded-full {{ $statusMeta['dot'] }} opacity-75"></span>
                            @endif
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>
                        </span>
                        {{ $statusMeta['label'] }}
                    </span>
                </div>

                {{-- Latency metric --}}
                <div class="mt-5">
                    <div class="flex items-baseline gap-1">
                        @if($check['latency'] !== null)
                            <span class="font-mono text-3xl font-semibold {{ $latColor }}">{{ $check['latency'] }}</span>
                            <span class="text-sm text-slate-500">ms</span>
                        @else
                            <span class="font-mono text-2xl font-semibold text-slate-600">—</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">response time</p>

                    {{-- Latency bar --}}
                    <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-slate-800">
                        <div class="bar-fill h-full rounded-full {{ $barColor }}" style="width: {{ $barWidth }}%"></div>
                    </div>
                </div>

                {{-- Message --}}
                <p class="mt-4 text-xs leading-relaxed text-slate-400">{{ $check['message'] }}</p>

                {{-- Details (collapsed, expand on hover via group) --}}
                @if(!empty($check['details']))
                <div class="mt-3 space-y-1 border-t border-slate-800 pt-3">
                    @foreach($check['details'] as $key => $value)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                        <span class="font-mono text-slate-400">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Footer: last checked --}}
                <div class="mt-3 border-t border-slate-800 pt-2.5">
                    <p class="text-xs text-slate-700 font-mono">
                        {{ \Carbon\Carbon::parse($check['checked_at'])->format('H:i:s') }}
                    </p>
                </div>
            </div>
            @endforeach

        </div>{{-- /grid --}}

        {{-- ══════════════════════════════════════════════════════════
             SCHEDULER HEARTBEAT SETUP NOTE
             (only visible in local/development)
        ══════════════════════════════════════════════════════════ --}}
        @if(app()->isLocal())
        <div class="mt-8 rounded-xl border border-amber-900/40 bg-amber-950/20 p-5 text-sm">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-400">Scheduler Heartbeat Setup</p>
                    <p class="mt-1 text-slate-400">Add this to your <span class="font-mono text-slate-300">app/Console/Kernel.php</span> to enable the scheduler check:</p>
                    <pre class="mt-2 overflow-x-auto rounded-lg bg-slate-900 p-3 text-xs text-slate-300 font-mono">$schedule->call(fn() => Cache::put('scheduler:heartbeat', now(), 300))
         ->everyMinute()
         ->name('scheduler:heartbeat')
         ->withoutOverlapping();</pre>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════
             FOOTER
        ══════════════════════════════════════════════════════════ --}}
        <footer class="mt-16 flex flex-col items-center justify-between gap-3 border-t border-slate-800 pt-6 text-xs text-slate-600 sm:flex-row">
            <p>{{ config('app.name') }} · System Status Page</p>
            <div class="flex items-center gap-2">
                <span>Auto-refreshes every</span>
                <span class="font-mono text-slate-500">30s</span>
                <span>·</span>
                <span>Next in</span>
                <span class="font-mono text-amber-600/70" x-text="`${countdown}s`"></span>
            </div>
        </footer>

    </div>{{-- /max-w-7xl --}}
</div>