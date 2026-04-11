<x-layouts.app page-name="Kids Books Library" :show-title-area="false" :full-width="true">

    <div class="min-h-screen bg-gradient-to-br from-yellow-50 via-pink-50 to-blue-50 dark:from-gray-900 dark:via-purple-900 dark:to-blue-900">

        <!-- Header Section -->
        <header id="main-header"
                class="min-h-[35vh] flex items-center relative overflow-hidden">

            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-200 via-pink-200 to-blue-200 dark:from-yellow-900/30 dark:via-pink-900/30 dark:to-blue-900/30 opacity-50">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\\'60\\' height=\\'60\\' viewBox=\\'0 0 60 60\\' xmlns=\\'http://www.w3.org/2000/svg\\'%3E%3Cg fill=\\'none\\' fill-rule=\\'evenodd\\'%3E%3Cg fill=\\'%239C92AC\\' fill-opacity=\\'0.1\\'%3E%3Cpath d=\\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="relative z-10 flex py-4 flex-col items-center w-full justify-center h-full px-3 md:px-6 text-center">
                <!-- Fun Title -->
                <h1 class="text-3xl md:text-6xl font-black leading-tight mb-6">
                    <span class="bg-gradient-to-r from-yellow-500 via-pink-500 to-blue-500 bg-clip-text text-transparent">
                        🌈 Kids Books Library 📚
                    </span>
                </h1>

                <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-6 font-medium">
                    Discover amazing books for young readers!
                </p>

                <!-- Search Bar -->
                <section class="w-full flex flex-col md:flex-row justify-center items-center">
                    <div class="w-full my-auto max-w-2xl relative group">
                        <form method="GET" action="{{ route('kids-books.index') }}" class="space-y-4">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-pink-400/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search for kids books by title or author..."
                                    class="relative w-full px-4 lg:px-6 py-3 text-sm md:text-base bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-2xl border-2 border-pink-300 dark:border-pink-600 focus:outline-none focus:ring-4 focus:ring-pink-400/30 focus:border-pink-500 transition-all duration-300 shadow-sm"
                                />
                                <button
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2.5 bg-gradient-to-r from-yellow-400 via-pink-500 to-blue-500 text-white rounded-xl hover:from-yellow-500 hover:via-pink-600 hover:to-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Age Group Filter Pills -->
                @if(count($ageGroups) > 0)
                <div class="mt-6 w-full max-w-3xl">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Filter by Age Group:</p>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <!-- All Books Pill -->
                        <form method="GET" action="{{ route('kids-books.index') }}" class="inline">
                            @if(request('age_groups'))
                                <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:border-pink-400 hover:bg-pink-50 dark:hover:bg-pink-900/20 shadow-md hover:shadow-lg">
                                    📚 All Kids Books
                                </button>
                            @else
                                <button class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-200 bg-gradient-to-r from-yellow-400 via-pink-500 to-blue-500 text-white border-2 border-transparent shadow-lg">
                                    📚 All Kids Books
                                </button>
                            @endif
                        </form>

                        <!-- Individual Age Group Pills -->
                        @foreach($ageGroups as $ageGroup)
                            <form method="GET" action="{{ route('kids-books.index') }}" class="inline">
                                <input type="hidden" name="age_groups[]" value="{{ $ageGroup }}">
                                @if(request('age_groups') == [$ageGroup])
                                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-200 bg-gradient-to-r from-yellow-400 via-pink-500 to-blue-500 text-white border-2 border-transparent shadow-lg">
                                        👶 {{ $ageGroup }} years
                                    </button>
                                @else
                                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:border-pink-400 hover:bg-pink-50 dark:hover:bg-pink-900/20 shadow-md hover:shadow-lg">
                                        👶 {{ $ageGroup }} years
                                    </button>
                                @endif
                            </form>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </header>

        <!-- Books Grid -->
        <div class="max-w-7xl mx-auto py-12 px-4">
            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        @include('livewire.books.partials.book-card', ['book' => $book])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-16 flex justify-center">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-24">
                    <div class="max-w-md mx-auto">
                        <div class="relative mb-10">
                            <div
                                class="w-32 h-32 bg-gradient-to-br from-yellow-100 to-pink-100 dark:from-yellow-900/30 dark:to-pink-900/30 rounded-3xl mx-auto flex items-center justify-center transform rotate-6 transition-transform duration-300 hover:rotate-12">
                                <svg class="w-16 h-16 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            No kids books found
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            @if(request('age_groups'))
                                No books available for the selected age group. Try selecting a different age group or view all kids books.
                            @else
                                There are no kids books available at the moment. Please check back later!
                            @endif
                        </p>
                        <a href="{{ route('kids-books.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-400 via-pink-500 to-blue-500 text-white rounded-xl font-semibold hover:from-yellow-500 hover:via-pink-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            View All Kids Books
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-layouts.app>
