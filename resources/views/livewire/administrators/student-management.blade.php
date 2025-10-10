<section class="">
    <div class="bg-white dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Management</h1>
                <x-button.primary type="button" size="sm"
                                  onclick="window.Modal.open('student-add-form')"
                                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Student
                </x-button.primary>
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-colors duration-200">
                <!-- List Header -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-colors duration-200">
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
                                           wire:model.debounce.300ms="searchTerm"
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
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 break-all">
                                                                {{ $student->user->email }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Action Buttons - Absolutely positioned -->
                                                    <div class="absolute top-4 right-4 flex items-center space-x-1">
                                                        <a href="{{ route('students.show', $student) }}"
                                                           class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-all duration-200"
                                                           title="View Details">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                        <button wire:click="edit({{ $student->id }})"
                                                                class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                 viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>
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
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $student->user->email }}
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
                                                    <div class="flex justify-end space-x-2">
                                                        <a href="{{ route('students.show', $student) }}"
                                                           class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 dark:bg-gray-700 opacity-0 group-hover:opacity-100 transition-all duration-200"
                                                           title="View Details">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                        <button wire:click="edit({{ $student->id }})"
                                                                class="inline-flex items-center p-1.5 border border-transparent rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                 viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>
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
                                    <button onclick="window.Modal.open('student-add-form')"
                                            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add Student
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <x-modal-component name="student-add-form" size="4xl"
                                   header-background=""
                >
                    <x-slot:header>

                            <div class="flex justify-between space-x-4">
                                <div class="flex-shrink-0 flex my-auto">
                                    <div
                                        class="w-12 h-12 my-auto bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center ring-2 ring-white ring-opacity-30">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="my-auto">
                                        <h2 class="text-2xl font-bold" id="modal-title">
                                            {{ $formMode === 'edit' ? 'Edit Student Profile' : 'Create New Student' }}
                                        </h2>
                                        <p class="text-indigo-100 hidden text-sm mt-1">
                                            {{ $formMode === 'edit' ? 'Update student information and academic assignments' : 'Add a new student to the academic system' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    @if($academicGroupId)
                                        <button type="button"
                                                onclick="window.Modal.open('teacher-manage-form')"
                                                class="inline-flex items-center px-4 py-2.5 border border-white border-opacity-30 text-sm font-medium rounded-xl shadow-sm text-white bg-white bg-opacity-10 hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-200 backdrop-blur-sm transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Manage Teachers
                                        </button>
                                    @endif

                                    <button
                                        onclick="window.Modal.open('teacher-add-form')"
                                        class="inline-flex items-center px-4 py-2.5 border border-white border-opacity-30 text-sm font-medium rounded-xl shadow-sm text-white bg-white bg-opacity-10 hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-200 backdrop-blur-sm transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Teacher
                                    </button>

                                    @if($isEditing)
                                        <button type="button" wire:click="resetForm"
                                                class="inline-flex items-center px-4 py-2.5 border border-white border-opacity-30 rounded-xl shadow-sm text-sm font-medium text-white bg-white bg-opacity-10 hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-200 backdrop-blur-sm transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                    @endif
                                </div>
                            </div>
                    </x-slot:header>

                    <div class="p-6 bg-white dark:bg-gray-800">
                        <form id="student-add-form"
                              wire:submit.prevent="{{ $formMode === 'edit' ? 'update' : 'create' }}">
                            <!-- Basic Information Section -->
                            <div class="mb-10">
                                <div class="flex items-center mb-6">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl mr-4 shadow-lg">
                                        <span class="text-lg font-bold text-white">1</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Basic Information</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Essential student details and contact
                                            information</p>
                                    </div>
                                    <div
                                        class="h-px flex-1 bg-gradient-to-r from-blue-200 to-transparent dark:from-blue-800"></div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- Full Name Field -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-blue-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Full Name
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative group">
                                            <input type="text" wire:model="name"
                                                   class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                                                   placeholder="Enter student's full name">
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('name')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Email Field -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-blue-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                            Email Address
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative group">
                                            <input type="email" wire:model="email"
                                                   class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                                                   placeholder="student@example.com">
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('email')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Second Row -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
                                    <!-- Password Field -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-blue-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Password
                                            @if($isEditing)
                                                <span
                                                    class="text-gray-500 text-xs ml-2 font-normal">(leave blank to keep current)</span>
                                            @else
                                                <span class="text-red-500 ml-1">*</span>
                                            @endif
                                        </label>
                                        <div class="relative group">
                                            <input type="password" wire:model="password"
                                                   class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 group-hover:shadow-md"
                                                   placeholder="Enter secure password">
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('password')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Student Group Field -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-blue-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Student Group
                                            <span
                                                class="text-gray-500 text-xs ml-2 font-normal">(Optional)</span>
                                        </label>
                                        <div class="relative group">
                                            <select wire:model="studentGroupId"
                                                    class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md">
                                                <option value="">-- Select Student Group
                                                    --
                                                </option>
                                                @foreach($studentGroups as $group)
                                                    <option
                                                        value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('studentGroupId')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Academic Information Section -->
                            <div
                                class="border-t border-gray-200 dark:border-gray-600 pt-10">
                                <div class="flex items-center mb-8">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl mr-4 shadow-lg">
                                        <span class="text-lg font-bold text-white">2</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Academic Assignment</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Academic groups, levels, and subject
                                            assignments</p>
                                    </div>
                                    <div
                                        class="h-px flex-1 bg-gradient-to-r from-purple-200 to-transparent dark:from-purple-800"></div>
                                </div>

                                <!-- Academic Group & Level Selection -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                                    <!-- Academic Group -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-purple-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Academic Group
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative group">
                                            <select wire:model.live="academicGroupId"
                                                    wire:key="academic-group-select"
                                                    class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md">
                                                <option value="">-- Select Academic Group
                                                    --
                                                </option>
                                                @foreach($academicGroups as $group)
                                                    <option
                                                        value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('academicGroupId')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Academic Level -->
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2 text-purple-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Academic Level
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative group">
                                            <select wire:model.live="academicLevelId"
                                                    wire:key="academic-level-select-{{ $academicGroupId }}"
                                                    class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none group-hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                                    @if(!$academicGroupId || $academicLevels->isEmpty()) disabled @endif>
                                                <option value="">
                                                    @if(!$academicGroupId)
                                                        -- Select Academic Group First --
                                                    @else
                                                        -- Select Academic Level --
                                                    @endif
                                                </option>
                                                @foreach($academicLevels as $level)
                                                    <option
                                                        value="{{ $level->id }}">{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                                <svg
                                                    class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors duration-200"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('academicLevelId')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Teacher Assignment -->
                                @if(!empty($availableTeachers) && count($availableTeachers) > 0)
                                    <div
                                        class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 rounded-2xl border border-blue-200 dark:border-blue-700 shadow-lg">
                                        <div class="flex items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-md">
                                                    <svg class="w-5 h-5 text-white"
                                                         fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    Teacher Assignment</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    Select teachers for this student and
                                                    assign a primary contact</p>
                                            </div>
                                        </div>

                                        <!-- Teachers Selection -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Available Teachers
                                            </label>
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-4 border border-blue-200 dark:border-blue-700 rounded-xl bg-white dark:bg-gray-800 shadow-inner">
                                                @foreach($availableTeachers as $teacher)
                                                    <label
                                                        class="flex items-center space-x-3 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 p-3 rounded-lg transition-all duration-200 group">
                                                        <input type="checkbox"
                                                               wire:model="selectedTeachers"
                                                               value="{{ $teacher->id }}"
                                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 transition-all duration-200">
                                                        <span
                                                            class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-700 dark:group-hover:text-blue-300 font-medium">{{ $teacher->user->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Primary Teacher Selection -->
                                        @if(!empty($selectedTeachers))
                                            <div
                                                class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-blue-200 dark:border-blue-700">
                                                <label
                                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    Primary Teacher
                                                </label>
                                                <div class="relative group">
                                                    <select wire:model="primaryTeacherId"
                                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 appearance-none">
                                                        <option value="">-- Select Primary
                                                            Teacher --
                                                        </option>
                                                        @foreach($availableTeachers->whereIn('id', $selectedTeachers) as $teacher)
                                                            <option
                                                                value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <svg
                                                            class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                                                    The primary teacher will be the main
                                                    contact for this student.</p>
                                            </div>
                                        @endif

                                        @error('selectedTeachers')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                        @error('primaryTeacherId')
                                        <div
                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-red-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span
                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                @endif

                                <!-- Subject Assignment -->
                                @if(!empty($levelSubjects) && count($levelSubjects) > 0)
                                    <div
                                        class="mb-8 p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900 rounded-2xl border border-green-200 dark:border-green-700 shadow-lg">
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                                                        <svg class="w-5 h-5 text-white"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        Subject Access</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Students automatically have access
                                                        to all subjects in their academic
                                                        level. You can customize access as
                                                        needed.
                                                    </p>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    wire:click="$toggle('showIndividualSubjects')"
                                                    class="inline-flex items-center px-4 py-2 border border-green-300 dark:border-green-600 rounded-xl shadow-sm text-sm font-medium text-green-700 dark:text-green-300 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    @if($showIndividualSubjects)
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878L4.2 4.2m9.646 7.096l3.536 3.536M21 12c0 2.5-1 4.5-2.5 6.5"></path>
                                                    @else
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    @endif
                                                </svg>
                                                {{ $showIndividualSubjects ? 'Hide' : 'Show' }}
                                                Individual Assignments
                                            </button>
                                        </div>

                                        <!-- Academic Level Subjects (Always included) -->
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 rounded-full text-xs font-bold mr-2">
                                        {{ count($levelSubjects) }}
                                    </span>
                                                Academic Level Subjects
                                            </h5>
                                            <div
                                                class="p-4 bg-green-100 dark:bg-green-800 border border-green-200 dark:border-green-700 rounded-xl">
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach($levelSubjects as $subject)
                                                        <div
                                                            class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-200">
                                                    {{ $subject->name }}
                                                </span>
                                                            @if(in_array($subject->id, $removedSubjects))
                                                                <span
                                                                    class="text-xs text-red-600 dark:text-red-400 font-medium">(Removed)</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        @if($showIndividualSubjects)
                                            <!-- Individual Subject Management -->
                                            <div
                                                class="space-y-6 mt-6 pt-6 border-t border-green-200 dark:border-green-700">
                                                <!-- Remove Level Subjects -->
                                                @if(!empty($levelSubjects) && count($levelSubjects) > 0)
                                                    <div
                                                        class="p-4 bg-red-50 dark:bg-red-900 rounded-xl border border-red-200 dark:border-red-700">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                            Remove Academic Level Subjects
                                                        </label>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                                            Select subjects to remove from
                                                            this student's access (overrides
                                                            academic level access).
                                                        </p>
                                                        <div
                                                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-32 overflow-y-auto p-3 border border-red-200 dark:border-red-700 rounded-lg bg-white dark:bg-gray-800">
                                                            @foreach($levelSubjects as $subject)
                                                                <label
                                                                    class="flex items-center space-x-2 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900 p-2 rounded transition-all duration-200">
                                                                    <input type="checkbox"
                                                                           wire:model="removedSubjects"
                                                                           value="{{ $subject->id }}"
                                                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                                                    <span
                                                                        class="text-sm text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @error('removedSubjects')
                                                        <div
                                                            class="flex items-center mt-2 p-2 bg-red-100 dark:bg-red-800 rounded-lg">
                                                            <svg
                                                                class="w-4 h-4 mr-2 text-red-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span
                                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                                        </div>
                                                        @enderror
                                                    </div>
                                                @endif

                                                <!-- Add Additional Subjects -->
                                                @if(!empty($availableAdditionalSubjects) && count($availableAdditionalSubjects) > 0)
                                                    <div
                                                        class="p-4 bg-blue-50 dark:bg-blue-900 rounded-xl border border-blue-200 dark:border-blue-700">
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                            Add Additional Subjects
                                                        </label>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                                            Select subjects from other
                                                            academic levels to add to this
                                                            student's access.
                                                        </p>
                                                        <div
                                                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-3 border border-blue-200 dark:border-blue-700 rounded-lg bg-white dark:bg-gray-800">
                                                            @foreach($availableAdditionalSubjects as $subject)
                                                                <label
                                                                    class="flex items-center space-x-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 p-2 rounded transition-all duration-200">
                                                                    <input type="checkbox"
                                                                           wire:model="additionalSubjects"
                                                                           value="{{ $subject->id }}"
                                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                                                                    <div class="flex-1">
                                                                                                    <span
                                                                                                        class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $subject->name }}</span>
                                                                        <div
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            ({{ $subject->academicLevel->name ?? 'Other Level' }}
                                                                            )
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @error('additionalSubjects')
                                                        <div
                                                            class="flex items-center mt-2 p-2 bg-red-50 dark:bg-red-900 rounded-lg">
                                                            <svg
                                                                class="w-4 h-4 mr-2 text-red-500"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span
                                                                class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                                                        </div>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <x-slot:footer>
                        <!-- Form Actions -->
                        <div
                            class="flex items-center justify-between">
                            <div
                                class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-red-500"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                Required fields
                            </div>
                            <div class="flex items-center space-x-4">
                                <button type="button"
                                        wire:click="hideForm" onclick="window.Modal.close('student-add-form')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <x-button.primary type="submit"
                                                  form="student-add-form"
                                                  class="inline-flex items-center px-8 py-3.5 border border-transparent text-sm font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 hover:from-indigo-600 hover:via-purple-700 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        @if($isEditing)
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        @else
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        @endif
                                    </svg>
                                    {{ $isEditing ? 'Update Student' : 'Create Student' }}
                                </x-button.primary>
                            </div>
                        </div>
                    </x-slot:footer>
                </x-modal-component>
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
    </div>
</section>





