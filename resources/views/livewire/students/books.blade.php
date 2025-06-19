<div class="space-y-6">
    @php use App\Models\BookSubscription; @endphp
    <!-- Enhanced Header with Statistics -->
    <div class="bg-gradient-to-r from-emerald-500 via-teal-600 to-cyan-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">My Library</h1>
                    <p class="text-emerald-100 mt-1">Explore, subscribe, and manage your digital book collection</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="hidden lg:flex items-center space-x-6">
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $totalBooks ?? 0 }}</div>
                    <div class="text-sm text-emerald-200">Available</div>
                </div>
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $subscribedCount ?? 0 }}</div>
                    <div class="text-sm text-emerald-200">Subscribed</div>
                </div>
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $borrowedCount ?? 0 }}</div>
                    <div class="text-sm text-emerald-200">Borrowed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages with Better Design -->
    @if (session()->has('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm dark:bg-green-900/20 dark:border-green-400" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-green-700 dark:text-green-200">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm dark:bg-red-900/20 dark:border-red-400" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-red-700 dark:text-red-200">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Enhanced Tab Navigation -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="changeTab('available')"
                        class="@if($bookTab === 'available') border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif flex items-center space-x-2 whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Available Books</span>
                    @if(isset($availableCount))
                        <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs font-medium px-2 py-1 rounded-full">{{ $availableCount }}</span>
                    @endif
                </button>
                <button wire:click="changeTab('subscribed')"
                        class="@if($bookTab === 'subscribed') border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif flex items-center space-x-2 whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>My Subscriptions</span>
                    @if(isset($subscribedCount))
                        <span class="bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200 text-xs font-medium px-2 py-1 rounded-full">{{ $subscribedCount }}</span>
                    @endif
                </button>
                <button wire:click="changeTab('borrowed')"
                        class="@if($bookTab === 'borrowed') border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif flex items-center space-x-2 whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0120 8.414V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    <span>Borrowed Books</span>
                    @if(isset($borrowedCount))
                        <span class="bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200 text-xs font-medium px-2 py-1 rounded-full">{{ $borrowedCount }}</span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- Enhanced Filters Section -->
        @if($bookTab === 'available')
            <div class="p-6 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
                    <!-- Search and Filters -->
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="relative">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Books</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input wire:model.debounce.300ms="search"
                                       type="text"
                                       id="search"
                                       class="pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors"
                                       placeholder="Search by title, author..."
                                       wire:loading.class="opacity-50"
                                       wire:loading.attr="disabled">
                            </div>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select wire:model.live="selectedCategory"
                                    id="category"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors"
                                    wire:loading.class="opacity-50"
                                    wire:loading.attr="disabled">
                                <option value="">All Categories</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format</label>
                            <select wire:model.live="selectedFormat"
                                    id="format"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors"
                                    wire:loading.class="opacity-50"
                                    wire:loading.attr="disabled">
                                <option value="">All Formats</option>
                                <option value="softcopy">📱 Digital</option>
                                <option value="hardcopy">📚 Physical</option>
                            </select>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price</label>
                            <select wire:model.live="selectedPrice"
                                    id="price"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-colors"
                                    wire:loading.class="opacity-50"
                                    wire:loading.attr="disabled">
                                <option value="">All Prices</option>
                                <option value="free">🆓 Free</option>
                                <option value="paid">💰 Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div>
                        <button wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading class="text-center py-8">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-emerald-500 hover:bg-emerald-400 transition ease-in-out duration-150 cursor-not-allowed">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading books...
        </div>
    </div>

    <!-- Content based on active tab -->
{{--    <div wire:loading.remove class="space-y-6">--}}
        @if($bookTab === 'available')
            @include('livewire.students.partials.available-books', ['availableBooks' => $this->availableBooks])
        @elseif($bookTab === 'subscribed')
            @include('livewire.students.partials.subscribed-books', ['subscribedBooks' => $this->subscribedBooks])
        @elseif($bookTab === 'borrowed')
            @include('livewire.students.partials.borrowed-books', ['borrowedBooks' => $this->borrowedBooks])
        @endif
    @livewire('students.book-subscription-modal')
    </div>
