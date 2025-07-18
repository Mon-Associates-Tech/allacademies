<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Parent Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Monitor your ward's academic progress and activities</p>
        </div>
    </div>

    <!-- Ward Selection -->
    @if($this->wards->count() > 1)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Select Ward</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->wards as $ward)
                    <div wire:click="selectWard({{ $ward->id }})"
                         class="cursor-pointer p-4 rounded-lg border-2 transition-colors {{ $selectedWardId == $ward->id ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-violet-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3">
                                {{ substr($ward->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $ward->user->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($this->selectedWard)
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Assessments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->academicSummary['total_assessments'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Score</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($this->academicSummary['average_score'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Book Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->bookSubscriptions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Tasks</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->academicSummary['pending_assessments'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Assessments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Assessments</h2>
                </div>
                <div class="p-6">
                    @if($this->recentAssessments->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->recentAssessments as $assessment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $assessment->subject->name ?? 'Unknown Subject' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $assessment->quiz->name ?? $assessment->examination->name ?? 'Assessment' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ $assessment->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold {{ $assessment->passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $assessment->score }}%
                                        </div>
                                        <div class="text-xs {{ $assessment->passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $assessment->passed ? 'Passed' : 'Failed' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('parent.performance') }}" class="text-violet-600 hover:text-violet-800 font-medium">
                                View All Assessments →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">No assessments yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Upcoming Events</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($this->upcomingEvents as $event)
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/20 rounded-full flex items-center justify-center">
                                        @if($event['type'] === 'meeting')
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        @elseif($event['type'] === 'exam')
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $event['title'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $event['date']->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Ward Profile Summary -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $this->selectedWard->user->name }}'s Profile</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Academic Information</h3>
                        <div class="space-y-1 text-sm">
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Level:</span> {{ $this->selectedWard->academicLevel->name ?? 'N/A' }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Group:</span> {{ $this->selectedWard->academicLevel->academicGroup->name ?? 'N/A' }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Class:</span> {{ $this->selectedWard->studentGroup->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Performance Overview</h3>
                        <div class="space-y-1 text-sm">
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Passed:</span> {{ $this->academicSummary['passed_assessments'] }} assessments
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Failed:</span> {{ $this->academicSummary['failed_assessments'] }} assessments
                            </p>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Pending:</span> {{ $this->academicSummary['pending_assessments'] }} assessments
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('parent.performance') }}" class="block text-sm text-violet-600 hover:text-violet-800">
                                View Detailed Performance →
                            </a>
                            <a href="{{ route('parent.reports') }}" class="block text-sm text-violet-600 hover:text-violet-800">
                                Generate Reports →
                            </a>
                            <a href="{{ route('parent.books') }}" class="block text-sm text-violet-600 hover:text-violet-800">
                                Manage Book Subscriptions →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100">No Wards Found</h3>
            <p class="mt-1 text-gray-600 dark:text-gray-400">You don't have any wards assigned to your account.</p>
        </div>
    @endif
</div>
