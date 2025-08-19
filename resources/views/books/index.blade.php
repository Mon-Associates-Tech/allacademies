<x-layouts.app page-name="Books Library" :show-title-area="false">

    <div
        class="min-h-screen bg-gradient-to-br  dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">

        <header id="main-header"
                class="min-h-[60vh] flex items-center md:h-[60vh] bg-gradient-to-br from-slate-900 via-purple-900 to-indigo-900 lg:rounded-t-xl relative overflow-hidden transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/20 to-transparent"></div>
            <div
                class="absolute -top-10 -right-10 w-80 h-80 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div
                class="absolute -bottom-12 -left-12 w-96 h-96 bg-gradient-to-tr from-indigo-500/20 to-pink-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div
                class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-full blur-2xl animate-pulse delay-500"></div>

            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 left-20 w-2 h-2 bg-blue-400/60 rounded-full animate-bounce delay-100"></div>
                <div
                    class="absolute top-40 right-32 w-1 h-1 bg-purple-400/60 rounded-full animate-bounce delay-300"></div>
                <div
                    class="absolute bottom-32 left-1/3 w-1.5 h-1.5 bg-cyan-400/60 rounded-full animate-bounce delay-700"></div>
                <div
                    class="absolute bottom-20 right-20 w-1 h-1 bg-pink-400/60 rounded-full animate-bounce delay-1000"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center w-full justify-center h-full px-3 md:px-6 text-center">
                <div class="mb-6 hidden md:block pt-2">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-sm text-blue-200 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Thousands of books available
                    </div>
                </div>

                <h1 class="text-2xl md:text-7xl font-black text-white mb-6 pt-3 leading-tight">
                    <span class="hidden md:block">All Academies</span>
                    <span
                        class="block text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 animate-pulse">
                Books Library
            </span>
                </h1>

                <section class="w-full flex flex-col md:flex-row justify-center items-center">
                    <div class="w-full my-auto max-w-3xl relative group mb-4 md:mb-0 md:mr-4">
                        <form method="GET" action="{{ route('books.index') }}" class="space-y-6">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search by title, author, or genre..."
                                    class="relative w-full px-2 lg:px-8  py-3 lg:py-5 text-sm  md:text-lg bg-white/15 backdrop-blur-xl text-white placeholder-gray-200 rounded-2xl border border-white/30 focus:outline-none focus:ring-4 focus:ring-blue-400/50 focus:border-blue-400/50 transition-all duration-300 shadow-2xl"
                                />
                                <button
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 p-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <button id="toggle-filters"
                            class="w-full md:w-auto text-nowrap px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Show Filters</span>
                    </button>
                </section>


                <div id="filters"
                     class="hidden mb-4 mt-6 w-full max-w-7xl mx-auto px-6 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-8">
                    <form method="GET" action="{{ route('books.index') }}" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-200">Category</label>
                                <div class="relative">
                                    <select name="category" id="category"
                                            class="w-full px-4 py-3 text-sm bg-white/10 border-2 border-white/20 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 text-white transition-all duration-300 appearance-none cursor-pointer hover:bg-white/20">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option
                                                value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-200">Format</label>
                                <div class="relative">
                                    <select name="format" id="format"
                                            class="w-full px-4 py-3 text-sm bg-white/10 border-2 border-white/20 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 text-white transition-all duration-300 appearance-none cursor-pointer hover:bg-white/20">
                                        <option value="">All Formats</option>
                                        <option
                                            value="softcopy" {{ request('format') == 'softcopy' ? 'selected' : '' }}>
                                            📱 Digital Only
                                        </option>
                                        <option
                                            value="hardcopy" {{ request('format') == 'hardcopy' ? 'selected' : '' }}>
                                            📚 Physical Only
                                        </option>
                                        <option value="both" {{ request('format') == 'both' ? 'selected' : '' }}>
                                            🔄 Both Available
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-200">Pricing</label>
                                <div class="relative">
                                    <select name="price" id="price"
                                            class="w-full px-4 py-3 text-sm bg-white/10 border-2 border-white/20 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 text-white transition-all duration-300 appearance-none cursor-pointer hover:bg-white/20">
                                        <option value="">All Books</option>
                                        <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>
                                            🆓 Free Books
                                        </option>
                                        <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>
                                            💰 Paid Books
                                        </option>
                                        <option
                                            value="subscribed" {{ request('price') == 'subscribed' ? 'selected' : '' }}>
                                            📋 Subscription
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="items-center flex flex-col md:flex-row gap-4 mt-5">
                                <button type="submit"
                                        class="w-full my-auto text-nowrap bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                    </svg>
                                    <span>Apply Filters</span>
                                </button>
                                <a href="{{ route('books.index') }}"
                                   class="w-full text-nowrap bg-white/10 hover:bg-white/20 text-gray-200 px-6 py-3 rounded-xl font-semibold text-sm text-center transition-all duration-300 flex items-center justify-center space-x-2 border-2 border-white/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Reset All</span>
                                </a>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </header>

        <script>
            document.getElementById('toggle-filters').addEventListener('click', function () {
                const filters = document.getElementById('filters');
                const header = document.getElementById('main-header');
                const toggleButton = this;

                filters.classList.toggle('hidden');

                if (filters.classList.contains('hidden')) {
                    header.classList.remove('h-auto');
                    header.classList.add('md:h-[60vh]');
                    toggleButton.querySelector('span').textContent = 'Show Filters';

                } else {
                    header.classList.add('h-auto');
                    header.classList.remove('md:h-[60vh]');
                    toggleButton.querySelector('span').textContent = 'Hide Filters';
                }
            });
        </script>
        <div class="max-w-7xl mx-auto py-12">
            @if($books->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($books as $book)
                        <div
                            class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transform hover:-translate-y-2">
                            @include('livewire.books.partials.book-card', ['book' => $book])
                        </div>
                    @endforeach
                </div>

                <div
                    class="mt-12 flex w-full  sm:flex-row items-center mx-auto space-y-4 sm:space-y-0 p-6 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex w-full mx-auto justify-center  space-x-2">
                        {{ $books->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-20">
                    <div class="max-w-md mx-auto">
                        <div class="relative mb-8">
                            <div
                                class="w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full mx-auto flex items-center justify-center">
                                <svg class="w-16 h-16 text-blue-500 dark:text-blue-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div
                                class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">No books found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                            We couldn't find any books matching your search criteria. Try adjusting your filters or
                            search terms to discover more books.
                        </p>

                        <div class="space-y-4">
                            <a href="{{ route('books.index') }}"
                               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Browse All Books
                            </a>

                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span>Popular searches:</span>
                                <div class="flex flex-wrap justify-center gap-2 mt-2">
                                    <a href="?category=fiction"
                                       class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-gray-600 dark:text-gray-300 rounded-full text-xs transition-colors duration-200">Fiction</a>
                                    <a href="?search=romance"
                                       class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-gray-600 dark:text-gray-300 rounded-full text-xs transition-colors duration-200">Romance</a>
                                    <a href="?search=mystery"
                                       class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-gray-600 dark:text-gray-300 rounded-full text-xs transition-colors duration-200">Mystery</a>
                                    <a href="?price=free"
                                       class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-gray-600 dark:text-gray-300 rounded-full text-xs transition-colors duration-200">Free
                                        Books</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div
            class="fixed inset-y-0 right-0 w-80 bg-white dark:bg-gray-800 shadow-2xl transform translate-x-full transition-transform duration-300 z-40"
            id="quickFilters">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Filters</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Popular Genres</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Fiction</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Romance</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Science Fiction</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 bg-black/50 hidden z-30" id="quickFiltersOverlay"></div>
    </div>

    @push('scripts')
        <script>
            // Enhanced auto-submit with debouncing
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                const selects = form.querySelectorAll('select');
                const searchInput = document.getElementById('search');

                // Auto-submit on select change
                selects.forEach(select => {
                    select.addEventListener('change', function () {
                        clearTimeout(window.filterTimeout);
                        window.filterTimeout = setTimeout(() => {
                            form.submit();
                        }, 300);
                    });
                });

                // Search input debouncing
                if (searchInput) {
                    let searchTimeout;
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            // Optional: Auto-submit search after typing stops
                            // form.submit();
                        }, 1000);
                    });
                }

                // Keyboard shortcut for search (Cmd/Ctrl + K)
                document.addEventListener('keydown', function (e) {
                    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                        e.preventDefault();
                        searchInput.focus();
                    }
                });

                // Quick suggestion clicks
                document.querySelectorAll('.quick-suggestion').forEach(suggestion => {
                    suggestion.addEventListener('click', function () {
                        const searchTerm = this.textContent;
                        searchInput.value = searchTerm;
                        form.submit();
                    });
                });

                // Floating action button functionality
                const fab = document.querySelector('.fixed.bottom-8.right-8 button');
                if (fab) {
                    fab.addEventListener('click', function () {
                        window.scrollTo({top: 0, behavior: 'smooth'});
                    });
                }

                // Show/hide FAB based on scroll
                let lastScrollTop = 0;
                window.addEventListener('scroll', function () {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const fabElement = document.querySelector('.fixed.bottom-8.right-8');

                    if (scrollTop > 500) {
                        fabElement.classList.remove('translate-y-20', 'opacity-0');
                        fabElement.classList.add('translate-y-0', 'opacity-100');
                    } else {
                        fabElement.classList.add('translate-y-20', 'opacity-0');
                        fabElement.classList.remove('translate-y-0', 'opacity-100');
                    }

                    lastScrollTop = scrollTop;
                });

                // Loading states for better UX
                form.addEventListener('submit', function () {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Searching...
                        `;
                    }
                });
            });

            // Enhanced animations and interactions
            function initializeAnimations() {
                // Animate stats on scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const statElements = entry.target.querySelectorAll('[data-stat]');
                            statElements.forEach(stat => {
                                const finalValue = stat.textContent;
                                stat.textContent = '0';
                                const increment = finalValue / 30;
                                let current = 0;
                                const timer = setInterval(() => {
                                    current += increment;
                                    if (current >= finalValue) {
                                        stat.textContent = finalValue;
                                        clearInterval(timer);
                                    } else {
                                        stat.textContent = Math.floor(current).toLocaleString();
                                    }
                                }, 50);
                            });
                        }
                    });
                }, {threshold: 0.5});

                // Add smooth hover effects to book cards
                document.querySelectorAll('.group').forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-8px) scale(1.02)';
                    });

                    card.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });
            }

            // Initialize animations when DOM is loaded
            document.addEventListener('DOMContentLoaded', initializeAnimations);
        </script>
    @endpush
</x-layouts.app>
