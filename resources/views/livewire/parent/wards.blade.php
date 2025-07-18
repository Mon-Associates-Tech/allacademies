<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-2xl p-8 mb-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-6 lg:mb-0">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">My Wards</h1>
                <p class="text-violet-100 text-lg">Track your children's academic journey in real-time</p>
                <div class="mt-4 flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <span class="text-sm">{{ $this->wards->count() }} Active Ward{{ $this->wards->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                        <span class="text-sm">{{ $this->performanceOverview['total_assessments'] ?? 0 }} Total Assessments</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold mb-1">{{ number_format($this->performanceOverview['overall_average'] ?? 0, 1) }}%</div>
                    <div class="text-sm text-violet-100">Overall Average</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="searchTerm"
                       placeholder="Search wards by name, class, or level..."
                       class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400">
            </div>

            <!-- Sort and Results -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>Sort by:</span>
                    <button wire:click="sortBy('name')"
                            class="flex items-center space-x-1 px-3 py-1 rounded-lg transition-colors {{ $sortBy === 'name' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <span>Name</span>
                        @if($sortBy === 'name')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $sortDirection === 'asc' ? '8 9l4-4 4 4' : '16 15l-4 4-4-4' }}"/>
                            </svg>
                        @endif
                    </button>
                </div>

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ $this->wards->count() }} result{{ $this->wards->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Wards List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @forelse($this->wards as $index => $ward)
            @php
                $wardPerformance = collect($this->performanceOverview['wards_data'])->firstWhere('ward.id', $ward->id);
            @endphp

            <div class="group border-b border-gray-200 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                <!-- Main Ward Row -->
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <!-- Ward Basic Info -->
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-r from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ substr($ward->user->name, 0, 1) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $ward->user->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ward->user->email }}</p>
                            </div>
                        </div>

                        <!-- Academic Level (Desktop) -->
                        <div class="hidden md:flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h1m4 0h1"/>
                            </svg>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ward->academicLevel->academicGroup->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ward->academicLevel->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Quick Stats (Desktop) -->
                        @if($wardPerformance)
                            <div class="hidden lg:flex items-center space-x-6">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($wardPerformance['average_score'], 1) }}%
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Average</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $wardPerformance['assessments_count'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tests</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        {{ $wardPerformance['passed_count'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Passed</div>
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <button wire:click="selectWard({{ $ward->id }})"
                                    class="inline-flex items-center px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View
                            </button>

                            <!-- More Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false"
                                        class="p-2 text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus:outline-none focus:ring-2 focus:ring-violet-500 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('parent.performance.student', $ward->id) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            Performance
                                        </a>
                                        <a href="{{ route('parent.reports') }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Reports
                                        </a>
                                        <a href="{{ route('parent.books') }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            Books
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Info Row (Mobile + Performance Details) -->
                <div class="px-6 pb-6">
                    <!-- Mobile Academic Info -->
                    <div class="md:hidden mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h1m4 0h1"/>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Performance Summary -->
                    @if($wardPerformance)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($wardPerformance['average_score'], 1) }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Average Score</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    {{ $wardPerformance['assessments_count'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Total Tests</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    {{ $wardPerformance['passed_count'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Passed</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <div class="flex items-center space-x-1">
                                    @if($wardPerformance['performance_trend'] === 'improving')
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Improving</span>
                                    @elseif($wardPerformance['performance_trend'] === 'declining')
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                        </svg>
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">Declining</span>
                                    @else
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Stable</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Trend</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Overall Progress</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($wardPerformance['average_score'], 0) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-violet-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ min($wardPerformance['average_score'], 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-16 text-center">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">No Wards Found</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    @if($searchTerm)
                        No wards match your search criteria. Try adjusting your search terms.
                    @else
                        You don't have any wards registered yet. Contact your school administrator to add ward connections.
                    @endif
                </p>
                @if($searchTerm)
                    <button wire:click="$set('searchTerm', '')"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Clear Search
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('parent.performance') }}"
               class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 dark:text-gray-100">View Performance</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Check academic progress</p>
                </div>
            </a>

            <a href="{{ route('parent.reports') }}"
               class="flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 dark:text-gray-100">Generate Reports</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Download detailed reports</p>
                </div>
            </a>

            <a href="{{ route('parent.books') }}"
               class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 dark:text-gray-100">Manage Books</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Book subscriptions</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Ward Details Modal -->
    @if($showWardDetails && $this->selectedWard)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ward Details</h3>
                        <button wire:click="closeWardDetails" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-r from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($this->selectedWard->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $this->selectedWard->user->name }}</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $this->selectedWard->user->email }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->selectedWard->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $selectedWard->academicLevel->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($this->wardPerformanceData)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $this->wardPerformanceData['total_assessments'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Assessments</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ number_format($this->wardPerformanceData['average_score'], 1) }}%
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Average Score</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $this->wardPerformanceData['passed_assessments'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Passed</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $this->wardPerformanceData['subjects_count'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Subjects</div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('parent.performance.student', $this->selectedWard->id) }}"
                           class="flex-1 bg-violet-500 hover:bg-violet-600 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                            View Full Performance
                        </a>
                        <a href="{{ route('parent.reports') }}"
                           class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors">
                            Generate Reports
                        </a>
                        <button wire:click="closeWardDetails"
                                class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-center py-3 px-4 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
