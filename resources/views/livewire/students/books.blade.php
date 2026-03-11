@php use App\Models\BookSubscription; @endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Books</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your book subscriptions and borrowings</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative dark:bg-green-900 dark:border-green-700 dark:text-green-200 animate-fadeIn" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative dark:bg-red-900 dark:border-red-700 dark:text-red-200 animate-fadeIn" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Enhanced Tabs with modern styling -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="changeTab('available')"
                    class="@if($bookTab === 'available') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Available Books
            </button>
            <button wire:click="changeTab('subscribed')"
                    class="@if($bookTab === 'subscribed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Subscribed Books
            </button>
            <button wire:click="changeTab('borrowed')"
                    class="@if($bookTab === 'borrowed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Borrowed Books
            </button>
        </nav>
    </div>

    <!-- Enhanced Filters -->
    @if($bookTab === 'available')
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="group">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.debounce.300ms="search"
                               type="text"
                               id="search"
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-400 sm:text-sm transition-colors duration-200"
                               placeholder="Search books..."
                               wire:loading.class="opacity-50"
                               wire:loading.attr="disabled">
                    </div>
                </div>
                <div class="group">
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select wire:model.live="selectedCategory"
                            id="category"
                            class="block w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-400 sm:text-sm transition-colors duration-200"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Categories</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="group">
                    <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format</label>
                    <select wire:model.live="selectedFormat"
                            id="format"
                            class="block w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-400 sm:text-sm transition-colors duration-200"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Formats</option>
                        <option value="softcopy">Softcopy</option>
                        <option value="hardcopy">Hardcopy</option>
                    </select>
                </div>
                <div class="group">
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price</label>
                    <select wire:model.live="selectedPrice"
                            id="price"
                            class="block w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-400 sm:text-sm transition-colors duration-200"
                            wire:loading.class="opacity-50"
                            wire:loading.attr="disabled">
                        <option value="">All Prices</option>
                        <option value="free">Free</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Enhanced Loading Indicator -->
        <div wire:loading class="flex justify-center items-center py-8">
            <div class="inline-flex items-center px-6 py-3 font-medium leading-6 text-sm text-indigo-600 bg-indigo-50 rounded-lg dark:bg-indigo-900 dark:text-indigo-300">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading books...
            </div>
        </div>
    @endif

    <!-- Enhanced Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6" wire:key="books-grid">
        @forelse($books as $book)
            @include('livewire.books.partials.book')
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-full w-full">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No books found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($bookTab === 'available')
                            Try adjusting your search or filters to find books.
                        @else
                            You don't have any {{ $bookTab }} books yet.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="">
        @if($books->hasPages())
            {{$books->links()}}
        @endif
    </div>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>

