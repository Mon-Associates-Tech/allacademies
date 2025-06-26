<x-layouts.app>

@section('title', 'Student Library')

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">📚 Student Library</h1>
                    <p class="text-xl text-blue-100 mb-8">Discover, Subscribe, and Read Books Online</p>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">{{ $books->total() }}</div>
                            <div class="text-sm text-blue-100">Available Books</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">{{ $categories->count() }}</div>
                            <div class="text-sm text-blue-100">Categories</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">{{ count($subscribedBookIds) }}</div>
                            <div class="text-sm text-blue-100">Your Subscriptions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Books</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search by title or author..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Format Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format</label>
                            <select name="format" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">All Formats</option>
                                <option value="softcopy" {{ request('format') == 'softcopy' ? 'selected' : '' }}>📱 Softcopy Only</option>
                                <option value="hardcopy" {{ request('format') == 'hardcopy' ? 'selected' : '' }}>📖 Hardcopy Only</option>
                                <option value="both" {{ request('format') == 'both' ? 'selected' : '' }}>📚 Both Formats</option>
                            </select>
                        </div>

                        <!-- Price Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price</label>
                            <select name="price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">All Books</option>
                                <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>🆓 Free Books</option>
                                <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>💰 Paid Books</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            🔍 Apply Filters
                        </button>
                        <a href="{{ route('books.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            🔄 Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                            <!-- Book Cover -->
                            <div class="relative h-48 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 text-blue-300 dark:text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <p class="text-sm text-blue-400 dark:text-gray-400 font-medium">{{ $book->title }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Status Badges -->
                                <div class="absolute top-2 right-2 flex flex-col gap-1">
                                    @if(in_array($book->id, $subscribedBookIds))
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">✓ Subscribed</span>
                                    @endif
                                    @if(in_array($book->id, $borrowedBookIds))
                                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium">📖 Borrowed</span>
                                    @endif
                                    @if(!$book->annual_subscription_fee || $book->annual_subscription_fee == 0)
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">🆓 Free</span>
                                    @endif
                                </div>

                                <!-- Format Indicators -->
                                <div class="absolute bottom-2 left-2 flex gap-1">
                                    @if($book->has_softcopy)
                                        <span class="bg-black/50 text-white text-xs px-2 py-1 rounded-full">📱 Digital</span>
                                    @endif
                                    @if($book->has_hardcopy)
                                        <span class="bg-black/50 text-white text-xs px-2 py-1 rounded-full">📖 Physical</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Book Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1 line-clamp-2">{{ $book->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $book->author->name ?? 'Unknown Author' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-3">{{ $book->bookCategory->name ?? 'Uncategorized' }}</p>

                                @if($book->annual_subscription_fee > 0)
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400 mb-3">
                                        {{ $book->formatted_subscription_fee }}
                                        <span class="text-sm font-normal text-gray-500">/year</span>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('books.show', $book) }}"
                                       class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium text-center transition-colors">
                                        📋 View Details
                                    </a>

                                    @if(in_array($book->id, $subscribedBookIds) && $book->has_softcopy)
                                        <a href="{{ route('books.read', $book) }}"
                                           class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium text-center transition-colors">
                                            📖 Read Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No books found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Try adjusting your search criteria or browse all books.</p>
                    <a href="{{ route('books.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Browse All Books
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif


@push('scripts')
    <script>
        // Auto-submit form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const selects = form.querySelectorAll('select');

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    // Auto-submit after a short delay to allow for multiple quick changes
                    clearTimeout(window.filterTimeout);
                    window.filterTimeout = setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            });
        });
    </script>
@endpush
</x-layouts.app>
