<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Application Log</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $exists ? "storage/logs/laravel.log · {$size}" : 'Log file does not exist.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('log-viewer.download') }}"
                   class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                   style="border-radius: 2px;">
                    Download
                </a>
                <form method="POST" action="{{ route('log-viewer.clear') }}"
                      onsubmit="return confirm('Clear the entire log file?')">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors"
                            style="border-radius: 2px;">
                        Clear Log
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800" style="border-radius: 2px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('log-viewer.index') }}"
              class="flex items-end gap-3 flex-wrap mb-4">
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Level</label>
                <select name="level"
                        class="px-2.5 py-1.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                        style="border-radius: 2px;">
                    <option value="">All</option>
                    @foreach(['emergency','alert','critical','error','warning','notice','info','debug'] as $lvl)
                        <option value="{{ $lvl }}" @selected($level === $lvl)>{{ ucfirst($lvl) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-48">
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Filter log lines…"
                       class="w-full px-2.5 py-1.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                       style="border-radius: 2px;">
            </div>
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Last N lines</label>
                <select name="lines"
                        class="px-2.5 py-1.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                        style="border-radius: 2px;">
                    @foreach([100, 250, 500, 1000, 2000, 5000] as $n)
                        <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-1.5 text-sm font-semibold text-white"
                    style="border-radius: 2px; background: linear-gradient(135deg, #0369a1, #38bdf8);">
                Filter
            </button>
            @if($search || $level)
                <a href="{{ route('log-viewer.index') }}"
                   class="px-3 py-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 underline">
                    Reset
                </a>
            @endif
        </form>

        {{-- Log output --}}
        <div class="bg-slate-950 text-slate-100 text-xs font-mono overflow-auto rounded"
             style="max-height: 75vh; border-radius: 2px;">
            @if($exists && count($lines))
                <div class="px-1 py-0.5 text-slate-500 border-b border-slate-800 sticky top-0 bg-slate-950">
                    Showing {{ $count }} line(s)
                </div>
                @foreach($lines as $line)
                    @php
                        $cls = 'text-slate-300';
                        if (str_contains($line, '.ERROR') || str_contains($line, '.CRITICAL') || str_contains($line, '.EMERGENCY') || str_contains($line, '.ALERT'))
                            $cls = 'text-red-400';
                        elseif (str_contains($line, '.WARNING'))
                            $cls = 'text-amber-400';
                        elseif (str_contains($line, '.INFO'))
                            $cls = 'text-sky-400';
                        elseif (str_contains($line, '.DEBUG'))
                            $cls = 'text-slate-500';
                    @endphp
                    <div class="px-4 py-0.5 hover:bg-slate-900 whitespace-pre-wrap break-all {{ $cls }}">{{ $line }}</div>
                @endforeach
            @elseif(!$exists)
                <p class="px-4 py-6 text-slate-500">Log file not found.</p>
            @else
                <p class="px-4 py-6 text-slate-500">No matching log entries.</p>
            @endif
        </div>

    </div>
</x-layouts.app>
