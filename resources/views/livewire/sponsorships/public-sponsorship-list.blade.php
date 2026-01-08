<section>
    <div
        class="py-12 bg-gradient-to-br from-gray-50 via-violet-50/30 to-gray-50 dark:from-gray-900 dark:via-violet-900/10 dark:to-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Header -->
            <div class="text-center mb-12 relative">
                <!-- Decorative Elements -->
                <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none">
                    <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                    </svg>
                </div>

                <div class="relative">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-violet-100 dark:bg-violet-900/30 rounded-full mb-4">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2" fill="currentColor"
                             viewBox="0 0 20 20">
                            <path
                                d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                        </svg>
                        <span
                            class="text-sm font-semibold text-violet-600 dark:text-violet-400">Make a Difference Today</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 bg-clip-text text-transparent bg-gradient-to-r from-violet-600 to-indigo-600">
                        Sponsorship Projects
                    </h1>
                    <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                        Support causes, projects, and individuals in need. Your contribution makes a real difference.
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span>Platform fee: <span class="font-semibold text-violet-600">1%</span></span>
                        <span class="mx-2">•</span>
                        <span>Secure payments via Paystack</span>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Active Projects</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $projects->total() }}</p>
                        </div>
                        <div
                            class="w-12 h-12 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Raised</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                GHS {{ number_format($projects->sum('amount_raised'), 0) }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Goal Amount</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                GHS {{ number_format($projects->sum('amount_goal'), 0) }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Beneficiaries</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                {{ $projects->sum(fn($p) => $p->beneficiaries->count()) }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters - Enhanced -->
            <div
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter & Search
                    </h3>
                    @if($search || $selectedType || $sortBy !== 'latest')
                        <button wire:click="$set('search', ''); $set('selectedType', ''); $set('sortBy', 'latest')"
                                class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear Filters
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search with icon -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search
                            Projects</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Search by name, code, or description..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                            @if($search)
                                <button wire:click="$set('search', '')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Type Filter with icons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project
                            Type</label>
                        <div class="relative">
                            <select wire:model.live="selectedType"
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                                <option value="">All Types</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sort with icons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                        <div class="relative">
                            <select wire:model.live="sortBy"
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                                <option value="latest">Latest Projects</option>
                                <option value="amount_high">Highest Goal</option>
                                <option value="amount_low">Lowest Goal</option>
                                <option value="progress">Most Progress</option>
                                <option value="deadline">Ending Soon</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if($search || $selectedType)
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
                        @if($search)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200">
                            Search: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-2 hover:text-violet-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                        @endif
                        @if($selectedType)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200">
                            Type: {{ $types[$selectedType] }}
                            <button wire:click="$set('selectedType', '')" class="ml-2 hover:text-violet-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="mb-4">
                <div
                    class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-lg p-4 flex items-center">
                    <svg class="animate-spin h-5 w-5 text-violet-600 mr-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-violet-700 dark:text-violet-300 font-medium">Loading projects...</span>
                </div>
            </div>

            <!-- Projects Grid - Enhanced Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($projects as $project)
                    <div
                        class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col transform hover:-translate-y-1">
                        <!-- Card Header with Gradient -->
                        <div class="relative h-3 bg-gradient-to-r from-violet-500 via-purple-500 to-indigo-500"></div>

                        <div class="p-6 flex-grow">
                            <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200 ring-2 ring-violet-500/20">
                                {{ $project->code }}
                            </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $project->type === 'emergency' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 ring-2 ring-red-500/20' : '' }}
                                {{ $project->type === 'scholarship' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ring-2 ring-blue-500/20' : '' }}
                                {{ $project->type === 'project' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 ring-2 ring-green-500/20' : '' }}
                                {{ $project->type === 'cause' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ring-2 ring-yellow-500/20' : '' }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                          clip-rule="evenodd"/>
                                </svg>
                                {{ ucfirst($project->type) }}
                            </span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors line-clamp-2">
                                {{ $project->name }}
                            </h3>

                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3 leading-relaxed">
                                {{ $project->description }}
                            </p>

                            @if($project->deadline)
                                <div
                                    class="mb-4 inline-flex items-center px-3 py-1 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 rounded-lg text-xs font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Ends: {{ $project->deadline->format('M d, Y') }}
                                    @if($project->deadline->isPast())
                                        <span class="ml-1 text-red-600">(Expired)</span>
                                    @elseif($project->deadline->diffInDays() <= 7)
                                        <span
                                            class="ml-1 animate-pulse">• {{ $project->deadline->diffForHumans() }}</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Enhanced Progress Section -->
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">Progress</span>
                                    <span class="text-violet-600 dark:text-violet-400 font-bold text-lg">
                                    {{ $project->progress_percentage }}%
                                </span>
                                </div>

                                <!-- Animated Progress Bar -->
                                <div
                                    class="relative w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-blue-500 via-violet-500 to-purple-600 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg"
                                        style="width: {{ $project->progress_percentage }}%">
                                        <!-- Shine effect -->
                                        <div
                                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                        <p class="text-gray-500 dark:text-gray-400 mb-1">Raised</p>
                                        <p class="font-bold text-green-600 dark:text-green-400">
                                            GHS {{ number_format($project->realized_amount, 2) }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                        <p class="text-gray-500 dark:text-gray-400 mb-1">Goal</p>
                                        <p class="font-bold text-blue-600 dark:text-blue-400">
                                            GHS {{ number_format($project->goal_amount, 2) }}
                                        </p>
                                    </div>
                                </div>

                                @if($project->left_amount > 0)
                                    <div
                                        class="text-center py-2 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-lg">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Still needed</p>
                                        <p class="font-bold text-red-600 dark:text-red-400">
                                            GHS {{ number_format($project->left_amount, 2) }}
                                        </p>
                                    </div>
                                @else
                                    <div
                                        class="text-center py-2 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg">
                                        <p class="text-sm font-bold text-green-600 dark:text-green-400 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Goal Achieved!
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Beneficiaries Info -->
                            @if($project->beneficiaries->count() > 0)
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <div class="flex -space-x-2 mr-2">
                                        @foreach($project->beneficiaries->take(3) as $beneficiary)
                                            <div
                                                class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-gray-800">
                                                {{ substr($beneficiary->name ?? 'B', 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <span class="font-medium">
                                    {{ $project->beneficiaries->count() }} beneficiar{{ $project->beneficiaries->count() === 1 ? 'y' : 'ies' }}
                                </span>
                                </div>
                            @endif
                        </div>

                        <!-- Enhanced Donate Action -->
                        <div
                            class="p-4 bg-gradient-to-r from-gray-50 to-violet-50/30 dark:from-gray-700/50 dark:to-violet-900/10 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('sponsorships.projects.contribute', $project) }}"
                               class="group/btn relative block w-full py-3 px-4 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white text-center rounded-xl text-sm font-semibold transition-all shadow-md hover:shadow-xl transform hover:scale-105 overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2 group-hover/btn:animate-bounce" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Donate Now
                                <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                                <!-- Hover shine effect -->
                                <div
                                    class="absolute inset-0 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                            </a>

                            <a href="{{ route('sponsorships.projects.show', $project) }}"
                               class="block w-full mt-2 py-2 px-4 text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 text-center rounded-lg text-sm font-medium transition-colors hover:bg-violet-50 dark:hover:bg-violet-900/20">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Enhanced Empty State -->
                    <div class="col-span-full">
                        <div class="max-w-md mx-auto text-center">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-12 border-2 border-dashed border-gray-200 dark:border-gray-700">
                                <div
                                    class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-900/30 dark:to-purple-900/30 mb-6">
                                    <svg class="h-12 w-12 text-violet-600 dark:text-violet-400" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                    No Projects Found
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                                    @if($search || $selectedType)
                                        No projects match your search criteria. Try adjusting your filters.
                                    @else
                                        There are no active sponsorship projects at the moment. Check back soon or
                                        become a benefactor to list your cause.
                                    @endif
                                </p>
                                @if($search || $selectedType)
                                    <button wire:click="$set('search', ''); $set('selectedType', '')"
                                            class="inline-flex items-center justify-center px-6 py-3 border-2 border-violet-600 text-base font-medium rounded-xl text-violet-600 hover:bg-violet-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Clear Filters
                                    </button>
                                @else
                                    @auth
                                        <a href="{{ route('benefactors.projects.create') }}"
                                           class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Create a Project
                                        </a>
                                    @else
                                        <a href="{{ route('sign-in') }}"
                                           class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                            Login to Create a Project
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($projects->hasPages())
                <div class="mt-12">
                    {{ $projects->links() }}
                </div>
            @endif

            <!-- Enhanced CTA Section -->
            <div
                class="mt-16 relative overflow-hidden bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 rounded-3xl p-12 shadow-2xl">
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

                <div class="relative text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-6">
                        <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                        </svg>
                        <span class="text-white font-semibold text-sm">Join Our Community</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                        Want to List Your Cause?
                    </h2>
                    <p class="text-violet-100 text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                        If you have a project, cause, or individuals that need support, register as a benefactor and
                        submit your project for verification.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ route('benefactors.index') }}"
                               class="group inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white hover:bg-white hover:text-violet-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                </svg>
                                Benefactor Dashboard
                            </a>
                        @else
                            <a href="{{ route('sign-up') }}"
                               class="group inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white hover:bg-white hover:text-violet-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Register as Benefactor
                            </a>
                        @endauth
                        <a href="{{ route('sponsorships.offers.index') }}"
                           class="group inline-flex items-center justify-center px-8 py-4 bg-white text-base font-semibold rounded-xl text-violet-600 hover:bg-violet-50 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            View Sponsor Offers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add shimmer animation -->
    <style>
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
    </style>

</section>
