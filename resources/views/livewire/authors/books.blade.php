<div>
    <!-- Page Header with Quick Actions -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">My Books Library</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage, track, and grow your published collection</p>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Quick Actions Dropdown -->
            <div class="relative hidden" x-data="{ open: false }">
                <button @click="open = !open" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                    Quick Actions
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                    <a href="{{ route('author.analytics.index') }}"
                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        📊 View Analytics
                    </a>
                    <a href="{{ route('author.revenue.index') }}"
                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        💰 Revenue Report
                    </a>
                    <a href="{{ route('author.promotions.index') }}"
                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        🎯 Promotions
                    </a>
                </div>
            </div>

            <a href="{{ route('author.books.create') }}"
               class="relative px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                <span>Add New Book</span>
                <div class="absolute inset-0 bg-white opacity-20 rounded-lg blur-xl -z-10"></div>
            </a>
        </div>
    </div>

    <!-- Enhanced Stats Cards with Trending Indicators -->
    @if($books)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow-sm p-6 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Books</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-white">{{ $books->total() }}</p>
                        <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                                Growing collection
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-blue-500 dark:bg-blue-600 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow-sm p-6 border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-white">{{ $books->sum('subscriptions_count') }}</p>
                        <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                          clip-rule="evenodd"/>
                                </svg>
                                +12% this month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-green-500 dark:bg-green-600 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M16 7c0-2.21-1.79-4-4-4s-4 1.79-4 4 1.79 4 4 4 4-1.79 4-4zm4 7v4h-2v-4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v4H4v-4c0-2.21 1.79-4 4-4h8c2.21 0 4 1.79 4 4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg shadow-sm p-6 border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Total Borrowings</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-white">{{ $books->sum('borrowings_count') }}</p>
                        <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                                Popular reads
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-purple-500 dark:bg-purple-600 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow-sm p-6 border border-yellow-200 dark:border-yellow-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Total Revenue</p>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-white">
                            GHS {{ number_format($books->sum('annual_subscription_fee'), 2) }}</p>
                        <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                          clip-rule="evenodd"/>
                                </svg>
                                +8% this month
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-500 dark:bg-yellow-600 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters with Visual Improvements -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filter & Search</h3>
                <button wire:click="resetFilters"
                        class="text-sm text-violet-600 hover:text-violet-700 dark:text-violet-400">
                    Clear All Filters
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Enhanced Search -->
                <div class="col-span-1 md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Books
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search"
                            wire:model.live="search"
                            placeholder="Search by title, publisher, or description..."
                            class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white transition-colors"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Category Filter -->
                <div>
                    <label for="categoryFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Category
                    </label>
                    <select
                        id="categoryFilter"
                        wire:model.live="categoryFilter"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white transition-colors"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Enhanced Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status
                    </label>
                    <select
                        id="statusFilter"
                        wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white transition-colors"
                    >
                        <option value="">All Status</option>
                        <option value="draft">📝 Draft</option>
                        <option value="published">✅ Published</option>
                        <option value="under_review">🔍 Under Review</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- View Toggle and Sort Options -->
        <div class="flex flex-wrap items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">View:</span>
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button
                        wire:click="$set('viewType', 'grid')"
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $viewType === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400' }}"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M10 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V2a2 2 0 012-2h2a2 2 0 012 2zm0 12v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2zm6-12v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V2a2 2 0 012-2h2a2 2 0 012 2zm0 12v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2z"/>
                        </svg>
                    </button>
                    <button
                        wire:click="$set('viewType', 'list')"
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $viewType === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400' }}"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Sort by:</span>
                <select
                    wire:model.live="sortBy"
                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                >
                    <option value="created_at">Date Created</option>
                    <option value="title">Title</option>
                    <option value="subscriptions_count">Subscriptions</option>
                    <option value="borrowings_count">Borrowings</option>
                    <option value="annual_subscription_fee">Revenue</option>
                </select>
                <button
                    wire:click="toggleSortDirection"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                >
                    <svg class="w-4 h-4 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Books Display -->
        @if($books->count() > 0)
            @if($viewType === 'grid')
                <!-- Grid View -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($books as $book)
                        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-xl hover:border-violet-300 dark:hover:border-violet-600 transition-all duration-300 transform hover:-translate-y-1.5">
                            <!-- Book Cover with Overlay -->
                            <div class="relative aspect-[3/4] max-h-64 w-full border-b border-gray-500 shadow-lg drop-shadow-md bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-t-xl overflow-hidden">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}"
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-50 to-indigo-50 dark:from-violet-900/30 dark:to-indigo-900/30">
                                        <svg class="w-16 h-16 text-violet-400 dark:text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badge - Positioned in top-right corner -->
                                <div class="absolute top-2 right-2">
                                    @if($book->status === 'published')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800/50 dark:text-green-200 rounded-full shadow-sm">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    Published
                </span>
                                    @elseif($book->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-800/50 dark:text-amber-200 rounded-full shadow-sm">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Draft
                </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-800/50 dark:text-blue-200 rounded-full shadow-sm">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    Review
                </span>
                                    @endif
                                </div>

                                <!-- Quick Actions Overlay - Appears on hover -->
                                <div class="absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-3">
                                    <button class="p-2 bg-white/80 dark:bg-gray-800/80 rounded-full text-gray-900 dark:text-gray-100 hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button class="p-2 bg-white/80 dark:bg-gray-800/80 rounded-full text-gray-900 dark:text-gray-100 hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Book Info -->
                            <div class="p-4">
                                <!-- Title and Category -->
                                <div class="mb-3">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors duration-200">
                                        {{ $book->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        {{ $book->bookCategory->name ?? 'No Category' }}
                                    </p>
                                </div>

                                <!-- Enhanced Stats Grid -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-lg p-2 text-center border border-green-100 dark:border-green-800">
                                        <div class="flex items-center justify-center mb-1">
                                            <svg class="w-3 h-3 text-green-600 dark:text-green-400 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span class="text-sm font-bold text-green-700 dark:text-green-300">{{ number_format($book->subscriptions_count) }}</span>
                                        </div>
                                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Subscriptions</div>
                                    </div>
                                    <div class="bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/30 dark:to-violet-900/30 rounded-lg p-2 text-center border border-purple-100 dark:border-purple-800">
                                        <div class="flex items-center justify-center mb-1">
                                            <svg class="w-3 h-3 text-purple-600 dark:text-purple-400 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm font-bold text-purple-700 dark:text-purple-300">{{ number_format($book->borrowings_count) }}</span>
                                        </div>
                                        <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">Borrowings</div>
                                    </div>
                                </div>

                                <!-- Revenue Section -->
                                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/30 dark:to-orange-900/30 rounded-lg p-2 text-center mb-3 border border-yellow-100 dark:border-yellow-800">
                                    <div class="flex items-center justify-center mb-1">
                                        <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-bold text-yellow-700 dark:text-yellow-300">GHS {{ number_format($book->annual_subscription_fee, 2) }}</span>
                                    </div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Annual Subscription</div>
                                </div>

                                <!-- Performance Indicator -->
                                @php
                                    $performanceScore = ($book->subscriptions_count * 2) + $book->borrowings_count;
                                    $performanceClass = $performanceScore >= 50 ? 'text-green-600 dark:text-green-400' :
                                                       ($performanceScore >= 20 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-500 dark:text-gray-400');
                                @endphp
                                <div class="flex items-center justify-center mb-3 text-xs">
                                    <span class="text-gray-600 dark:text-gray-400 mr-1">Performance:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $performanceScore >= ($i * 10) ? $performanceClass : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>

                                <!-- Enhanced Actions -->
                                <div class="flex space-x-2">
                                    <a href="{{route('books.show', ['book' => $book])}}" class="flex-1 inline-flex items-center justify-center px-2 py-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white text-xs font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{route('author.books.edit', ['book' => $book])}}" class="flex-1 inline-flex items-center justify-center px-2 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <button wire:click="confirmDelete({{ $book->id }})" class="inline-flex items-center justify-center px-2 py-1 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-xs font-medium rounded-lg transition-all duration-200 border border-red-200 dark:border-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Books List</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Book
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Subscriptions
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Borrowings
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Revenue
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($books as $book)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-12">
                                                @if($book->cover_image)
                                                    <img src="{{  $book->cover_image }}"
                                                         alt="{{ $book->title }}"
                                                         class="h-16 w-12 object-cover rounded">
                                                @else
                                                    <div
                                                        class="h-16 w-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $book->title }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $book->publisher ?? 'No Publisher' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $book->bookCategory->name ?? 'No Category' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($book->status === 'published')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    ✅ Published
                                                </span>
                                        @elseif($book->status === 'draft')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                    📝 Draft
                                                </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                    🔍 Under Review
                                                </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                                <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                                    {{ $book->subscriptions_count }}
                                                </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                                <span class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                                    {{ $book->borrowings_count }}
                                                </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                                <span class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                                                    GHS {{ number_format($book->annual_subscription_fee, 2) }}
                                                </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{route('books.show', $book)}}"
                                               class="btn btn-sm bg-violet-500 hover:bg-violet-600 text-white">
                                                View
                                            </a>
                                            <a href="{{route('author.books.edit', $book)}}"
                                               class="btn btn-sm bg-gray-500 hover:bg-gray-600 text-white">
                                                Edit
                                            </a>
                                            <button wire:click="confirmDelete({{ $book->id }})"
                                                    class="btn btn-sm bg-red-500 hover:bg-red-600 text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>

    @elseif($books && $books->count() === 0)
        <!-- Empty State -->
        <div class="text-center py-12">
            <div
                class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No books found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                @if($search || $categoryFilter || $statusFilter)
                    No books match your current filters. Try adjusting your search criteria.
                @else
                    You haven't published any books yet. Create your first book to get started!
                @endif
            </p>
            <div class="flex justify-center space-x-4">
                @if($search || $categoryFilter || $statusFilter)
                    <button
                        wire:click="$set('search', '')"
                        class="btn bg-gray-500 hover:bg-gray-600 text-white">
                        Clear Filters
                    </button>
                @endif
                <a href="{{ route('author.books.create') }}"
                   class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    Create Your First Book
                </a>
            </div>
        </div>
    @else
        <!-- Not an Author -->
        <div class="text-center py-12">
            <div
                class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Author Profile Required</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                You need to complete your author profile to start publishing books.
            </p>
            <a href="{{ route('author.profile.show') }}"
               class="btn bg-violet-500 hover:bg-violet-600 text-white">
                Complete Author Profile
            </a>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Book</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete "{{ $bookToDelete->title }}"? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-4">
                    <button
                        wire:click="cancelDelete"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button
                        wire:click="deleteBook"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
