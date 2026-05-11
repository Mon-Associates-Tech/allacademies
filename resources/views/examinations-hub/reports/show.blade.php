<x-layouts.app>
    <x-examinations-hub.navigation active="reports" />

    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7 no-print"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Performance Report
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Period: {{ $data['period']['start'] }} to {{ $data['period']['end'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                            style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                    <a href="{{ route('examinations-hub.reports.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                       style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Generate New Report
                    </a>
                </div>
            </div>
        </div>

        {{-- ── TOKEN USAGE ── --}}
        @if($usage)
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">AI Tokens Used</span>
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ number_format($usage['total_tokens'] ?? 0) }} tokens
                        <span class="text-slate-400 font-normal">
                            (Prompt: {{ number_format($usage['prompt_tokens'] ?? 0) }}, 
                            Completion: {{ number_format($usage['completion_tokens'] ?? 0) }})
                        </span>
                    </span>
                </div>
            </div>
        @endif

        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Total Exams --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Exams</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $data['overview']['total_exams'] }}</p>
                </div>
            </div>

            {{-- Total Submissions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Submissions</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $data['overview']['total_submissions'] }}</p>
                </div>
            </div>

            {{-- Average Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Average Score</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($data['overview']['average_score'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>

            {{-- Completion Rate --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Completion Rate</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($data['overview']['completion_rate'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>
        </div>

        {{-- ── AI GENERATED REPORT ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">AI-Generated Analysis</h2>
            </div>
            <div class="p-5 prose prose-slate dark:prose-invert max-w-none" style="font-size: 0.875rem;">
                {!! \Illuminate\Support\Str::markdown($report) !!}
            </div>
        </div>

        {{-- ── DATA VISUALIZATIONS ── --}}
        <div class="space-y-6">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-sm" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">Data Visualizations</h2>
            </div>

            {{-- Grade Distribution --}}
            @if(!empty($data['grade_distribution']))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Grade Distribution</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="gradeDistributionChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Daily Submissions --}}
            @if(!empty($data['daily_submissions']))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Daily Submission Trends</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="dailySubmissionsChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Exam Performance --}}
            @if(!empty($data['exam_performance']))
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Exam Performance Overview</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="examPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>{{-- /container --}}

    <style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        const tickColor   = isDark ? '#94a3b8' : '#64748b';
        const tooltipBg   = 'rgba(15,23,42,0.92)';

        // ── Grade Distribution ─────────────────────────────────
        @if(!empty($data['grade_distribution']))
        new Chart(document.getElementById('gradeDistributionChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($data['grade_distribution'])) !!},
                datasets: [{
                    label: 'Number of Students',
                    data: {!! json_encode(array_values($data['grade_distribution'])) !!},
                    backgroundColor: [
                        'rgba(5,150,105,0.85)',
                        'rgba(37,99,235,0.85)',
                        'rgba(217,119,6,0.85)',
                        'rgba(234,88,12,0.85)',
                        'rgba(220,38,38,0.85)'
                    ],
                    borderColor: [
                        'rgb(5,150,105)',
                        'rgb(37,99,235)',
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
        @endif

        // ── Daily Submissions ──────────────────────────────────
        @if(!empty($data['daily_submissions']))
        new Chart(document.getElementById('dailySubmissionsChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($data['daily_submissions'])) !!},
                datasets: [{
                    label: 'Submissions',
                    data: {!! json_encode(array_values($data['daily_submissions'])) !!},
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
        @endif

        // ── Exam Performance ───────────────────────────────────
        @if(!empty($data['exam_performance']))
        const examData = {!! json_encode($data['exam_performance']) !!};
        new Chart(document.getElementById('examPerformanceChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: examData.map(e => e.title.length > 20 ? e.title.substring(0, 20) + '...' : e.title),
                datasets: [
                    {
                        label: 'Average Score (%)',
                        data: examData.map(e => e.average_score),
                        backgroundColor: 'rgba(124,58,237,0.75)',
                        borderColor: 'rgb(124,58,237)',
                        borderWidth: 1.5,
                        borderRadius: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Pass Rate (%)',
                        data: examData.map(e => e.pass_rate),
                        backgroundColor: 'rgba(5,150,105,0.75)',
                        borderColor: 'rgb(5,150,105)',
                        borderWidth: 1.5,
                        borderRadius: 2,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: tickColor, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        padding: 12,
                        cornerRadius: 2
                    }
                }
            }
        });
        @endif
    });
    </script>
</x-layouts.app>