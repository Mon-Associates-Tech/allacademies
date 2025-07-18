<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Terminal Reports</h1>
            <p class="text-gray-600 dark:text-gray-400">Generate comprehensive terminal reports for your ward's academic performance</p>
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
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">Terminal Report Configuration</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Term Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Term</label>
                    <select wire:model.live="selectedTerm" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        @foreach($this->availableTerms as $key => $term)
                            <option value="{{ $key }}">{{ $term }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                    <select wire:model.live="selectedYear" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        @foreach($this->availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-3">
                    <button wire:click="generateTerminalReport"
                            class="bg-violet-500 hover:bg-violet-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Generate Report
                    </button>

                    @if($generatedReport)
                        <button wire:click="downloadTerminalReport('pdf')"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            PDF
                        </button>
                        <button wire:click="printTerminalReport"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Terminal Report Preview -->
        @if($showReportPreview && $generatedReport)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <!-- Report Header -->
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 text-white p-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold mb-2">ALL ACADEMIES</h2>
                        <h3 class="text-xl font-semibold mb-4">TERMINAL REPORT</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <strong>Student:</strong> {{ $generatedReport['ward']->user->name }}
                            </div>
                            <div>
                                <strong>Term:</strong> {{ $this->availableTerms[$generatedReport['term']] }}
                            </div>
                            <div>
                                <strong>Year:</strong> {{ $generatedReport['year'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Student Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Student Information</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Name:</strong> {{ $generatedReport['ward']->user->name }}</div>
                                <div><strong>Email:</strong> {{ $generatedReport['ward']->user->email }}</div>
                                <div><strong>Student ID:</strong> {{ $generatedReport['ward']->student_id ?? 'N/A' }}</div>
                                <div><strong>Class:</strong> {{ $generatedReport['ward']->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $generatedReport['ward']->academicLevel->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Overall Performance</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Overall Grade:</strong> <span class="text-lg font-bold text-violet-600">{{ $generatedReport['overall_summary']['overall_grade'] }}</span></div>
                                <div><strong>Average Score:</strong> {{ number_format($generatedReport['overall_summary']['average_score'], 1) }}%</div>
                                <div><strong>Class Rank:</strong> {{ $generatedReport['overall_summary']['class_rank'] }}</div>
                                <div><strong>Attendance:</strong> {{ $generatedReport['overall_summary']['attendance_rate'] }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Performance -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Subject Performance</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Highest</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remarks</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($generatedReport['subjects'] as $subject)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $subject['subject']->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $subject['total_assessments'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format($subject['average_score'], 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format($subject['highest_score'], 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $subject['grade'] >= 'B' ? 'bg-green-100 text-green-800' :
                                                       ($subject['grade'] >= 'C' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $subject['grade'] }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $subject['remarks'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Performance Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Academic Summary</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Total Assessments:</span>
                                    <span class="font-medium">{{ $generatedReport['overall_summary']['total_assessments'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Passed:</span>
                                    <span class="font-medium text-green-600">{{ $generatedReport['overall_summary']['passed_assessments'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Failed:</span>
                                    <span class="font-medium text-red-600">{{ $generatedReport['overall_summary']['failed_assessments'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Highest Score:</span>
                                    <span class="font-medium">{{ number_format($generatedReport['overall_summary']['highest_score'], 1) }}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Lowest Score:</span>
                                    <span class="font-medium">{{ number_format($generatedReport['overall_summary']['lowest_score'], 1) }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Additional Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Conduct Grade:</span>
                                    <span class="font-medium">{{ $generatedReport['overall_summary']['conduct_grade'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Attendance Rate:</span>
                                    <span class="font-medium">{{ $generatedReport['overall_summary']['attendance_rate'] }}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Class Rank:</span>
                                    <span class="font-medium">{{ $generatedReport['overall_summary']['class_rank'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Next Term Begins:</span>
                                    <span class="font-medium">{{ $generatedReport['next_term_begins'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Teacher's Comments</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $generatedReport['teacher_comments'] }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Parent's Comments</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @if($generatedReport['parent_comments'])
                                    {{ $generatedReport['parent_comments'] }}
                                @else
                                    <em>No parent comments yet.</em>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                            <div>
                                <div class="border-b border-gray-300 dark:border-gray-600 mb-2 pb-8"></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Class Teacher</p>
                            </div>
                            <div>
                                <div class="border-b border-gray-300 dark:border-gray-600 mb-2 pb-8"></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Principal</p>
                            </div>
                            <div>
                                <div class="border-b border-gray-300 dark:border-gray-600 mb-2 pb-8"></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Parent/Guardian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
