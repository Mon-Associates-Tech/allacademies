<x-layouts.app page-name="Multi-Center Live Monitoring">
    <x-examination-hub.navigation active="live-monitoring" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
         x-data="multiCenterMonitoring({{ json_encode($initialData) }}, {{ json_encode($exams->toArray()) }})"
         x-init="init()">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                        <span class="text-slate-400">Multi-Center Sync</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white leading-snug flex items-center gap-3"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Multi-Center Live Monitoring
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Real-time candidate tracking across all examination centers
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500" x-text="'Last updated: ' + lastUpdated"></span>
                    <button @click="refreshData()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #334155, #475569);">
                        <span :class="{ 'animate-spin': loading }">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                        </span>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- ── OVERALL STATS GRID ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <template x-for="stat in overallStatsCards" :key="stat.label">
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="h-0.5 w-full" :style="'background:' + stat.gradient"></div>
                    <div class="px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                             :style="'border-radius: 2px; background:' + stat.gradient">
                            <x-heroicon-o-users class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-900 dark:text-white leading-none" x-text="stat.value"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="stat.label"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- ── FILTERS ── --}}
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Filter by Center:</label>
                <select x-model="selectedCenter"
                        class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500"
                        style="border-radius: 2px;">
                    <option value="">All Centers</option>
                    <template x-for="center in centersList" :key="center.id">
                        <option :value="center.id" x-text="center.name + ' (' + center.code + ')'"></option>
                    </template>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Filter by Exam:</label>
                <select x-model="selectedExam"
                        class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500"
                        style="border-radius: 2px;">
                    <option value="">All Exams</option>
                    <template x-for="exam in examsList" :key="exam.id">
                        <option :value="exam.id" x-text="exam.title"></option>
                    </template>
                </select>
            </div>
            <button @click="applyFilters()"
                    class="px-4 py-2 text-sm font-semibold text-white transition-all"
                    style="border-radius: 2px; background: linear-gradient(135deg, #6366f1, #818cf8);">
                Apply Filters
            </button>
        </div>

        {{-- ── CENTER CARDS ── --}}
        <template x-for="(centerData, centerId) in filteredCenters" :key="centerId">
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                {{-- Center Header --}}
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex items-center justify-center"
                             style="border-radius: 2px; background: linear-gradient(135deg, #6366f1, #818cf8);">
                            <x-heroicon-o-building-office-2 class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="centerData.center.name"></h3>
                            <p class="text-xs text-slate-500" x-text="centerData.center.code + (centerData.center.app_version ? ' • v' + centerData.center.app_version : '')"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500" x-text="centerData.center.last_push_at ? 'Pushed: ' + centerData.center.last_push_at : 'Never pushed'"></span>
                        <span :class="centerData.center.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'"
                              class="px-2 py-1 text-xs font-medium"
                              style="border-radius: 2px;"
                              x-text="centerData.center.is_active ? 'Active' : 'Inactive'">
                        </span>
                    </div>
                </div>

                {{-- Center Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 px-6 py-4">
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="centerData.stats.total_candidates"></p>
                        <p class="text-xs text-slate-500">Candidates</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600" x-text="centerData.stats.joined"></p>
                        <p class="text-xs text-slate-500">Joined</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-600" x-text="centerData.stats.in_progress"></p>
                        <p class="text-xs text-slate-500">In Progress</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600" x-text="centerData.stats.submitted"></p>
                        <p class="text-xs text-slate-500">Submitted</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-violet-600" x-text="centerData.stats.average_percentage + '%'"></p>
                        <p class="text-xs text-slate-500">Avg Score</p>
                    </div>
                </div>

                {{-- Submissions Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-t border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Candidate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Index #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Progress</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Score</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Started</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="sub in centerData.submissions" :key="sub.id">
                                <tr class="border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-4 py-2">
                                        <div>
                                            <p class="font-medium text-slate-900 dark:text-white" x-text="sub.participant_name"></p>
                                            <p class="text-xs text-slate-500" x-text="sub.exam_title"></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400" x-text="sub.index_number || '-'"></td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs font-medium"
                                              :class="statusBadgeClass(sub.status)"
                                              style="border-radius: 2px;"
                                              x-text="sub.status">
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-1.5" style="border-radius: 2px;">
                                                <div class="h-1.5 bg-indigo-500" :style="'width:' + sub.progress + '%'" style="border-radius: 2px;"></div>
                                            </div>
                                            <span class="text-xs text-slate-500" x-text="sub.answers_count + '/' + sub.total_questions"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span x-show="sub.score !== null" class="font-medium text-slate-900 dark:text-white" x-text="sub.score + '/' + sub.percentage + '%'"></span>
                                        <span x-show="sub.score === null" class="text-slate-400">-</span>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-slate-500" x-text="sub.started_at ? new Date(sub.started_at).toLocaleTimeString() : '-'"></td>
                                </tr>
                            </template>
                            <template x-if="centerData.submissions.length === 0">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        No submissions from this center yet
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- ── LOCAL (CENTRAL) SUBMISSIONS ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #10b981, #34d399);">
                        <x-heroicon-o-server class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Local (Central Server)</h3>
                        <p class="text-xs text-slate-500">Exams running directly on central</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-500"><span class="font-bold text-slate-900 dark:text-white" x-text="initialData.local?.stats?.total_candidates || 0"></span> candidates</span>
                    <span class="text-slate-500"><span class="font-bold text-emerald-600" x-text="initialData.local?.stats?.submitted || 0"></span> submitted</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-t border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Candidate</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Index #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Progress</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="sub in (initialData.local?.submissions || [])" :key="'local-' + sub.id">
                            <tr class="border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-2">
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-white" x-text="sub.participant_name"></p>
                                        <p class="text-xs text-slate-500" x-text="sub.exam_title"></p>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-400" x-text="sub.index_number || '-'"></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-medium"
                                          :class="statusBadgeClass(sub.status)"
                                          style="border-radius: 2px;"
                                          x-text="sub.status">
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-1.5" style="border-radius: 2px;">
                                            <div class="h-1.5 bg-indigo-500" :style="'width:' + sub.progress + '%'" style="border-radius: 2px;"></div>
                                        </div>
                                        <span class="text-xs text-slate-500" x-text="sub.answers_count + '/' + sub.total_questions"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <span x-show="sub.score !== null" class="font-medium text-slate-900 dark:text-white" x-text="sub.score + '/' + sub.percentage + '%'"></span>
                                    <span x-show="sub.score === null" class="text-slate-400">-</span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!(initialData.local?.submissions?.length)">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    No local submissions
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function multiCenterMonitoring(initialData, exams) {
        return {
            loading: false,
            lastUpdated: new Date().toLocaleTimeString(),
            selectedCenter: '',
            selectedExam: '',
            examsList: exams || [],
            data: initialData,

            get centersList() {
                return Object.values(this.data.centers || {}).map(c => c.center);
            },

            get filteredCenters() {
                let centers = this.data.centers || {};
                if (this.selectedCenter) {
                    centers = { [this.selectedCenter]: centers[this.selectedCenter] };
                }
                return centers;
            },

            get overallStatsCards() {
                const stats = this.data.overall_stats || {};
                return [
                    { label: 'Centers', value: this.data.active_centers || 0, gradient: 'linear-gradient(135deg, #6366f1, #818cf8)', icon: 'building' },
                    { label: 'Candidates', value: stats.total_candidates || 0, gradient: 'linear-gradient(135deg, #3b82f6, #60a5fa)', icon: 'users' },
                    { label: 'Joined', value: stats.joined || 0, gradient: 'linear-gradient(135deg, #10b981, #34d399)', icon: 'check' },
                    { label: 'In Progress', value: stats.in_progress || 0, gradient: 'linear-gradient(135deg, #f59e0b, #fbbf24)', icon: 'clock' },
                    { label: 'Submitted', value: stats.submitted || 0, gradient: 'linear-gradient(135deg, #8b5cf6, #a78bfa)', icon: 'check-circle' },
                    { label: 'Avg Score', value: (stats.average_percentage || 0) + '%', gradient: 'linear-gradient(135deg, #ec4899, #f472b6)', icon: 'chart' },
                    { label: 'Proctoring', value: stats.total_proctoring_events || 0, gradient: 'linear-gradient(135deg, #f97316, #fb923c)', icon: 'flag' },
                    { label: 'High Severity', value: stats.high_severity_events || 0, gradient: 'linear-gradient(135deg, #ef4444, #f87171)', icon: 'exclamation' },
                ];
            },

            init() {
                // Auto-refresh every 30 seconds
                setInterval(() => this.refreshData(), 30000);
            },

            refreshData() {
                this.loading = true;
                const params = new URLSearchParams();
                if (this.selectedCenter) params.set('center_id', this.selectedCenter);
                if (this.selectedExam) params.set('exam_id', this.selectedExam);

                fetch('/examinations/live-monitoring/centers/api?' + params.toString())
                    .then(r => r.json())
                    .then(data => {
                        this.data = data;
                        this.lastUpdated = new Date().toLocaleTimeString();
                    })
                    .catch(() => {})
                    .finally(() => { this.loading = false; });
            },

            applyFilters() {
                this.refreshData();
            },

            statusBadgeClass(status) {
                const classes = {
                    'in_progress': 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                    'submitted': 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                    'graded': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                    'completed': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                    'flagged': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                };
                return classes[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
            },
        }
    }
    </script>
    @endpush
</x-layouts.app>
