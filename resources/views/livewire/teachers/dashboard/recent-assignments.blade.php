
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Assignments -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Assignments</h3>
            <div class="space-y-3">
                @forelse($recentAssignments as $assignment)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $assignment->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $assignment->academicSubject->name }}</p>
                                <p class="text-xs text-gray-500">
                                    Due: {{ $assignment->ends_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($assignment->status === 'published') bg-green-100 text-green-800
                                    @elseif($assignment->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                                <a href="{{ route('teachers.assignments.show', $assignment) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No assignments created yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Submissions -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Submissions to Grade</h3>
            <div class="space-y-3">
                @forelse($pendingSubmissions as $submission)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $submission->assignment->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $submission->student->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    Submitted: {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <a href="{{ route('teachers.assignments.grade', [$submission->assignment, $submission]) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition duration-200">
                                Grade
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No submissions pending grading.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
