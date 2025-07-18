<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Library Books</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your library's book collection</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Books</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->totalBooks }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->availableBooks }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Borrowed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->borrowedBooks }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Categories</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->categories->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="relative lg:col-span-2">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search books, authors, descriptions..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Category Filter -->
            <div>
                <select
                    wire:model.live="categoryFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="">All Categories</option>
                    @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select
                    wire:model.live="statusFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <!-- Format Filter -->
            <div>
                <select
                    wire:model.live="formatFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="">All Formats</option>
                    <option value="physical">Physical</option>
                    <option value="digital">Digital</option>
                    <option value="both">Both</option>
                </select>
            </div>

            <!-- Availability Filter -->
            <div>
                <select
                    wire:model.live="availabilityFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="">All Books</option>
                    <option value="available">Available</option>
                    <option value="borrowed">Borrowed</option>
                </select>
            </div>
        </div>

        <!-- Sort Options and Reset -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Sort by:</span>
                <button
                    wire:click="sortBy('title')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $sortBy === 'title' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
                >
                    Title
                    @if ($sortBy === 'title')
                        <svg class="w-4 h-4 ml-1 inline {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @endif
                </button>
                <button
                    wire:click="sortBy('created_at')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $sortBy === 'created_at' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
                >
                    Date Added
                    @if ($sortBy === 'created_at')
                        <svg class="w-4 h-4 ml-1 inline {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @endif
                </button>
                <button
                    wire:click="sortBy('total_borrowings_count')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors {{ $sortBy === 'total_borrowings_count' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}"
                >
                    Popularity
                    @if ($sortBy === 'total_borrowings_count')
                        <svg class="w-4 h-4 ml-1 inline {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @endif
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $books->total() }} books found</span>
                <button
                    wire:click="resetFilters"
                    class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                >
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
        @forelse ($books as $book)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Book Cover -->
                <div class="relative h-48 bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center">
                    @if ($book->cover_image)
                        <img
                            src="{{$book->cover_image}}"
                            alt="{{ $book->title }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="text-white text-center">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="text-sm font-medium">{{ Str::limit($book->title, 20) }}</p>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-2 left-2">
                        @if ($book->status === 'published')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        @endif
                    </div>

                    <!-- Availability Badge -->
                    <div class="absolute top-2 right-2">
                        @if ($book->active_borrowings_count > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Borrowed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Available
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Book Details -->
                <div class="p-4">
                    <div class="mb-2">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm line-clamp-2">
                            {{ $book->title }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            by {{ $book->author->user->name ?? 'Unknown Author' }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span>{{ $book->bookCategory->name ?? 'Uncategorized' }}</span>
                        <span>{{ $book->format ?? 'Physical' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span>{{ $book->total_borrowings_count }} borrowings</span>
                        <span>{{ $book->created_at->format('M Y') }}</span>
                    </div>

                    @if ($book->description)
                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                            {{ $book->description }}
                        </p>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button
                            wire:click="toggleBookStatus({{ $book->id }})"
                            class="flex-1 px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $book->status === 'published' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                        >
                            {{ $book->status === 'published' ? 'Unpublish' : 'Publish' }}
                        </button>

                        <button
                            class="flex-1 px-3 py-1 text-xs font-medium text-violet-700 bg-violet-100 rounded-lg hover:bg-violet-200 transition-colors"
                            onclick="window.location.href='{{ route('librarian.books.show', $book->id) }}'"
                        >
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No books found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($search || $categoryFilter || $statusFilter || $formatFilter || $availabilityFilter)
                            Try adjusting your search criteria or filters.
                        @else
                            Get started by adding some books to your library.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($books->hasPages())
        <div class="flex justify-center">
            {{ $books->links() }}
        </div>
    @endif
</div>

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush
