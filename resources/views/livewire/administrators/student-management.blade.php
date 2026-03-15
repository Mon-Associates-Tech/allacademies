<section class="">
    <div class="bg-white dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Management</h1>
                <div class="flex space-x-2">
                    <button
                        x-data
                        @click="$dispatch('open-modal', 'import-students')"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Students
                    </button>
                    <a href="{{ route('admin.students.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add New Student
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div
                    class="mb-6 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg relative">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div
                    class="mb-6 bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg relative">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$standaloneForm)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-colors duration-200">
                <!-- Filters Section -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filters
                            @if($activeFiltersCount > 0)
                                <span
                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                                    {{ $activeFiltersCount }} active
                                </span>
                            @endif
                        </h3>
                        @if($activeFiltersCount > 0)
                            <button wire:click="clearFilters"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear All
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Academic Group Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Academic Group
                            </label>
                            <select wire:model.live="filterAcademicGroup"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Groups</option>
                                @foreach($filterAcademicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Level Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Academic Level
                            </label>
                            <select wire:model.live="filterAcademicLevel"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Levels</option>
                                @foreach($filterAcademicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Student Group Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Student Group
                            </label>
                            <select wire:model.live="filterStudentGroup"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Student Groups</option>
                                @foreach($filterStudentGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Teacher Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Teacher
                            </label>
                            <select wire:model.live="filterTeacher"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Teachers</option>
                                @foreach($filterTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Subject
                            </label>
                            <select wire:model.live="filterSubject"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Subjects</option>
                                @foreach($filterSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                        @if($subject->academicLevel)
                                            ({{ $subject->academicLevel->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- List Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Left Section -->
                        <div class="flex items-center">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Students</h2>
                            <span
                                class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                {{ $students->total() }}
            </span>
                        </div>

                        <!-- Right Section -->
                        <div class="flex items-center gap-4">
                            <!-- View Mode Toggle -->
                            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                <button wire:click="$set('viewMode', 'card')"
                                        class="px-3 py-1.5 rounded-md text-sm font-medium {{ $viewMode === 'card'
                            ? 'bg-white dark:bg-gray-600 text-gray-700 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span class="hidden sm:inline">Cards</span>
                                    </div>
                                </button>
                                <button wire:click="$set('viewMode', 'list')"
                                        class="px-3 py-1.5 rounded-md text-sm font-medium {{ $viewMode === 'list'
                            ? 'bg-white dark:bg-gray-600 text-gray-700 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                        <span class="hidden sm:inline">List</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Search Bar -->
                            <div class="relative">
                                <input type="text"
                                       wire:model.live.debounce.300ms="searchTerm"
                                       placeholder="Search students..."
                                       class="w-full sm:w-64 px-4 py-2 pl-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-6">
                    @if($students->count() > 0)
                        @if($viewMode === 'card')
                            <!-- Card View -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($students as $student)
                                    <div
                                        class="relative group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
                                        <!-- Card Header - Restructured for long text -->
                                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-start space-x-3">
                                                <!-- Avatar -->
                                                <x-avatar :name="$student->user->name"
                                                          avatar="{{ $student->user->avatar }}"
                                                          class="w-10 h-10 rounded-full"></x-avatar>

                                                <!-- Student Info - With text truncation -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="pr-8">
                                                        <!-- Padding right to prevent overlap with actions -->
                                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white leading-5 break-words">
                                                            {{ $student->user->name }}
                                                        </h3>
                                                        <div class="flex items-center flex-wrap gap-2 mt-1">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100">
                                                                {{ $student->user->username ?? 'username pending' }}
                                                            </span>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 break-all">
                                                                {{ $student->user->email ?? 'No email on file' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons - Absolutely positioned -->
                                                <div class="absolute top-4 right-4 flex items-center space-x-1">
                                                    <a href="{{ route('students.show', $student) }}"
                                                       class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-all duration-200"
                                                       title="View Details">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.students.edit', $student->id) }}"
                                                       class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                                       title="Edit Student">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>
                                                    <button wire:click="delete({{ $student->id }})"
                                                            onclick="return confirm('Are you sure you want to delete this student?')"
                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Content -->
                                        <div class="p-4 space-y-3">
                                            <!-- Academic Info -->
                                            <div class="flex items-start space-x-2">
                                                <div class="flex-shrink-0 w-4 h-4 mt-0.5 text-indigo-500">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 break-words">
                                                        {{ $student->academicGroup?->name ?? 'Not Assigned' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        {{ $student->academicLevel?->name ?? 'No Level' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Teachers -->
                                            @php
                                                $primaryTeacher = $student->teachers->where('pivot.is_primary', true)->first();
                                                $teacherCount = $student->teachers->count();
                                            @endphp
                                            <div class="flex items-start space-x-2">
                                                <div class="flex-shrink-0 w-4 h-4 mt-0.5 text-green-500">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    @if($primaryTeacher)
                                                        <p class="text-sm text-gray-600 dark:text-gray-300 break-words">
                                                            {{ $primaryTeacher->user->name }}
                                                        </p>
                                                        @if($teacherCount > 1)
                                                            <p class="text-xs text-gray-400 mt-0.5">
                                                                +{{ $teacherCount - 1 }} other
                                                                teacher{{ $teacherCount - 1 > 1 ? 's' : '' }}
                                                            </p>
                                                        @endif
                                                    @else
                                                        <p class="text-sm text-gray-400 dark:text-gray-500">
                                                            No teachers assigned
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Subjects Summary -->
                                            @php
                                                $subjectDetails = $student->getSubjectDetails();
                                                $totalAccessible = $subjectDetails['total_accessible']->count();
                                                $individualActive = $subjectDetails['individual_active']->count();
                                                $individualRemoved = $subjectDetails['individual_removed']->count();
                                            @endphp
                                            <div
                                                class="flex items-center justify-between pt-3 mt-2 border-t border-gray-100 dark:border-gray-700">
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">Subjects</span>
                                                <div class="flex flex-wrap gap-2 justify-end">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-200">
                                        {{ $totalAccessible }} Total
                                    </span>
                                                    @if($individualActive > 0)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200">
                                            +{{ $individualActive }}
                                        </span>
                                                    @endif
                                                    @if($individualRemoved > 0)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200">
                                            -{{ $individualRemoved }}
                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- List View -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Student
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Academic Info
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Teachers
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Subjects
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div
                                                            class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $student->user->name }}
                                                        </div>
                                                        <div class="mt-1 space-y-1">
                                                            <div>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                                                                    {{ $student->user->username ?? 'username pending' }}
                                                                </span>
                                                            </div>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                                {{ $student->user->email ?? 'No email' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $student->academicGroup?->name ?? 'Not Assigned' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $student->academicLevel?->name ?? 'No Level' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm">
                                                    @php
                                                        $primaryTeacher = $student->teachers->where('pivot.is_primary', true)->first();
                                                        $teacherCount = $student->teachers->count();
                                                    @endphp

                                                    @if($primaryTeacher)
                                                        <div class="font-medium text-gray-900 dark:text-white">
                                                            {{ $primaryTeacher->user->name }}
                                                        </div>
                                                        @if($teacherCount > 1)
                                                            <div class="text-gray-500 dark:text-gray-400">
                                                                +{{ $teacherCount - 1 }}
                                                                other{{ $teacherCount - 1 > 1 ? 's' : '' }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-500 dark:text-gray-400">No teachers assigned</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $subjectDetails = $student->getSubjectDetails();
                                                    $totalAccessible = $subjectDetails['total_accessible']->count();
                                                    $individualActive = $subjectDetails['individual_active']->count();
                                                    $individualRemoved = $subjectDetails['individual_removed']->count();
                                                @endphp
                                                <div class="flex flex-wrap gap-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                                                {{ $totalAccessible }} Total
                                            </span>
                                                    @if($individualActive > 0)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                                    +{{ $individualActive }}
                                                </span>
                                                    @endif
                                                    @if($individualRemoved > 0)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                                    -{{ $individualRemoved }}
                                                </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-endg space-x-2">
                                                    <a href="{{ route('students.show', $student) }}"
                                                       class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 group-hover:opacity-100 transition-all duration-200"
                                                       title="View Details">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.students.edit', $student->id) }}"
                                                       class="inline-flex items-center p-1.5 border border-transparent rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                       title="Edit Student">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>
                                                    <button wire:click="delete({{ $student->id }})"
                                                            onclick="return confirm('Are you sure you want to delete this student?')"
                                                            class="inline-flex items-center p-1.5 border border-transparent rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Pagination -->
                        @if($students->hasPages())
                            <div class="mt-6">
                                {{ $students->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-16 h-16 bg-indigo-100 dark:bg-indigo-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">No students found</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your
                                    first student.</p>
                                <a href="{{ route('admin.students.create') }}"
                                   class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Student
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

@if($standaloneForm)
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            @include('livewire.administrators.partials.student-form-header')
        </div>
        @include('livewire.administrators.partials.student-form-body')
    </div>
@endif
            <x-modal-component name="teacher-add-form">
                <x-slot:header>
                    <div
                        class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Create New Teacher</h3>
                        </div>
                    </div>
                </x-slot:header>

                <x-slot:footer>
                    <div
                        class="flex justify-between">
                        <button type="button" onclick="window.Modal.close('teacher-add-form')"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <x-button.primary type="submit"
                                          form="teacher-add-form"
                                          class="inline-flex items-center py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create Teacher
                        </x-button.primary>
                    </div>
                </x-slot:footer>
                <div
                    class="relative w-full">
                    <form id="teacher-add-form" wire:submit.prevent="createTeacher" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="teacherName"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                                       placeholder="Enter teacher's full name">
                                @error('teacherName') <p
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="email" wire:model="teacherEmail"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                                       placeholder="teacher@example.com">
                                @error('teacherEmail') <p
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" wire:model="teacherPassword"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                                       placeholder="Enter password">
                                @error('teacherPassword') <p
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            @if($academicGroupId || $academicLevelId)
                                <div
                                    class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        This teacher will be automatically assigned
                                        to
                                        the selected academic group and level.
                                    </p>
                                </div>
                            @endif
                        </div>


                    </form>
                </div>

            </x-modal-component>
            <x-modal-component name="teacher-manage-form">

                <x-slot:header>
                    <div
                        class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Manage Teachers</h3>
                        </div>
                    </div>
                </x-slot:header>
                <div class="p-6 max-h-96 overflow-y-auto">
                    <!-- Academic Group Teachers -->
                    @if($academicGroupId)
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-white" fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    Academic Group Teachers</h4>
                            </div>

                            <!-- Assign Teachers to Group -->
                            <div
                                class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                                <label
                                    class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-3">Assign
                                    Teachers to Group</label>
                                <div class="space-y-3">
                                    <select
                                        wire:model="selectedTeachersForGroup"
                                        multiple
                                        class="w-full px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-lg bg-white dark:bg-blue-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                        size="4">
                                        @foreach($teachersToAssignToGroup as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                    class="py-2">{{ $teacher->user->name }}
                                                ({{ $teacher->user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="assignTeachersToGroup"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Assign to Group
                                    </button>
                                </div>
                            </div>

                            <!-- Current Group Teachers -->
                            <div class="mb-6">
                                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Current Teachers in Group</h5>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @forelse($groupTeachers as $teacher)
                                        <div
                                            class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div
                                                class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                                                                                        <span
                                                                                            class="text-xs font-medium text-white">{{ strtoupper(substr($teacher->user->name, 0, 2)) }}</span>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->user->name }}</span>
                                            </div>
                                            <button type="button"
                                                    wire:click="removeTeacherFromGroup({{ $teacher->id }})"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3 mr-1"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                            No teachers assigned to this group
                                            yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Academic Level Teachers -->
                    @if($academicLevelId)
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-white" fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    Academic Level Teachers</h4>
                            </div>

                            <!-- Assign Teachers to Level -->
                            <div
                                class="mb-6 p-4 bg-purple-50 dark:bg-purple-900 rounded-lg border border-purple-200 dark:border-purple-700">
                                <label
                                    class="block text-sm font-medium text-purple-800 dark:text-purple-200 mb-3">Assign
                                    Teachers to Level</label>
                                <div class="space-y-3">
                                    <select
                                        wire:model="selectedTeachersForLevel"
                                        multiple
                                        class="w-full px-3 py-2 border border-purple-300 dark:border-purple-600 rounded-lg bg-white dark:bg-purple-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-colors duration-200"
                                        size="4">
                                        @foreach($teachersToAssignToLevel as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                    class="py-2">{{ $teacher->user->name }}
                                                ({{ $teacher->user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="assignTeachersToLevel"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Assign to Level
                                    </button>
                                </div>
                            </div>

                            <!-- Current Level Teachers -->
                            <div class="mb-6">
                                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Current Teachers in Level</h5>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @forelse($levelTeachers as $teacher)
                                        <div
                                            class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div
                                                class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-purple-400 to-purple-500 rounded-full flex items-center justify-center">
                                                                                        <span
                                                                                            class="text-xs font-medium text-white">{{ strtoupper(substr($teacher->user->name, 0, 2)) }}</span>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->user->name }}</span>
                                            </div>
                                            <button type="button"
                                                    wire:click="removeTeacherFromLevel({{ $teacher->id }})"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3 mr-1"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                            No teachers assigned to this level
                                            yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <x-slot:footer>
                    <div
                        class="flex justify-end">
                        <button type="button" onclick="window.Modal.close('teacher-manage-form')"
                                wire:click="closeManageTeachersModal"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal-component>
        </div>
    </div>
    <x-modal name="import-students" :show="false">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Import Students
                    </h3>
                    <div class="mt-2">
                        <form id="import-form" action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    CSV File
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md p-6 w-full text-center cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                CSV files only
                                            </p>
                                        </div>
                                        <input type="file" name="file" class="hidden" accept=".csv">
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Default School
                                </label>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('owner'))
                                    <select name="school_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">Select School</option>
                                        @foreach(App\Models\School::all() as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                                    <div class="mt-1 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                                        <p class="text-sm text-blue-800 dark:text-blue-200">All imported data will be associated with your school: <strong>{{ auth()->user()->school->name ?? 'N/A' }}</strong></p>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit" form="import-form" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 dark:bg-indigo-700 text-base font-medium text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                Import
            </button>
            <button @click="$dispatch('close-modal', 'import-students')" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
            </button>
        </div>
    </x-modal>
</section>
