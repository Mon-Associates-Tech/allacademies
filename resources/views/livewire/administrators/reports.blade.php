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
                    <option value="teachers">Teacher Statistics</option>
                    <option value="students">Student Statistics</option>
                    <option value="librarians">Librarian Statistics</option>
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

            @if($reportType === 'attendance')
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
                        <!-- Borrowing Status Chart -->
                        <canvas id="borrowingStatusChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Daily Borrowings -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Daily Borrowings</h3>
                    <div class="h-60">
                        <!-- Daily Borrowings Chart -->
                        <canvas id="dailyBorrowingsChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Borrowings by Book Category</h3>
                    <div class="h-60">
                        <!-- Category Distribution Chart -->
                        <canvas id="categoryDistributionChart" class="h-full w-full"></canvas>
                    </div>
                </div>
            @endif
            @if($reportType === 'subscription')
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
                        <!-- Subscription Status Chart -->
                        <canvas id="subscriptionStatusChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Daily Subscriptions -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">New Subscriptions</h3>
                    <div class="h-60">
                        <!-- Daily Subscriptions Chart -->
                        <canvas id="dailySubscriptionsChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Subscriptions by Book Category</h3>
                    <div class="h-60">
                        <!-- Category Distribution Chart -->
                        <canvas id="subscriptionCategoryChart" class="h-full w-full"></canvas>
                    </div>
                </div>
            @elseif($reportType === 'assessment')
                <!-- Score Distribution -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Score Distribution</h3>
                    <div class="h-60">
                        <!-- Score Distribution Chart -->
                        <canvas id="scoreDistributionChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Group Averages -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Average Scores by Group</h3>
                    <div class="h-60">
                        <!-- Group Averages Chart -->
                        <canvas id="groupAveragesChart" class="h-full w-full"></canvas>
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
                        <!-- Attendance Rate Chart -->
                        <canvas id="attendanceRateChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Attendance by Day of Week -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Attendance by Day of Week</h3>
                    <div class="h-60">
                        <!-- Daily Attendance Chart -->
                        <canvas id="dailyAttendanceChart" class="h-full w-full"></canvas>
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

            <!-- New Reports Section for Teachers, Students, and Librarians -->
            @if($reportType === 'teachers')
                <!-- Teacher Statistics -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Teacher Statistics</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Total Teachers</div>
                            <div class="text-xl font-semibold">{{ $chartData['teacherStats']['total'] }}</div>
                        </div>
                        <div class="bg-indigo-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Teachers with Active Classes</div>
                            <div class="text-xl font-semibold">{{ $chartData['teacherStats']['withActiveClasses'] }}</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- Teacher Activity Chart -->
                        <canvas id="teacherActivityChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Students per Teacher -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Students per Teacher</h3>
                    <div class="h-60">
                        <!-- Student Distribution Chart -->
                        <canvas id="studentDistributionChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Teachers with Student Count -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Teachers with Student Count</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Count</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($chartData['teachersWithStudentsCount'] as $teacher)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $teacher->assigned_students_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Students with Teacher Count -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Students with Teacher Count</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher Count</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($chartData['studentsWithTeachersCount'] as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $student->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $student->teachers_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Subject Distribution -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Subject Distribution</h3>
                    <div class="h-60">
                        <!-- Subject Distribution Chart -->
                        <canvas id="subjectDistributionChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Teacher Student Relationship Summary -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Teacher-Student Relationship Summary</h3>
                    @if(isset($chartData['teacherStudentRelationship']) && is_array($chartData['teacherStudentRelationship']))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Total Relationships</h4>
                                <p class="text-3xl font-bold text-blue-600">{{ $chartData['teacherStudentRelationship']['relationship_summary']['total_relationships'] ?? 0 }}</p>
                                <p class="text-sm text-gray-500 mt-1">Total teacher-student connections</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Average per Teacher</h4>
                                <p class="text-3xl font-bold text-green-600">
                                    {{ $chartData['teacherStudentRelationship']['relationship_summary']['average_per_teacher'] ?? 0 }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Students per teacher (average)</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Average per Student</h4>
                                <p class="text-3xl font-bold text-purple-600">
                                    {{ $chartData['teacherStudentRelationship']['relationship_summary']['average_per_student'] ?? 0 }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Teachers per student (average)</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No teacher-student relationship data available.</p>
                    @endif
                </div>

                <!-- Teachers with Students Chart -->
                <div class="border rounded-lg p-4 mt-6">
                    <h3 class="text-md font-medium mb-4">Teachers with Students Distribution</h3>
                    <div class="h-64">
                        <canvas id="teachersWithStudentsChart" class="w-full h-full"></canvas>
                    </div>
                    @if(!isset($chartData['teacherStudentRelationship']) || empty($chartData['teacherStudentRelationship']))
                        <p class="text-gray-500 italic text-center py-4">No data to display.</p>
                    @endif
                </div>

                <!-- Students with Teachers Chart -->
                <div class="border rounded-lg p-4 mt-6">
                    <h3 class="text-md font-medium mb-4">Students with Teachers Distribution</h3>
                    <div class="h-64">
                        <canvas id="studentsWithTeachersChart" class="w-full h-full"></canvas>
                    </div>
                    @if(!isset($chartData['teacherStudentRelationship']) || empty($chartData['teacherStudentRelationship']))
                        <p class="text-gray-500 italic text-center py-4">No data to display.</p>
                    @endif
                </div>

            @elseif($reportType === 'students')
                <!-- Student Statistics -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Student Statistics</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Total Students</div>
                            <div class="text-xl font-semibold">{{ $chartData['studentStats']['total'] }}</div>
                        </div>
                        <div class="bg-teal-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Active Students</div>
                            <div class="text-xl font-semibold">{{ $chartData['studentStats']['active'] }}</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- Student Status Chart -->
                        <canvas id="studentStatusChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Students by Group -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Students by Group</h3>
                    <div class="h-60">
                        <!-- Group Distribution Chart -->
                        <canvas id="groupDistributionChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Student Performance -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Top Performing Students</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GPA</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 1</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">4.0</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 2</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">3.9</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Student 3</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Sample Group</td>
                                    <td class="px-6 py-4 whitespace-nowrap">3.8</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if($reportType === 'librarians')
                <!-- Librarian Statistics -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Librarian Statistics</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-purple-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Total Librarians</div>
                            <div class="text-xl font-semibold">{{ $chartData['librarianStats']['total'] }}</div>
                        </div>
                        <div class="bg-violet-50 p-3 rounded-lg text-center">
                            <div class="text-sm text-gray-500">Active Librarians</div>
                            <div class="text-xl font-semibold">{{ $chartData['librarianStats']['active'] }}</div>
                        </div>
                    </div>

                    <div class="h-60">
                        <!-- Librarian Activity Chart -->
                        <canvas id="librarianActivityChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Book Approvals by Librarian -->
                <div class="border rounded-lg p-4">
                    <h3 class="text-md font-medium mb-4">Book Approvals</h3>
                    <div class="h-60">
                        <!-- Approvals Chart -->
                        <canvas id="approvalsChart" class="h-full w-full"></canvas>
                    </div>
                </div>

                <!-- Librarian Activity -->
                <div class="border rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-md font-medium mb-4">Librarian Activity</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Librarian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books Approved</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Worked</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Jane Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap">150</td>
                                    <td class="px-6 py-4 whitespace-nowrap">40</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                                    <td class="px-6 py-4 whitespace-nowrap">120</td>
                                    <td class="px-6 py-4 whitespace-nowrap">35</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Emily Johnson</td>
                                    <td class="px-6 py-4 whitespace-nowrap">135</td>
                                    <td class="px-6 py-4 whitespace-nowrap">38</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 text-right">
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 010 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 012 0v7.586l1.293-1.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Export Report
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Borrowing Status Chart
        const borrowingStatusCtx = document.getElementById('borrowingStatusChart').getContext('2d');
        new Chart(borrowingStatusCtx, {
            type: 'pie',
            data: {
                labels: ['On Time Returns', 'Late Returns', 'Overdue'],
                datasets: [{
                    label: 'Return Status',
                    data: [
                        {{ $chartData['returnStatus']['onTime'] }},
                        {{ $chartData['returnStatus']['late'] }},
                        {{ $chartData['returnStatus']['overdue'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Borrowing Return Status'
                    }
                }
            }
        });

        // Daily Borrowings Chart
        const dailyBorrowingsCtx = document.getElementById('dailyBorrowingsChart').getContext('2d');
        new Chart(dailyBorrowingsCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($chartData['dailyBorrowings'] as $borrowing)
                        '{{ $borrowing->date }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Daily Borrowings',
                    data: [
                        @foreach($chartData['dailyBorrowings'] as $borrowing)
                            {{ $borrowing->count }},
                        @endforeach
                    ],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Daily Borrowings'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Category Distribution Chart
        const categoryDistributionCtx = document.getElementById('categoryDistributionChart').getContext('2d');
        new Chart(categoryDistributionCtx, {
            type: 'bar',
            data: {
                labels: Object.keys({!! json_encode($chartData['categoryBorrowings']) !!} || {}),
                datasets: [{
                    label: 'Books Borrowed',
                    data: Object.values({!! json_encode($chartData['categoryBorrowings']) !!} || {}),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Borrowings by Book Category'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Subscription Status Chart
        const subscriptionStatusCtx = document.getElementById('subscriptionStatusChart').getContext('2d');
        new Chart(subscriptionStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active Subscriptions', 'Expired Subscriptions'],
                datasets: [{
                    label: 'Subscription Status',
                    data: [
                        {{ $chartData['subscriptionStatus']['active'] }},
                        {{ $chartData['subscriptionStatus']['expired'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Subscription Status'
                    }
                }
            }
        });

        // Daily Subscriptions Chart
        const dailySubscriptionsCtx = document.getElementById('dailySubscriptionsChart').getContext('2d');
        new Chart(dailySubscriptionsCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($chartData['dailySubscriptions'] as $subscription)
                        '{{ $subscription->date }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'New Subscriptions',
                    data: [
                        @foreach($chartData['dailySubscriptions'] as $subscription)
                            {{ $subscription->count }},
                        @endforeach
                    ],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Daily Subscriptions'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Category Subscriptions Chart
        const categorySubscriptionsCtx = document.getElementById('subscriptionCategoryChart').getContext('2d');
        new Chart(categorySubscriptionsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys({!! json_encode($chartData['categorySubscriptions']) !!} || {}),
                datasets: [{
                    label: 'Books Subscribed',
                    data: Object.values({!! json_encode($chartData['categorySubscriptions']) !!} || {}),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Subscriptions by Book Category'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Score Distribution Chart
        const scoreDistributionCtx = document.getElementById('scoreDistributionChart').getContext('2d');
        new Chart(scoreDistributionCtx, {
            type: 'bar',
            data: {
                labels: ['0-20', '21-40', '41-60', '61-80', '81-100'],
                datasets: [{
                    label: 'Student Scores',
                    data: [
                        {{ $chartData['scoreDistribution']['0-20'] }},
                        {{ $chartData['scoreDistribution']['21-40'] }},
                        {{ $chartData['scoreDistribution']['41-60'] }},
                        {{ $chartData['scoreDistribution']['61-80'] }},
                        {{ $chartData['scoreDistribution']['81-100'] }}
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Score Distribution'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Group Averages Chart
        const groupAveragesCtx = document.getElementById('groupAveragesChart').getContext('2d');
        new Chart(groupAveragesCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chartData['groupScores'] as $group)
                        '{{ $group->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Average Score',
                    data: [
                        @foreach($chartData['groupScores'] as $group)
                            {{ number_format($group->average_score, 1) }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Average Scores by Group'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Attendance Rate Chart
        const attendanceRateCtx = document.getElementById('attendanceRateChart').getContext('2d');
        new Chart(attendanceRateCtx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [
                        {{ $chartData['attendanceRate']['present'] }},
                        {{ $chartData['attendanceRate']['absent'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Overall Attendance Rate'
                    }
                }
            }
        });

        // Daily Attendance Chart
        const dailyAttendanceCtx = document.getElementById('dailyAttendanceChart').getContext('2d');
        new Chart(dailyAttendanceCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($chartData['attendanceByDay'] as $day)
                        '{{ $day['day'] }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: [
                        @foreach($chartData['attendanceByDay'] as $day)
                            {{ $day['rate'] }},
                        @endforeach
                    ],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Attendance by Day of Week'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Teacher Activity Chart
        const teacherActivityCtx = document.getElementById('teacherActivityChart').getContext('2d');
        new Chart(teacherActivityCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chartData['teachersPerSubject'] as $subject => $count)
                        '{{ $subject }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Number of Teachers',
                    data: [
                        @foreach($chartData['teachersPerSubject'] as $subject => $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Teacher Distribution by Subject'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Student Distribution Chart
        const studentDistributionCtx = document.getElementById('studentDistributionChart').getContext('2d');
        new Chart(studentDistributionCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chartData['studentsPerTeacher'] as $teacher)
                        '{{ $teacher->user->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Students per Teacher',
                    data: [
                        @foreach($chartData['studentsPerTeacher'] as $teacher)
                            {{ $teacher->students_count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Students per Teacher'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Subject Distribution Chart
        const subjectDistributionCtx = document.getElementById('subjectDistributionChart').getContext('2d');
        new Chart(subjectDistributionCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($chartData['subjectDistribution'] as $subject => $count)
                        '{{ $subject }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Subjects',
                    data: [
                        @foreach($chartData['subjectDistribution'] as $subject => $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($chartData['subjectDistribution'] as $subject => $count)
                            'rgba({{ rand(0, 255) }}, {{ rand(0, 255) }}, {{ rand(0, 255) }}, 0.6)',
                        @endforeach
                    ],
                    borderColor: [
                        @foreach($chartData['subjectDistribution'] as $subject => $count)
                            'rgba({{ rand(0, 255) }}, {{ rand(0, 255) }}, {{ rand(0, 255) }}, 1)',
                        @endforeach
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Teacher Subject Distribution'
                    }
                }
            }
        });

        // Student Status Chart
        const studentStatusCtx = document.getElementById('studentStatusChart').getContext('2d');
        new Chart(studentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Total Students', 'Active Students'],
                datasets: [{
                    label: 'Student Status',
                    data: [
                        {{ $chartData['studentStats']['total'] }},
                        {{ $chartData['studentStats']['active'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Student Status Distribution'
                    }
                }
            }
        });

        // Group Distribution Chart
        const groupDistributionCtx = document.getElementById('groupDistributionChart').getContext('2d');
        new Chart(groupDistributionCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chartData['studentsByGroup'] as $group)
                        '{{ $group->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Students per Group',
                    data: [
                        @foreach($chartData['studentsByGroup'] as $group)
                            {{ $group->students_count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Students by Group'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Librarian Activity Chart
        const librarianActivityCtx = document.getElementById('librarianActivityChart').getContext('2d');
        new Chart(librarianActivityCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chartData['librarianActivity'] as $librarian)
                        '{{ $librarian->user->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Book Approvals',
                    data: [
                        @foreach($chartData['librarianActivity'] as $librarian)
                            {{ $librarian->book_approvals_count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Librarian Activity (Book Approvals)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Approvals Chart
        const approvalsChartCtx = document.getElementById('approvalsChart').getContext('2d');
        new Chart(approvalsChartCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($chartData['approvalsByLibrarian'] as $librarian)
                        '{{ $librarian->user->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Approvals per Librarian',
                    data: [
                        @foreach($chartData['approvalsByLibrarian'] as $librarian)
                            {{ $librarian->book_approvals_count }},
                        @endforeach
                    ],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Book Approvals by Librarian'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>


@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        console.log('Initializing teacher-student charts - DOMContentLoaded');
        initializeTeacherStudentCharts();
    });

    // Reinitialize charts after Livewire updates
    document.addEventListener('livewire:load', function () {
        Livewire.on('contentChanged', function () {
            console.log('Reinitializing teacher-student charts - contentChanged');
            initializeTeacherStudentCharts();
        });
    });

    function initializeTeacherStudentCharts() {
        console.log('Initializing teacher-student charts data:', @json($chartData['teacherStudentRelationship']));

        // Teachers with Students Distribution Chart
        const teachersWithStudentsChart = document.getElementById('teachersWithStudentsChart');
        if (teachersWithStudentsChart) {
            console.log('Found teachersWithStudentsChart element, initializing...');
            new Chart(teachersWithStudentsChart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(@json($chartData['teacherStudentRelationship']['teachers_distribution'])),
                    datasets: [{
                        label: 'Number of Students',
                        data: Object.values(@json($chartData['teacherStudentRelationship']['teachers_distribution'])),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } else {
            console.error('teachersWithStudentsChart element not found');
        }

        // Students with Teachers Distribution Chart
        const studentsWithTeachersChart = document.getElementById('studentsWithTeachersChart');
        console.log(studentsWithTeachersChart)
        if (studentsWithTeachersChart) {
            console.log('Found studentsWithTeachersChart element, initializing...');
            new Chart(studentsWithTeachersChart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(@json($chartData['teacherStudentRelationship']['students_distribution'])),
                    datasets: [{
                        label: 'Number of Teachers',
                        data: Object.values(@json($chartData['teacherStudentRelationship']['students_distribution'])),
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } else {
            console.error('studentsWithTeachersChart element not found');
        }
    }
</script>
@endpush
