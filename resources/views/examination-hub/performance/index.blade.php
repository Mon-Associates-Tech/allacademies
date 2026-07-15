{{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examination-hub.navigation active="performance" />
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #2563eb, #60a5fa, #93c5fd);"></div>
        <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Participant Performance
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    View examination performance and metrics for all participants
                </p>
            </div>
            <div class="flex-shrink-0">
                <div class="flex gap-2">
                    <a href="{{ route('examination-hub.performance.export-all-excel') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);"
                       title="Export all participants to Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('examination-hub.performance.export-all-pdf') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 2px 8px rgba(220,38,38,0.3);"
                       title="Export all participants to PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CHARTS ROW ── --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Performance Distribution --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Performance Distribution</h2>
            </div>
            <div class="p-5">
                <div class="h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Average Grade Distribution --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Average Grade Distribution</h2>
            </div>
            <div class="p-5">
                <div class="h-64">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Performers --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Top 10 Performers</h2>
            </div>
            <div class="p-5">
                <div class="h-64">
                    <canvas id="topPerformersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PARTICIPANTS TABLE ── --}}
    <div id="participants-table" class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        
        {{-- Search Header --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <div class="w-1 h-5" style="background: linear-gradient(180deg, #0369a1, #38bdf8); border-radius: 1px;"></div>
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participants</h2>
        </div>

        <div class="p-5 border-b border-slate-50 dark:border-slate-800">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Name or Email..."
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                        style="border-radius: 2px;"
                    >
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">
                            <a href="?sort_by=name&sort_order={{ $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                               class="inline-flex items-center gap-1 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                Participant
                                @if($sortBy === 'name')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortOrder === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">
                            <a href="?sort_by=submissions&sort_order={{ $sortBy === 'submissions' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                               class="inline-flex items-center justify-center gap-1 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                Submissions
                                @if($sortBy === 'submissions')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortOrder === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">
                            <a href="?sort_by=performance&sort_order={{ $sortBy === 'performance' && $sortOrder === 'asc' ? 'desc' : 'asc' }}{{ request('search') ? '&search=' . request('search') : '' }}#participants-table" 
                               class="inline-flex items-center justify-center gap-1 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                Avg Score
                                @if($sortBy === 'performance')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortOrder === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($participants as $participant)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center"
                                         style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
                                        <span class="text-white font-semibold text-sm">
                                            {{ strtoupper(substr($participant->participant_name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                            {{ $participant->participant_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $participant->participant_email ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $typeStyle = $participant->participant_type === 'configured'
                                        ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                        : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;';
                                @endphp
                                <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                      style="border-radius: 2px; {{ $typeStyle }}">
                                    {{ ucfirst($participant->participant_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-700 dark:text-slate-300 font-medium">
                                {{ $participant->submission_count }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $pct = round($participant->avg_percentage, 1);
                                    $pctStyle = $pct >= 80
                                        ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                        : ($pct >= 60
                                            ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                            : ($pct >= 50
                                                ? 'color:#92400e;background:#fffbeb;border-color:#fde68a;'
                                                : 'color:#991b1b;background:#fef2f2;border-color:#fecaca;'));
                                @endphp
                                <span class="inline-flex items-center justify-center text-xs font-bold px-2.5 py-1 border"
                                      style="border-radius: 2px; {{ $pctStyle }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('examination-hub.performance.show', [$participant->participant_type, $participant->participant_id ?? 0]) }}" 
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                    View Performance
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                No participants found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($participants->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $participants->links() }}
            </div>
        @endif
    </div>

</div>{{-- /container --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        const tickColor   = isDark ? '#94a3b8' : '#64748b';
        const tooltipBg   = 'rgba(15,23,42,0.92)';

        // ── Performance Distribution ─────────────────────────
        const performanceData = @json($performanceDistribution);
        new Chart(document.getElementById('performanceChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(performanceData),
                datasets: [{
                    data: Object.values(performanceData),
                    backgroundColor: [
                        'rgba(5,150,105,0.85)',
                        'rgba(37,99,235,0.85)',
                        'rgba(217,119,6,0.85)',
                        'rgba(220,38,38,0.85)'
                    ],
                    borderColor: [
                        'rgb(5,150,105)',
                        'rgb(37,99,235)',
                        'rgb(217,119,6)',
                        'rgb(220,38,38)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11, family: 'system-ui' },
                            color: tickColor,
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        padding: 12,
                        cornerRadius: 2,
                        titleFont: { size: 13 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });

        // ── Grade Distribution ───────────────────────────────
        const gradeData = @json($gradeDistribution);
        new Chart(document.getElementById('gradeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['A+', 'A', 'B', 'C', 'D', 'F'],
                datasets: [{
                    label: 'Participants',
                    data: ['A+', 'A', 'B', 'C', 'D', 'F'].map(grade => gradeData[grade] || 0),
                    backgroundColor: [
                        'rgba(5,150,105,0.85)',
                        'rgba(37,99,235,0.85)',
                        'rgba(124,58,237,0.85)',
                        'rgba(217,119,6,0.85)',
                        'rgba(234,88,12,0.85)',
                        'rgba(220,38,38,0.85)'
                    ],
                    borderColor: [
                        'rgb(5,150,105)',
                        'rgb(37,99,235)',
                        'rgb(124,58,237)',
                        'rgb(217,119,6)',
                        'rgb(234,88,12)',
                        'rgb(220,38,38)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, stepSize: 1, font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        padding: 12,
                        cornerRadius: 2
                    }
                }
            }
        });

        // ── Top Performers ───────────────────────────────────
        const topPerformers = @json($topPerformers);
        new Chart(document.getElementById('topPerformersChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: topPerformers.map(p => p.participant_name.length > 15 ? p.participant_name.substring(0, 15) + '...' : p.participant_name),
                datasets: [{
                    label: 'Average Score',
                    data: topPerformers.map(p => p.avg_percentage),
                    backgroundColor: 'rgba(124,58,237,0.75)',
                    borderColor: 'rgb(124,58,237)',
                    borderWidth: 1.5,
                    borderRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, callback: v => v + '%', font: { size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        padding: 12,
                        cornerRadius: 2,
                        callbacks: {
                            label: function(context) {
                                return 'Average Score: ' + context.parsed.x + '%';
                            }
                        }
                    }
                }
            }
        });
    });

    // Smooth scroll to table if sort parameters are present
    if (window.location.hash === '#participants-table') {
        setTimeout(function() {
            document.getElementById('participants-table').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);
    }
</script>
@endpush
</x-layouts.app>