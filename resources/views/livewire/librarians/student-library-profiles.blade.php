<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Student Library Profiles</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage student accounts and track library activity</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $students->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Borrowers</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $students->where('current_borrows', '>', 0)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overdue Books</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $students->where('overdue_borrows', '>', 0)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Suspended</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $students->where('library_suspended', true)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="relative md:col-span-2">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, email, or student ID..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Status Filter -->
            <div>
                <select
                    wire:model.live="statusFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="all">All Students</option>
                    <option value="active">Active Borrowers</option>
                    <option value="overdue">Has Overdue Books</option>
                    <option value="suspended">Suspended</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="name">Name</option>
                    <option value="total_borrows">Total Borrowed</option>
                    <option value="current_borrows">Current Borrows</option>
                    <option value="overdue_borrows">Overdue Books</option>
                    <option value="total_late_fees">Late Fees</option>
                </select>
            </div>

            <!-- Per Page -->
            <div class="flex items-center space-x-2">
                <select
                    wire:model.live="perPage"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="10">10 per page</option>
                    <option value="15">15 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <!-- Sort Direction and Reset -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-2">
                <button
                    wire:click="sortBy('{{ $sortBy }}')"
                    class="px-3 py-1 text-sm rounded-lg transition-colors bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300"
                >
                    {{ ucfirst(str_replace('_', ' ', $sortBy)) }}
                    <svg class="w-4 h-4 ml-1 inline {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $students->total() }} students</span>
                <button
                    wire:click="resetFilters"
                    class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                >
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Student
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Academic Level
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Library Activity
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Late Fees
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- Student Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-violet-600 dark:text-violet-400">
                                                {{ substr($student->user->name ?? 'Unknown', 0, 2) }}
                                            </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student->user->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $student->user->email ?? 'No email' }}
                                    </div>
                                    @if ($student->student_id)
                                        <div class="text-xs text-gray-400">
                                            ID: {{ $student->student_id }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Academic Level -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $student->academicLevel->name ?? 'Not Set' }}
                            </div>
                        </td>

                        <!-- Library Activity -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold">{{ $student->total_borrows ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">Total</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-blue-600">{{ $student->current_borrows ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">Current</div>
                                    </div>
                                    @if ($student->overdue_borrows > 0)
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-red-600">{{ $student->overdue_borrows }}</div>
                                            <div class="text-xs text-gray-500">Overdue</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                @if ($student->library_suspended)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 008.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Suspended
                                        </span>
                                @elseif ($student->overdue_borrows > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Has Overdue
                                        </span>
                                @elseif ($student->current_borrows > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Active
                                        </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                            </svg>
                                            Inactive
                                        </span>
                                @endif
                            </div>
                        </td>

                        <!-- Late Fees -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${{ number_format($student->total_late_fees ?? 0, 2) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- View Profile -->
                                <button
                                    wire:click="openProfileModal({{ $student->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900"
                                    title="View profile"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                <!-- Send Reminder -->
                                @if ($student->overdue_borrows > 0)
                                    <button
                                        wire:click="sendReminder({{ $student->id }})"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900"
                                        title="Send reminder"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                @endif

                                <!-- Suspend/Unsuspend -->
                                @if ($student->library_suspended)
                                    <button
                                        wire:click="unsuspendStudent({{ $student->id }})"
                                        wire:confirm="Are you sure you want to restore this student's library access?"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1 rounded-md hover:bg-green-50 dark:hover:bg-green-900"
                                        title="Restore access"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button
                                        wire:click="suspendStudent({{ $student->id }})"
                                        wire:confirm="Are you sure you want to suspend this student's library access?"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900"
                                        title="Suspend access"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No students found</h3>
                                <p class="text-gray-500 dark:text-gray-400">
                                    @if ($search || $statusFilter !== 'all')
                                        Try adjusting your search criteria or filters.
                                    @else
                                        No students are registered in the system.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($students->hasPages())
        <div class="mt-6">
            {{ $students->links() }}
        </div>
    @endif

    <!-- Student Profile Modal -->
    @if ($showProfileModal && $selectedStudent)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="profile-modal">
            <div class="relative top-8 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $selectedStudent->user->name ?? 'Student' }} - Library Profile
                        </h3>
                        <button
                            wire:click="closeProfileModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Student Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Student Information</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Name:</strong> {{ $selectedStudent->user->name ?? 'Unknown' }}</div>
                                <div><strong>Email:</strong> {{ $selectedStudent->user->email ?? 'No email' }}</div>
                                <div><strong>Student ID:</strong> {{ $selectedStudent->student_id ?? 'Not set' }}</div>
                                <div><strong>Academic Level:</strong> {{ $selectedStudent->academicLevel->name ?? 'Not set' }}</div>
                                @if ($selectedStudent->library_suspended)
                                    <div class="text-red-600 font-semibold">
                                        <strong>Status:</strong> Suspended
                                        <div class="text-xs text-gray-500 mt-1">
                                            Since: {{ $selectedStudent->library_suspended_at ? $selectedStudent->library_suspended_at->format('M d, Y') : 'Unknown' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Library Statistics -->
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Library Statistics</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $studentStats['total_borrowed'] ?? 0 }}</div>
                                    <div class="text-gray-500">Total Borrowed</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $studentStats['total_returned'] ?? 0 }}</div>
                                    <div class="text-gray-500">Total Returned</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $studentStats['currently_borrowed'] ?? 0 }}</div>
                                    <div class="text-gray-500">Currently Borrowed</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ $studentStats['overdue_books'] ?? 0 }}</div>
                                    <div class="text-gray-500">Overdue Books</div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm space-y-1">
                                    <div><strong>Total Late Fees:</strong> ${{ number_format($studentStats['total_late_fees'] ?? 0, 2) }}</div>
                                    <div><strong>Avg. Borrow Duration:</strong> {{ $studentStats['average_borrow_duration'] ?? 0 }} days</div>
                                    <div><strong>Borrowing Frequency:</strong> {{ $studentStats['borrowing_frequency'] ?? 0 }} books/month</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currently Borrowed Books -->
                    @if (isset($studentStats['current_borrows']) && $studentStats['current_borrows']->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Currently Borrowed Books</h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <div class="space-y-2">
                                    @foreach ($studentStats['current_borrows'] as $borrow)
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border">
                                            <div>
                                                <div class="font-medium">{{ $borrow->book->title ?? 'Unknown Book' }}</div>
                                                <div class="text-sm text-gray-500">Due: {{ $borrow->due_date->format('M d, Y') }}</div>
                                            </div>
                                            @if ($borrow->due_date->isPast())
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                                    {{ $borrow->due_date->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                    {{ $borrow->due_date->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Favorite Categories -->
                    @if (isset($studentStats['favorite_categories']) && !empty($studentStats['favorite_categories']))
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Favorite Categories</h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($studentStats['favorite_categories'] as $category)
                                        <span class="px-3 py-1 bg-violet-100 text-violet-800 text-sm rounded-full">
                                            {{ $category['category'] }} ({{ $category['count'] }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Borrowing History -->
                    @if (!empty($borrowingHistory))
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Recent Borrowing History</h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 max-h-64 overflow-y-auto">
                                <div class="space-y-2">
                                    @foreach (array_slice($borrowingHistory, 0, 10) as $borrow)
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border text-sm">
                                            <div>
                                                <div class="font-medium">{{ $borrow['book_copy']['book']['title'] ?? 'Unknown' }}</div>
                                                <div class="text-gray-500">
                                                    Borrowed: {{ \Carbon\Carbon::parse($borrow['borrowed_at'])->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($borrow['returned_at'])
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                        Returned
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
                                                        Active
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Modal Actions -->
                    <div class="flex justify-end space-x-3">
                        <button
                            wire:click="closeProfileModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500"
                        >
                            Close
                        </button>

                        @if ($selectedStudent->overdue_borrows > 0)
                            <button
                                wire:click="sendReminder({{ $selectedStudent->id }})"
                                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700"
                            >
                                Send Reminder
                            </button>
                        @endif

                        @if ($selectedStudent->library_suspended)
                            <button
                                wire:click="unsuspendStudent({{ $selectedStudent->id }})"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                            >
                                Restore Access
                            </button>
                        @else
                            <button
                                wire:click="suspendStudent({{ $selectedStudent->id }})"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                            >
                                Suspend Access
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
