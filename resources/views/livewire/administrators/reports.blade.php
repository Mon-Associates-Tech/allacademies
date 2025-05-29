<div>
    <h1 class="text-2xl font-bold mb-6">Reports & Analytics</h1>

    <!-- Report Controls -->
    <div class="mb-8 bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select wire:model="reportType" class="w-full p-2 border rounded" wire:change="generateReport">
                    <option value="borrowing">Book Borrowings</option>
                    <option value="subscription">Book Subscriptions</option>
                    <option value="assessment">Student Assessments</option>
                    <option value="attendance">Student Attendance</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select wire:model="dateRange" class="w-full p-2 border rounded">
                    <option value="week">Current Week</option>
                    <option value="month">Current Month</option>
                    <option value="quarter">Current Quarter</option>
                    <option value="year">Current Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            @if($dateRange === 'custom')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" wire:model="startDate" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" wire:model="endDate" class="w-full p-2 border rounded">
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student Group (Optional)</label>
                <select wire:model="studentGroupId" class="w-full p-2 border rounded">
                    <option value="">All Groups</option>
                    @foreach($studentGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($reportType === 'attendance')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teacher (Optional)</label>
                <select wire:model="teacherId" class="w-full p-2 border rounded">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($dateRange === 'custom')
            <div class="md:col-span-2">
                <button wire:click="generateReport" class="mt-5 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Generate Report
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">
            @if($reportType === 'borrowing')
                Book Borrowing Report
            @elseif($reportType === 'subscription')
                Book Subscription Report
            @elseif($reportType === 'assessment')
                Student Assessment Report
            @elseif($reportType === 'attendance')
                Student Attendance Report
            @endif

            <span class="text-sm font-normal text-gray-500 ml-2">
                {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} -
                {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </span>
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if($reportType === 'borrowing')
                <!-- Borrowing Stats -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Borrowing Status</h3>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">On Time Returns</div>
                            <div class="text-xl font-semibold">{{ $chartData['returnStatus']['onTime'] }}</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Late Returns</div>
                            <div class="text-xl font-semibold">{{ $chartData['returnStatus']['late'] }}</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Overdue</div>
                            <div class="text-xl font-semibold">{{ $chartData['returnStatus']['overdue'] }}</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Borrowing Status Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Borrowings -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Daily Borrowings</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Daily Borrowings Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Borrowings by Book Category</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Category Distribution Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($reportType === 'subscription')
                <!-- Subscription Stats -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Subscription Status</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Active Subscriptions</div>
                            <div class="text-xl font-semibold">{{ $chartData['subscriptionStatus']['active'] }}</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Expired Subscriptions</div>
                            <div class="text-xl font-semibold">{{ $chartData['subscriptionStatus']['expired'] }}</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Subscription Status Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Subscriptions -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">New Subscriptions</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Daily Subscriptions Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Subscriptions by Book Category</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Category Distribution Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($reportType === 'assessment')
                <!-- Score Distribution -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Score Distribution</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Score Distribution Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group Averages -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Average Scores by Group</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Group Averages Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Students -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Top Performing Students</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Score</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 1</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">95.4</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 2</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">92.7</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 3</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">91.2</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($reportType === 'attendance')
                <!-- Attendance Rate -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Overall Attendance Rate</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Present</div>
                            <div class="text-xl font-semibold">{{ $chartData['attendanceRate']['present'] }}%</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Absent</div>
                            <div class="text-xl font-semibold">{{ $chartData['attendanceRate']['absent'] }}%</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Attendance Rate Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance by Day -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Attendance by Day of Week</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Daily Attendance Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance by Group -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Attendance by Student Group</h3>
                    <div class="h-60">
                        <!-- In a real app, you would use a chart library like Chart.js -->
                        <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                            <div class="text-center">
                                <div class="text-gray-500">Group Attendance Chart</div>
                                <div class="mt-2 text-xs text-gray-400">(Chart visualization would be here)</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 text-right">
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Export Report
            </button>
        </div>
    </div>
</div>
