<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.school-switcher') }}"
           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to School Switcher
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <!-- School Header with Logo -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
            <div class="flex items-center gap-6">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-20 w-20 rounded-lg bg-white p-2">
                @else
                    <div class="h-20 w-20 rounded-lg bg-white flex items-center justify-center">
                        <span class="text-3xl font-bold text-blue-600">{{ substr($school->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-white">{{ $school->name }}</h1>
                    <p class="mt-1 text-blue-100">{{ $school->code }}</p>
                    <div class="mt-3 flex items-center gap-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $school->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($school->status) }}
                        </span>
                        @if($school->type)
                            <span class="text-blue-100 text-sm">{{ ucfirst($school->type) }}</span>
                        @endif
                        @if($school->subscription_plan)
                            <span class="text-blue-100 text-sm">{{ ucfirst($school->subscription_plan) }} Plan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border-b border-gray-200 dark:border-gray-700">
            <div class="px-6 py-5 border-r border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Students</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_students'] ?? 0) }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400">{{ $stats['active_students'] ?? 0 }} active</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5 border-r border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teachers</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_teachers'] ?? 0) }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400">{{ $stats['active_teachers'] ?? 0 }} active</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5 border-r border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Groups</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['academic_groups'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['academic_levels'] ?? 0 }} levels</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-md p-3">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Staff</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format(($stats['total_librarians'] ?? 0) + ($stats['total_parents'] ?? 0)) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Librarians & Parents</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <a href="mailto:{{ $school->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">{{ $school->email }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <a href="tel:{{ $school->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">{{ $school->phone }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($school->website)
                            <a href="{{ $school->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                {{ $school->website }}
                            </a>
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $school->address }}<br>
                        {{ $school->city }}@if($school->state), {{ $school->state }}@endif {{ $school->postal_code }}<br>
                        {{ $school->country }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Timezone</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $school->timezone ?? 'UTC' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Currency</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $school->currency ?? 'GHS' }}</dd>
                </div>
            </div>
        </div>

        @if($school->description)
        <!-- About Section -->
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">About</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $school->description }}</p>
        </div>
        @endif

        <!-- Academic Structure -->
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Academic Structure</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($academicGroups->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Groups ({{ $academicGroups->count() }})</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($academicGroups as $group)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $group->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($academicLevels->count() > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Levels ({{ $academicLevels->count() }})</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($academicLevels->take(10) as $level)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ $level->name }}
                                </span>
                            @endforeach
                            @if($academicLevels->count() > 10)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    +{{ $academicLevels->count() - 10 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Groups</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['student_groups'] ?? 0 }}</dd>
                    <dd class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['active_student_groups'] ?? 0 }} active</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Capacity</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $school->student_capacity ? number_format($school->student_capacity) : 'Unlimited' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Periods</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $stats['total_academic_periods'] ?? 0 }}</dd>
                    <dd class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['active_periods'] ?? 0 }} active</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $school->isRegistrationOpen() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                            {{ $school->isRegistrationOpen() ? 'Open' : 'Closed' }}
                        </span>
                    </dd>
                </div>
            </div>
        </div>

        <!-- Current Academic Period -->
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Current Academic Period</h3>
            @if($stats['current_period'] && $stats['current_period'] !== 'No active period')
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['current_period'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Academic year in progress</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['current_period_progress'] ?? 0 }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Complete</p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['current_period_progress'] ?? 0 }}%"></div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No active academic period</p>
            @endif
        </div>

        <!-- Recent Academic Periods -->
        @if($recentPeriods->count() > 0)
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Academic Periods</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Period</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Academic Year</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dates</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentPeriods as $period)
                        <tr>
                            <td class="px-3 py-3 text-sm text-gray-900 dark:text-white">{{ $period->name }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 dark:text-white">{{ $period->academic_year }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $period->start_date?->format('M d') }} - {{ $period->end_date?->format('M d, Y') }}
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $period->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $period->status === 'upcoming' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $period->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Subscription & System Info -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Subscription Details</h3>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plan</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $school->subscription_plan ? ucfirst($school->subscription_plan) : 'Free' }}</dd>
                        </div>
                        @if($school->subscription_ends_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $school->subscription_ends_at->format('M d, Y') }}</dd>
                                <dd class="text-xs {{ $school->subscription_ends_at->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $school->subscription_ends_at->isPast() ? 'Expired' : $school->subscription_ends_at->diffForHumans() }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">System Information</h3>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">School Code</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $school->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $school->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $school->updated_at->diffForHumans() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.school-switcher') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
            Back to List
        </a>
    </div>
</div>
