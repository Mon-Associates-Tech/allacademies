<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Book Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your ward's book subscriptions, borrowings, and discover new books</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="changeViewMode('grid')"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button wire:click="changeViewMode('list')"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            </div>
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
                            <div class="w-10 h-10 bg-violet-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($ward->user->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $ward->user->name }}</h3>
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
        <!-- Book Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subscribed</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->subscriptionStats['active_subscriptions'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Borrowed</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->subscriptionStats['active_borrowings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->subscriptionStats['total_subscriptions'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expired</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->subscriptionStats['expired_subscriptions'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cancelled</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->subscriptionStats['cancelled_subscriptions'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button wire:click="changeTab('subscribed')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'subscribed' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Subscribed Books
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-violet-100 text-violet-800 dark:bg-violet-900/20 dark:text-violet-400">
                                {{ $this->subscriptionStats['active_subscriptions'] }}
                            </span>
                        </div>
                    </button>

                    <button wire:click="changeTab('borrowed')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'borrowed' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Borrowed Books
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                {{ $this->subscriptionStats['active_borrowings'] }}
                            </span>
                        </div>
                    </button>

                    <button wire:click="changeTab('available')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'available' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Available Books
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Search and Filters -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input wire:model.live="searchTerm" type="text" placeholder="Search books..."
                                   class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <select wire:model.live="selectedCategoryId"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Categories</option>
                            @foreach($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <select wire:model.live="sortBy"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                            <option value="title">Sort by Title</option>
                            <option value="created_at">Sort by Date</option>
                        </select>
                        <button wire:click="$toggle('sortDirection')"
                                class="p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Books Content -->
            <div class="p-6">
                @if($this->books->count() > 0)
                    @if($viewMode === 'grid')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                            @foreach($this->books as $book)
                                @include('livewire.books.partials.book-card', ['book' => $book])
                            @endforeach
                        </div>
                    @else
                        <!-- List view -->
                        <div class="space-y-4">
                            @foreach($this->books as $book)
                                @include('livewire.books.partials.book-card', ['book' => $book])
                            @endforeach
                        </div>
                    @endif

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $this->books->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            No {{ $activeTab }} books found
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            @if($activeTab === 'available')
                                Explore available books to subscribe to for your ward.
                            @elseif($activeTab === 'subscribed')
                                Subscribe to books to start reading them.
                            @else
                                No borrowed books at the moment.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Subscription Modal -->
    @if($showSubscriptionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Subscribe to Book</h3>
                    @if($selectedBook)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedBook->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $selectedBook->author->name ?? 'Unknown Author' }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subscription Type
                        </label>
                        <select wire:model="subscriptionType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>

                    <div class="flex justify-center mt-6 space-x-3">
                        <button wire:click="closeSubscriptionModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="subscribeToBook"
                                class="px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 transition-colors">
                            Subscribe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
