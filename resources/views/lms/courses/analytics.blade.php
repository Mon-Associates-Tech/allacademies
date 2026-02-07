<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('course-management.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Course Management
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">Analytics</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Course Analytics</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $course->title }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('course-management.enrollments', $course) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        View Enrollments
                    </a>
                    <a href="{{ route('course-management.edit', $course) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit Course
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Enrollments --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Enrollments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $enrollmentStats['total'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Completion Rate --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Rate</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completionRate }}%</p>
                    </div>
                </div>
            </div>

            {{-- Average Progress --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Progress</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($averageProgress, 1) }}%</p>
                    </div>
                </div>
            </div>

            {{-- Average Grade --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Grade</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $averageGrade ? number_format($averageGrade, 1) . '%' : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Enrollment Status Breakdown --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enrollment Status</h2>
                <div class="space-y-4">
                    {{-- Enrolled --}}
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Enrolled (Not Started)</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $enrollmentStats['enrolled'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $enrollmentStats['total'] > 0 ? ($enrollmentStats['enrolled'] / $enrollmentStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- In Progress --}}
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">In Progress</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $enrollmentStats['in_progress'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $enrollmentStats['total'] > 0 ? ($enrollmentStats['in_progress'] / $enrollmentStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Completed --}}
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Completed</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $enrollmentStats['completed'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $enrollmentStats['total'] > 0 ? ($enrollmentStats['completed'] / $enrollmentStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Dropped --}}
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Dropped</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $enrollmentStats['dropped'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $enrollmentStats['total'] > 0 ? ($enrollmentStats['dropped'] / $enrollmentStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Content Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Course Content</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $course->chapters->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Chapters</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $course->sections()->count() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sections</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $course->getTotalContentsCount() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Contents</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $course->getRequiredContentsCount() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Required Contents</p>
                    </div>
                </div>

                {{-- Course Info --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($course->status === 'published') bg-green-100 text-green-800
                                    @elseif($course->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Difficulty</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($course->difficulty_level) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Price</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $course->is_free ? 'Free' : number_format($course->price, 2) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Audience</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $course->audience)) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Recent Enrollments --}}
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Enrollments</h2>
                <a href="{{ route('course-management.enrollments', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    View all →
                </a>
            </div>
            @if($recentEnrollments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Student
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Progress
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Enrolled
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentEnrollments as $enrollment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($enrollment->user->avatar)
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $enrollment->user->avatar) }}" alt="{{ $enrollment->user->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-600 font-medium text-sm">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($enrollment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($enrollment->status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($enrollment->status === 'enrolled') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-700 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $enrollment->enrolled_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No enrollments yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Students will appear here once they enroll in this course.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
