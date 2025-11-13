<div>
    <div>
        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Assignment Performance</h1>
                <p class="text-gray-600 dark:text-gray-400">Monitor your ward's assignment submissions and academic progress</p>
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

        <!-- Ward Selection Header -->
        @if($this->wards->count() > 0)
            <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-2xl font-bold">
                            {{ $this->selectedWard ? substr($this->selectedWard->user->name, 0, 2) : '?' }}
                        </div>
                        <div class="ml-4 flex-1">
                            @if($this->selectedWard)
                                <h2 class="text-2xl font-bold">{{ $this->selectedWard->user->name }}</h2>
                                <p class="text-violet-100">
                                    {{ $this->selectedWard->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $this->selectedWard->academicLevel->name ?? 'N/A' }}
                                </p>
                            @else
                                <h2 class="text-2xl font-bold">Select a Ward</h2>
                                <p class="text-violet-100">Choose a student to view their performance</p>
                            @endif
                        </div>
                    </div>

                    <!-- Dropdown Selector (only if multiple wards) -->
                    @if($this->wards->count() > 1)
                        <div x-data="{ open: false }" class="relative" @click.away="open = false">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition-all">
                                <span class="font-medium">Switch Ward</span>
                                <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto"
                                 style="display: none;">
                                <div class="p-2">
                                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Your Wards ({{ $this->wards->count() }})
                                    </div>
                                    @foreach($this->wards as $ward)
                                        <button wire:click="selectWard({{ $ward->id }})"
                                                @click="open = false"
                                                class="w-full flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ $selectedWardId == $ward->id ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                                            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                                {{ substr($ward->user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-3 flex-1 text-left">
                                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $ward->user->name }}</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                            @if($selectedWardId == $ward->id)
                                                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assignment Type</label>
                        <select wire:model.live="selectedAssignmentType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <option value="all">All Types</option>
                            <option value="quiz">Quizzes</option>
                            <option value="examination">Examinations</option>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Assignments</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->performanceAnalytics['total_assignments'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $this->performanceAnalytics['graded_assignments'] }} graded
                            </p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                High: {{ number_format($this->performanceAnalytics['highest_score'], 1) }}%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pass Rate</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->performanceAnalytics['pass_rate'], 1) }}%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $this->performanceAnalytics['passed_count'] }}/{{ $this->performanceAnalytics['passed_count'] + $this->performanceAnalytics['failed_count'] }} passed
                            </p>
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

            <!-- Time Management Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Time Management</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">On-Time Submissions</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                            {{ number_format($this->performanceAnalytics['time_management']['on_time_rate'], 0) }}%
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $this->performanceAnalytics['time_management']['on_time'] }} on time,
                            {{ $this->performanceAnalytics['time_management']['late'] }} late
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Avg. Time Spent</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                            {{ number_format($this->performanceAnalytics['time_management']['average_time_spent'], 0) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">minutes per assignment</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Time Invested</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">
                            {{ number_format($this->performanceAnalytics['time_management']['total_time_spent'] / 60, 1) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">hours</p>
                    </div>
                </div>
            </div>
        @endif

        @if($viewMode === 'detailed')
            <!-- Detailed Assignments List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Assignment Submissions</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assignment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time Spent</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->assignmentSubmissions as $submission)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $submission->submitted_at?->format('M j, Y') ?? 'Pending' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $submission->assignment->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $submission->assignment->academicSubject->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $submission->assignment->type === 'quiz' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' }}">
                                        {{ ucfirst($submission->assignment->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @if($submission->total_marks > 0)
                                        {{ number_format(($submission->score / $submission->total_marks) * 100, 1) }}%
                                        <span class="text-xs text-gray-500">({{ $submission->score }}/{{ $submission->total_marks }})</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $submission->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $submission->time_spent_minutes ?? 0 }} min
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No assignment submissions found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $this->assignmentSubmissions->links() }}
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
                        @foreach($this->performanceAnalytics['subject_breakdown'] as $subjectData)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $subjectData['subject_name'] }}</h4>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $subjectData['count'] }} assignments</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        Average: {{ number_format($subjectData['average'], 1) }}%
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        Passed: {{ $subjectData['passed'] }}/{{ $subjectData['count'] }}
                                    </span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-violet-500 h-2 rounded-full" style="width: {{ $subjectData['average'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Assignment Type Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Assignment Type Performance</h3>
                    <div class="space-y-4">
                        @foreach($this->performanceAnalytics['assignment_type_breakdown'] as $typeData)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($typeData['type']) }}</h4>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $typeData['count'] }} submissions</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        Average: {{ number_format($typeData['average'], 1) }}%
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        Graded: {{ $typeData['graded'] }}/{{ $typeData['count'] }}
                                    </span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $typeData['average'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Monthly Performance Trend -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Monthly Trend</h3>
                    <div class="space-y-4">
                        @foreach($this->performanceAnalytics['monthly_trend'] as $month => $data)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                    </span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $data['count'] }} assignments ({{ $data['graded'] }} graded)</p>
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
