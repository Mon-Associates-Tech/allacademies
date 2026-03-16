<div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Verification Queue</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Review and approve sponsorship
                        projects</p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session()->has('message'))
            <div
                class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-start">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mt-0.5 mr-3 flex-shrink-0"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-emerald-800 dark:text-emerald-200">{{ session('message') }}</p>
            </div>
        @endif

        @if(session()->has('error'))
            <div
                class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl flex items-start">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor"
                     viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-rose-800 dark:text-rose-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pending Review</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_count'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Awaiting verification</p>
                    </div>
                    <div
                        class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Approved Today</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['approved_today'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Verified today</p>
                    </div>
                    <div
                        class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Verified</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['verified_total'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">All approved</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Rejected</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['rejected_total'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Returned to draft</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="setTab('pending')"
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'pending' ? 'border-slate-600 text-slate-600 dark:border-slate-400 dark:text-slate-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    Pending Review
                    @if($stats['pending_count'] > 0)
                        <span
                            class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full {{ $activeTab === 'pending' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $stats['pending_count'] }}
                            </span>
                    @endif
                </button>
                <button wire:click="setTab('verified')"
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'verified' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    Verified projects
                    @if($stats['verified_total'] > 0)
                        <span
                            class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full {{ $activeTab === 'verified' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $stats['verified_total'] }}
                            </span>
                    @endif
                </button>
                <button wire:click="setTab('rejected')"
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'rejected' ? 'border-red-600 text-red-600 dark:border-red-400 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    Rejected projects
                    @if($stats['rejected_total'] > 0)
                        <span
                            class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full {{ $activeTab === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $stats['rejected_total'] }}
                            </span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- Search & Filter Bar -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <!-- Search -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by program name, code, or benefactor..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500">
                    @if($search)
                        <button wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Type Filter -->
                <div class="md:w-64 relative">
                    <select wire:model.live="selectedType"
                            class="w-full appearance-none pl-4 pr-10 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500">
                        <option value="">All Program Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $selectedType)
                <div class="mt-4 flex flex-wrap gap-2 items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Active filters:</span>
                    @if($search)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            Search: "{{ $search }}"
                            <button wire:click="$set('search', '')"
                                    class="ml-2 hover:text-slate-900 dark:hover:text-slate-100">
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
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            Type: {{ $types[$selectedType] }}
                            <button wire:click="$set('selectedType', '')"
                                    class="ml-2 hover:text-slate-900 dark:hover:text-slate-100">
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
        <div wire:loading.delay class="mb-6">
            <div
                class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-center">
                <svg class="animate-spin h-5 w-5 text-slate-600 dark:text-slate-400 mr-3" fill="none"
                     viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-slate-700 dark:text-slate-300 font-medium text-sm">Loading projects...</span>
            </div>
        </div>

        <!-- projects List -->
        <div class="space-y-6">
            @forelse($programs as $program)
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Header Bar -->
                    <div
                        class="h-1.5 bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700"></div>

                    <div class="p-6">
                        <!-- Program Header -->
                        <div class="flex items-start justify-between mb-5">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $program->name }}</h3>
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $program->code }}
                                        </span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ ucfirst($program->type) }}
                                        </span>
                                    <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Submitted {{ $program->created_at->diffForHumans() }}
                                        </span>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 text-xs font-medium rounded-lg border
                                    {{ $activeTab === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border-amber-100 dark:border-amber-800' : '' }}
                                    {{ $activeTab === 'verified' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : '' }}
                                    {{ $activeTab === 'rejected' ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border-red-100 dark:border-red-800' : '' }}">
                                    {{ $activeTab === 'pending' ? 'Pending Review' : '' }}
                                {{ $activeTab === 'verified' ? 'Verified' : '' }}
                                {{ $activeTab === 'rejected' ? 'Rejected' : '' }}
                                </span>
                        </div>

                        <!-- Benefactor Info (Always Visible) -->
                        <div
                            class="mb-5 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Benefactor
                                    Information</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div class="flex items-center">
                                    <span class="text-gray-500 dark:text-gray-400 min-w-[60px]">Name:</span>
                                    <span
                                        class="font-medium text-gray-900 dark:text-white ml-2">{{ $program->user->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-500 dark:text-gray-400 min-w-[60px]">Email:</span>
                                    <span
                                        class="font-medium text-gray-900 dark:text-white ml-2">{{ $program->user->email }}</span>
                                </div>
                                @if($program->school)
                                    <div class="flex items-center md:col-span-2">
                                        <span class="text-gray-500 dark:text-gray-400 min-w-[60px]">School:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white ml-2">{{ $program->school->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Project Description Preview (Always Visible) -->
                        <div class="mb-5">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Project Description
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ in_array($program->id, $expandedPrograms) ? $program->description : Str::limit($program->description, 300) }}
                            </p>
                        </div>

                        <!-- Expandable Content -->
                        @if(in_array($program->id, $expandedPrograms))
                            <div class="space-y-5">

                                @if($program->affected_individuals)
                                    <div
                                        class="p-4 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-800/50 dark:to-slate-800/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Expected
                                            Impact</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $program->affected_individuals }}</p>
                                    </div>
                                @endif

                                <!-- Program Stats Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        class="p-4 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-800/50 dark:to-slate-800/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Funding
                                            Goal</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            GHS {{ number_format($program->amount_goal, 2) }}</p>
                                    </div>

                                    @if($program->deadline)
                                        <div
                                            class="p-4 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-slate-800/50 dark:to-slate-800/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                Deadline</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $program->deadline->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Beneficiaries Section -->
                                @if($program->beneficiaries->count() > 0)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Beneficiaries ({{ $program->beneficiaries->count() }})
                                        </h4>
                                        <div class="grid gap-3">
                                            @foreach($program->beneficiaries as $beneficiary)
                                                <div
                                                    class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700 text-sm">
                                                    <div class="flex items-center justify-between">
                                                            <span
                                                                class="font-medium text-gray-900 dark:text-white">{{ $beneficiary->beneficiary_name }}</span>
                                                        <span
                                                            class="px-2 py-0.5 text-xs rounded-md bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                                {{ ucfirst($beneficiary->beneficiary_type) }}
                                                            </span>
                                                    </div>
                                                    @if($beneficiary->beneficiary_description)
                                                        <p class="text-gray-600 dark:text-gray-400 mt-1.5">{{ $beneficiary->beneficiary_description }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Expand/Collapse Button -->
                        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                            <button wire:click="toggleExpand({{ $program->id }})"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                                <span>{{ in_array($program->id, $expandedPrograms) ? 'Show Less' : 'Show More Details' }}</span>
                                <svg
                                    class="w-5 h-5 transition-transform {{ in_array($program->id, $expandedPrograms) ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Action Buttons -->
                        @if($activeTab === 'pending')
                            <div
                                class="flex flex-col sm:flex-row items-center gap-3 pt-5 border-t border-gray-100 dark:border-gray-700 mt-5">
                                <button wire:click="approve({{ $program->id }})"
                                        class="w-full sm:flex-1 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors shadow-sm hover:shadow flex items-center justify-center"
                                        wire:loading.attr="disabled"
                                        wire:target="approve({{ $program->id }})">
                                    <span wire:loading.remove wire:target="approve({{ $program->id }})"
                                          class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve Program
                                    </span>
                                    <span wire:loading wire:target="approve({{ $program->id }})"
                                          class="flex items-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Approving...
                                    </span>
                                </button>
                                <button wire:click="openRejectModal({{ $program->id }})"
                                        class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-950/30 hover:border-red-300 dark:hover:border-red-800 rounded-xl font-medium transition-all shadow-sm flex items-center justify-center group">
                                    <svg
                                        class="w-5 h-5 mr-2 text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span
                                        class="text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300">Reject Program</span>
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <div
                        class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Projects Pending Review</h3>
                    <p class="text-gray-500 dark:text-gray-400">All submitted Projects have been verified!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($programs->hasPages())
            <div class="mt-8">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

    <!-- Enhanced Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             wire:click="closeRejectModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full" wire:click.stop>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reject Project</h3>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Please provide a detailed reason for rejecting this project. This feedback will help the
                        benefactor improve their submission.
                    </p>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rejection Reason <span class="text-rose-500">*</span>
                        </label>
                        <textarea wire:model="rejectionReason"
                                  rows="5"
                                  class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                                  placeholder="Explain why this project does not meet the verification criteria..."></textarea>
                        @error('rejectionReason')
                        <p class="text-rose-600 dark:text-rose-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button wire:click="closeRejectModal"
                                class="px-5 py-2.5 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                            Cancel
                        </button>
                        <button wire:click="reject"
                                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-medium transition-colors shadow-sm flex items-center"
                                wire:loading.attr="disabled"
                                wire:target="reject">
                            <span wire:loading.remove wire:target="reject">Confirm Rejection</span>
                            <span wire:loading wire:target="reject" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Rejecting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
