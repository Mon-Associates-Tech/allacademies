<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="mt-2 text-gray-600">Comprehensive reporting dashboard for monitoring system performance</p>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Report Controls -->
        <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Report Configuration</h2>
                <p class="mt-1 text-sm text-gray-600">Configure your report parameters and filters</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Report Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Type</label>
                        <select wire:model="reportType" wire:change="generateReport"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="borrowing">📚 Book Borrowings</option>
                            <option value="subscription">📋 Book Subscriptions</option>
                            <option value="assessment">📊 Student Assessments</option>
                            <option value="attendance">✅ Student Attendance</option>
                            <option value="teachers">👨‍🏫 Teacher Statistics</option>
                            <option value="students">👥 Student Statistics</option>
                            <option value="librarians">📖 Librarian Statistics</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Range</label>
                        <select wire:model="dateRange"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="week">Current Week</option>
                            <option value="month">Current Month</option>
                            <option value="quarter">Current Quarter</option>
                            <option value="year">Current Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    @if($dateRange === 'custom')
                        <!-- Start Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <input type="date" wire:model="startDate"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- End Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                            <input type="date" wire:model="endDate"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    @endif

                    <!-- Student Group Filter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student Group (Optional)</label>
                        <select wire:model="studentGroupId"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">All Groups</option>
                            @foreach($studentGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($reportType === 'attendance')
                        <!-- Teacher Filter -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teacher (Optional)</label>
                            <select wire:model="teacherId"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">All Teachers</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button wire:click="generateReport"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                wire:loading.attr="disabled">
                            <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg wire:loading class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Generate Report</span>
                            <span wire:loading>Generating...</span>
                        </button>

                        <button wire:click="exportReport"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Report
                        </button>
                    </div>

                    <div class="text-sm text-gray-500">
                        Last updated: {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading wire:target="generateReport" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-900 font-medium">Generating report...</span>
            </div>
        </div>

        <!-- Report Content -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            @if($reportType === 'borrowing')
                                📚 Book Borrowing Report
                            @elseif($reportType === 'subscription')
                                📋 Book Subscription Report
                            @elseif($reportType === 'assessment')
                                📊 Student Assessment Report
                            @elseif($reportType === 'attendance')
                                ✅ Student Attendance Report
                            @elseif($reportType === 'teachers')
                                👨‍🏫 Teacher Statistics Report
                            @elseif($reportType === 'students')
                                👥 Student Statistics Report
                            @elseif($reportType === 'librarians')
                                📖 Librarian Statistics Report
                            @endif
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Period: {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($dateRange) }} Report
                        </span>
                        @if($studentGroupId)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Filtered by Group
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Chart Content Based on Report Type -->
                @if($reportType === 'borrowing')
                    @include('livewire.administrators.reports.borrowing-charts')
                @elseif($reportType === 'subscription')
{{--                    @include('livewire.administrators.reports.subscription-charts')--}}
                @elseif($reportType === 'assessment')
{{--                    @include('livewire.administrators.reports.assessment-charts')--}}
                @elseif($reportType === 'attendance')
{{--                    @include('livewire.administrators.reports.attendance-charts')--}}
                @elseif($reportType === 'teachers')
{{--                    @include('livewire.administrators.reports.teacher-charts')--}}
                @elseif($reportType === 'students')
{{--                    @include('livewire.administrators.reports.student-charts')--}}
                @elseif($reportType === 'librarians')
{{--                    @include('livewire.administrators.reports.librarian-charts')--}}
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts when component loads
            initializeCharts(@json($chartData));

            // Listen for chart data updates
            Livewire.on('chartDataUpdated', (chartData) => {
                updateCharts(chartData);
            });
        });

        function initializeCharts(chartData) {
            // Initialize charts based on current report type
            const reportType = @json($reportType);

            switch(reportType) {
                case 'borrowing':
                    initializeBorrowingCharts(chartData);
                    break;
                case 'subscription':
                    initializeSubscriptionCharts(chartData);
                    break;
                case 'assessment':
                    initializeAssessmentCharts(chartData);
                    break;
                case 'attendance':
                    initializeAttendanceCharts(chartData);
                    break;
                case 'teachers':
                    initializeTeacherCharts(chartData);
                    break;
                case 'students':
                    initializeStudentCharts(chartData);
                    break;
                case 'librarians':
                    initializeLibrarianCharts(chartData);
                    break;
            }
        }

        function updateCharts(chartData) {
            // Update existing charts with new data
            Object.keys(window.charts || {}).forEach(chartId => {
                if (window.charts[chartId]) {
                    window.charts[chartId].destroy();
                }
            });

            initializeCharts(chartData);
        }

        // Chart initialization functions for each report type
        function initializeBorrowingCharts(chartData) {
            window.charts = window.charts || {};

            // Return Status Pie Chart
            if (document.getElementById('returnStatusChart')) {
                const ctx = document.getElementById('returnStatusChart').getContext('2d');
                window.charts.returnStatus = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Overdue', 'On Time', 'Late'],
                        datasets: [{
                            data: [
                                chartData.returnStatus.overdue,
                                chartData.returnStatus.onTime,
                                chartData.returnStatus.late
                            ],
                            backgroundColor: ['#ef4444', '#10b981', '#f59e0b'],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }

            // Daily Borrowings Line Chart
            if (document.getElementById('dailyBorrowingsChart')) {
                const ctx = document.getElementById('dailyBorrowingsChart').getContext('2d');
                window.charts.dailyBorrowings = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.dailyBorrowings.labels || [],
                        datasets: [{
                            label: 'Daily Borrowings',
                            data: chartData.dailyBorrowings.data || [],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Category Borrowings Bar Chart
            if (document.getElementById('categoryBorrowingsChart')) {
                const ctx = document.getElementById('categoryBorrowingsChart').getContext('2d');
                window.charts.categoryBorrowings = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.categoryBorrowings.labels || [],
                        datasets: [{
                            label: 'Borrowings by Category',
                            data: chartData.categoryBorrowings.data || [],
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: '#6366f1',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }

        // Similar functions for other chart types...
        function initializeSubscriptionCharts(chartData) {
            // Implementation for subscription charts
        }

        function initializeAssessmentCharts(chartData) {
            // Implementation for assessment charts
        }

        function initializeAttendanceCharts(chartData) {
            // Implementation for attendance charts
        }

        function initializeTeacherCharts(chartData) {
            // Implementation for teacher charts
        }

        function initializeStudentCharts(chartData) {
            // Implementation for student charts
        }

        function initializeLibrarianCharts(chartData) {
            // Implementation for librarian charts
        }
    </script>
@endpush
