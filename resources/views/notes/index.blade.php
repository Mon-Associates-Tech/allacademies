<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        {{-- Header Section --}}
        <div class="page-header-blue py-6 rounded-t-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl tracking-tight">My Notes</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage, organize, and share your academic notes.
                        <span class="font-medium">{{ $notes->total() }} {{ Str::plural('note', $notes->total()) }} found</span>
                    </p>
                </div>
                <a href="{{ route('notes.create') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Note
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-white dark:bg-gray-800 border-x border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filters Section --}}
        <div class="bg-white dark:bg-gray-800 border-x border-t border-gray-200 dark:border-gray-700" x-data="{ filtersOpen: {{ request()->hasAny(['search', 'ownership', 'book_id', 'subject_id', 'visibility', 'date_from', 'date_to', 'sort_by', 'sort_order']) ? 'true' : 'false' }} }">
            {{-- Filter Toggle Bar --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <button @click="filtersOpen = !filtersOpen"
                        class="w-full flex items-center justify-between text-left focus:outline-none group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Filters & Search</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-show="!filtersOpen">Click to expand filter options</span>
                                <span x-show="filtersOpen">Click to collapse</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Active Filters Count --}}
                        @if(count($activeFilters) > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                {{ count($activeFilters) }} active
                            </span>
                        @endif

                        {{-- Toggle Icon --}}
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                             :class="{ 'rotate-180': filtersOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Active Filters Pills (Always Visible) --}}
                @if(count($activeFilters) > 0)
                    <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Active:</span>
                        @foreach($activeFilters as $key => $value)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                <span class="font-semibold mr-1">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                {{ Str::limit($value, 25) }}
                                <a href="{{ request()->fullUrlWithQuery([$key => null]) }}"
                                   class="ml-1.5 hover:text-blue-900 dark:hover:text-blue-100"
                                   title="Remove filter">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </span>
                        @endforeach
                        <a href="{{ route('notes.index') }}"
                           class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Clear all
                        </a>
                    </div>
                @endif
            </div>

            {{-- Expandable Filter Content --}}
            <div x-show="filtersOpen"
                 x-collapse
                 class="px-6 py-6 bg-gray-50 dark:bg-gray-900/30 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('notes.index') }}" class="space-y-5" id="filters-form">
                    {{-- Quick Filters Row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Search --}}
                        <div class="sm:col-span-2">
                            <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Search
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by title or content..."
                                       class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        {{-- Ownership --}}
                        <div>
                            <label for="ownership" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Type
                            </label>
                            <select name="ownership" id="ownership"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">All Notes</option>
                                <option value="my_notes" {{ request('ownership') === 'my_notes' ? 'selected' : '' }}>My Notes</option>
                                <option value="shared_with_me" {{ request('ownership') === 'shared_with_me' ? 'selected' : '' }}>Shared</option>
                            </select>
                        </div>

                        {{-- Visibility --}}
                        <div>
                            <label for="visibility" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Visibility
                            </label>
                            <select name="visibility" id="visibility"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">All</option>
                                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                            </select>
                        </div>
                    </div>

                    {{-- Advanced Filters --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Book --}}
                        <div>
                            <label for="book_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Book
                            </label>
                            <select name="book_id" id="book_id"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">All Books</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ Str::limit($book->title, 30) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Subject
                            </label>
                            <select name="subject_id" id="subject_id"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date From --}}
                        <div>
                            <label for="date_from" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                From Date
                            </label>
                            <input type="date"
                                   id="date_from"
                                   name="date_from"
                                   value="{{ request('date_from') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>

                        {{-- Date To --}}
                        <div>
                            <label for="date_to" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                To Date
                            </label>
                            <input type="date"
                                   id="date_to"
                                   name="date_to"
                                   value="{{ request('date_to') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>
                    </div>

                    {{-- Sort Options --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sort_by" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Sort By
                            </label>
                            <select name="sort_by" id="sort_by"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Date Updated</option>
                                <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Title</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort_order" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Order
                            </label>
                            <select name="sort_order" id="sort_order"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($notes->total() > 0)
                                Showing {{ $notes->firstItem() }}-{{ $notes->lastItem() }} of {{ $notes->total() }}
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                </svg>
                                Apply
                            </button>

                            @if(request()->hasAny(['search', 'ownership', 'book_id', 'subject_id', 'visibility', 'date_from', 'date_to', 'sort_by', 'sort_order']))
                                <a href="{{ route('notes.index') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Notes Grid --}}
        <div class="bg-white dark:bg-gray-800 border-x border-b border-gray-200 dark:border-gray-700 rounded-b-xl shadow-sm p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($notes as $note)
                    <div class="group relative bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 flex flex-col h-full overflow-hidden">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex flex-wrap gap-2">
                                    @if($note->academicSubject)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                            {{ $note->academicSubject->name }}
                                        </span>
                                    @endif
                                    @if($note->is_public)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                            Public
                                        </span>
                                    @endif
                                    @if($note->user_id !== Auth::id())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20">
                                            Shared
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0" title="{{ $note->created_at->format('M d, Y H:i') }}">
                                    {{ $note->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="{{ route('notes.show', $note) }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $note->title }}
                                </a>
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 flex-1">
                                {!! Str::limit(strip_tags($note->content), 150) !!}
                            </p>

                            @if($note->book)
                                <div class="mt-auto pt-3 flex items-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="truncate font-medium">{{ $note->book->title }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                    {{ substr($note->user->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate max-w-[8rem]">
                                    {{ $note->user->name }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1">
                                @if($note->book)
                                    <a href="{{ route('books.show', $note->book) }}"
                                       class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-all"
                                       title="Go to Book">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </a>
                                @endif

                                @if($note->canUserEdit(Auth::id()))
                                    <a href="{{ route('notes.edit', $note) }}"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-all"
                                       title="Edit Note">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-16 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No notes found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(request()->hasAny(['search', 'ownership', 'book_id', 'subject_id', 'visibility', 'date_from', 'date_to']))
                                    Try adjusting your filters or search criteria.
                                @else
                                    Get started by creating a new note for your studies.
                                @endif
                            </p>
                            <div class="mt-6">
                                @if(request()->hasAny(['search', 'ownership', 'book_id', 'subject_id', 'visibility', 'date_from', 'date_to']))
                                    <a href="{{ route('notes.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('notes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create Note
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($notes->hasPages())
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
