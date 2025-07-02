<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $assignment->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $assignment->type === 'quiz' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($assignment->type) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $assignment->status === 'published' ? 'bg-green-100 text-green-800' :
                               ($assignment->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                        <span>{{ $assignment->academicSubject->name ?? 'No Subject' }}</span>
                        @if($assignment->is_randomized)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            Randomized
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="duplicateAssignment"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V9a2 2 0 01-2 2H8z"/>
                        </svg>
                        Duplicate
                    </button>
                    <a href="{{ route('teachers.assignment.edit', $assignment->id) }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit
                    </a>
                    <button wire:click="deleteAssignment"
                            onclick="return confirm('Are you sure you want to delete this assignment?')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Assignment Details -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Assignment Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Duration & Timing -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Timing</h4>
                            <div class="space-y-1 text-sm text-blue-800">
                                <div>Duration: {{ $assignment->duration_in_minutes }} minutes</div>
                                <div>Starts: {{ $assignment->starts_at->format('M j, Y g:i A') }}</div>
                                <div>Ends: {{ $assignment->ends_at->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>

                        <!-- Marks -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                            <h4 class="text-sm font-medium text-green-900 mb-2">Scoring</h4>
                            <div class="space-y-1 text-sm text-green-800">
                                <div>Total Marks: {{ $assignment->total_marks }}</div>
                                <div>Questions: {{ $questionStatistics['total_questions'] }}</div>
                                <div>Est. Duration: {{ $questionStatistics['estimated_duration'] }} min</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($assignment->description)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($assignment->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Instructions -->
                    @if($assignment->instructions)
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h3 class="text-lg font-medium text-yellow-900 mb-3">Instructions</h3>
                            <div class="prose prose-sm max-w-none text-yellow-800">
                                {!! nl2br(e($assignment->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Questions Section -->
                    @if($assignment->questions)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Questions Overview</h3>
                                @if($previewQuestions && !$assignment->is_randomized)
                                    <button wire:click="toggleQuestionPreview"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        {{ $showQuestionPreview ? 'Hide Preview' : 'Preview Questions' }}
                                    </button>
                                @endif
                            </div>

                            <!-- Question Type Distribution -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                @foreach($questionStatistics['by_type'] as $type => $count)
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                                        <div class="text-sm text-gray-600">{{ str_replace('_', ' ', ucwords($type, '_')) }}</div>
                                    </div>
                                @endforeach
                            </div>

                            @if($showQuestionPreview && $previewQuestions)
                                <div class="border-t pt-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Question Preview</h4>
                                    <div class="space-y-3 max-h-96 overflow-y-auto">
                                        @foreach($previewQuestions as $index => $questionData)
                                            <div class="bg-gray-50 p-3 rounded border-l-4 border-blue-500">
                                                <div class="flex justify-between items-start mb-2">
                                        <span class="text-sm font-medium text-gray-600">
                                            Question {{ $index + 1 }} - {{ ucwords(str_replace('_', ' ', $questionData['type'])) }}
                                        </span>
                                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                                            {{ $questionData['points'] }} pts
                                        </span>
                                                </div>
                                                <div class="text-sm text-gray-800">
                                                    {!! Str::limit(strip_tags($questionData['model']->question->down), 100) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Submission Statistics -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Submission Overview</h3>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $completedSubmissions }}</div>
                                <div class="text-sm text-green-700">Completed</div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $inProgressSubmissions }}</div>
                                <div class="text-sm text-blue-700">In Progress</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-gray-600">{{ $notStartedCount }}</div>
                                <div class="text-sm text-gray-700">Not Started</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $eligibleStudents->count() }}</div>
                                <div class="text-sm text-purple-700">Total Students</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>{{ $eligibleStudents->count() > 0 ? round(($completedSubmissions / $eligibleStudents->count()) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full"
                                     style="width: {{ $eligibleStudents->count() > 0 ? ($completedSubmissions / $eligibleStudents->count()) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <button wire:click="toggleStudentDetails"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            {{ $showStudentDetails ? 'Hide' : 'View' }} Student Details
                        </button>
                    </div>

                    <!-- Assignment Targets -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment Targets</h3>

                        @if($assignment->academicGroups->count() > 0)
                            <div class="mb-3">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Academic Groups</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($assignment->academicGroups as $group)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $group->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($assignment->academicLevels->count() > 0)
                            <div class="mb-3">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Academic Levels</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($assignment->academicLevels as $level)
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $level->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($assignment->studentGroups->count() > 0)
                            <div class="mb-3">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Student Groups</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($assignment->studentGroups as $group)
                                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $group->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($assignment->topics->count() > 0)
                            <div class="mb-3">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Topics</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($assignment->topics as $topic)
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ $topic->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Student Details Section -->
            @if($showStudentDetails)
                <div class="mt-8 bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Student Submissions</h3>

                        <!-- Filter Buttons -->
                        <div class="flex space-x-2">
                            <button wire:click="setFilter('all')"
                                    class="px-3 py-1 text-sm rounded {{ $selectedFilter === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                All ({{ $studentsWithStatus->count() }})
                            </button>
                            <button wire:click="setFilter('graded')"
                                    class="px-3 py-1 text-sm rounded {{ $selectedFilter === 'graded' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                Graded ({{ $studentsWithStatus->where('status', 'graded')->count() }})
                            </button>
                            <button wire:click="setFilter('submitted')"
                                    class="px-3 py-1 text-sm rounded {{ $selectedFilter === 'submitted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                Submitted ({{ $studentsWithStatus->where('status', 'submitted')->count() }})
                            </button>
                            <button wire:click="setFilter('in_progress')"
                                    class="px-3 py-1 text-sm rounded {{ $selectedFilter === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                In Progress ({{ $inProgressSubmissions }})
                            </button>
                            <button wire:click="setFilter('not_started')"
                                    class="px-3 py-1 text-sm rounded {{ $selectedFilter === 'not_started' ? 'bg-gray-100 text-gray-700' : 'bg-gray-100 text-gray-600' }}">
                                Not Started ({{ $notStartedCount }})
                            </button>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->filteredStudents as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-medium text-gray-700">
                                                    {{ substr($student['name'], 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $student['name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $student['status_color'] === 'green' ? 'bg-green-100 text-green-800' :
                                           ($student['status_color'] === 'blue' ? 'bg-blue-100 text-blue-800' :
                                           ($student['status_color'] === 'purple' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $student['status_label'] }}
                                    </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($student['score'] !== null)
                                            {{ $student['score'] }}/{{ $student['total_marks'] }}
                                            <span class="text-gray-500">({{ round(($student['score'] / $student['total_marks']) * 100) }}%)</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($student['time_spent_minutes'])
                                            {{ $student['time_spent_minutes'] }} min
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($student['submitted_at'])
                                            {{ \Carbon\Carbon::parse($student['submitted_at'])->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($student['submission_id'])
                                            <a href="{{ route('teachers.submission.view', $student['submission_id']) }}"
                                               class="text-indigo-600 hover:text-indigo-900">View Submission</a>
                                        @else
                                            <span class="text-gray-400">No submission</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($this->filteredStudents->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            No students found for the selected filter.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
