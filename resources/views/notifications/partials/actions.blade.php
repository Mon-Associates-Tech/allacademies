@php
    $type = $notificationData['type'] ?? 'generic';
    $category = $notificationData['category'] ?? 'general';
    $data = $notificationData['data'] ?? [];
@endphp

<div class="flex flex-col sm:flex-row gap-4">
    @if($type === 'assignment' && !empty($data['assignment_id']))
        <!-- Assignment Actions -->
        @php
            $assignmentStatus = $data['status'] ?? 'unknown';
            $startsAt = !empty($data['starts_at']) ? \Carbon\Carbon::parse($data['starts_at']) : null;
            $endsAt = !empty($data['ends_at']) ? \Carbon\Carbon::parse($data['ends_at']) : null;
            $now = now();
            $canTake = $startsAt && $endsAt && $now->between($startsAt, $endsAt) && $assignmentStatus === 'published';
        @endphp

        @if($canTake)
            <a href="{{ route('students.assignment.take', ['assignment' => $data['assignment_id']]) }}"
               class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Start Assignment
            </a>
        @elseif($startsAt && $now->lt($startsAt))
            <div class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-xl text-amber-700 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Starts {{ $startsAt->diffForHumans() }}
            </div>
        @elseif($endsAt && $now->gt($endsAt))
            <div class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-xl text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Assignment Ended
            </div>
        @endif

        <a href="{{ route('students.assignments') }}"
           class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            View All Assignments
        </a>

    @elseif($type === 'assessment' && !empty($data['quiz_id']))
        <!-- Assessment Actions -->
        <a href="{{ route('students.assessments') }}"
           class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            View All Assessments
        </a>

        <a href="{{ route('students.performance') }}"
           class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            View Performance
        </a>

    @else
        <!-- Generic/Other Actions -->
        @if(!empty($data['url']) || !empty($data['action_url']))
            <a href="{{ $data['url'] ?? $data['action_url'] }}"
               class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                View Details
            </a>
        @endif
    @endif

    <!-- Back Button (always shown) -->
    <button onclick="window.history.back()"
            class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Go Back
    </button>
</div>
