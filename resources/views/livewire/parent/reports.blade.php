<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Reports & Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400">Generate comprehensive reports for your ward's academic performance</p>
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
        <!-- Report Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">Report Configuration</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Report Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                    <select wire:model.live="reportType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="performance">Performance Report</option>
                        <option value="attendance">Attendance Report</option>
                        <option value="assessment">Assessment Report</option>
                        <option value="summary">Summary Report</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <select wire:model.live="dateRange" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Subject Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject (Optional)</label>
                    <select wire:model.live="selectedSubjectId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="">All Subjects</option>
                        @foreach($this->availableSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Date Range -->
{{--                @if($this->dateRange === 'custom')--}}
{{--                    <div>--}}
{{--                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>--}}
{{--                        <input type="date" wire:model.live="customStartDate" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">--}}
{{--                    </div>--}}
{{--                    <div>--}}
{{--                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>--}}
{{--                        <input type="date" wire:model.live="customEndDate" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">--}}
{{--                    </div>--}}
{{--                @endif--}}
            </div>

            <div class="mt-6 flex items-center space-x-4">
                <button wire:click="generateReport"
                        class="bg-violet-500 hover:bg-violet-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Generate Report
                </button>

                @if($generatedReport)
                    <button wire:click="downloadReport('pdf')"
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Download PDF
                    </button>
                    <button wire:click="downloadReport('excel')"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Download Excel
                    </button>
                @endif
            </div>
        </div>

        <!-- Report Preview -->
        @if($showReportPreview && $generatedReport)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Report Preview</h2>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Generated on {{ $generatedReport['generated_at']->format('M d, Y \a\t h:i A') }}
                    </div>
                </div>

                <!-- Report Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        {{ ucfirst($generatedReport['report_type']) }} Report - {{ $generatedReport['ward']->user->name }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Period: {{ $generatedReport['date_range']['start']->format('M d, Y') }} - {{ $generatedReport['date_range']['end']->format('M d, Y') }}
                    </p>
                    @if($generatedReport['subject'])
                        <p class="text-gray-600 dark:text-gray-400">Subject: {{ $generatedReport['subject']->name }}</p>
                    @endif
                </div>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $generatedReport['summary']['total_assessments'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Assessments</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($generatedReport['summary']['average_score'], 1) }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Average Score</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $generatedReport['summary']['passed_count'] }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Passed</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($generatedReport['summary']['pass_rate'], 1) }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pass Rate</div>
                    </div>
                </div>

                <!-- Subject Breakdown -->
                @if($generatedReport['subject_breakdown']->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Subject Breakdown</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Passed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Failed</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($generatedReport['subject_breakdown'] as $breakdown)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $breakdown['subject']->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $breakdown['count'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format($breakdown['average'], 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                                            {{ $breakdown['passed'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                                            {{ $breakdown['failed'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Recent Assessments -->
                @if($generatedReport['assessments']->isNotEmpty())
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Recent Assessments</h4>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($generatedReport['assessments']->take(10) as $assessment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $assessment->academicSubject->name ?? 'N/A' }}
                                        </h5>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $assessment->created_at->format('M d, Y h:i A') }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $assessment->quiz ? 'Quiz' : 'Examination' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold {{ $assessment->passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($assessment->score, 1) }}%
                                        </span>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $assessment->passed ? 'Passed' : 'Failed' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
