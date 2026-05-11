<x-layouts.exam>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="min-h-screen py-8"
         style="font-family: 'system-ui', -apple-system, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-7">

            @if($needsEmail)
                {{-- ── EMAIL VERIFICATION CARD ── --}}
                <div class="max-w-md mx-auto">
                    <div class="overflow-hidden"
                         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                        <div class="h-1 w-full" style="background: linear-gradient(90deg, #2563eb, #60a5fa, #93c5fd);"></div>
                        <div class="px-7 py-6 text-center">
                            <div class="w-16 h-16 mx-auto flex items-center justify-center mb-4"
                                 style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                Access Your Results
                            </h2>
                            <p class="text-slate-400 mt-2 text-sm">Enter your email to view your exam results</p>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="mt-4 overflow-hidden"
                             style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                                 style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                                <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                                <h2 class="font-bold text-red-800 dark:text-red-300 text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Error</h2>
                            </div>
                            <div class="p-5">
                                <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('examinations-hub.results.index') }}" class="bg-white dark:bg-slate-900 overflow-hidden"
                          style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="p-5">
                            <label for="email" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                                style="border-radius: 2px;"
                                placeholder="your.email@example.com">
                        </div>
                        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                                    style="border-radius: 2px; background: linear-gradient(135deg, #2563eb, #60a5fa); box-shadow: 0 2px 10px rgba(37,99,235,0.3);">
                                View My Results
                            </button>
                        </div>
                    </form>
                </div>

            @else
                {{-- ── PAGE HEADER ── --}}
                <div class="overflow-hidden"
                     style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                    <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
                    <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                My Exam Results
                            </h1>
                            <p class="text-slate-400 mt-2 text-sm">{{ $email }}</p>
                        </div>
                        <a href="{{ route('examinations-hub.results.index') }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-amber-400 transition-colors">
                            Change Email
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- ── METRICS STRIP ── --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Total Submissions --}}
                    <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                             style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total Submissions</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $summary['total_submissions'] }}</p>
                        </div>
                    </div>

                    {{-- Results Released --}}
                    <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                             style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Results Released</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $summary['results_released'] }}</p>
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
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($summary['average_percentage'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                        </div>
                    </div>

                    {{-- Best Score --}}
                    <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                             style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Best Score</p>
                            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ number_format($summary['best_percentage'], 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                        </div>
                    </div>
                </div>

                {{-- ── CHARTS ROW (Conditional) --}}
                @if(false && $submissions->isNotEmpty() && isset($performanceTrend) && isset($gradeDistribution))
                <div class="grid lg:grid-cols-2 gap-6">
                    {{-- Performance Trend --}}
                    <div class="bg-white dark:bg-slate-900 overflow-hidden"
                         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Performance Trend</h2>
                        </div>
                        <div class="p-5">
                            <div class="h-64">
                                <canvas id="performanceTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Grade Distribution --}}
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
                </div>
                @endif

                {{-- ── SUBMISSIONS TABLE ── --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">All Submissions</h2>
                    </div>

                    @if($submissions->isEmpty())
                        <div class="p-8 text-center">
                            <p class="text-sm text-slate-400 dark:text-slate-500">No exam submissions found.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @foreach($submissions as $submission)
                                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-slate-900 dark:text-white">{{ $submission->assignment->title }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">by {{ $submission->assignment->user->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                                {{ $submission->submitted_at?->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-slate-900 dark:text-white">
                                                    {{ $submission->score }}/{{ $submission->total_marks }}
                                                    <span class="text-slate-500 font-normal">({{ number_format($submission->percentage, 1) }}%)</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $grade = $submission->grade;
                                                    $gradeStyle = in_array($grade, ['A+', 'A'])
                                                        ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                                        : ($grade === 'B'
                                                            ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                                            : ($grade === 'C'
                                                                ? 'color:#92400e;background:#fffbeb;border-color:#fde68a;'
                                                                : 'color:#991b1b;background:#fef2f2;border-color:#fecaca;'));
                                                @endphp
                                                <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                                      style="border-radius: 2px; {{ $gradeStyle }}">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($submission->canViewResults())
                                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                                          style="border-radius: 2px; color:#065f46;background:#ecfdf5;border-color:#a7f3d0;">
                                                        Released
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 border"
                                                          style="border-radius: 2px; color:#475569;background:#f1f5f9;border-color:#e2e8f0;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($submission->canViewResults())
                                                    <a href="{{ route('examinations-hub.results.show', ['submission' => $submission, 'email' => $email]) }}" 
                                                       class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors">
                                                        View Details
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <span class="text-xs text-slate-400 dark:text-slate-500">Not Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- /container --}}
    </div>

    {{-- ── CHARTS SCRIPT (Conditional) --}}
    @if(false && !$needsEmail && $submissions->isNotEmpty() && isset($performanceTrend) && isset($gradeDistribution))
    <script>
    (function() {
        if (window.chartsInitialized) return;
        window.chartsInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor   = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const tickColor   = isDark ? '#94a3b8' : '#64748b';
            const tooltipBg   = 'rgba(15,23,42,0.92)';

            // ── Performance Trend ─────────────────────────────
            const trendCanvas = document.getElementById('performanceTrendChart');
            if (trendCanvas && !trendCanvas.chart) {
                trendCanvas.chart = new Chart(trendCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($performanceTrend->keys()) !!},
                        datasets: [{
                            label: 'Score (%)',
                            data: {!! json_encode($performanceTrend->values()) !!},
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
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                padding: 12,
                                cornerRadius: 2,
                                callbacks: { label: ctx => 'Score: ' + ctx.parsed.y + '%' }
                            }
                        }
                    }
                });
            }

            // ── Grade Distribution ───────────────────────────
            const gradeCanvas = document.getElementById('gradeDistributionChart');
            if (gradeCanvas && !gradeCanvas.chart) {
                const gradeData = {!! json_encode($gradeDistribution) !!};
                const grades = ['A+', 'A', 'B', 'C', 'D', 'F'];
                gradeCanvas.chart = new Chart(gradeCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: grades,
                        datasets: [{
                            label: 'Count',
                            data: grades.map(g => gradeData[g] || 0),
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
            }
        });
    })();
    </script>
    @endif
</x-layouts.exam>