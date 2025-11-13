<div x-show="activeTab === 'academic-periods'" class="space-y-6 animate-fade-in">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Academic Periods
        </h3>
        <button wire:click="createAcademicPeriod"
                class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Period
            </span>
        </button>
    </div>

    @if(count($periods) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Period</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Duration</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Progress</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($periods as $period)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $period['name'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $period['academic_year'] }}</p>
                                    </div>
                                    @if($period['is_current'])
                                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full">
                                            Current
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white capitalize">{{ $period['type'] }} {{ $period['sequence'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($period['start_date'])->format('M d, Y') }}
                                    <span class="text-gray-500 dark:text-gray-400"> - </span>
                                    {{ \Carbon\Carbon::parse($period['end_date'])->format('M d, Y') }}
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $period['weeks'] }} weeks</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full capitalize
                                    @if($period['status'] === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($period['status'] === 'upcoming') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @endif">
                                    {{ $period['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($period['status'] === 'active')
                                    <div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $period['progress'] }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $period['progress'] }}%</p>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(!$period['is_current'] && $period['status'] !== 'completed')
                                        <button wire:click="setCurrentPeriod({{ $period['id'] }})"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                            Set Current
                                        </button>
                                    @endif
                                    <button wire:click="editAcademicPeriod({{ $period['id'] }})"
                                            class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            title="Edit Period">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteAcademicPeriod({{ $period['id'] }})"
                                            onclick="return confirm('Are you sure you want to delete this academic period?')"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            title="Delete Period">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No academic periods</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first academic period.</p>
                <div class="mt-6">
                    <button wire:click="createAcademicPeriod"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Academic Period
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
