<x-layouts.app pageName="Financial Reports">
    <x-slot name="title">Financial Reports</x-slot>

    <div class="space-y-6">
        <!-- Report Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Payment Summary Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate a summary of all payments by period, status, and type</p>
                <button onclick="openReportModal('payment_summary')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Generate Report
                </button>
            </div>

            <!-- Student Payments Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Payments</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">View payment history for individual students or groups</p>
                <button onclick="openReportModal('student_payments')" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Generate Report
                </button>
            </div>

            <!-- Outstanding Payments Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Outstanding Payments</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">List of pending and failed payment transactions</p>
                <button onclick="openReportModal('outstanding_payments')" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Generate Report
                </button>
            </div>

            <!-- Revenue Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Report</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Analyze revenue trends by period and payment type</p>
                <button onclick="openReportModal('revenue')" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Generate Report
                </button>
            </div>

            <!-- Financial Aid Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Financial Aid</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Summary of financial aid programs and beneficiaries</p>
                <button onclick="openReportModal('financial_aid')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Generate Report
                </button>
            </div>

            <!-- Custom Report -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Custom Report</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Create a custom report with specific filters and criteria</p>
                <button onclick="openReportModal('custom')" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalTitle">Generate Report</h3>
                <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('accountant.reports.generate') }}" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="report_type" id="reportType">
                
                <!-- Basic Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="date" name="start_date" value="{{ now()->subMonth()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                            <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format *</label>
                        <select name="format" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="view">View in Browser</option>
                            <option value="pdf">PDF Download</option>
                            <option value="excel">Excel Download</option>
                            <option value="csv">CSV Download</option>
                        </select>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Advanced Filters</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Student Filter -->
                        <div id="studentFilter" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specific Student</label>
                            <select name="student_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Students</option>
                                <!-- Students will be loaded via AJAX -->
                            </select>
                        </div>

                        <!-- Payment Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type</label>
                            <select name="payment_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Types</option>
                                @foreach($paymentTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Statuses</option>
                                <option value="succeeded">Successful</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <!-- Academic Year Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                            <select name="academic_year_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Period Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Period</label>
                            <select name="academic_period_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Periods</option>
                                @foreach($academicPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Level Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Level</label>
                            <select name="academic_level_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Levels</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Group Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Group</label>
                            <select name="academic_group_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Groups</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                            <select name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Methods</option>
                                <option value="card">Card Payment</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Generate Report
                    </button>
                    <button type="button" onclick="closeReportModal()" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openReportModal(type) {
            document.getElementById('reportType').value = type;
            document.getElementById('modalTitle').textContent = 'Generate ' + type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' Report';
            
            // Show/hide student filter for student-specific reports
            const studentFilter = document.getElementById('studentFilter');
            if (type === 'student_payments') {
                studentFilter.classList.remove('hidden');
                loadStudents();
            } else {
                studentFilter.classList.add('hidden');
            }
            
            document.getElementById('reportModal').classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
        }

        function loadStudents() {
            fetch('/accountant/students/api')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="student_id"]');
                    select.innerHTML = '<option value="">All Students</option>';
                    data.forEach(student => {
                        select.innerHTML += `<option value="${student.id}">${student.student_id} - ${student.name}</option>`;
                    });
                })
                .catch(error => console.error('Error loading students:', error));
        }

        // Close modal when clicking outside
        document.getElementById('reportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReportModal();
            }
        });
    </script>
    @endpush
</x-layouts.app>
