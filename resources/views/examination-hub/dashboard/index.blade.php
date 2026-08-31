{{-- ═══════════════════════════════════════════════════════════
     PAGE SHELL
═══════════════════════════════════════════════════════════ --}}
<x-layouts.app>
    <x-examination-hub.navigation active="dashboard"/>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Examinations Hub
                </h1>
            </div>
        </div>

        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            {{-- Total Exams --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                       style="font-size: 10px; letter-spacing: 0.1em;">Exams</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                       style="letter-spacing: -0.04em;">{{ $summary['total_exams'] }}</p>
                </div>
            </div>

            {{-- Total Submissions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-4 0V5a2 2 0 114 0v2m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                       style="font-size: 10px; letter-spacing: 0.1em;">Submissions</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                       style="letter-spacing: -0.04em;">{{ $summary['total_submissions'] }}</p>
                </div>
            </div>

            {{-- Average Score --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                       style="font-size: 10px; letter-spacing: 0.1em;">Average Score</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                       style="letter-spacing: -0.04em;">{{ $summary['avg_score'] }}<span
                            class="text-lg font-medium text-slate-500">%</span></p>
                </div>
            </div>

            {{-- Auto-Gradable --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                       style="font-size: 10px; letter-spacing: 0.1em;">Auto-Gradable</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                       style="letter-spacing: -0.04em;">{{ $summary['auto_gradable'] }}</p>
                </div>
            </div>

            {{-- Manual Review --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #92400e, #b45309); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                       style="font-size: 10px; letter-spacing: 0.1em;">Manual Review</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5"
                       style="letter-spacing: -0.04em;">{{ $summary['manual_review'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── CHARTS ROW ── --}}
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Submission Trend --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Submission Trend (Last 30 Days)</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="submissionTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Exam Status Distribution --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Exam Status Distribution</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="examStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── EXAMINATIONS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            
            {{-- Header & Filters --}}
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Recent Examinations</h2>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('examination-hub.dashboard') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title, code, or subject..." 
                               class="w-full sm:w-64 pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <select name="subject" onchange="this.form.submit()" 
                            class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        <option value="">All Subjects</option>
                        @foreach($availableSubjects as $subj)
                            <option value="{{ $subj->id }}" {{ ($filters['subject'] ?? '') == $subj->id ? 'selected' : '' }}>
                                {{ $subj->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()" 
                            class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ ($filters['status'] ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    
                    @if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['subject']))
                        <a href="{{ route('examination-hub.dashboard') }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-md transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                @php
                    $sortField = request('sort_by', 'created_at');
                    $sortDirection = request('sort_direction', 'desc');
                    
                    $toggleSort = function($field) use ($sortField, $sortDirection) {
                        $newDirection = ($sortField === $field && $sortDirection === 'asc') ? 'desc' : 'asc';
                        return request()->fullUrlWithQuery(['sort_by' => $field, 'sort_direction' => $newDirection]);
                    };
                    
                    $sortIcon = function($field) use ($sortField, $sortDirection) {
                        if ($sortField !== $field) {
                            return '<svg class="w-3 h-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
                        }
                        if ($sortDirection === 'asc') {
                            return '<svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>';
                        }
                        return '<svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
                    };
                @endphp

                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 transition-colors" style="letter-spacing: 0.08em;">
                            <a href="{{ $toggleSort('title') }}" class="flex items-center gap-1">
                                Title
                                {!! $sortIcon('title') !!}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 transition-colors" style="letter-spacing: 0.08em;">
                            <a href="{{ $toggleSort('subject') }}" class="flex items-center gap-1">
                                Subject
                                {!! $sortIcon('subject') !!}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 transition-colors" style="letter-spacing: 0.08em;">
                            <a href="{{ $toggleSort('access_code') }}" class="flex items-center gap-1">
                                Code
                                {!! $sortIcon('access_code') !!}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Sections</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Questions</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200 transition-colors" style="letter-spacing: 0.08em;">
                            <a href="{{ $toggleSort('submissions_count') }}" class="flex items-center justify-center gap-1">
                                Submissions
                                {!! $sortIcon('submissions_count') !!}
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $exam->title }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 dark:text-slate-300">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $exam->academicSubject->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                               <span class="inline-flex items-center justify-center text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                                    {{ $exam->access_code }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                {{ $exam->sections_count }}
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                {{ $exam->questions_count }}
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-700 dark:text-slate-300 font-medium">
                                {{ $exam->submissions_count }}
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors"
                                       href="{{ route('examination-hub.exams.show', $exam) }}">
                                        Open
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                    @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                        <span class="text-slate-300 dark:text-slate-600">|</span>
                                        <a class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-colors"
                                           href="{{ route('examination-hub.exams.edit', $exam) }}">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                No examinations found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $exams->withQueryString()->links() }}
            </div>
        </div>

    </div>{{-- /container --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                const tickColor = isDark ? '#94a3b8' : '#64748b';
                const tooltipBg = 'rgba(15,23,42,0.92)';

                // ── Submission Trend ───────────────────────────────
                const submissionTrend = @json($summary['submission_trend']);
                new Chart(document.getElementById('submissionTrendChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: Object.keys(submissionTrend),
                        datasets: [{
                            label: 'Submissions',
                            data: Object.values(submissionTrend),
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
                                grid: {color: gridColor},
                                ticks: {color: tickColor, stepSize: 1, font: {size: 11}}
                            },
                            x: {
                                grid: {display: false},
                                ticks: {color: tickColor, font: {size: 11}, maxRotation: 45, minRotation: 45}
                            }
                        },
                        plugins: {
                            legend: {display: false},
                            tooltip: {
                                backgroundColor: tooltipBg,
                                padding: 12,
                                cornerRadius: 2
                            }
                        }
                    }
                });

                // ── Exam Status Distribution ───────────────────────
                const statusData = @json($summary['exam_status_distribution']);
                new Chart(document.getElementById('examStatusChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: [
                                'rgba(217,119,6,0.85)',
                                'rgba(5,150,105,0.85)',
                                'rgba(100,116,139,0.85)'
                            ],
                            borderColor: [
                                'rgb(217,119,6)',
                                'rgb(5,150,105)',
                                'rgb(100,116,139)'
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
                                    font: {size: 11, family: 'system-ui'},
                                    color: tickColor,
                                    usePointStyle: true,
                                    pointStyle: 'rectRounded'
                                }
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                padding: 12,
                                cornerRadius: 2
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>