<x-layouts.app :has-action="false" page-name="Dashboard">

    @php
        $user = Auth::user();
        $primaryRole = $user->role->value;
        $isImpersonating = session()->has('impersonated_by');
    @endphp


    @if(config('app.debug'))
        <div class="container bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 mx-auto rounded mb-4">
            <strong>Debug:</strong>
            Primary Role: {{ $primaryRole }} |
            All Roles: {{ implode(', ', $user->getRoleNames()) }} |
            Is Impersonating: {{ $isImpersonating ? 'Yes' : 'No' }}
        </div>
    @endif

    <div class="container mx-auto px-4 py-4">
        @auth
            <livewire:trial-expiration-banner/>
        @endauth

    </div>

    @if($primaryRole === 'student')
        @livewire('students.dashboard')
    @elseif($primaryRole === 'teacher')
        @livewire('teachers.dashboard')

    @elseif($primaryRole === 'librarian')
        @livewire('librarians.library-dashboard')

    @elseif(in_array($primaryRole, ['admin', 'owner']))
        <livewire:administrators.overview/>

    @elseif($primaryRole === 'moderator')
        @livewire('moderator.dashboard')

    @elseif($primaryRole === 'author')
        @livewire('authors.dashboard')

    @elseif($primaryRole === 'parent')
        @livewire('parent.dashboard')

    @elseif($primaryRole === 'subscriber')
        @livewire('subscribers.subscriber-dashboard')

    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Error:</strong> No dashboard found for role: {{ $primaryRole }}
        </div>
    @endif

    {{-- some useful components  --}}
    <div class="mb-4 hidden">
        <livewire:chats.token-usage-monitor/>
    </div>

    <div class="w-64 hidden">
        <livewire:chats.token-usage-horizontal/>
    </div>

    {{-- Use Vertical Compact in sidebar --}}
    <div class="w-16 hidden">
        <livewire:chats.token-usage-vertical/>
    </div>

    {{-- Use Circular in dashboard widget --}}
    <div class="w-full max-w-xs hidden">
        <livewire:chats.token-usage-circular/>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 hidden lg:px-8 py-6">
        <!-- Full version for prominent display -->
        <livewire:subscription-features-banner placement="dashboard"/>
    </div>

    @if( in_array(auth()->user()->role->value, ['admin', 'owner', 'moderator', 'subscriber', 'teacher']) && Route::is('dashboard'))

        <section>
            @if ($academicSubjects->count() || request()->hasAny(['search', 'academic_group', 'academic_level']))
                <section class="mt-10 w-full mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-3xl text-gray-900 dark:text-gray-100">My Courses</h3>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $academicSubjects->total() }} {{ Str::plural('course', $academicSubjects->total()) }}
                                @if(request()->hasAny(['search', 'academic_group', 'academic_level']))
                                    found
                                @else
                                    available
                                @endif
                            </div>
                        </div>

                        <!-- View Toggle Controls -->
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                <button onclick="toggleView('grid')"
                                        id="grid-btn"
                                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                    Grid
                                </button>
                                <button onclick="toggleView('list')"
                                        id="list-btn"
                                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    List
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters & Search -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
                        <div class="p-6">
                            <form method="GET" action="{{ route('dashboard') }}" class="space-y-4" id="filters-form">
                                <!-- Search Row -->
                                <div class="flex flex-col lg:flex-row gap-4">
                                    <div class="flex-1">
                                        <label for="search"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Search Courses
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                     fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <input type="text"
                                                   id="search"
                                                   name="search"
                                                   value="{{ $filters['search'] ?? '' }}"
                                                   placeholder="Search by subject name, code, level, or group..."
                                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                        </div>
                                    </div>

                                    <div class="lg:w-48">
                                        <label for="academic_group"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Academic Group
                                        </label>
                                        <select name="academic_group"
                                                id="academic_group"
                                                onchange="updateLevels()"
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                                            <option value="">All Groups</option>
                                            @foreach($academicGroups as $group)
                                                <option
                                                    value="{{ $group->id }}" {{ ($filters['academic_group'] ?? '') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="lg:w-48">
                                        <label for="academic_level"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Academic Level
                                        </label>
                                        <select name="academic_level"
                                                id="academic_level"
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                                            <option value="">All Levels</option>
                                            @foreach($academicLevels as $level)
                                                <option value="{{ $level->id }}"
                                                        data-group="{{ $level->academic_group_id }}"
                                                    {{ ($filters['academic_level'] ?? '') == $level->id ? 'selected' : '' }}>
                                                    {{ $level->name }} ({{ $level->academicGroup?->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Sorting and Action Row -->
                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <div class="flex items-center gap-2">
                                            <label for="sort_by"
                                                   class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Sort
                                                by:</label>
                                            <select name="sort_by" id="sort_by"
                                                    class="text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded px-2 py-1">
                                                <option
                                                    value="name" {{ ($filters['sort_by'] ?? '') == 'name' ? 'selected' : '' }}>
                                                    Name
                                                </option>
                                                <option
                                                    value="group" {{ ($filters['sort_by'] ?? '') == 'group' ? 'selected' : '' }}>
                                                    Group
                                                </option>
                                                <option
                                                    value="level" {{ ($filters['sort_by'] ?? '') == 'level' ? 'selected' : '' }}>
                                                    Level
                                                </option>
                                                <option
                                                    value="quizzes_count" {{ ($filters['sort_by'] ?? '') == 'quizzes_count' ? 'selected' : '' }}>
                                                    Quiz Count
                                                </option>
                                                <option
                                                    value="examinations_count" {{ ($filters['sort_by'] ?? '') == 'examinations_count' ? 'selected' : '' }}>
                                                    Exam Count
                                                </option>
                                            </select>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <select name="sort_order" id="sort_order"
                                                    class="text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded px-2 py-1">
                                                <option
                                                    value="asc" {{ ($filters['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>
                                                    ↑ Ascending
                                                </option>
                                                <option
                                                    value="desc" {{ ($filters['sort_order'] ?? '') == 'desc' ? 'selected' : '' }}>
                                                    ↓ Descending
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">

                                        <x-button.primary type="submit" size="sm">
                                            <x-slot:icon>
                                                <svg class="w-4 my-auto h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </x-slot:icon>
                                            Filter
                                        </x-button.primary>

                                        @if(request()->hasAny(['search', 'academic_group', 'academic_level']) || ($filters['sort_by'] ?? 'name') !== 'name' || ($filters['sort_order'] ?? 'asc') !== 'asc')
                                            <a href="{{ route('dashboard') }}"
                                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                Clear
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Active Filters Display -->
                                @if(request()->hasAny(['search', 'academic_group', 'academic_level']))
                                    <div
                                        class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active filters:</span>

                                        @if($filters['search'])
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                                            Search: "{{ $filters['search'] }}"
                                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                               class="ml-1.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        </span>
                                        @endif

                                        @if($filters['academic_group'])
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            Group: {{ $academicGroups->find($filters['academic_group'])->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['academic_group' => null]) }}"
                                               class="ml-1.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        </span>
                                        @endif

                                        @if($filters['academic_level'])
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            Level: {{ $academicLevels->find($filters['academic_level'])->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['academic_level' => null]) }}"
                                               class="ml-1.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if($academicSubjects->count())
                        <!-- Grid View -->
                        <div id="grid-view" class="grid hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            @foreach ($academicSubjects as $academicSubject)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-200 hover:shadow-lg group">
                                    <!-- Course Header -->
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <!-- Subject hierarchy -->
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
        <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded text-xs">
            {{ $academicSubject->academicLevel->academicGroup->name }}
        </span>
                                                    <span class="mx-1">•</span>
                                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 px-2 py-1 rounded text-xs">
            {{ $academicSubject->academicLevel->name }}
        </span>
                                                </div>

                                                <!-- Subject name -->
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors">
                                                    {{ $academicSubject->name }}
                                                </h4>

                                                @if($academicSubject->code)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $academicSubject->code }}</p>
                                                @endif
                                            </div>

                                            <!-- Subject icon -->
                                            <div class="ml-3">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path
                                                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick stats -->
                                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                {{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }}
                                                Quizzes
                                            </div>
                                            @can('privileged', $currentTeam)
                                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }}
                                                    Exams
                                                </div>
                                            @endcan
                                        </div>

                                        <!-- Action buttons -->
                                        <div class="flex flex-col space-y-2">
                                            <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                Practice Quizzes
                                            </a>

                                            @can('privileged', $currentTeam)
                                                <a href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                   class="inline-flex items-center justify-center px-4 py-2 border border-primary-600 dark:border-primary-500 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-sm font-medium rounded-md transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                    Take Examinations
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- List View -->
                        <div id="list-view"
                             class="hidden bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-8">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Course Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Statistics
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($academicSubjects as $academicSubject)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-lg flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-white" fill="currentColor"
                                                                 viewBox="0 0 20 20">
                                                                <path
                                                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $academicSubject->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                {{ $academicSubject->academicLevel->academicGroup->name }}
                            </span>
                                                            <span class="mx-1">•</span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                {{ $academicSubject->academicLevel->name }}
                            </span>
                                                            @if($academicSubject->code)
                                                                <span class="mx-1">•</span>
                                                                <span
                                                                    class="text-gray-500 dark:text-gray-400">{{ $academicSubject->code }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                             viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                  d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }}
                                                        Quizzes
                                                    </div>
                                                    @can('privileged', $currentTeam)
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                 viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                      clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }}
                                                            Exams
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 transition-colors duration-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                             viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                  d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                        Quizzes
                                                    </a>
                                                    @can('privileged', $currentTeam)
                                                        <a href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                           class="inline-flex items-center px-3 py-1.5 border border-primary-600 dark:border-primary-500 text-xs font-medium rounded-full text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors duration-200">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                 viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                      clip-rule="evenodd"/>
                                                            </svg>
                                                            Exams
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Enhanced Pagination -->
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing {{ $academicSubjects->firstItem() }} to {{ $academicSubjects->lastItem() }}
                                of {{ $academicSubjects->total() }} courses
                            </div>
                            {{ $academicSubjects->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- No Results Found -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.291-1.267-5.543-3.259M6.343 6.343A8 8 0 0112.001 20a8 8 0 011.498-15.657"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'academic_group', 'academic_level']))
                                    No courses match your current filters. Try adjusting your search criteria.
                                @else
                                    You don't have access to any courses yet.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'academic_group', 'academic_level']))
                                <div class="mt-6">
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Clear Filters
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </section>

                <!-- JavaScript for enhanced functionality -->
                <script>
                    // View toggle functionality
                    function toggleView(viewType) {
                        const gridView = document.getElementById('grid-view');
                        const listView = document.getElementById('list-view');
                        const gridBtn = document.getElementById('grid-btn');
                        const listBtn = document.getElementById('list-btn');

                        if (viewType === 'grid') {
                            gridView.classList.remove('hidden');
                            listView.classList.add('hidden');
                            gridBtn.classList.add('bg-white', 'text-gray-700', 'shadow-sm');
                            gridBtn.classList.remove('text-gray-500', 'hover:text-gray-700');
                            listBtn.classList.remove('bg-white', 'text-gray-700', 'shadow-sm');
                            listBtn.classList.add('text-gray-500', 'hover:text-gray-700');
                        } else {
                            gridView.classList.add('hidden');
                            listView.classList.remove('hidden');
                            listBtn.classList.add('bg-white', 'text-gray-700', 'shadow-sm');
                            listBtn.classList.remove('text-gray-500', 'hover:text-gray-700');
                            gridBtn.classList.remove('bg-white', 'text-gray-700', 'shadow-sm');
                            gridBtn.classList.add('text-gray-500', 'hover:text-gray-700');
                        }

                        // Save preference to localStorage
                        localStorage.setItem('courseViewPreference', viewType);
                    }

                    // Dynamic level filtering based on selected group
                    function updateLevels() {
                        const groupSelect = document.getElementById('academic_group');
                        const levelSelect = document.getElementById('academic_level');
                        const selectedGroupId = groupSelect.value;

                        // Show all levels if no group selected
                        if (!selectedGroupId) {
                            Array.from(levelSelect.options).forEach(option => {
                                if (option.value !== '') {
                                    option.style.display = 'block';
                                }
                            });
                            return;
                        }

                        // Hide/show levels based on selected group
                        Array.from(levelSelect.options).forEach(option => {
                            if (option.value === '') {
                                option.style.display = 'block';
                                return;
                            }

                            const levelGroupId = option.getAttribute('data-group');
                            if (levelGroupId === selectedGroupId) {
                                option.style.display = 'block';
                            } else {
                                option.style.display = 'none';
                            }
                        });

                        // Clear level selection if current selection is not valid for the selected group
                        const currentLevelOption = levelSelect.options[levelSelect.selectedIndex];
                        if (currentLevelOption && currentLevelOption.style.display === 'none') {
                            levelSelect.value = '';
                        }
                    }

                    // Auto-submit form on filter changes (optional)
                    // Load saved preference on page load
                    document.addEventListener('DOMContentLoaded', function () {
                        const savedView = localStorage.getItem('courseViewPreference') || 'grid';
                        toggleView(savedView);

                        // Initialize level filtering
                        updateLevels();

                        // Optional: Auto-submit form when filters change
                        const autoSubmitElements = ['academic_group', 'academic_level'];
                        autoSubmitElements.forEach(id => {
                            const element = document.getElementById(id);
                            if (element) {
                                element.addEventListener('change', function () {
                                    // Uncomment the next line to enable auto-submit
                                    // document.getElementById('filters-form').submit();
                                });
                            }
                        });

                        // Search input debounce
                        const searchInput = document.getElementById('search');
                        if (searchInput) {
                            let searchTimeout;
                            searchInput.addEventListener('input', function () {
                                clearTimeout(searchTimeout);
                                searchTimeout = setTimeout(() => {
                                    // Uncomment the next line to enable auto-submit on search
                                    // document.getElementById('filters-form').submit();
                                }, 500);
                            });
                        }
                    });
                </script>
            @else
                <div class="max-w-md mx-auto my-10 hidden p-8 text-center">
                    <!-- Animated Icon -->
                    <div class="relative mb-6">
                        <div class="w-24 h-24 mx-auto relative">
                            <div class="absolute inset-0 animate-pulse">
                                <svg class="w-full h-full text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Heading -->
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Ready to Learn?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Your academic journey awaits
                    </p>

                    <!-- Feature Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-blue-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path
                                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Premium Content</h3>
                            <p class="text-xs text-gray-600">Access all courses</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-green-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path fill-rule="evenodd"
                                          d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Certificates</h3>
                            <p class="text-xs text-gray-600">Earn credentials</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-purple-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Progress Tracking</h3>
                            <p class="text-xs text-gray-600">Monitor growth</p>
                        </div>

                        <div class="p-4 text-center">
                            <div class="w-12 h-12 mx-auto mb-2 text-orange-600">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="w-full h-full">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-800">Expert Support</h3>
                            <p class="text-xs text-gray-600">Get guidance</p>
                        </div>
                    </div>

                    <!-- Urgency Message -->
                    <div class="mb-6 p-3 text-center">
                        <p class="text-sm text-gray-700 font-medium">
                            🚀 Limited time: Start learning today
                        </p>
                        <p class="text-xs text-gray-500">
                            Join 10,000+ active learners
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('subscriptions.create') }}"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 text-lg transition-all duration-200 transform hover:scale-105">
                            Subscribe Now
                        </a>

                        <button class="w-full text-gray-600 hover:text-gray-800 text-sm underline transition-colors"
                                onclick="toggleDetails()">
                            Learn more about benefits
                        </button>
                    </div>

                    <!-- Expandable Details -->
                    <div id="details" class="hidden mt-6 p-4 text-left text-sm text-gray-600 space-y-2">
                        <p>✓ Access to all premium courses and materials</p>
                        <p>✓ Interactive quizzes and assessments</p>
                        <p>✓ Downloadable resources and study guides</p>
                        <p>✓ Community forum access</p>
                        <p>✓ Mobile app compatibility</p>
                        <p>✓ 30-day money-back guarantee</p>
                    </div>
                </div>

                <script>
                    function toggleDetails() {
                        const details = document.getElementById('details');
                        details.classList.toggle('hidden');
                    }
                </script>
            @endif
        </section>
    @endif

</x-layouts.app>
