<div class="space-y-6">
    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Parent Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Welcome back! Here's a comprehensive overview of your ward's activities.
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Ward Selection Header -->
    @if($this->wards->count() > 0)
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ $this->selectedWard ? substr($this->selectedWard->user->name, 0, 2) : '?' }}
                    </div>
                    <div class="ml-4 flex-1">
                        @if($this->selectedWard)
                            <h2 class="text-2xl font-bold">{{ $this->selectedWard->user->name }}</h2>
                            <p class="text-violet-100">
                                {{ $this->selectedWard->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $this->selectedWard->academicLevel->name ?? 'N/A' }}
                            </p>
                        @else
                            <h2 class="text-2xl font-bold">Select a Ward</h2>
                            <p class="text-violet-100">Choose a student to view their information</p>
                        @endif
                    </div>
                </div>

                <!-- Dropdown Selector (only if multiple wards) -->
                @if($this->wards->count() > 1)
                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                        <button @click="open = !open"
                                class="flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition-all">
                            <span class="font-medium">Switch Ward</span>
                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto"
                             style="display: none;">
                            <div class="p-2">
                                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Your Wards ({{ $this->wards->count() }})
                                </div>
                                @foreach($this->wards as $ward)
                                    <button wire:click="selectWard({{ $ward->id }})"
                                            @click="open = false"
                                            class="w-full flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ $selectedWardId == $ward->id ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                            {{ substr($ward->user->name, 0, 2) }}
                                        </div>
                                        <div class="ml-3 flex-1 text-left">
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $ward->user->name }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        @if($selectedWardId == $ward->id)
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($this->selectedWard)
        @php
            $academic = $this->academicOverview;
            $attendance = $this->attendanceOverview;
            $fees = $this->feeOverview;
            $library = $this->libraryActivity;
        @endphp

            <!-- Key Insights -->
        @if($this->insights->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->insights as $insight)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 {{ $insight['type'] === 'success' ? 'border-green-500' : ($insight['type'] === 'warning' ? 'border-yellow-500' : 'border-blue-500') }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($insight['icon'] === 'trophy')
                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"/>
                                    </svg>
                                @elseif($insight['icon'] === 'alert')
                                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $insight['title'] }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $insight['message'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Main Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Academic Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Academic Progress</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $academic['total_submissions'] }}</p>
                <div class="mt-3 flex items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ $academic['pending_assignments'] }} pending</span>
                    @if($academic['average_score'] > 0)
                        <span class="ml-2 text-green-600 dark:text-green-400 font-medium">
                            • {{ number_format($academic['average_score'], 1) }}% avg
                        </span>
                    @endif
                </div>
            </div>

            <!-- Attendance Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($attendance['attendance_rate'], 1) }}%</p>
                <div class="mt-3 flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <span class="text-green-600">{{ $attendance['present_days'] }} present</span>
                    @if($attendance['absent_days'] > 0)
                        <span class="ml-2 text-red-600">• {{ $attendance['absent_days'] }} absent</span>
                    @endif
                </div>
            </div>

            <!-- Fee Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Fee Balance</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $fees['currency'] }} {{ number_format($fees['balance'], 2) }}</p>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                        <span>Paid: {{ $fees['currency'] }} {{ number_format($fees['total_paid'], 2) }}</span>
                        <span>{{ number_format($fees['payment_percentage'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full transition-all" style="width: {{ min($fees['payment_percentage'], 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Library Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Library</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $library['books_borrowed'] }}</p>
                <div class="mt-3 flex items-center text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ $library['active_subscriptions'] }} active</span>
                    @if($library['has_overdues'])
                        <span class="ml-2 text-red-600 dark:text-red-400">• {{ $library['overdue_books'] }} overdue</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Recent Activity & Upcoming Events -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Activity</h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Last 7 days</span>
                    </div>
                    <div class="p-6">
                        @forelse($this->recentActivity as $activity)
                            <div class="flex items-start space-x-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $activity['color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900/20' : ($activity['color'] === 'green' ? 'bg-green-100 dark:bg-green-900/20' : ($activity['color'] === 'purple' ? 'bg-purple-100 dark:bg-purple-900/20' : 'bg-gray-100 dark:bg-gray-700')) }}">
                                        @if($activity['icon'] === 'document')
                                            <svg class="w-5 h-5 {{ $activity['color'] === 'blue' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @elseif($activity['icon'] === 'check')
                                            <svg class="w-5 h-5 {{ $activity['color'] === 'green' ? 'text-green-600 dark:text-green-400' : ($activity['color'] === 'red' ? 'text-red-600' : 'text-yellow-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $activity['date']->diffForHumans() }}</p>
                                </div>
                                @if(isset($activity['meta']))
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $activity['meta'] }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Upcoming Events & Deadlines</h2>
                    </div>
                    <div class="p-6">
                        @forelse($this->upcomingEvents as $event)
                            <div class="flex items-start space-x-3 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg flex flex-col items-center justify-center {{ $event['urgency'] === 'high' ? 'bg-red-100 dark:bg-red-900/20' : 'bg-blue-100 dark:bg-blue-900/20' }}">
                                        <span class="text-xs font-bold {{ $event['urgency'] === 'high' ? 'text-red-600' : 'text-blue-600' }}">{{ $event['date']->format('d') }}</span>
                                        <span class="text-[10px] {{ $event['urgency'] === 'high' ? 'text-red-500' : 'text-blue-500' }}">{{ $event['date']->format('M') }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $event['title'] }}</p>
                                        @if($event['urgency'] === 'high')
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Urgent
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event['description'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $event['date']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No upcoming events</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Summary -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Quick Actions</h2>
                    <div class="space-y-2">
                        <a href="{{ route('parent.performance') }}" class="block w-full px-4 py-3 text-center bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition font-medium">
                            View Performance
                        </a>
                        <a href="{{ route('parent.fees.index') }}" class="block w-full px-4 py-3 text-center bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                            Pay Fees
                        </a>
                        <a href="{{ route('parent.reports') }}" class="block w-full px-4 py-3 text-center border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                            Download Reports
                        </a>
                        <a href="{{ route('parent.library') }}" class="block w-full px-4 py-3 text-center border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                            Library
                        </a>
                    </div>
                </div>

                <!-- Current Borrowings -->
                @if($library['books_borrowed'] > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Current Books</h2>
                        <div class="space-y-3">
                            @foreach($library['current_borrowings'] as $borrowing)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $borrowing->book->title ?? 'Book' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Due: {{ $borrowing->due_date ? $borrowing->due_date->format('M d, Y') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Summary This Month -->
                <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <h2 class="text-lg font-semibold mb-4">This Month Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-violet-100">Assignments</span>
                            <span class="text-lg font-bold">{{ $academic['total_submissions'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-violet-100">Attendance</span>
                            <span class="text-lg font-bold">{{ number_format($attendance['attendance_rate'], 1) }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-violet-100">Books Read</span>
                            <span class="text-lg font-bold">{{ $library['books_this_month'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Ward Selected -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">No Wards Found</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">You don't have any wards assigned to your account.</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Please contact your school administrator to link your ward(s).</p>
        </div>
    @endif
</div>
