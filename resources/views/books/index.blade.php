<x-layouts.app page-name="Books Library">

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-700 text-white relative rounded-t-lg overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30v-4h-2v4h-4v2h4v4h2v-4h4V4h-4z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
                <div class="text-center">
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2 tracking-tight animate-fade-in">
                        <span class="inline-block transform transition-transform duration-300 hover:scale-105">📚</span>
                        Library
                    </h1>
                    <p class="text-sm sm:text-base text-blue-100 mb-4 max-w-xl mx-auto opacity-90 animate-slide-up">
                        Discover, Subscribe, and Read Books Online
                    </p>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3 max-w-2xl mx-auto mt-4">
                        <div class="bg-white/10 dark:bg-gray-900/20 backdrop-blur-md rounded-xl p-3 transition-all duration-300 hover:bg-white/20 dark:hover:bg-gray-900/30 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="text-xl font-bold text-blue-50">{{ $books->total() }}</div>
                            <div class="text-xs text-blue-200 font-medium">Available Books</div>
                        </div>
                        <div class="bg-white/10 dark:bg-gray-900/20 backdrop-blur-md rounded-xl p-3 transition-all duration-300 hover:bg-white/20 dark:hover:bg-gray-900/30 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="text-xl font-bold text-blue-50">{{ $categories->count() }}</div>
                            <div class="text-xs text-blue-200 font-medium">Categories</div>
                        </div>
                        <div class="bg-white/10 dark:bg-gray-900/20 backdrop-blur-md rounded-xl p-3 transition-all duration-300 hover:bg-white/20 dark:hover:bg-gray-900/30 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="text-xl font-bold text-blue-50">{{ count($subscribedBookIds) }}</div>
                            <div class="text-xs text-blue-200 font-medium">Your Subscriptions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Custom Animations */
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slide-up {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 0.9;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.6s ease-out forwards;
            }

            .animate-slide-up {
                animation: slide-up 0.8s ease-out forwards;
            }
        </style>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-900 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <form method="GET" action="{{ route('books.index') }}" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label for="search"
                                   class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors duration-200">
                                Search Books
                            </label>
                            <div class="relative group">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                       placeholder="Search by title or author..."
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg
                                        class="h-5 w-5 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category"
                                   class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors duration-200">
                                Category
                            </label>
                            <select name="category" id="category"
                                    class="w-full px-3 py-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option
                                        value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Format Filter -->
                        <div>
                            <label for="format"
                                   class="block text-xs font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors duration-200">
                                Format
                            </label>
                            <select name="format" id="format"
                                    class="w-full px-3 text-sm py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600">
                                <option value="">All Formats</option>
                                <option value="softcopy" {{ request('format') == 'softcopy' ? 'selected' : '' }}>📱
                                    Softcopy Only
                                </option>
                                <option value="hardcopy" {{ request('format') == 'hardcopy' ? 'selected' : '' }}>📖
                                    Hardcopy Only
                                </option>
                                <option value="both" {{ request('format') == 'both' ? 'selected' : '' }}>📚 Both
                                    Formats
                                </option>
                            </select>
                        </div>

                        <!-- Price Filter -->
                        <div>
                            <label for="price"
                                   class="block text-xs font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors duration-200">
                                Price
                            </label>
                            <select name="price" id="price"
                                    class="w-full px-3 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600">
                                <option value="">All Books</option>
                                <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>🆓 Free Books
                                </option>
                                <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>💰 Paid Books
                                </option>
                                <option value="subscribed" {{ request('price') == 'subscribed' ? 'selected' : '' }}>💰 Subscribed Books
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button type="submit"
                                class="bg-blue-600 text-sm hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 hover:shadow-md">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('books.index') }}"
                           class="bg-gray-200 dark:bg-gray-700 text-sm hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-6 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 hover:shadow-md">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Books Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($books->count() > 0)
                <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
                    @foreach($books as $book)
                        <!-- Book Card -->
                        @include('livewire.books.partials.book-card', ['book' => $book])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No books found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Try adjusting your search criteria or browse all
                        books.</p>
                    <a href="{{ route('books.index') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Browse All Books
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif


    @push('scripts')
        <script>
            // Auto-submit form on filter change
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                const selects = form.querySelectorAll('select');

                selects.forEach(select => {
                    select.addEventListener('change', function () {
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
