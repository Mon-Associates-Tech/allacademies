<!-- Summary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
            {{ $data['summary']['total_days'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Days</div>
    </div>
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
            {{ $data['summary']['present_days'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Present</div>
    </div>
    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
            {{ $data['summary']['absent_days'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Absent</div>
    </div>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
            {{ $data['summary']['late_days'] }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Late</div>
    </div>
    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            {{ number_format($data['summary']['attendance_rate'], 1) }}%
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Attendance Rate</div>
    </div>
</div>

<!-- Attendance Status Indicator -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Attendance Status</h4>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            {{ $data['summary']['attendance_rate'] >= 90 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
               ($data['summary']['attendance_rate'] >= 75 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
               'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
            {{ $data['summary']['attendance_rate'] >= 90 ? 'Excellent' :
               ($data['summary']['attendance_rate'] >= 75 ? 'Good' : 'Needs Improvement') }}
        </span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
        <div class="h-4 rounded-full transition-all {{ $data['summary']['attendance_rate'] >= 90 ? 'bg-green-500' :
            ($data['summary']['attendance_rate'] >= 75 ? 'bg-yellow-500' : 'bg-red-500') }}"
             style="width: {{ $data['summary']['attendance_rate'] }}%"></div>
    </div>
    <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
        <span>0%</span>
        <span>50%</span>
        <span>100%</span>
    </div>
</div>

<!-- Monthly Breakdown -->
@if($data['monthly_breakdown']->isNotEmpty())
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Monthly Breakdown</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Present</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Absent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Late</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($data['monthly_breakdown'] as $month)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $month['month'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $month['total'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                            {{ $month['present'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                            {{ $month['absent'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 dark:text-yellow-400">
                            {{ $month['late'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mr-2">
                                        {{ number_format($month['rate'], 1) }}%
                                    </span>
                                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $month['rate'] }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Recent Attendance Records -->
@if($data['recent_records']->isNotEmpty())
    <div>
        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Recent Attendance Records</h4>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @foreach($data['recent_records'] as $record)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center
                                {{ $record->status === 'present' ? 'bg-green-100 dark:bg-green-900/20' :
                                   ($record->status === 'absent' ? 'bg-red-100 dark:bg-red-900/20' :
                                   'bg-yellow-100 dark:bg-yellow-900/20') }}">
                                @if($record->status === 'present')
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($record->status === 'absent')
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-gray-100 capitalize">
                                {{ $record->status }}
                            </h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($record->date)->format('l, M d, Y') }}
                            </p>
                            @if($record->session)
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    Session: {{ ucfirst($record->session) }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @if($record->remarks)
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record->remarks }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
