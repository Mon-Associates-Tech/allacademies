<div class="p-6">
    @if(!$student)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Student profile not found. Please contact your administrator.</p>
        </div>
    @else
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Assignments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAssignments }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedAssignments }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Ongoing Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ongoing</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $ongoingAssignments }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Overdue Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                        <p class="text-2xl font-bold text-red-600">{{ $overdueAssignments }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Average Score -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Performance</h3>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-100 mb-4">
                        <span class="text-3xl font-bold text-indigo-600">{{ $averageAssignmentScore }}%</span>
                    </div>
                    <p class="text-sm text-gray-600">Average Score</p>
                </div>
            </div>

            <!-- Activity Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">This Week</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $assignmentsThisWeek }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">This Month</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $assignmentsThisMonth }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Upcoming</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $upcomingAssignments }}</span>
                    </div>
                </div>
            </div>

            <!-- Self-Assessment Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Self-Assessment</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Quizzes</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $totalSelfAssessments }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Average Score</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $averageSelfAssessmentScore }}%</span>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('students.assessments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Take a quiz →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assignments & Upcoming Due -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Assignments</h3>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['percentage'] >= 80 ? 'bg-green-100 text-green-800' :
                                       ($assignment['percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $assignment['percentage'] }}%
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $assignment['submitted_at']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No completed assignments yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Due Assignments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Due</h3>
                    <a href="{{ route('students.assignments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingDueAssignments as $assignment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $assignment['title'] }}</p>
                                <p class="text-sm text-gray-600">{{ $assignment['subject'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assignment['days_until_due'] <= 1 ? 'bg-red-100 text-red-800' :
                                       ($assignment['days_until_due'] <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ abs($assignment['days_until_due']) }} {{ abs($assignment['days_until_due']) == 1 ? 'day' : 'days' }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $assignment['due_date']->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No upcoming assignments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        @if(count($subjectPerformance) > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Subject</h3>
                <div class="space-y-4">
                    @foreach($subjectPerformance as $subject)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $subject['subject'] }}</span>
                                <span class="text-sm text-gray-600">{{ $subject['average_score'] }}% ({{ $subject['assignments_count'] }} assignments)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full
                                    {{ $subject['average_score'] >= 80 ? 'bg-green-500' :
                                       ($subject['average_score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width: {{ $subject['average_score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Self-Assessments -->
        @if(count($recentSelfAssessments) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Book Quizzes</h3>
                    <a href="{{ route('students.assessments') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentSelfAssessments as $assessment)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-900 mb-2">{{ $assessment['book_title'] }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Score:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assessment['score'] >= 80 ? 'bg-green-100 text-green-800' :
                                       ($assessment['score'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $assessment['score'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $assessment['completed_at']->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
