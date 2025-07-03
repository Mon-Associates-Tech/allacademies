<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">My Teaching Subjects</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage and view all subjects assigned to you</p>
            </div>
        </div>

        <!-- Teacher Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Academic Groups Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Academic Groups</h3>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $academicGroups->count() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    @foreach($academicGroups as $group)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 mr-2 mb-2">
                            {{ $group->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Academic Levels Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Academic Levels</h3>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $academicLevels->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    @foreach($academicLevels as $level)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mr-2 mb-2">
                            {{ $level->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Total Subjects Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Subjects</h3>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $subjects->total() }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and filters -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live="searchTerm" type="text"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                               placeholder="Search subjects, codes, levels, or groups...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Grid -->
        @if($subjects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($subjects as $subject)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $subject->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        Code: {{ $subject->code }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Level and Group -->
                            <div class="mb-4">
                                @if($subject->academicLevel)
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $subject->academicLevel->name }}
                                        </span>
                                        @if($subject->academicLevel->academicGroup)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ $subject->academicLevel->academicGroup->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Teachers -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Teaching with:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($subject->teachers as $teacher)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $teacher->id === $this->teacher->id ? 'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ $teacher->user->name }}
                                            @if($teacher->id === $this->teacher->id)
                                                (You)
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <button wire:click="showSubjectDetails({{ $subject->id }})"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-violet-700 dark:text-violet-300 bg-violet-100 dark:bg-violet-900 hover:bg-violet-200 dark:hover:bg-violet-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $subjects->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No subjects found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($searchTerm)
                        Try adjusting your search terms or clearing the search.
                    @else
                        You haven't been assigned to any subjects yet.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Subject Details Modal -->
    @if($showSubjectModal && $selectedSubject)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedSubject->name }}
                                </h3>
                                <button wire:click="closeSubjectModal" class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Basic Info -->
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Code:</span> {{ $selectedSubject->code }}
                                    </p>
                                    @if($selectedSubject->academicLevel)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Academic Level:</span> {{ $selectedSubject->academicLevel->name }}
                                        </p>
                                    @endif
                                    @if($selectedSubject->academicLevel && $selectedSubject->academicLevel->academicGroup)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Academic Group:</span> {{ $selectedSubject->academicLevel->academicGroup->name }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Teaching Staff -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Teaching Staff</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedSubject->teachers as $teacher)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $teacher->id === $this->teacher->id ? 'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ $teacher->user->name }}
                                                @if($teacher->id === $this->teacher->id)
                                                    (You)
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Statistics -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Lessons</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedSubject->lessons->count() }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Topics</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedSubject->academicTopics->count() }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Quizzes</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedSubject->quizzes->count() }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Examinations</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedSubject->examinations->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button wire:click="closeSubjectModal" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
