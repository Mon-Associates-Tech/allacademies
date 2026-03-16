<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Report Card Preparation</h1>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h3 class="font-medium text-gray-900 dark:text-white mb-3">Select Configuration</h3>
                <select wire:model.live="selectedConfigId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Choose...</option>
                    @foreach($configurations as $config)
                        <option value="{{ $config->id }}">
                            {{ $config->academicLevel?->name ?? 'N/A' }} - {{ $config->academicPeriod?->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedConfigId)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-4">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-3">Filter by Group</h3>
                    <select wire:model.live="selectedGroupId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-3">Students</h3>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($students as $student)
                            <button wire:click="$set('selectedStudentId', {{ $student->id }})"
                                    class="w-full text-left px-3 py-2 rounded {{ $selectedStudentId == $student->id ? 'bg-blue-100 dark:bg-blue-900 text-blue-900 dark:text-blue-100' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <div class="text-sm font-medium">{{ $student->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->student_id }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($reportCard)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $reportCard->student->user->name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $reportCard->configuration?->academicLevel?->name ?? 'N/A' }} - {{ $reportCard->term }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="saveAll" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save All
                            </button>
                            <button wire:click="submitForApproval" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Submit
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subject</th>
                                    @foreach($reportCard->configuration->template?->getColumns() ?? [] as $column)
                                        @if(!in_array($column['name'], ['subject', 'total_score', 'grade']))
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $column['label'] }}</th>
                                        @endif
                                    @endforeach
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grade</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($grades as $gradeId => $gradeData)
                                    <tr>
                                        <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $gradeData['subject_name'] }}
                                        </td>
                                        @foreach($gradeData['scores'] as $key => $value)
                                            <td class="px-3 py-3">
                                                <input type="number"
                                                       wire:model.blur="grades.{{ $gradeId }}.scores.{{ $key }}"
                                                       step="0.01"
                                                       class="w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                                       @if(!$gradeData['can_edit']) disabled @endif>
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($gradeData['total_score'], 2) }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $gradeData['grade_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if($gradeData['can_edit'])
                                                <button wire:click="autoCalculate({{ $gradeId }})"
                                                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                    Auto
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="100%" class="px-3 py-2 bg-gray-50 dark:bg-gray-900">
                                            <textarea wire:model.blur="grades.{{ $gradeId }}.remarks"
                                                      placeholder="Remarks..."
                                                      rows="1"
                                                      class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                      @if(!$gradeData['can_edit']) disabled @endif></textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Select a configuration and student to begin</p>
                </div>
            @endif
        </div>
    </div>
</div>
