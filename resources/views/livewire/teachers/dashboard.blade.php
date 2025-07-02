
<div class="teachers-dashboard">
    <!-- Dashboard Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Teachers Dashboard</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Welcome back, {{ auth()->user()->name }}
                    @if($currentTeam)
                        - {{ $currentTeam->name }}
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('teachers.assignments.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Assignment
                </a>
                <button wire:click="refreshDashboard"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>

        </div>
    </div>

    <!-- Dashboard Navigation Tabs -->
    <div class="bg-white border-b border-gray-200">
        <nav class="px-6">
            <div class="flex space-x-8">
                <button
                    wire:click="setActiveTab('dashboard')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'dashboard' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Overview
                </button>
                <button
                    wire:click="setActiveTab('students')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fas fa-users mr-2"></i>
                    My Students ({{ $totalStudents }})
                </button>
                <button
                    wire:click="setActiveTab('assignments')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'assignments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fas fa-tasks mr-2"></i>
                    Assignments ({{ $totalAssignments }})
                </button>
                <button
                    wire:click="setActiveTab('subjects')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'subjects' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fas fa-book mr-2"></i>
                    Subjects ({{ $totalSubjects }})
                </button>
                <button
                    wire:click="setActiveTab('levels')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'levels' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <i class="fas fa-layer-group mr-2"></i>
                    Academic Levels
                </button>
            </div>
        </nav>
    </div>

    <!-- Dashboard Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        @if($activeTab === 'dashboard')
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-tasks text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Assignments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalAssignments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-book text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Subjects</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalSubjects }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Upcoming Assignments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ count($upcomingAssignments) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent and Upcoming Assignments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Assignments -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Assignments</h3>
                    </div>
                    <div class="p-6">
                        @if(count($recentAssignments) > 0)
                            <div class="space-y-4">
                                @foreach($recentAssignments as $assignment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $assignment['title'] ?? 'Untitled Assignment' }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Created: {{ \Carbon\Carbon::parse($assignment['created_at'])->format('M j, Y') }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                            {{ $assignment['status'] ?? 'Active' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No recent assignments</p>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Assignments -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Assignments</h3>
                    </div>
                    <div class="p-6">
                        @if(count($upcomingAssignments) > 0)
                            <div class="space-y-4">
                                @foreach($upcomingAssignments as $assignment)
                                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $assignment['title'] ?? 'Untitled Assignment' }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                            </p>
                                            <p class="text-xs text-orange-600 font-medium">
                                                Due: {{ \Carbon\Carbon::parse($assignment['ends_at'])->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded">
                                            Due Soon
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No upcoming assignments</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Students Tab -->
        @if($activeTab === 'students')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">My Students</h3>
                </div>
                <div class="p-6">
                    @if(count($myStudents) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($myStudents as $student)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $student['user']['name'] ?? 'Unknown Student' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $student['academic_level']['name'] ?? 'No Level' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $student['academic_level']['academic_group']['name'] ?? 'No Group' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No students assigned yet</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Assignments Tab -->
        @if($activeTab === 'assignments')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">All Assignments</h3>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Create Assignment
                    </button>
                </div>
                <div class="p-6">
                    @if(count($recentAssignments) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentAssignments as $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $assignment['title'] ?? 'Untitled Assignment' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit($assignment['description'] ?? '', 50) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $assignment['academic_subject']['name'] ?? 'Unknown Subject' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($assignment['created_at'])->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $assignment['status'] ?? 'Active' }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('teachers.assignment.view', $assignment['id']) }}"
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    View Details
                                                </a>

                                                <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                            <button class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No assignments created yet</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Subjects Tab -->
        @if($activeTab === 'subjects')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">My Subjects</h3>
                </div>
                <div class="p-6">
                    @if(count($mySubjects) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($mySubjects as $subject)
                                <div class="p-6 border border-gray-200 rounded-lg">
                                    <div class="flex items-center mb-4">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-purple-600 text-xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $subject['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $subject['code'] ?? 'No Code' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Level:</span>
                                            {{ $subject['academic_level']['name'] ?? 'Not assigned' }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Group:</span>
                                            {{ $subject['academic_level']['academic_group']['name'] ?? 'Not assigned' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No subjects assigned yet</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Academic Levels Tab -->
        @if($activeTab === 'levels')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">My Academic Levels</h3>
                </div>
                <div class="p-6">
                    @if(count($myAcademicLevels) > 0)
                        <div class="space-y-6">
                            @foreach($myAcademicLevels as $level)
                                <div class="p-6 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">{{ $level['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $level['academic_group']['name'] ?? 'No Group' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ count($level['students'] ?? []) }} Students
                                            </p>
                                            <p class="text-xs text-gray-500">Enrolled</p>
                                        </div>
                                    </div>
                                    @if(!empty($level['description']))
                                        <p class="text-sm text-gray-600 mb-3">{{ $level['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No academic levels assigned yet</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
    <style>
        .teachers-dashboard {
            min-height: 100vh;
            background-color: #f9fafb;
        }
    </style>
</div>


