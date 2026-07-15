{{--
|──────────────────────────────────────────────────────────────────────────
| Status Page  ·  Livewire component view
| All display logic is pre-computed in StatusPage::getProcessedChecks()
| This template is purely presentational.
|──────────────────────────────────────────────────────────────────────────
--}}
<x-layouts.status>
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
    x-on:livewire:navigated.window="start()"
    class="min-h-screen pb-20"
>

    {{-- ══════════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════════ --}}
    <header class="sticky top-0 z-50 border-b border-slate-800/60 bg-slate-950/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/10 ring-1 ring-amber-500/30">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-widest text-slate-500">{{ config('app.name') }}</p>
                    <h1 class="text-sm font-semibold leading-none text-slate-100">System Status</h1>
                </div>
            </div>

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
                    <span wire:loading.remove>Refresh</span>
                    <span wire:loading>…</span>
                    <span class="font-mono text-slate-500" x-text="countdown + 's'"></span>
                </button>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ══════════════════════════════════════════════════════════
             HERO BANNER
        ══════════════════════════════════════════════════════════ --}}
        <div class="mt-8">
            <div class="relative overflow-hidden rounded-2xl border {{ $hero['border'] }} {{ $hero['bg'] }} px-6 py-8">

                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full {{ $hero['ring'] }} opacity-40 blur-3xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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

                    <div class="flex items-center gap-6 sm:shrink-0">
                        <div class="text-center">
                            <p class="font-mono text-2xl font-semibold text-slate-100">
                                {{ $operationalCount }}<span class="text-slate-500">/{{ $totalCount }}</span>
                            </p>
                            <p class="mt-0.5 text-xs uppercase tracking-wider text-slate-500">Operational</p>
                        </div>
                        <div class="h-10 w-px bg-slate-700"></div>
                        <div class="text-center">
                            <p class="font-mono text-2xl font-semibold text-slate-100">
                                {{ $avgLatency }}<span class="text-sm text-slate-500"> ms</span>
                            </p>
                            <p class="mt-0.5 text-xs uppercase tracking-wider text-slate-500">Avg Latency</p>
                        </div>
                    </div>
                </div>

                <p class="relative mt-5 border-t border-slate-800/60 pt-4 text-xs text-slate-600">
                    Last checked: <span class="font-mono text-slate-500">{{ $lastUpdated }}</span>
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION HEADING
        ══════════════════════════════════════════════════════════ --}}
        <div class="mb-5 mt-10 flex items-center gap-3">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-500">Service Health</h3>
            <div class="h-px flex-1 bg-slate-800"></div>
            <span class="font-mono text-xs text-slate-600">{{ count($processedChecks) }} checks</span>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SERVICE CARDS
        ══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-60">

            @foreach($processedChecks as $check)
            <div class="service-card {{ $check['cardStatus'] }} group relative overflow-hidden rounded-xl border border-slate-800 bg-slate-900/70 p-5 backdrop-blur-sm">

                {{-- Top accent line --}}
                <div class="absolute inset-x-0 top-0 h-px {{ $check['accentLine'] }}"></div>

                {{-- Header: icon + badge --}}
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $check['iconBg'] }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $check['iconPath'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-100">{{ $check['name'] }}</h4>
                            <p class="text-xs text-slate-500">{{ $check['key'] }}</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $check['badgeBg'] }} {{ $check['badgeText'] }} {{ $check['badgeRing'] }}">
                        <span class="relative flex h-1.5 w-1.5">
                            @unless($check['isDown'])
                            <span class="ping-ring absolute inline-flex h-full w-full rounded-full {{ $check['dotClass'] }} opacity-75"></span>
                            @endunless
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $check['dotClass'] }}"></span>
                        </span>
                        {{ $check['badgeLabel'] }}
                    </span>
                </div>

                {{-- Latency --}}
                <div class="mt-5">
                    <div class="flex items-baseline gap-1">
                        @if($check['latency'] !== null)
                            <span class="font-mono text-3xl font-semibold {{ $check['latColor'] }}">{{ $check['latency'] }}</span>
                            <span class="text-sm text-slate-500">ms</span>
                        @else
                            <span class="font-mono text-2xl font-semibold text-slate-600">—</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">response time</p>

                    <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-slate-800">
                        <div class="bar-fill h-full rounded-full {{ $check['barColor'] }}" style="width: {{ $check['barWidth'] }}%"></div>
                    </div>
                </div>

                {{-- Message --}}
                <p class="mt-4 text-xs leading-relaxed text-slate-400">{{ $check['message'] }}</p>

                {{-- Details --}}
                @if(!empty($check['details']))
                <div class="mt-3 space-y-1 border-t border-slate-800 pt-3">
                    @foreach($check['details'] as $detailKey => $detailVal)
                    <div class="flex items-center justify-between text-xs">
                        <span class="capitalize text-slate-600">{{ str_replace('_', ' ', $detailKey) }}</span>
                        <span class="font-mono text-slate-400">{{ $detailVal }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Checked at --}}
                <div class="mt-3 border-t border-slate-800 pt-2.5">
                    <p class="font-mono text-xs text-slate-700">{{ $check['checkedAtTime'] }}</p>
                </div>
            </div>
            @endforeach

        </div>

        {{-- ══════════════════════════════════════════════════════════
             SCHEDULER NOTE (local env only)
        ══════════════════════════════════════════════════════════ --}}
        @if(app()->isLocal())
        <div class="mt-8 rounded-xl border border-amber-900/40 bg-amber-950/20 p-5 text-sm">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-400">Scheduler Heartbeat Setup</p>
                    <p class="mt-1 text-slate-400">Add this to <span class="font-mono text-slate-300">app/Console/Kernel.php</span> to enable the scheduler check:</p>
                    <pre class="mt-2 overflow-x-auto rounded-lg bg-slate-900 p-3 font-mono text-xs text-slate-300">$schedule->call(fn() => Cache::put('scheduler:heartbeat', now(), 300))
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
            <p>{{ config('app.name') }} &middot; System Status</p>
            <div class="flex items-center gap-2">
                <span>Auto-refreshes every <span class="font-mono text-slate-500">30s</span></span>
                <span>&middot;</span>
                <span>Next in <span class="font-mono text-amber-600/70" x-text="countdown + 's'"></span></span>
            </div>
        </footer>

    </div>
</div>
</x-layouts.status>