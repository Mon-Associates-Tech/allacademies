<div class="py-12 bg-gradient-to-br from-gray-50 via-indigo-50/30 to-gray-50 dark:from-gray-900 dark:via-indigo-900/10 dark:to-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Header -->
        <div class="text-center mb-12 relative">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                </svg>
            </div>

            <div class="relative">
                <div class="inline-flex items-center px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-full mb-4">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Find Sponsorship Opportunities</span>
                </div>

                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                    Sponsor Offers
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                    Browse available sponsorship opportunities and apply with your verified programs
                </p>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active Offers</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $offers->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Offered</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            GHS {{ number_format($offers->sum('amount_offered'), 0) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Applications</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $offers->sum('bids_count') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Accepting Bids</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $offers->where('accepts_bids', true)->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Search and Filters -->
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Search & Filter
                </h3>
                @if($search || $sortBy !== 'latest')
                    <button wire:click="$set('search', ''); $set('sortBy', 'latest')"
                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Clear Filters
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search with icon -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search offers by title, code, or description..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    @if($search)
                        <button wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Sort -->
                <div class="relative">
                    <select wire:model.live="sortBy"
                            class="w-full appearance-none pl-4 pr-10 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="latest">Latest Offers</option>
                        <option value="amount_high">Highest Amount</option>
                        <option value="amount_low">Lowest Amount</option>
                        <option value="expiring">Expiring Soon</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if($search)
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        Search: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-2 hover:text-indigo-900">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </span>
                </div>
            @endif
        </div>

        <!-- Loading State -->
        <div wire:loading.delay class="mb-4">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 flex items-center">
                <svg class="animate-spin h-5 w-5 text-indigo-600 mr-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-indigo-700 dark:text-indigo-300 font-medium">Loading offers...</span>
            </div>
        </div>

        <!-- Enhanced Offers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($offers as $offer)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col transform hover:-translate-y-1">
                    <!-- Card Header with Gradient -->
                    <div class="relative h-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                    <div class="p-6 flex-grow">
                        <!-- Title and Status -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/20 mb-2">
                                    {{ $offer->code }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                    {{ $offer->title }}
                                </h3>
                            </div>
                            @if($offer->accepts_bids)
                                <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 ring-2 ring-green-500/20">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Open
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3 leading-relaxed">
                            {{ $offer->description }}
                        </p>

                        <!-- Amount Offered - Highlighted -->
                        <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Amount Offered</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 flex items-baseline">
                                GHS {{ number_format($offer->amount_offered, 0) }}
                                <span class="text-sm ml-2 text-gray-500">.{{ str_pad((int)(($offer->amount_offered - floor($offer->amount_offered)) * 100), 2, '0', STR_PAD_LEFT) }}</span>
                            </p>
                        </div>

                        <!-- Criteria -->
                        @if($offer->criteria)
                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Eligibility Criteria:</span>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $offer->criteria }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Meta Information -->
                        <div class="space-y-2 mb-4 text-sm">
                            @if($offer->expires_at)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Expires {{ $offer->expires_at->diffForHumans() }}</span>
                                    @if($offer->expires_at->diffInDays() <= 7)
                                        <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded text-xs font-medium animate-pulse">
                                            Urgent
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>By {{ $offer->user->name }}</span>
                            </div>

                            @if($offer->bids_count > 0)
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>{{ $offer->bids_count }} application{{ $offer->bids_count === 1 ? '' : 's' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Enhanced Action Button -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-indigo-50/30 dark:from-gray-700/50 dark:to-indigo-900/10 border-t border-gray-100 dark:border-gray-700">
                        @auth
                            @if($offer->accepts_bids)
                                <button wire:click="openBidModal({{ $offer->id }})"
                                        class="group/btn relative block w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-center rounded-xl text-sm font-semibold transition-all shadow-md hover:shadow-xl transform hover:scale-105 overflow-hidden mb-2">
                                    <span class="relative z-10 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2 group-hover/btn:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Apply Now
                                        <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                    <div class="absolute inset-0 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                                </button>

                                <a href="{{ route('sponsorship.offers.show', $offer) }}"
                                   class="block w-full py-2 px-4 text-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-lg text-sm font-medium transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                    View Full Details
                                </a>
                            @else
                                <a href="{{ route('sponsorship.offers.show', $offer) }}"
                                   class="block w-full py-3 px-4 text-center border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-indigo-600 dark:hover:text-white rounded-xl text-sm font-semibold transition-all">
                                    View Details
                                </a>
                            @endif
                        @else
                            <a href="{{ route('sign-in') }}"
                               class="group/btn relative block w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-center rounded-xl text-sm font-semibold transition-all shadow-md hover:shadow-xl transform hover:scale-105 mb-2">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign in to Apply
                                </span>
                            </a>

                            <a href="{{ route('sponsorship.offers.show', $offer) }}"
                               class="block w-full py-2 px-4 text-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-lg text-sm font-medium transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                View Details
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <!-- Enhanced Empty State -->
                <div class="col-span-full">
                    <div class="max-w-md mx-auto text-center">
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-12 border-2 border-dashed border-gray-200 dark:border-gray-700">
                            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 mb-6">
                                <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                No Offers Found
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                                @if($search)
                                    No offers match your search criteria. Try adjusting your filters.
                                @else
                                    No active sponsor offers at the moment. Check back soon or consider creating your own offer!
                                @endif
                            </p>
                            @if($search)
                                <button wire:click="$set('search', '')"
                                        class="inline-flex items-center justify-center px-6 py-3 border-2 border-indigo-600 text-base font-medium rounded-xl text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Clear Filters
                                </button>
                            @else
                                @auth
                                    <a href="{{ route('sponsor.offers.create') }}"
                                       class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Create an Offer
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($offers->hasPages())
            <div class="mt-12">
                {{ $offers->links() }}
            </div>
        @endif

        <!-- Enhanced CTA Section -->
        <div class="mt-16 relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-12 shadow-2xl">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative text-center">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                    Have a Program That Needs Sponsorship?
                </h2>
                <p class="text-indigo-100 text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                    Create and verify your sponsorship program to apply for these amazing opportunities!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('benefactor.programs.create') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 text-base font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create a Program
                        </a>
                        <a href="{{ route('sponsorship.programs.index') }}"
                           class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-semibold rounded-xl text-white hover:bg-white hover:text-indigo-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            View All Programs
                        </a>
                    @else
                        <a href="{{ route('sign-up') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 text-base font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Bid Modal -->
    @if($showBidModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="closeBidModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl" wire:click.stop>
                <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Submit Your Application
                        </h3>
                        <button wire:click="closeBidModal" class="text-white hover:text-gray-200 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if(session()->has('message'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <p class="text-green-800 dark:text-green-200">{{ session('message') }}</p>
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form wire:submit.prevent="submitBid">
                        <!-- Select Program -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Your Program <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="selectedProgramId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Choose a verified program...</option>
                                @foreach($userPrograms as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->name }} ({{ $program->code }}) - Goal: GHS {{ number_format($program->amount_goal, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedProgramId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                            @if(count($userPrograms) === 0)
                                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        ⚠️ You need to create and get your program verified first before applying for sponsorship offers.
                                    </p>
                                    <a href="{{ route('benefactor.programs.create') }}" class="mt-2 inline-flex items-center text-sm font-medium text-yellow-700 dark:text-yellow-300 hover:text-yellow-900">
                                        Create a program now →
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Message -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Application Message (Optional)
                            </label>
                            <textarea wire:model="bidMessage" rows="5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" placeholder="Explain why your program is a perfect match for this sponsorship opportunity..."></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tell the sponsor how this funding will help achieve your program's goals</p>
                            @error('bidMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="closeBidModal" class="px-6 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition font-semibold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" {{ count($userPrograms) === 0 ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="submitBid" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Submit Application
                                </span>
                                <span wire:loading wire:target="submitBid" class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
