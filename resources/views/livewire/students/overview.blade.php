<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Welcome Card -->
        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-600 dark:text-gray-300">Track your academic progress, access learning resources, and stay on schedule.</p>
        </div>

        <!-- Quick Stats Cards -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">Overall Score</h3>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $overallScore }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">My Books</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $bookCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 2A3.5 3.5 0 002 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 007.38 2.732 3.5 3.5 0 005.5 2zM6 5.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">Upcoming Activities</h3>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $upcomingActivitiesCount }}</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Activities -->
        <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Upcoming Activities</h3>
            </div>
            <div class="p-4">
                @if(count($upcomingActivities) > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($upcomingActivities as $activity)
                            <div class="py-3 flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium">{{ $activity->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $activity->is_group_activity ? 'Group: ' . $activity->group->name : 'Individual' }}
                                        @if($activity->subject)
                                            · {{ $activity->subject->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full
                                        @if($activity->activity_type === 'exam') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($activity->activity_type === 'quiz') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($activity->activity_type === 'assessment') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                        @elseif($activity->activity_type === 'group_meeting') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($activity->activity_type === 'book_reading') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                    </span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $activity->start_time->format('M d, g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400">No upcoming activities</p>
                    </div>
                @endif
                <div class="mt-4">
                    <button wire:click="$dispatch('studentTabChanged', {tab: 'schedule'})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                        View full schedule →
                    </button>
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Subject Performance</h3>
            </div>
            <div class="p-4">
                @if(count($subjectPerformance) > 0)
                    @foreach($subjectPerformance as $subject)
                        <div class="mb-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium">{{ $subject['name'] }}</span>
                                <span class="text-sm font-medium">{{ $subject['score'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $subject['score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400">No assessment data available</p>
                    </div>
                @endif
                <div class="mt-4 text-right">
                    <button wire:click="dispatch('studentTabChanged', {tab: 'performance'})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                        View detailed performance →
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Books -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold">My Recent Books</h3>
        </div>
        <div class="p-4">
            @if(count($bookSubscriptions) > 0)

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($bookSubscriptions as  $subscription)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col">
                            <div class="h-40 bg-gray-200 dark:bg-gray-600 rounded-lg mb-3 overflow-hidden">
                                @if($subscription->book->cover_image)
                                    <img src="{{ $subscription->book->cover_image }}" alt="{{ $subscription->book->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-medium mb-1 line-clamp-1">{{ $subscription->book->title }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 line-clamp-1">{{ $subscription->book->author->name }}</p>
                            <div class="mt-auto">
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mb-2">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $subscription->book->progress }}%"></div>
                                </div>
                                <a href="{{ route('dashboard', $subscription->book->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                                    Continue Reading
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-right">
                    <button wire:click="$dispatch('studentTabChanged', {tab: 'my-books'})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                        View all books →
                    </button>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No books yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by browsing available books.</p>
                    <div class="mt-6">
                        <button wire:click="$dispatch('studentTabChanged', {tab: 'my-books'})" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Browse Books
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Assessments -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Recent Assessments</h3>
            <button wire:click="$dispatch('studentTabChanged', {tab: 'self-assessment'})" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-3 rounded-md">
                Take New Assessment
            </button>
        </div>
        <div class="p-4">
            @if(count($recentAssessments) > 0)
                <div class="overflow-x-auto">
                    <div class="min-w-full inline-block align-middle">
                        <div class="overflow-hidden border-b border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th scope="col" class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Topic
                                    </th>
                                    <th scope="col" class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentAssessments as $assessment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $assessment->subject->name }}
                                            </div>
                                            <div class="md:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $assessment->created_at->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $assessment->topic ? $assessment->topic->name : 'All Topics' }}
                                        </td>
                                        <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $assessment->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($assessment->status === 'completed')
                                                <span class="font-semibold {{ $assessment->percentage_score >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ round($assessment->percentage_score, 1) }}%
                                            </span>
                                            @elseif($assessment->status === 'needs_grading')
                                                <span class="text-yellow-600 dark:text-yellow-400">Pending</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($assessment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($assessment->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($assessment->status === 'needs_grading') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-right">
                    <button wire:click="$dispatch('studentTabChanged', {tab: 'performance'})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                        View all assessments →
                    </button>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assessments completed</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Take a self-assessment to track your progress.</p>
                    <div class="mt-6">
                        <button wire:click="$dispatch('studentTabChanged', {tab: 'self-assessment'})" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Start Assessment
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

