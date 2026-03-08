<x-layouts.app page-name="Library and Bookshop" :show-title-area="false" :full-width="true">

    <div
        class="min-h-screen bg-gradient-to-br">

        <header id="main-header"
                class="min-h-[40vh] flex items-center md:h-[40vh] lg:rounded-t-xl relative transition-all duration-300">

            <!-- Background Image with Blur -->
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1920&q=80" 
                     alt="Library Background" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 backdrop-blur-sm bg-white/40 dark:bg-gray-900/40"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center w-full justify-center h-full px-3 md:px-6 text-center">
                <h1 class="text-xl md:text-5xl font-black leading-tight mb-6">
                    <span class="hidden md:block text-white drop-shadow-lg">All Academies</span>
                    <span
                        class="block pb-2 drop-shadow-lg bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                Library and Bookshop
            </span>
                </h1>

                <section class="w-full flex flex-col md:flex-row justify-center items-center">
                    <div class="w-full my-auto max-w-3xl relative group">
                        <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search by title, author, or genre..."
                                    class="relative w-full px-4 lg:px-6 py-2.5 lg:py-3 text-sm md:text-base bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-2xl border-2 border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm"
                                />
                                <button
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <button id="toggle-filters"
                            class="w-full md:w-auto text-nowrap md:ml-4 px-5 py-3 bg-gray-900 dark:bg-gray-700 text-white rounded-xl font-semibold hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Show Filters</span>
                    </button>
                </section>

                <div id="filters"
                     class="hidden mb-4 mt-6 w-full max-w-7xl mx-auto px-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 shadow-lg">
                    <form method="GET" action="{{ route('books.index') }}" class="space-y-6" id="filter-form">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Categories</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
                                    'selected' => request('categories', []),
                                    'placeholder' => 'Select categories...',
                                    'name' => 'categories',
                                    'valueKey' => 'id',
                                    'labelKey' => 'name',
                                    'size' => 'sm'
                                ])
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Format</label>
                                <div class="relative">
                                    <select name="format" id="format"
                                            class="w-full px-4 py-2 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 transition-all duration-300 appearance-none cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <option value="" class="text-gray-800 bg-white">All Formats</option>
                                        <option value="softcopy"
                                                {{ request('format') == 'softcopy' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            📱 Digital Copy
                                        </option>
                                        <option value="hardcopy"
                                                {{ request('format') == 'hardcopy' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            📚 Physical Copy
                                        </option>
                                        <option value="both"
                                                {{ request('format') == 'both' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            📄 Both Copies
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
                                <label
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Pricing</label>
                                <div class="relative">
                                    <select name="price" id="price"
                                            class="w-full px-4 py-2 text-sm rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 transition-all duration-300 appearance-none cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <option value="" class="text-gray-800 bg-white">All Books</option>
                                        <option value="free"
                                                {{ request('price') == 'free' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            🆓 Free Books
                                        </option>
                                        <option value="paid"
                                                {{ request('price') == 'paid' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            💰 Paid Books
                                        </option>
                                        <option value="subscribed"
                                                {{ request('price') == 'subscribed' ? 'selected' : '' }} class="text-gray-800 bg-white">
                                            📋 Subscribed books
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
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Age Groups</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => collect($ageGroups)->map(fn($age) => ['id' => $age, 'name' => $age . ' years'])->toArray(),
                                    'selected' => request('age_groups', []),
                                    'placeholder' => 'Select age groups...',
                                    'name' => 'age_groups',
                                    'valueKey' => 'id',
                                    'labelKey' => 'name',
                                    'size' => 'sm'
                                ])
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Groups</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => $academicGroups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->toArray(),
                                    'selected' => request('academic_groups', []),
                                    'placeholder' => 'Select groups...',
                                    'name' => 'academic_groups',
                                    'valueKey' => 'id',
                                    'labelKey' => 'name',
                                    'size' => 'sm'
                                ])
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Levels</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => $academicLevels->map(fn($l) => [
                                        'id' => $l->id, 
                                        'name' => $l->name . ' (' . ($l->academicGroup->name ?? 'No Group') . ')'
                                    ])->toArray(),
                                    'selected' => request('academic_levels', []),
                                    'placeholder' => 'Select levels...',
                                    'name' => 'academic_levels',
                                    'valueKey' => 'id',
                                    'labelKey' => 'name',
                                    'size' => 'sm'
                                ])
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Subjects</label>
                                @livewire('common.searchable-multi-select', [
                                    'items' => $academicSubjects->map(fn($s) => [
                                        'id' => $s->id, 
                                        'name' => $s->name . ' - ' . ($s->academicLevel->name ?? 'N/A') . ' (' . ($s->academicLevel->academicGroup->name ?? 'N/A') . ')'
                                    ])->toArray(),
                                    'selected' => request('academic_subjects', []),
                                    'placeholder' => 'Select subjects...',
                                    'name' => 'academic_subjects',
                                    'valueKey' => 'id',
                                    'labelKey' => 'name',
                                    'size' => 'sm'
                                ])
                            </div>

                            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-1 flex items-end">
                                <div class="w-full flex flex-col sm:flex-row gap-3">
                                    <button type="submit"
                                            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-xl font-semibold text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                        </svg>
                                        <span>Apply</span>
                                    </button>
                                    <a href="{{ route('books.index') }}"
                                       class="flex-1 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-xl font-semibold text-sm text-center transition-all duration-300 flex items-center justify-center space-x-2 border-2 border-gray-200 dark:border-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Reset</span>
                                    </a>
                                </div>
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
                    header.classList.add('md:h-[40vh]');
                    toggleButton.querySelector('span').textContent = 'Show Filters';

                } else {
                    header.classList.add('h-auto');
                    header.classList.remove('md:h-[40vh]');
                    toggleButton.querySelector('span').textContent = 'Hide Filters';
                }
            });
        </script>


        <div class="max-w-7xl mx-auto py-12 px-4">
            @if(isset($topCategories) && !request()->hasAny(['search', 'categories', 'format', 'price', 'age_groups', 'academic_groups', 'academic_levels', 'academic_subjects']))
                {{-- Category Sections - Only show when no filters are applied --}}
                @foreach($topCategories as $category)
                    <div class="mb-20">
                        {{-- Category Header --}}
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                                        {{ $category->name }}
                                    </h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">
                                        Explore our curated collection
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('books.index', ['category' => $category->id]) }}"
                               class="group inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 bg-gray-200/50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg font-medium transition-all duration-200">
                                <span>View All</span>
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Category Books Grid --}}
                        @if($category->books->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($category->books->take(6) as $book)
                                    @include('livewire.books.partials.book-card', ['book' => $book])
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full mx-auto flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No books available in this
                                    category yet</p>
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- All Books Section Header --}}
                <div class="mb-8 pt-12 border-t-2 border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                                All Books
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">
                                Complete library collection
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        @include('livewire.books.partials.book-card', ['book' => $book])
                    @endforeach
                </div>

                <div
                    class="mt-16 flex justify-center">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-24">
                    <div class="max-w-md mx-auto">
                        <div class="relative mb-10">
                            <div
                                class="w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-3xl mx-auto flex items-center justify-center transform rotate-6 transition-transform duration-300 hover:rotate-12">
                                <svg class="w-16 h-16 text-indigo-500 dark:text-indigo-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">No books found</h3>
                        
                        @php
                            $activeFilters = [];
                            if(request('search')) $activeFilters[] = 'Search: "' . request('search') . '"';
                            if(request('categories')) {
                                $catNames = $categories->whereIn('id', request('categories'))->pluck('name')->join(', ');
                                if($catNames) $activeFilters[] = 'Categories: ' . $catNames;
                            }
                            if(request('format')) $activeFilters[] = 'Format: ' . ucfirst(request('format'));
                            if(request('price')) $activeFilters[] = 'Price: ' . ucfirst(request('price'));
                            if(request('age_groups')) $activeFilters[] = 'Age Groups: ' . collect(request('age_groups'))->join(', ');
                            if(request('academic_groups')) {
                                $groupNames = $academicGroups->whereIn('id', request('academic_groups'))->pluck('name')->join(', ');
                                if($groupNames) $activeFilters[] = 'Academic Groups: ' . $groupNames;
                            }
                            if(request('academic_levels')) {
                                $levelNames = $academicLevels->whereIn('id', request('academic_levels'))->pluck('name')->join(', ');
                                if($levelNames) $activeFilters[] = 'Academic Levels: ' . $levelNames;
                            }
                            if(request('academic_subjects')) {
                                $subjectNames = $academicSubjects->whereIn('id', request('academic_subjects'))->pluck('name')->join(', ');
                                if($subjectNames) $activeFilters[] = 'Subjects: ' . $subjectNames;
                            }
                        @endphp
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed text-lg">
                            We couldn't find any books matching your criteria.
                        </p>
                        
                        @if(count($activeFilters) > 0)
                            <div class="mb-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Active Filters:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($activeFilters as $filter)
                                        <span class="inline-flex items-center px-3 py-1 text-sm bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full">
                                            {{ $filter }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <p class="text-gray-500 dark:text-gray-400 mb-10 text-base">
                            Try adjusting your filters or explore our collection.
                        </p>

                        <a href="{{ route('books.index') }}"
                           class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Browse All Books</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
