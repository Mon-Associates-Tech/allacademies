<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('teachers.students.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Students List
            </a>
        </div>

        <!-- Student Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-24 w-24">
                        <div class="h-24 w-24 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                            <span class="text-3xl font-medium text-indigo-600 dark:text-indigo-400">
                                {{ strtoupper(substr($student->user->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $student->user->name }}
                        </h1>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $student->user->email }}
                        </div>
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                {{ $student->academicLevel->name ?? 'No Level Assigned' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                {{ $student->academicLevel->academicGroup->name ?? 'No Group Assigned' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Academic History Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Academic History</h2>
                            <div class="flex items-center space-x-2">
                                <select wire:model="historyFilter" class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="level_change">Level Changes</option>
                                    <option value="assessment">Assessments</option>
                                    <option value="achievement">Achievements</option>
                                    <option value="attendance">Attendance</option>
                                    <option value="behavior">Behavior</option>
                                    <option value="award">Awards</option>
                                    <option value="certification">Certifications</option>
                                    <option value="milestone">Milestones</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        @if($academicHistory->count() > 0)
                            <div class="space-y-6">
                                @foreach($academicHistory as $history)
                                    <div class="relative pl-8 pb-6 last:pb-0">
                                        <!-- Timeline dot and line -->
                                        <div class="absolute left-0 top-0 h-full w-6 flex items-center justify-center">
                                            <div class="h-full w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                                            <div class="absolute top-1 w-3 h-3 rounded-full {{ $this->getTypeColor($history->type) }}"></div>
                                        </div>

                                        <!-- Content -->
                                        <div class="ml-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $history->title }}
                                                    </h3>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTypeBadgeColor($history->type) }} mt-1">
                                        {{ ucfirst(str_replace('_', ' ', $history->type)) }}
                                    </span>
                                                </div>
                                                <time class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $history->recorded_date->format('M d, Y') }}
                                                </time>
                                            </div>

                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $history->description }}
                                            </p>

                                            @if($history->achievement_score)
                                                <div class="mt-2 flex items-center">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Achievement Score:</span>
                                                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $history->achievement_score }}
                                    </span>
                                                </div>
                                            @endif

                                            @if($history->academic_period)
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Period: {{ $history->academic_period }}
                                                </div>
                                            @endif

                                            @if($history->supporting_documents)
                                                <div class="mt-3">
                                                    <div class="flex items-center space-x-2">
                                                        @foreach($history->supporting_documents as $document)
                                                            <a href="{{ $document['url'] }}"
                                                               target="_blank"
                                                               class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                </svg>
                                                                {{ $document['name'] }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($history->notes)
                                                <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                                    <details class="cursor-pointer">
                                                        <summary class="text-xs font-medium text-indigo-600 dark:text-indigo-400">Additional Notes</summary>
                                                        <p class="mt-1 pl-4">{{ $history->notes }}</p>
                                                    </details>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No academic history available.</p>
                        @endif
                    </div>
                </div>
                <!-- Assignments -->
                <!-- Assignments -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Assignments</h2>
                            <select wire:model="submissionFilter" class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Status</option>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="submitted">Submitted</option>
                                <option value="graded">Graded</option>
                            </select>
                        </div>

                        @if($assignmentSubmissions->count() > 0)
                            <div class="space-y-4">
                                @foreach($assignmentSubmissions as $submission)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $submission->assignment->title }}
                                                    </h3>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                        {{ ucfirst($submission->assignment->type) }}
                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $submission->assignment->description }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $submission->status_color }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Duration:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                    {{ $submission->assignment->duration_in_minutes }} minutes
                                </span>
                                            </div>

                                            @if($submission->time_spent_minutes)
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Time Spent:</span>
                                                    <span class="ml-2 text-gray-900 dark:text-white">
                                        {{ $submission->time_spent_minutes }} minutes
                                    </span>
                                                </div>
                                            @endif

                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Available From:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                    {{ $submission->assignment->starts_at->format('M d, Y H:i') }}
                                </span>
                                            </div>

                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Due By:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                    {{ $submission->assignment->ends_at->format('M d, Y H:i') }}
                                </span>
                                            </div>

                                            @if($submission->started_at)
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Started:</span>
                                                    <span class="ml-2 text-gray-900 dark:text-white">
                                        {{ $submission->started_at->format('M d, Y H:i') }}
                                    </span>
                                                </div>
                                            @endif

                                            @if($submission->submitted_at)
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Submitted:</span>
                                                    <span class="ml-2 text-gray-900 dark:text-white">
                                        {{ $submission->submitted_at->format('M d, Y H:i') }}
                                    </span>
                                                </div>
                                            @endif

                                            @if($submission->status === 'graded')
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Score:</span>
                                                    <span class="ml-2 text-gray-900 dark:text-white">
                                        {{ $submission->score }}/{{ $submission->total_marks }}
                                        ({{ round(($submission->score / $submission->total_marks) * 100) }}%)
                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($submission->status === 'not_started' && now()->between($submission->assignment->starts_at, $submission->assignment->ends_at))
                                            <div class="mt-4 flex justify-end">
                                                <button
                                                    wire:click="startAssignment({{ $submission->id }})"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                >
                                                    Start Assignment
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No assignments available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Student Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Student Details</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Active
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $student->source }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $student->created_at?->format('M d, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                        @if($activities->count() > 0)
                            <div class="space-y-4">
                                @foreach($activities as $activity)
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $activity->description }}
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No recent activity.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
