<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Academic Performance</h1>
            <p class="text-gray-600 dark:text-gray-400">Monitor your ward's academic progress and performance analytics</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="changeViewMode('overview')"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'overview' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    Overview
                </button>
                <button wire:click="changeViewMode('detailed')"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'detailed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    Detailed
                </button>
                <button wire:click="changeViewMode('analytics')"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'analytics' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    Analytics
                </button>
            </div>
        </div>
    </div>

    <!-- Ward Selection -->
    @if($this->wards->count() > 1)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Select Ward</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->wards as $ward)
                    <div wire:click="selectWard({{ $ward->id }})"
                         class="cursor-pointer p-4 rounded-lg border-2 transition-colors {{ $selectedWardId == $ward->id ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-violet-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($ward->user->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $ward->user->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($this->selectedWard)
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                    <select wire:model.live="selectedSubjectId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="">All Subjects</option>
                        @foreach($this->availableSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period</label>
                    <select wire:model.live="selectedPeriod" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="all">All Time</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assessment Type</label>
                    <select wire:model.live="selectedAssessmentType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="all">All Types</option>
                        <option value="quiz">Quizzes</option>
                        <option value="exam">Examinations</option>
                    </select>
                </div>
            </div>
        </div>

        @if($viewMode === 'overview')
            <!-- Performance Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Assessments</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->performanceAnalytics['total_assessments'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->performanceAnalytics['average_score'], 1) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pass Rate</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->performanceAnalytics['pass_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-full">
                            @if($this->performanceAnalytics['performance_trend'] === 'improving')
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            @elseif($this->performanceAnalytics['performance_trend'] === 'declining')
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Trend</p>
                            <p class="text-lg font-bold capitalize {{ $this->performanceAnalytics['performance_trend'] === 'improving' ? 'text-green-600' : ($this->performanceAnalytics['performance_trend'] === 'declining' ? 'text-red-600' : 'text-yellow-600') }}">
                                {{ $this->performanceAnalytics['performance_trend'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($viewMode === 'detailed')
            <!-- Detailed Assessments List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Assessment Details</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->assessments as $assessment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $assessment->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $assessment->academicSubject->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $assessment->quiz->name ?? $assessment->examination->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $assessment->quiz_id ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' }}">
                                            {{ $assessment->quiz_id ? 'Quiz' : 'Exam' }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $assessment->score }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $assessment->passed ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $assessment->passed ? 'Passed' : 'Failed' }}
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $this->assessments->links() }}
                </div>
            </div>
        @endif

        @if($viewMode === 'analytics')
            <!-- Analytics Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Subject Performance Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Subject Performance</h3>
                    <div class="space-y-4">
                        @foreach($this->performanceAnalytics['subject_breakdown'] as $subjectId => $data)
                            @php
                                $subject = $this->availableSubjects->firstWhere('id', $subjectId);
                            @endphp
                            @if($subject)
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</h4>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $data['count'] }} assessments</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Average: {{ number_format($data['average'], 1) }}%
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Passed: {{ $data['passed'] }}/{{ $data['count'] }}
                                        </span>
                                    </div>
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-violet-500 h-2 rounded-full" style="width: {{ $data['average'] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Monthly Performance Trend -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Monthly Trend</h3>
                    <div class="space-y-4">
                        @foreach($this->performanceAnalytics['monthly_trend'] as $month => $data)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                    </span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $data['count'] }} assessments</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($data['average'], 1) }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100">No Wards Found</h3>
            <p class="mt-1 text-gray-600 dark:text-gray-400">You don't have any wards assigned to your account.</p>
        </div>
    @endif
</div>
