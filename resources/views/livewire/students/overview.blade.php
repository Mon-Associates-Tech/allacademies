<div class="p-6">
    @if(!$student)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Student profile not found. Please contact your administrator.</p>
        </div>
    @else
        <!-- Filters / Range selector -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Dashboard</h2>
            <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1 text-sm" role="tablist" aria-label="Select time range">
                <button wire:click="$set('range','7d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='7d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">7d</button>
                <button wire:click="$set('range','30d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='30d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">30d</button>
                <button wire:click="$set('range','90d')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='90d' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">90d</button>
                <button wire:click="$set('range','term')" type="button" class="px-3 py-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $range==='term' ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">Term</button>
            </div>
        </div>

        <!-- Progress overview (Consolidated strip + Gauge) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Stat strip -->
                <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                        <p class="text-xs font-medium text-gray-500">Total</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAssignments }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                        <p class="text-xs font-medium text-gray-500">Completed</p>
                        <p class="mt-1 text-2xl font-bold text-green-600">{{ $completedAssignments }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                        <p class="text-xs font-medium text-gray-500">Ongoing</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $ongoingAssignments }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 dark:border-gray-700 p-3">
                        <p class="text-xs font-medium text-gray-500">Overdue</p>
                        <p class="mt-1 text-2xl font-bold text-red-600">{{ $overdueAssignments }}</p>
                    </div>
                </div>

                <!-- Gauge KPI -->
                <div class="flex items-center justify-center">
                    <livewire:charts.gauge-chart :value="$gaugeValue" :min="$gaugeMin" :max="$gaugeMax" :thresholds="$gaugeThresholds" center-label="Completion" height-class="h-40" />
                </div>
            </div>
        </div>

        <!-- Charts Section: Bar + Pie in a coherent grid -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 lg:col-span-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Performance by Subject</h3>
                    <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                </div>
                <livewire:charts.bar-chart :labels="$barLabels" :datasets="$barDatasets" :options="$barOptions" height-class="h-72" />
            </div>
            <div class="col-span-12 lg:col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Assignments Status</h3>
                    <span class="text-xs text-gray-500">Range: {{ strtoupper($range) }}</span>
                </div>
                <livewire:charts.pie-chart :labels="$pieLabels" :values="$pieValues" :options="$pieOptions" height-class="h-72" />
            </div>
        </div>

        <!-- Activity & Upcoming Due -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Assignments</h3>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['percentage'] >= 80 ? 'bg-green-100 text-green-800' :
                                       ($assignment['percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $assignment['percentage'] }}%
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $assignment['submitted_at']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No completed assignments yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Due Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Assignments</h3>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingDueAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['days_until_due'] <= 1 ? 'bg-red-100 text-red-800' :
                                       ($assignment['days_until_due'] <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ abs($assignment['days_until_due']) }} {{ abs($assignment['days_until_due']) == 1 ? 'day' : 'days' }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $assignment['due_date']->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming assignments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Subject Performance list (optional small details under charts) -->
        @if(count($subjectPerformance) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subject Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjectPerformance as $subject)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $subject['subject'] }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $subject['average_score'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $subject['average_score'] >= 80 ? 'bg-green-500' : ($subject['average_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $subject['average_score'] }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $subject['assignments_count'] }} assignments</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Self-Assessments -->
        @if(count($recentSelfAssessments) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Book Quizzes</h3>
                    <a href="{{ route('students.assessments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentSelfAssessments as $assessment)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-900 mb-2">{{ $assessment['book_title'] }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Score:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assessment['score'] >= 80 ? 'bg-green-100 text-green-800' :
                                       ($assessment['score'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $assessment['score'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $assessment['completed_at']->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
