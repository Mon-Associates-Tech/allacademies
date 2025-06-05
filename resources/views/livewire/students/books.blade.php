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
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="changeTab('available')"
                    class="@if($activeTab === 'available') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Available Books
            </button>
            <button wire:click="changeTab('subscribed')"
                    class="@if($activeTab === 'subscribed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Subscribed Books
            </button>
            <button wire:click="changeTab('borrowed')"
                    class="@if($activeTab === 'borrowed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Borrowed Books
            </button>
        </nav>
    </div>

    <!-- Filters -->
    @if($activeTab === 'available')
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                <input wire:model.debounce.300ms="search" type="text" id="search"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                       placeholder="Search books...">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select wire:model="selectedCategory" id="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                <select wire:model="selectedFormat" id="format"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    <option value="">All Formats</option>
                    <option value="softcopy">Softcopy</option>
                    <option value="hardcopy">Hardcopy</option>
                </select>
            </div>
        </div>
    </div>
    @endif

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($books as $item)
            @php
                $book = $activeTab === 'available' ? $item : $item->book;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Book Cover -->
                <div class="aspect-w-3 aspect-h-4 bg-gray-200 dark:bg-gray-700">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="flex items-center justify-center h-48">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Book Info -->
                <div class="p-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $book->title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $book->author }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $book->category->name ?? 'Uncategorized' }}</p>

                    <!-- Format Badges -->
                    <div class="flex gap-2 mt-2">
                        @if($book->has_softcopy)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Softcopy
                            </span>
                        @endif
                        @if($book->has_hardcopy)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Hardcopy
                            </span>
                        @endif
                    </div>

                    <!-- Status for subscribed/borrowed books -->
                    @if($activeTab !== 'available')
                        <div class="mt-2">
                            @if($activeTab === 'subscribed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($item->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @elseif($activeTab === 'borrowed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($item->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($item->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($item->status === 'returned') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                                @if($item->due_date && $item->status === 'approved')
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Due: {{ $item->due_date->format('M d, Y') }}
                                    </p>
                                @endif
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if($activeTab === 'available')
                        <div class="mt-4 space-y-2">
                            @if($book->has_softcopy)
                                <button wire:click="subscribeToBook({{ $book->id }})"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-2 px-3 rounded-md transition duration-150 ease-in-out">
                                    Subscribe
                                </button>
                            @endif
                            @if($book->has_hardcopy && $book->available_copies > 0)
                                <button wire:click="borrowBook({{ $book->id }})"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-3 rounded-md transition duration-150 ease-in-out">
                                    Borrow ({{ $book->available_copies }} available)
                                </button>
                            @elseif($book->has_hardcopy)
                                <button disabled
                                        class="w-full bg-gray-400 text-white text-xs font-medium py-2 px-3 rounded-md cursor-not-allowed">
                                    No copies available
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No books found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($activeTab === 'available')
                            Try adjusting your search or filters.
                        @else
                            You haven't {{ $activeTab === 'subscribed' ? 'subscribed to' : 'borrowed' }} any books yet.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
{{--    @if($books->hasPages())--}}
        <div class="mt-6">
{{--            {{ $books->links() }}--}}
        </div>
{{--    @endif--}}
</div>
